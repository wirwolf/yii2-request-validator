Yii2 Request Validator
============================

Library to validate input data(method,payload,headers)

Installation
------------

Run command 

```
composer require wirwolf/yii2-request-validator
composer update
```

Or add

```
"wirwolf/yii2-request-validator": "*"
```

to the require section of your `composer.json` file.

Usage
------------

In any controller add behavior requestValidator.

Example:
```php
    public function behaviors()
    {
        return [
            'requestValidator' => [
                'class' => ActionValidator::className(),
                'actions' => [
                    'index' => [
                        'get' => [
                            'headers' => false,
                            'fields' => [
                                'key' => '',
                                'recursiveKey' => ['key']
                            ],
                        ]
                    ],
                ],
            ]
        ];
    }

```

TODO:
--------------

Bugfix: remote any value from fields

Add: field type validator. Required, type(\yii\validators) and default value

Add: Functional tests
