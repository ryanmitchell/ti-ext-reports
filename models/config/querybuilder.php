<?php

return [
    'list' => [
        'toolbar' => [
            'buttons' => [
		        'create' => [
		            'label' => 'lang:admin::lang.button_new',
		            'class' => 'btn btn-primary',
		            'href' => 'thoughtco/reports/builder/create',
		        ],
                'delete' => ['label' => 'lang:admin::lang.button_delete', 'class' => 'btn btn-danger', 'data-request-form' => '#list-form', 'data-request' => 'onDelete', 'data-request-data' => "_method:'DELETE'", 'data-request-data' => "_method:'DELETE'", 'data-request-confirm' => 'lang:admin::lang.alert_warning_confirm'],

			],
        ],
		'filter' => [],
        'columns' => [
            'edit' => [
                'type' => 'button',
                'iconCssClass' => 'fa fa-pencil',
                'attributes' => [
                    'class' => 'btn btn-edit',
                    'href' => 'thoughtco/reports/builder/edit/{id}',
                ],
            ],
            'title' => [
                'label' => 'lang:thoughtco.reports::default.column_title',
                'type' => 'text',
                'sortable' => TRUE,
            ],
            'link' => [
                'label' => '',
                'type' => 'text',
                'valueFrom' => 'id',
                'formatter' => function($record, $column, $value) {
                    return '<a class="btn btn-primary" href="'.admin_url('thoughtco/reports/builder/view/'.$value).'">'.lang('thoughtco.reports::default.btn_view').'</a>';
                },
            ],
        ],
    ],

    'form' => [
        'toolbar' => [
            'buttons' => [
                'back' => ['label' => 'lang:admin::lang.button_icon_back', 'class' => 'btn btn-default', 'href' => 'thoughtco/reports/builder'],
                'save' => [
                    'label' => 'lang:admin::lang.button_save',
                    'class' => 'btn btn-primary',
                    'data-request' => 'onSave',
                ],
                'saveClose' => [
                    'label' => 'lang:admin::lang.button_save_close',
                    'class' => 'btn btn-default',
                    'data-request' => 'onSave',
                    'data-request-data' => 'close:1',
                ],
            ],
        ],
		'fields' => [
            'title' => [
                'label' => 'lang:thoughtco.reports::default.label_title',
                'type' => 'text',
            ],
            'builderjson' => [
                'label' => 'lang:thoughtco.reports::default.label_rules',
                'type' => 'querybuilder',
				'filters' => [
		            '\Admin\Models\Customers_model' => [
		                'label' => lang('thoughtco.reports::default.qb.label_customers'),
		                'filters' => [
		                    [
		                        'id' => 'customers.name',
		                        'label' => lang('thoughtco.reports::default.qb.label_customer_name'),
		                        'type' => 'string',
		                    ],
		                    [
		                        'id' => 'email',
		                        'label' => lang('thoughtco.reports::default.qb.label_customer_email'),
		                        'type' => 'string',
		                    ],
		                    [
		                        'id' => 'customer_group_id',
		                        'label' => lang('thoughtco.reports::default.qb.label_customer_group'),
		                        'type' => 'string',
		                        'input' => 'select',
		                        'values' => \Admin\Models\Customer_groups_model::getDropdownOptions(),
                                'operators' => [
                                    'equal', 'not_equal',
                                ],
		                    ],
		                    [
		                        'id' => 'customers.orderdate',
		                        'label' => lang('thoughtco.reports::default.qb.label_customer_orderdate'),
		                        'type' => 'date',
                                'validation' => [
                                    'format' => 'YYYY/MM/DD',
                                ],
                                'plugin' => 'datepicker',
                                'plugin_config' => [
                                    'format' => 'yyyy/mm/dd',
                                    'todayBtn' => 'linked',
                                    'todayHighlight' => true,
                                    'autoclose' => true,
                                ],
                                'operators' => [
                                    'equal', 'not_equal',
                                    'less', 'less_or_equal',
                                    'greater', 'greater_or_equal',
                                ],
		                    ],
		                    [
		                        'id' => 'date_added',
		                        'label' => lang('thoughtco.reports::default.qb.label_customer_joined'),
		                        'type' => 'date',
                                'validation' => [
                                    'format' => 'YYYY/MM/DD',
                                ],
                                'plugin' => 'datepicker',
                                'plugin_config' => [
                                    'format' => 'yyyy/mm/dd',
                                    'todayBtn' => 'linked',
                                    'todayHighlight' => true,
                                    'autoclose' => true,
                                ],
                                'operators' => [
                                    'equal', 'not_equal',
                                    'less', 'less_or_equal',
                                    'greater', 'greater_or_equal',
                                ],
		                    ],

		                ]
		            ],
		            '\Admin\Models\Orders_model' => [
		                'label' => 'Orders',
		                'filters' => [
		                    [
		                        'id' => 'location_id',
		                        'label' => lang('thoughtco.reports::default.qb.label_location'),
		                        'type' => 'integer',
		                        'input' => 'select',
		                        'values' => \AdminLocation::listLocations(),
		                    ],
		                    [
		                        'id' => 'orders.customer_name',
		                        'label' => lang('thoughtco.reports::default.qb.label_customer_name'),
		                        'type' => 'string',
		                    ],
		                    [
		                        'id' => 'email',
		                        'label' => lang('thoughtco.reports::default.qb.label_customer_email'),
		                        'type' => 'string',
		                    ],
		                    [
		                        'id' => 'orders.customer_group',
		                        'label' => lang('thoughtco.reports::default.qb.label_customer_group'),
		                        'type' => 'integer',
		                        'input' => 'select',
		                        'values' => \Admin\Models\Customer_groups_model::getDropdownOptions(),
		                    ],
		                    [
		                        'id' => 'date_added',
		                        'label' => lang('thoughtco.reports::default.qb.label_orders_added'),
		                        'type' => 'date',
                                'validation' => [
                                    'format' => 'YYYY/MM/DD',
                                ],
                                'plugin' => 'datepicker',
                                'plugin_config' => [
                                    'format' => 'yyyy/mm/dd',
                                    'todayBtn' => 'linked',
                                    'todayHighlight' => true,
                                    'autoclose' => true,
                                ],
                                'operators' => [
                                    'equal', 'not_equal',
                                    'less', 'less_or_equal',
                                    'greater', 'greater_or_equal',
                                ],
		                    ],
		                    [
		                        'id' => 'order_date',
		                        'label' => lang('thoughtco.reports::default.qb.label_orders_date'),
		                        'type' => 'date',
                                'validation' => [
                                    'format' => 'YYYY/MM/DD',
                                ],
                                'plugin' => 'datepicker',
                                'plugin_config' => [
                                    'format' => 'yyyy/mm/dd',
                                    'todayBtn' => 'linked',
                                    'todayHighlight' => true,
                                    'autoclose' => true,
                                ],
                                'operators' => [
                                    'equal', 'not_equal',
                                    'less', 'less_or_equal',
                                    'greater', 'greater_or_equal',
                                ],
		                    ],
		                    [
		                        'id' => 'order_type',
		                        'label' => lang('thoughtco.reports::default.qb.label_orders_type'),
		                        'type' => 'string',
		                        'input' => 'select',
                                'options' => ['Admin\Models\Locations_model', 'getOrderTypeOptions'],
		                    ],
		                    [
		                        'id' => 'orders.delivery_address',
		                        'label' => lang('thoughtco.reports::default.qb.label_orders_address'),
		                        'type' => 'string',
                                'operators' => [
                                    'contains', 'not_contains',
                                ],
		                    ],
		                    [
		                        'id' => 'orders.categories',
		                        'label' => lang('thoughtco.reports::default.qb.label_orders_categories'),
		                        'type' => 'string',
		                        'input' => 'select',
		                        'values' => \Admin\Models\Categories_model::getDropdownOptions(),
                                'operators' => [
                                    'contains', 'not_contains',
                                ],
		                    ],
		                    [
		                        'id' => 'orders.menus',
		                        'label' => lang('thoughtco.reports::default.qb.label_orders_menus'),
		                        'type' => 'string',
		                        'input' => 'select',
		                        'values' => \Admin\Models\Menus_model::all()->pluck('menu_name', 'menu_id'),
                                'operators' => [
                                    'contains', 'not_contains',
                                ],
		                    ],
		                ]
		            ]
		        ],
            ],
		],
    ],
];
