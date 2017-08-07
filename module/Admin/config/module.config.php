<?php
return array(
    'router' => array(
        'routes' => array(
            'admin' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/admin',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Admin\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Admin\Controller\Index' => 'Admin\Controller\IndexController',
            'Admin\Controller\Login' => 'Admin\Controller\LoginController',
            'Admin\Controller\Logout' => 'Admin\Controller\LogoutController',
            'Admin\Controller\Forget' => 'Admin\Controller\ForgetController',
            'Admin\Controller\Menu' => 'Admin\Controller\MenuController',
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'layout/admin' => __DIR__ . '/../view/layout/layout.phtml',
            'layout/login' => __DIR__ . '/../view/layout/login.phtml',

            'admin/partial/message' => __DIR__ . '/../view/partial/message.phtml',
            'admin/partial/message_line' => __DIR__ . '/../view/partial/message_line.phtml',
            'admin/partial/form_title' => __DIR__ . '/../view/partial/form_title.phtml',
            'admin/partial/form_button' => __DIR__ . '/../view/partial/form_button.phtml',
            'admin/partial/form_action_title' => __DIR__ . '/../view/partial/form_action_title.phtml',
            'admin/partial/form_normal' => __DIR__ . '/../view/partial/form_normal.phtml',
            'admin/partial/table_normal' => __DIR__ . '/../view/partial/table_normal.phtml',
            'admin/partial/table_title' => __DIR__ . '/../view/partial/table_title.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);