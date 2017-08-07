<?php
return array(
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
        'invokables' => array(
            'upload_file'  => 'Services\Service\UploadFile',
            'send_mail' => 'Services\Service\SendMail',
            'encrypt' => 'Services\Service\Encrypt',
            'cache' => 'Services\Service\Cache',
            'writer' => 'Services\Service\Writer',
        )
    ),
);