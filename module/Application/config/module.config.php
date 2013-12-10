<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */
return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        'controller' => 'index',
                        'action' => 'index'
                    )
                )
            ),
            'services' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/services.json',
                    'defaults' => array(
                        'controller' => 'index',
                        'action' => 'services'
                    )
                )
            ),
            'google' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/google[/]',
                    'defaults' => array(
                        'controller' => 'google',
                        'action' => 'index'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'auth' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => 'auth[/]',
                            'defaults' => array(
                                'action' => 'auth'
                            )
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'check' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => 'check',
                                    'defaults' => array(
                                        'action' => 'auth-check'
                                    )
                                )
                            ),
                            'complete' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => 'complete',
                                    'defaults' => array(
                                        'action' => 'complete'
                                    )
                                )
                            ),
                            'logout' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => 'logout',
                                    'defaults' => array(
                                        'action' => 'logout'
                                    )
                                )
                            )
                        )
                    )
                )
            ),
            'bitly' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/bitly[/]',
                    'defaults' => array(
                        'controller' => 'bitly',
                        'action' => 'index'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'auth' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => 'auth[/]',
                            'defaults' => array(
                                'action' => 'auth'
                            )
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'check' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => 'check',
                                    'defaults' => array(
                                        'action' => 'auth-check'
                                    )
                                )
                            ),
                            'complete' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => 'complete',
                                    'defaults' => array(
                                        'action' => 'complete'
                                    )
                                )
                            ),
                            'logout' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => 'logout',
                                    'defaults' => array(
                                        'action' => 'logout'
                                    )
                                )
                            )
                        )
                    )
                )
            ),
            'twitter' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/twitter[/]',
                    'defaults' => array(
                        'controller' => 'twitter',
                        'action' => 'index'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'auth' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => 'auth[/]',
                            'defaults' => array(
                                'action' => 'auth'
                            )
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'check' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => 'check',
                                    'defaults' => array(
                                        'action' => 'auth-check'
                                    )
                                )
                            ),
                            'complete' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => 'complete',
                                    'defaults' => array(
                                        'action' => 'complete'
                                    )
                                )
                            ),
                            'logout' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => 'logout',
                                    'defaults' => array(
                                        'action' => 'logout'
                                    )
                                )
                            )
                        )
                    )
                )
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/application',
                    'defaults' => array(
                        'controller' => 'index',
                        'action' => 'index'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
                            ),
                            'defaults' => array()
                        )
                    )
                )
            )
        )
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory'
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator'
        )
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo'
            )
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'index' => 'Application\Controller\IndexController',
            'google' => 'Application\Controller\GoogleController',
            'bitly' => 'Application\Controller\BitlyController',
            'twitter' => 'Application\Controller\TwitterController'
        )
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml'
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view'
        ),
        'strategies' => array(
            'ViewJsonStrategy'
        )
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array()
        )
    )
);
