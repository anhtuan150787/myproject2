<?php
return array(
    'router' => array(
        'routes' => array(
            'captcha' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/captcha-reload',
                    'defaults' => [
                        '__NAMESPACE__' => 'Home\Controller',
                        'controller' => 'Home\Controller\Captcha',
                        'action'     => 'index',
                    ],
                ],
            ],


            'home-product-category' => [
                'type' => 'Segment',
                'options' => array(
                    'route' => '/[:name]-pc-[:id]',
                    'constraints' => [
                        'controller' => 'Home\Controller\Product',
                        'action' => 'category',
                        'name' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*'
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'Home\Controller',
                        'controller' => 'Home\Controller\Product',
                        'action' => 'category',
                    ],
                ),
            ],
            'home-product-detail' => [
                'type' => 'Segment',
                'options' => array(
                    'route' => '/[:name]-pd-[:id]',
                    'constraints' => [
                        'controller' => 'Home\Controller\Product',
                        'action' => 'detail',
                        'name' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*'
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'Home\Controller',
                        'controller' => 'Home\Controller\Product',
                        'action' => 'detail',
                    ],
                ),
            ],
            'product-all' => [
                'type' => 'Segment',
                'options' => array(
                    'route' => '/san-pham',
                    'constraints' => [
                        'controller' => 'Home\Controller\Product',
                        'action' => 'all',
                        'name' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*'
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'Home\Controller',
                        'controller' => 'Home\Controller\Product',
                        'action' => 'all',
                    ],
                ),
            ],
            'product-sale' => [
                'type' => 'Segment',
                'options' => array(
                    'route' => '/san-pham-sale',
                    'constraints' => [
                        'controller' => 'Home\Controller\Product',
                        'action' => 'sale',
                        'name' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*'
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'Home\Controller',
                        'controller' => 'Home\Controller\Product',
                        'action' => 'sale',
                    ],
                ),
            ],

            'home-news-category' => [
                'type' => 'Segment',
                'options' => array(
                    'route' => '/[:name]-nc-[:id]',
                    'constraints' => [
                        'controller' => 'Home\Controller\News',
                        'action' => 'category',
                        'name' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*'
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'Home\Controller',
                        'controller' => 'Home\Controller\News',
                        'action' => 'category',
                    ],
                ),
            ],
            'home-news-detail' => [
                'type' => 'Segment',
                'options' => array(
                    'route' => '/[:name]-n-[:id]',
                    'constraints' => [
                        'controller' => 'Home\Controller\News',
                        'action' => 'detail',
                        'name' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*'
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'Home\Controller',
                        'controller' => 'Home\Controller\News',
                        'action' => 'detail',
                    ],
                ),
            ],
            'home-page' => [
                'type' => 'Segment',
                'options' => array(
                    'route' => '/[:name]-p-[:id]',
                    'constraints' => [
                        'controller' => 'Home\Controller\Page',
                        'action' => 'index',
                        'name' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*'
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'Home\Controller',
                        'controller' => 'Home\Controller\Page',
                        'action' => 'index',
                    ],
                ),
            ],
            'home-order' => [
                'type' => 'Segment',
                'options' => array(
                    'route' => '/dat-hang[:id]',
                    'constraints' => [
                        'controller' => 'Home\Controller\Order',
                        'action' => 'index',
                        'name' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*'
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'Home\Controller',
                        'controller' => 'Home\Controller\Order',
                        'action' => 'index',
                    ],
                ),
            ],
            'home-order-delete' => [
                'type' => 'Segment',
                'options' => array(
                    'route' => '/xoa-don-hang-[:id]',
                    'constraints' => [
                        'controller' => 'Home\Controller\Order',
                        'action' => 'delete-item',
                        'id' => '[0-9]*'
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'Home\Controller',
                        'controller' => 'Home\Controller\Order',
                        'action' => 'delete-item',
                    ],
                ),
            ],
            'home-order-update' => [
                'type' => 'Segment',
                'options' => array(
                    'route' => '/cap-nhat-don-hang',
                    'constraints' => [
                        'controller' => 'Home\Controller\Order',
                        'action' => 'update',
                        'id' => '[0-9]*'
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'Home\Controller',
                        'controller' => 'Home\Controller\Order',
                        'action' => 'update',
                    ],
                ),
            ],
            'home-order-confirm' => [
                'type' => 'Segment',
                'options' => array(
                    'route' => '/xac-nhan-don-hang',
                    'constraints' => [
                        'controller' => 'Home\Controller\Order',
                        'action' => 'confirm',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'Home\Controller',
                        'controller' => 'Home\Controller\Order',
                        'action' => 'confirm',
                    ],
                ),
            ],
            'home-order-success' => [
                'type' => 'Segment',
                'options' => array(
                    'route' => '/dat-hang-thanh-cong',
                    'constraints' => [
                        'controller' => 'Home\Controller\Order',
                        'action' => 'success',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'Home\Controller',
                        'controller' => 'Home\Controller\Order',
                        'action' => 'success',
                    ],
                ),
            ],
            'home-lien-he' => [
                'type' => 'Segment',
                'options' => array(
                    'route' => '/lien-he',
                    'constraints' => [
                        'controller' => 'Home\Controller\Contact',
                        'action' => 'index',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'Home\Controller',
                        'controller' => 'Home\Controller\Contact',
                        'action' => 'index',
                    ],
                ),
            ],
            'home-lien-he-success' => [
                'type' => 'Segment',
                'options' => array(
                    'route' => '/lien-he-success',
                    'constraints' => [
                        'controller' => 'Home\Controller\Contact',
                        'action' => 'success',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'Home\Controller',
                        'controller' => 'Home\Controller\Contact',
                        'action' => 'success',
                    ],
                ),
            ],
            'home-email-customer' => [
                'type' => 'Segment',
                'options' => array(
                    'route' => '/email-customer',
                    'defaults' => [
                        '__NAMESPACE__' => 'Home\Controller',
                        'controller' => 'Home\Controller\Index',
                        'action' => 'email-customer',
                    ],
                ),
            ],
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Frontend\Controller\Index' => 'Frontend\Controller\IndexController',
        ),
    ),

    'view_manager' => array(
        'template_map' => array(
            'layout/test' => __DIR__ . '/../view/layout/test.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);