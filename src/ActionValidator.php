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
        $this->requestData = \Yii::$app->request->getBodyParams();
        $this->requestMethod = \Yii::$app->request->getMethod();
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
            throw new InvalidParamException('Request method '.$this->requestAction->id.' is not allowed', 405);
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
            throw new MethodNotAllowedHttpException('Method Not Allowed. This url can only handle the following request methods: ' . implode(', ', $allowed) . '.');
        }
        $this->currentParams = $this->currentParams[strtolower($this->requestMethod)];
        return true;
    }


    /**
     * @return bool
     */
    private function checkRequestStructure()
    {
        if(false == $this->currentParams['fields']) {
            return true;
        }
        $res = ArrayHelper::arrayStructureKeyDiffKeys($this->currentParams['fields'], ArrayHelper::toArray($this->requestData));
        foreach ($res as $val) {
            if (ArrayHelper::arrayKeyExistsRecursive((string)$val, $this->currentParams['fields'])) {
                $missing[] = $val;
            }
        }
        if (isset($missing)) {
            throw new InvalidParamException('Parameters ' . implode(',', $missing) . ' not found', 400);
        }
        return true;
    }

    /**
     * @throws \Exception
     */
    private function checkHeaders()
    {
        $originalStructure = $this->currentParams['headers'];
        if(false == $originalStructure) {
            return true;
        }
        /**
         * if token validator is active
         */

        foreach ($originalStructure as $header) {
            if (!$this->requestHeaders->get($header)) {
                throw new InvalidParamException('Header' . $header . 'is empty', 406);
            }
        }
        return true;
    }


}