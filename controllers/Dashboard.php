<?php

namespace Thoughtco\Reports\Controllers;

use AdminMenu;
use Admin\Widgets\DashboardContainer;
use Request;
use Template;

/**
 * Dashboard Summary
 */
class Dashboard extends \Admin\Classes\AdminController
{
	protected $requiredPermissions = 'Thoughtco.Reports.*';
	
    public $containerConfig = [];

	public function __construct()
	{
		parent::__construct();
		AdminMenu::setContext('reports', 'dashboard');
	}
	
    public function index()
	{
		Template::setTitle(lang('lang:thoughtco.reports::default.text_title'));
		$this->initDashboardContainer();
        return $this->makeView('dashboard');
	}
	
    public function initDashboardContainer()
    {
        $this->containerConfig['canManage'] = false;//$this->canManageWidgets();
        $this->containerConfig['canSetDefault'] = $this->canManageWidgets();
        $this->containerConfig['defaultWidgets'] = $this->getDefaultWidgets();
		$this->containerConfig['context'] = 'reports';
        $container = new DashboardContainer($this, $this->containerConfig);
    }
	
    protected function canManageWidgets()
    {
        return $this->getUser()->hasPermission('Thoughtco.Reports.Manage');
    }
	
    protected function getDefaultWidgets()
    {
        return [
            'date_range' => [
                'class' => \Thoughtco\Reports\Widgets\DateRange::class,
                'priority' => 1,
                'config' => [
                    'context' => 'date_range',
                    'width' => '12',
                ],
            ],			
            'total_sales' => [
                'class' => \Thoughtco\Reports\Widgets\Statistics::class,
                'priority' => 1,
                'config' => [
                    'context' => 'total_sales',
                    'width' => '4',
                ],
            ],
            'total_orders' => [
                'class' => \Thoughtco\Reports\Widgets\Statistics::class,
                'priority' => 2,
                'config' => [
                    'context' => 'total_orders',
                    'width' => '4',
                ],
            ],
            'total_items' => [
                'class' => \Thoughtco\Reports\Widgets\Statistics::class,
                'priority' => 3,
                'config' => [
                    'context' => 'total_items',
                    'width' => '4',
                ],
            ],
            'collection_orders' => [
                'class' => \Thoughtco\Reports\Widgets\Statistics::class,
                'priority' => 4,
                'config' => [
                    'context' => 'pickup_orders_value',
                    'width' => '4',
                ],
            ],
            'delivery_orders' => [
                'class' => \Thoughtco\Reports\Widgets\Statistics::class,
                'priority' => 5,
                'config' => [
                    'context' => 'delivery_orders_value',
                    'width' => '4',
                ],
            ],
            'cancelled_orders' => [
                'class' => \Thoughtco\Reports\Widgets\Statistics::class,
                'priority' => 6,
                'config' => [
                    'context' => 'cancelled_orders_value',
                    'width' => '4',
                ],
            ],
            'top_customers' => [
                'class' => \Thoughtco\Reports\Widgets\Lists::class,
                'priority' => 7,
                'config' => [
                    'context' => 'top_customers',
                    'width' => '6',
                ],
            ],			
            'bottom_customers' => [
                'class' => \Thoughtco\Reports\Widgets\Lists::class,
                'priority' => 8,
                'config' => [
                    'context' => 'bottom_customers',
                    'width' => '6',
                ],
            ],	
            'orders_by_day' => [
                'class' => \Thoughtco\Reports\Widgets\Piechart::class,
                'priority' => 9,
                'config' => [
                    'context' => 'orders_by_day',
                    'width' => '3',
                ],
            ],
            'orders_by_hour' => [
                'class' => \Thoughtco\Reports\Widgets\Piechart::class,
                'priority' => 10,
                'config' => [
                    'context' => 'orders_by_hour',
                    'width' => '3',
                ],
            ],				
            'orders_by_category' => [
                'class' => \Thoughtco\Reports\Widgets\Piechart::class,
                'priority' => 11,
                'config' => [
                    'context' => 'orders_by_category',
                    'width' => '3',
                ],
            ],			
            'orders_by_payment_method' => [
                'class' => \Thoughtco\Reports\Widgets\Piechart::class,
                'priority' => 12,
                'config' => [
                    'context' => 'orders_by_payment_method',
                    'width' => '3',
                ],
            ],							
        ];
    }
}
