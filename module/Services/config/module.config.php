<?php
return array(
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
            'SendMail' => function($sm) {
                return new \Services\Service\SendMail($sm);
            }
        ),
        'invokables' => array(
            'UploadFile'   => 'Services\Service\UploadFile',
            'Encrypt'       => 'Services\Service\Encrypt',
            'Cache'         => 'Services\Service\Cache',
            'Writer'        => 'Services\Service\Writer',
        )
    ),
);