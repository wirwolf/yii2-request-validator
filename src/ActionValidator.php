<?php
namespace wirwolf\yii2RequestValidator;
/**
 * Created by wir_wolf.
 * User: Andru Cherny
 * Date: 27.01.16
 * Time: 12:43
 */
use yii\base\Action;
use yii\base\Behavior;
use yii\base\InvalidParamException;
use yii\helpers\BaseArrayHelper;
use yii\web\Controller;
use yii\web\HeaderCollection;
use yii\web\MethodNotAllowedHttpException;

/**
 * Class Validator
 * @package wirwolf\yii2RequestValidator
 */
class ActionValidator extends Behavior
{

    public $actions = [];


    /**
     * Declares event handlers for the [[owner]]'s events.
     * @return array events (array keys) and the corresponding event handler methods (array values).
     */
    public function events()
    {
        return [Controller::EVENT_BEFORE_ACTION => 'beforeAction'];
    }

    /**
     * @var Action
     */
    private $requestAction = null;
    /**
     * @var HeaderCollection
     */
    private $requestHeaders;
    /**
     * @var string
     */
    private $requestMethod = null;
    /**
     * @var array
     */
    private $requestData = [];

    private $currentParams;


    public function beforeAction($event)
    {
        $this->requestAction = $event->action;
        $this->requestHeaders = \Yii::$app->request->getHeaders();
        $this->requestData = BaseArrayHelper::merge(\Yii::$app->request->getBodyParams(),\Yii::$app->request->get());
        $this->requestMethod = \Yii::$app->request->getMethod();
        if($this->requestMethod === 'OPTIONS') {
            return;
        }
        $this->checkAction();
        $this->checkMethod();
        $this->checkRequestStructure();
        $this->checkHeaders();
        return true;
    }

    /**
     * @return bool
     */
    private function checkAction()
    {
        $action = $this->requestAction->id;
        if (isset($this->actions[$action])) {
            $this->currentParams = $this->actions[$action];
            return true;
        } elseif (isset($this->actions['*'])) {
            $this->currentParams = $this->actions['*'];
           return true;
        } else {
            throw new InvalidParamException(\Yii::t('system','Request method \'{0}\' is not allowed',[$this->requestAction->id]), 405);
        }
    }

    /**
     * @return bool
     * @throws MethodNotAllowedHttpException
     */
    private function checkMethod() {
        $allowed = array_map('strtoupper', array_keys($this->currentParams));
        if (!in_array($this->requestMethod, $allowed)) {
            // http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.7
            \Yii::$app->getResponse()->getHeaders()->set('Allow', implode(', ', $allowed));
            throw new MethodNotAllowedHttpException(\Yii::t('system','This url can only handle the following request methods:{0}',[ implode(', ', $allowed) ]));
        }
        $this->currentParams = $this->currentParams[strtolower($this->requestMethod)];
        return true;
    }


    /**
     * @return bool
     */
    private function checkRequestStructure()
    {
        if(!isset($this->currentParams['fields']))
        {
            throw new InvalidParamException('Property fields not found in config. Mode info:https://github.com/wirwolf/yii2-request-validator/wiki/Errors#property-fields-not-found-in-config');
        }

        $res = ArrayHelper::arrayStructureKeyDiffKeys($this->currentParams['fields'], ArrayHelper::toArray($this->requestData));
        foreach ($res as $val) {
            if (ArrayHelper::arrayKeyExistsRecursive((string)$val, $this->currentParams['fields'])) {
                $missing[] = $val;
            }
        }
        if (isset($missing)) {
            throw new InvalidParamException(\Yii::t('system','Parameters {0} not found',[ implode(',', $missing) ]), 400);
        }
        return true;
    }

    /**
     * @return bool
     */
    private function checkHeaders()
    {
        if(!isset($this->currentParams['headers'])) {
            return true;
        }

        $originalStructure = $this->currentParams['headers'];

        /**
         * if token validator is active
         */

        foreach ($originalStructure as $header) {
            if (!$this->requestHeaders->get($header)) {
                throw new InvalidParamException(\Yii::t('system','Header \'{0}\' is empty',[$header]), 406);
            }
        }
        return true;
    }


}
