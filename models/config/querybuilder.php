<?php

use Admin\Facades\AdminLocation;

$output_options = [
    json_encode([
        'key' => 'customer_name',
        'contexts' => [
            \Admin\Models\Customers_model::class,
            \Admin\Models\Orders_model::class,
        ],
    ]) => 'lang:thoughtco.reports::default.qb.label_customer_name',
    json_encode([
        'key' => 'email',
        'contexts' => [
            \Admin\Models\Customers_model::class,
            \Admin\Models\Orders_model::class,
        ],
    ]) => 'lang:thoughtco.reports::default.qb.label_customer_email',
    json_encode([
        'key' => 'order_total',
        'contexts' => [
            \Admin\Models\Orders_model::class,
        ],
    ]) => 'lang:admin::lang.orders.label_order_total',
    json_encode([
        'key' => 'order_date',
        'contexts' => [
            \Admin\Models\Orders_model::class,
        ],
    ]) => 'lang:admin::lang.orders.label_order_date',
    json_encode([
        'key' => 'order_type',
        'contexts' => [
            \Admin\Models\Orders_model::class,
        ],
    ]) => 'lang:admin::lang.orders.label_order_type',
    json_encode([
        'key' => 'customer_address',
        'contexts' => [
            \Admin\Models\Customers_model::class,
        ],
    ]) => 'lang:admin::lang.orders.label_delivery_address',
];

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
                'sortable' => true,
            ],
            'link' => [
                'label' => '',
                'type' => 'text',
                'valueFrom' => 'id',
                'formatter' => function ($record, $column, $value) {
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
                    'context' => ['create', 'edit'],
                    'partial' => 'form/toolbar_save_button',
                    'class' => 'btn btn-primary',
                    'data-request' => 'onSave',
                    'data-progress-indicator' => 'admin::lang.text_saving',
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
                    \Admin\Models\Customers_model::class => [
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
                            [
                                'id' => 'date_added_relative',
                                'label' => lang('thoughtco.reports::default.qb.label_customer_joined_relative'),
                                'input' => 'select',
                                'values' => [
                                    '7' => lang('thoughtco.reports::default.qb.value_date_relative_7'),
                                    '14' => lang('thoughtco.reports::default.qb.value_date_relative_14'),
                                    '30' => lang('thoughtco.reports::default.qb.value_date_relative_30'),
                                    '90' => lang('thoughtco.reports::default.qb.value_date_relative_90'),
                                    '365' => lang('thoughtco.reports::default.qb.value_date_relative_365'),
                                ],
                                'operators' => [
                                    'equal', 'not_equal',
                                    'less', 'less_or_equal',
                                    'greater', 'greater_or_equal',
                                ],
                            ],
                        ],
                    ],
                    \Admin\Models\Orders_model::class => [
                        'label' => 'Orders',
                        'filters' => [
                            [
                                'id' => 'location_id',
                                'label' => lang('thoughtco.reports::default.qb.label_location'),
                                'type' => 'integer',
                                'input' => 'select',
                                'values' => AdminLocation::listLocations(),
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
                                'id' => 'order_date_relative',
                                'label' => lang('thoughtco.reports::default.qb.label_orders_date_relative'),
                                'input' => 'select',
                                'values' => [
                                    '7' => lang('thoughtco.reports::default.qb.value_date_relative_7'),
                                    '14' => lang('thoughtco.reports::default.qb.value_date_relative_14'),
                                    '30' => lang('thoughtco.reports::default.qb.value_date_relative_30'),
                                    '90' => lang('thoughtco.reports::default.qb.value_date_relative_90'),
                                    '365' => lang('thoughtco.reports::default.qb.value_date_relative_365'),
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
                                'values' => \Admin\Models\Locations_model::getOrderTypeOptions()->mapWithKeys(function ($value, $key) {
                                    return [$key => lang($value)];
                                }),
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
                        ],
                    ],
                ],
            ],
            'list_columns' => [
                'label' => 'lang:thoughtco.reports::default.label_list_cols',
                'type' => 'repeater',
                'sortable' => true,
                'commentAbove' => 'lang:thoughtco.reports::default.help_list_cols',
                'form' => [
                    'fields' => [
                        'priority' => [
                            'type' => 'hidden',
                        ],
                        'column' => [
                            'label' => 'lang:thoughtco.reports::default.label_column',
                            'type' => 'select',
                            'options' => $output_options,
                        ],
                        'label' => [
                            'label' => 'lang:thoughtco.reports::default.label_label',
                            'type' => 'text',
                        ],
                    ],
                ],
            ],
            'csv_columns' => [
                'label' => 'lang:thoughtco.reports::default.label_csv_cols',
                'type' => 'repeater',
                'sortable' => true,
                'commentAbove' => 'lang:thoughtco.reports::default.help_csv_cols',
                'form' => [
                    'fields' => [
                        'priority' => [
                            'type' => 'hidden',
                        ],
                        'column' => [
                            'label' => 'lang:thoughtco.reports::default.label_column',
                            'type' => 'select',
                            'options' => $output_options,
                        ],
                        'label' => [
                            'label' => 'lang:thoughtco.reports::default.label_label',
                            'type' => 'text',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
