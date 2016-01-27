<?php
/**
 * Created by wir_wolf.
 * User: Andru Cherny
 * Date: 27.01.16
 * Time: 15:30
 */

namespace wirwolf\yii2RequestValidator;

/**
 * Class ActionParams
 * @package wirwolf\yii2RequestValidator
 * @TODO add value validation system
 */
/*'actions' => [
    'index' => [
        'post' => [
            'headers' => [
                'Auth' => new ActionParamValue(true,NumberValidator::className(),50)
            ],
            'fields' => [
                'key' => new ActionParamValue(true,NumberValidator::className(),10),
                'subKey' => [
                    'key' => new ActionParamValue(true,NumberValidator::className(),10000),
                ]
            ],
        ]
    ],
],*/
class ActionParamValue
{
    /**
     * ActionParamValue constructor.
     * @param $isRequired
     * @param $type
     * @param $default
     */
    public function __construct($isRequired,$type,$default) {

    }

}
