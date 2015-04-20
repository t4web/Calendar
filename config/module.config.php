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
                'type' => 'Segment',
                'options' => array(
                    'route' => '/calendar[/:year]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'T4webCalendar\Controller\User',
                        'controller' => 'Show',
                        'action' => 'default',
                    ),
                ),
            ),
            'calendar-ajax-type-list' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/calendar/ajax/type-list',
                    'defaults' => array(
                        '__NAMESPACE__' => 'T4webCalendar\Controller\User',
                        'controller' => 'Ajax',
                        'action' => 'type-list',
                    ),
                ),
            ),
            'calendar-ajax-save' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/calendar/ajax/save',
                    'defaults' => array(
                        '__NAMESPACE__' => 'T4webCalendar\Controller\User',
                        'controller' => 'Ajax',
                        'action' => 'save',
                    ),
                ),
            ),
            'calendar-ajax-delete' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/calendar/ajax/delete',
                    'defaults' => array(
                        '__NAMESPACE__' => 'T4webCalendar\Controller\User',
                        'controller' => 'Ajax',
                        'action' => 'delete',
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
                        'route' => 'calendar init',
                        'defaults' => array(
                            '__NAMESPACE__' => 'T4webCalendar\Controller\Console',
                            'controller' => 'Init',
                            'action' => 'run'
                        )
                    )
                ),
            )
        )
    ),

    'view_helpers' => array(
        'invokables' => array(
            'calendarYearPaginator' => 'T4webCalendar\View\Helper\CalendarYearPaginator',
        ),
    ),

    'db' => array(
        'tables' => array(
            't4webcalendar-calendar' => array(
                'name' => 'calendar',
                'columnsAsAttributesMap' => array(
                    'id' => 'id',
                    'name' => 'name',
                    'date' => 'date',
                    'type' => 'type',
                ),
            ),
        ),
    ),

    'criteries' => array(
        'Calendar' => array(
            'empty' => array(
                'table' => 'calendar',
            ),
            'id' => array(
                'table' => 'calendar',
                'field' => 'id',
                'buildMethod' => 'addFilterEqual',
            ),
            'ids' => array(
                'table' => 'calendar',
                'field' => 'id',
                'buildMethod' => 'addFilterIn',
            ),
            'date' => array(
                'table' => 'calendar',
                'field' => 'date',
                'buildMethod' => 'addFilterEqual',
            ),
            'dateFrom' => array(
                'table' => 'calendar',
                'field' => 'date',
                'buildMethod' => 'addFilterMoreOrEqual',
            ),
            'dateTo' => array(
                'table' => 'calendar',
                'field' => 'date',
                'buildMethod' => 'addFilterLessOrEqual',
            ),
            'orderBy' => array(
                'table' => 'calendar',
                'field' => 'date',
                'buildMethod' => 'order',
            ),
        ),
    ),
);
