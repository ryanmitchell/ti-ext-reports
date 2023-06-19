<?php namespace Thoughtco\Reports;

use Admin\Controllers\Dashboard;
use Admin\DashboardWidgets\Charts;
use Admin\DashboardWidgets\Statistics;
use Admin\Widgets\DashboardContainer;
use System\Classes\BaseExtension;
use Thoughtco\Reports\Classes\ReportsCache;

/**
 * Extension Information File
 **/
class Extension extends BaseExtension
{
    public function boot()
    {
        Statistics::registerCards(function () {
            return [
                'total_items' => [
                    'label' => 'lang:thoughtco.reports::default.text_total_items',
                    'icon' => ' bg-warning text-white fa fa-line-chart',
                    'valueFrom' => [ReportsCache::class, 'getValue'],
                ],
                'pickup_orders_value' => [
                    'label' => 'lang:thoughtco.reports::default.text_collection_orders',
                    'icon' => ' bg-blue text-white fa fa-store',
                    'valueFrom' => [ReportsCache::class, 'getValue'],
                ],
                'pickup_orders_count' => [
                    'label' => 'lang:thoughtco.reports::default.text_collection_orders_count',
                    'icon' => ' bg-blue text-white fa fa-store',
                    'valueFrom' => [ReportsCache::class, 'getValue'],
                ],
                'delivery_orders_value' => [
                    'label' => 'lang:thoughtco.reports::default.text_delivery_orders',
                    'icon' => ' bg-blue text-white fa fa-shipping-fast',
                    'valueFrom' => [ReportsCache::class, 'getValue'],
                ],
                'delivery_orders_count' => [
                    'label' => 'lang:thoughtco.reports::default.text_delivery_orders_count',
                    'icon' => ' bg-blue text-white fa fa-shipping-fast',
                    'valueFrom' => [ReportsCache::class, 'getValue'],
                ],
                'cancelled_orders_value' => [
                    'label' => 'lang:thoughtco.reports::default.text_cancelled_orders',
                    'icon' => ' bg-danger text-white fa fa-exclamation-circle',
                    'valueFrom' => [ReportsCache::class, 'getValue'],
                ],
                'cancelled_orders_count' => [
                    'label' => 'lang:thoughtco.reports::default.text_cancelled_orders_count',
                    'icon' => ' bg-danger text-white fa fa-exclamation-circle',
                    'valueFrom' => [ReportsCache::class, 'getValue'],
                ],
            ];
        });

        Charts::registerDatasets(function () {
            return [
                'orders_by_day_data' => [
                    'label' => 'lang:thoughtco.reports::default.text_orders_by_day',
                    'type' => 'doughnut',
                    'icon' => ' fa fa-calendar',
                    'datasetFrom' => [ReportsCache::class, 'getValue'],
                ],
                'orders_by_hour_data' => [
                    'label' => 'lang:thoughtco.reports::default.text_orders_by_hour',
                    'type' => 'doughnut',
                    'icon' => ' fa fa-clock',
                    'datasetFrom' => [ReportsCache::class, 'getValue'],
                ],
                'orders_by_category_data' => [
                    'label' => 'lang:thoughtco.reports::default.text_orders_by_category',
                    'type' => 'pie',
                    'icon' => ' fa fa-stream',
                    'datasetFrom' => [ReportsCache::class, 'getValue'],
                ],
                'orders_by_payment_method_data' => [
                    'label' => 'lang:thoughtco.reports::default.text_orders_by_payment_type',
                    'type' => 'pie',
                    'icon' => ' fa fa-money-check-alt',
                    'datasetFrom' => [ReportsCache::class, 'getValue'],
                ],
            ];
        });

        Dashboard::extend(function (Dashboard $dashboard) {
            $dashboard->extendDashboard(function (DashboardContainer $widget) {
                $widget->defaultWidgets = array_merge($widget->defaultWidgets, [
                    'top_customers' => [
                        'widget' => 'lists',
                        'priority' => 40,
                        'context' => 'top_customers',
                        'width' => '6',
                    ],
                    'bottom_customers' => [
                        'widget' => 'lists',
                        'priority' => 40,
                        'context' => 'bottom_customers',
                        'width' => '6',
                    ],
                    'orders_by_day' => [
                        'widget' => 'charts',
                        'priority' => 50,
                        'dataset' => 'orders_by_day_data',
                        'width' => '3',
                    ],
                    'orders_by_hour' => [
                        'widget' => 'charts',
                        'priority' => 50,
                        'dataset' => 'orders_by_hour_data',
                        'width' => '3',
                    ],
                    'orders_by_category' => [
                        'widget' => 'charts',
                        'priority' => 50,
                        'dataset' => 'orders_by_category_data',
                        'width' => '3',
                    ],
                    'orders_by_payment_method' => [
                        'widget' => 'charts',
                        'priority' => 50,
                        'dataset' => 'orders_by_payment_method_data',
                        'width' => '3',
                    ],
                ]);
            });
        });
    }

    public function registerFormWidgets()
    {
        return [
            'Thoughtco\Reports\FormWidgets\QueryBuilder' => [
                'label' => 'lang:thoughtco.reports::default.qb.text_title',
                'code' => 'querybuilder',
            ],
            'Thoughtco\Reports\FormWidgets\ReportsTable' => [
                'label' => 'lang:thoughtco.reports::default.qb.text_reportstable',
                'code' => 'reportstable',
            ],
        ];
    }

    public function registerDashboardWidgets()
    {
        return [
            \Thoughtco\Reports\Widgets\Lists::class => [
                'code' => 'lists',
                'label' => 'lang:thoughtco.reports::default.text_list_widget_title',
            ],
        ];
    }

    public function registerNavigation()
    {
        return [
            'reports' => [
                'icon' => 'fa-chart-pie',
                'title' => lang('lang:thoughtco.reports::default.text_title'),
                'priority' => 35,
                'class' => 'pages',
                'href' => admin_url('thoughtco/reports/builder'),
                'permission' => 'Thoughtco.Reports.View',
            ],
        ];
    }

    public function registerPermissions()
    {
        return [
            'Thoughtco.Reports.View' => [
                'description' => 'View reports',
                'group' => 'module',
            ],
        ];
    }

}

?>
