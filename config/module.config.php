<?php

return array(

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'display_exceptions' => true,
        'display_not_found_reason' => true,
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),

    'router' => array(
        'routes' => array(
            'calendar' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/calendar',
                    'defaults' => array(
                        '__NAMESPACE__' => 'T4webCalendar\Controller\User',
                        'controller'    => 'Show',
                        'action'        => 'default',
                    ),
                ),
            ),
        ),
    ),

    'console' => array(
        'router' => array(
            'routes' => array(
                'calendar-init' => array(
                    'options' => array(
                        'route'    => 'calendar init',
                        'defaults' => array(
                            '__NAMESPACE__' => 'T4webCalendar\Controller\Console',
                            'controller' => 'Init',
                            'action'     => 'run'
                        )
                    )
                ),
            )
        )
    ),

    'db' => array(
        'tables' => array(
            't4webcalendar-calendar' => array(
                'name' => 'calendar',
                'columnsAsAttributesMap' => array(
                    'id' => 'id',
                    'text' => 'text',
                    'date' => 'date',
                ),
            ),
        ),
    ),

    'criteries' => array(
        'Calendar' => array(
            'empty' => array('table' => 'calendar'),
        ),
    ),
);
