<?php

namespace Thoughtco\Reports\Controllers;

use AdminMenu;
use Admin\Facades\AdminLocation;
use Admin\Models\Categories_model;
use Admin\Models\Customers_model;
use Admin\Models\Locations_model;
use Admin\Models\Menus_model;
use Admin\Models\Orders_model;
use ApplicationException;
use Carbon\Carbon;
use DB;
use Igniter\Flame\Currency;
use Request;
use Template;

/**
 * Dashboard Summary
 */
class Dashboard extends \Admin\Classes\AdminController
{

	protected $requiredPermissions = 'Thoughtco.Reports.*';

	private $locations;
	private $locationNames;

	public function __construct()
	{
		parent::__construct();

		AdminMenu::setContext('sales', 'reports');
		Template::setTitle(lang('lang:thoughtco.reports::default.text_title'));

	}

	public function index()
	{
		$this->addJs('/app/system/assets/ui/js/vendor/moment.min.js', 'moment-js');
		$this->addJs('/app/admin/formwidgets/datepicker/assets/vendor/datepicker/bootstrap-datepicker.min.js', 'bootstrap-datepicker-js');
		$this->addJs('/app/admin/formwidgets/datepicker/assets/js/datepicker.js', 'datepicker-js');
		$this->addJs('/app/admin/dashboardwidgets/charts/assets/vendor/chartjs/Chart.min.js', 'chart-js');
		$this->addJs('$/thoughtco/reports/assets/js/charts.js', 'thoughtco-reports-charts');
		
		$this->addCss('/app/admin/formwidgets/datepicker/assets/vendor/datepicker/bootstrap-datepicker.min.css', 'bootstrap-datepicker-css');
		$this->addCss('/app/admin/formwidgets/datepicker/assets/css/datepicker.css', 'datepicker-css');
        $this->addCss('/app/admin/dashboardwidgets/statistics/assets/css/statistics.css', 'statistics-css');

		[$locationParam, $startDate, $endDate] = $this->getParams();
		$this->vars['locationParam'] = $locationParam;
		$this->vars['startDate'] = $startDate;
		$this->vars['endDate'] = $endDate;
		$this->vars['results'] = $this->buildDashboard();
	}

	public function getParams()
	{

		$locations = $this->getLocations();

		$locationParam = Request::get('location', array_keys($locations)[0]);
		$startDate = Request::get('start_date', strtotime('-7 days'));
		$endDate = Request::get('end_date', strtotime('today'));

		return [$locationParam, new Carbon($startDate), new Carbon($endDate)];

	}

	public function getLocations()
	{
		if ($this->locationNames)
			return $this->locationNames;

		$locationNames = [];
		$locations = [];

		foreach (Locations_model::all() as $l)
		{
			if (AdminLocation::getId() === NULL || AdminLocation::getId() == $l->location_id)
			{
				//if ($l->location_status)
				//{
					$locationNames[$l->location_id] = $l->location_name;
					$locations[] = $l;
				//}
			}
		}

		$this->locationNames = $locationNames;
		$this->locations = collect($locations)->keyBy('location_id');
		
		return $locationNames;
	}

	public function buildDashboard()
	{
		[$locationParam, $startDate, $endDate] = $this->getParams();

		$results = [
			'total_sales' => 0,
			'total_orders' => 0,
			'quantity_of_items' => 0,
			'cancelled_orders' => ['value' => 0, 'count' => 0],
			'delivery_orders' => ['value' => 0, 'count' => 0],
			'pickup_orders' => ['value' => 0, 'count' => 0],
			'top_customers' => [],
			'bottom_customers' => [],
			'order_items' => [],
			'top_items' => [],
			'bottom_items' => [],
			'orders_by_hour' => [],
			'orders_by_day' => [],
			'orders_by_customer' => [],
			'orders_by_category' => [],
		];

		// get order ids for the time period
		$orders = Orders_model::where([
			['order_date', '>=', $startDate->format('Y-m-d')],
			['order_date', '<=', $endDate->format('Y-m-d')],
		])
		->whereIn('status_id', setting('completed_order_status'))
		->get();

		// get total sales
		$results['total_sales'] = currency_format($orders->sum('order_total'));

		// get total orders
		$results['total_orders'] = $orders->count();

		// pickup order stats
		$pickupOrders = $orders->filter(function($order) {
			return $order->order_type == 'collection';
		});

		$results['pickup_orders'] = (object)[
			'value' => $pickupOrders->sum('order_total'),
			'count' => $pickupOrders->count(),
		];

		// delivery order stats
		$deliveryOrders = $orders->filter(function($order) {
			return $order->order_type == 'delivery';
		});

		$results['delivery_orders'] = (object)[
			'value' => $deliveryOrders->sum('order_total'),
			'count' => $deliveryOrders->count(),
		];

		// cancelled order stats
		$cancelledOrders = $orders->filter(function($order) {
			return $order->status_id == setting('canceled_order_status');
		});

		$results['cancelled_orders'] = (object)[
			'value' => $cancelledOrders->sum('order_total'),
			'count' => $cancelledOrders->count(),
		];

		// orders by customer
		$ordersByCustomer = $orders->groupBy('email');

		// get orders by customers customers
		$results['orders_by_customer'] = $ordersByCustomer = $ordersByCustomer
		->map(function($ordersByCustomer, $key) {

			if (!$first = $ordersByCustomer->first())
				return false;

			return (object)[
				'customer_id' => $first->customer_id,
				'name' => $first->first_name.' '.$first->last_name,
				'email' => $first->email,
				'value' => $ordersByCustomer->sum('order_total'),
			];
		});
				
		// build customer items to include zero sales
		$customerSalesWithZero = Customers_model::all()
		->map(function($customer) use ($ordersByCustomer){
			if ($el = $ordersByCustomer->firstWhere('customer_id', $customer->customer_id))
				return $el;
				
			return (object)[
				'customer_id' => $customer->customer_id,
				'name' => $customer->first_name.' '.$customer->last_name,
				'email' => 0,
				'value' => 0,				
			];
		});		

		$results['top_customers'] = $customerSalesWithZero
		->sortBy('name')
		->sortByDesc('value')
		->slice(0, 10);

		$results['bottom_customers'] = $customerSalesWithZero
		->sortBy('name')
		->sortBy('value')
		->slice(0, 10);

		// get order value and count by day
		$results['orders_by_day'] = collect([0, 1, 2, 3, 4, 5, 6])
		->map(function($dayOfWeek) use($orders) {

			$ordersOnDay = $orders->filter(function($order) use($dayOfWeek) {
				return $order->order_date->dayOfWeek == $dayOfWeek;
			});

			return (object)[
				'day' => $dayOfWeek,
				'value' => $ordersOnDay->sum('order_total'),
				'count' => $ordersOnDay->count(),
				'color' => 'hsl(50, 98.3%, '.(80 - floor(53.5 * $dayOfWeek/7)).'%)',
			];

		});
		
		$results['orders_by_day_data'] = [
			'datasets' => [
        		[
					'data' => $results['orders_by_day']->map(function($v){ return $v->value; }),
					'backgroundColor' => $results['orders_by_day']->map(function($v){ return $v->color; })
				],
			],
			'labels' => $results['orders_by_day']->map(function($v){ return date('l', strtotime('Sunday +'.$v->day.' days')). ' ('.currency_format($v->value).' / '.$v->count.')'; }),
		];

		// get order value and count by hour
		$results['orders_by_hour'] = collect([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23])
		->map(function($hourOfDay) use($orders) {

			$ordersInHour = $orders->filter(function($order) use($hourOfDay) {
				$time = explode(':', $order->order_time);
				return intval(array_shift($time)) == $hourOfDay;
			});

			return (object)[
				'hour' => $hourOfDay,
				'value' => round($ordersInHour->sum('order_total'), 2),
				'count' => $ordersInHour->count(),
				'color' => 'hsl(211, 100%, '.(100 - floor(50 * $hourOfDay/24)).'%)',
			];

		});
		
		$results['orders_by_hour_data'] = [
			'datasets' => [
        		[
					'data' => $results['orders_by_hour']->map(function($v){ return $v->value; }),
					'backgroundColor' => $results['orders_by_hour']->map(function($v){ return $v->color; })
				],
			],
			'labels' => $results['orders_by_hour']->map(function($v){ return str_pad($v->hour, 2, '0', STR_PAD_LEFT).':00-'.str_pad($v->hour+1, 2, '0', STR_PAD_LEFT).':00 ('.currency_format($v->value).' / '.$v->count.')'; }),
		];

		// get ids
		$orderIds = $orders->pluck('order_id');

		// get order items
		$orderItems = DB::table('order_menus')
		->whereIn('order_id', $orderIds)
		->get();

		// get quantity of items
		$results['quantity_of_items'] = $orderItems->count();

		// get all order items
		$results['order_items'] = $orderItems = $orderItems
		->groupBy('menu_id')
		->map(function($orderItems, $key) {
			if (!$first = $orderItems->first())
				return false;

			return (object)[
				'subtotal' => $orderItems->sum('subtotal'),
				'quantity' => $orderItems->sum('quantity'),
				'menu_id' => $key,
				'name' => $first->name,
			];
		});
		
		// get a list of menus vs categories
		$menusCategories = Menus_model::get()
		->map(function($menu) {
			return (object)[
				'menu_id' => $menu->menu_id,
				'name' => $menu->menu_name,
				'categories' => $menu->categories->pluck('category_id')->toArray()
			];
		})
		->keyBy('menu_id');
		
		// build menu items to include zero sales
		$menuSalesWithZero = $menusCategories->map(function($menu) use ($orderItems){
			if ($el = $orderItems->firstWhere('menu_id', $menu->menu_id))
				return $el;
			
			return (object)[
				'subtotal' => 0,
				'quantity' => 0,
				'menu_id' => $menu->menu_id,
				'name' => $menu->name,				
			];
		});
		
		// get best selling items
		$results['top_items'] = $menuSalesWithZero
		->sortBy('name')
		->sortByDesc('quantity')
		->slice(0, 10);

		// get worst selling items
		$results['bottom_items'] = $menuSalesWithZero
		->sortBy('name')
		->sortBy('quantity')
		->slice(0, 10);

		// get a list of categories
		$categories = Categories_model::where('status', 1)
		->get()
		->sortBy('name')
		->pluck('name', 'category_id');
		
		$categoryCount = $categories->count();
		$categoryIndex = 0;

		// get sales by category
		$results['orders_by_category'] = $categories
		->map(function($category, $categoryKey) use($orderItems, $menusCategories, $categoryCount, &$categoryIndex) {

			$ordersInThisCategory = $orderItems
			->filter(function($orderItem) use($categoryKey, $menusCategories) {
				if ($categoryList = $menusCategories->get($orderItem->menu_id))
				{
					if (in_array($categoryKey, $categoryList->categories))
						return true;
				}
				return false;
			});

			return (object)[
				'name' => $category,
				'value' => $ordersInThisCategory->sum('subtotal'),
				'count' => $ordersInThisCategory->count(),
				'color' => 'hsl(134, 61.4%, '.(80 - floor(40.6 * $categoryIndex++/$categoryCount)).'%)',
			];

		});
		
		$results['orders_by_category_data'] = [
			'datasets' => [
        		[
					'data' => $results['orders_by_category']->map(function($v){ return $v->value; })->values(),
					'backgroundColor' => $results['orders_by_category']->map(function($v){ return $v->color; })->values()
				],
			],
			'labels' => $results['orders_by_category']->map(function($v){ return $v->name. ' ('.currency_format($v->value).' / '.$v->count.')'; })->values(),
		];

		// get payment methods for this location
		$paymentMethods = $this->locations->get($locationParam)
		->listAvailablePayments()
		->map(function($method) {
			return (object)[
				'name' => $method->name,
				'code' => $method->code,
			];
		});
		
		$paymentMethodCount = $paymentMethods->count();
		$paymentMethodIndex = 0;
		
		// get orders by payment method
		$results['orders_by_payment_method'] = $paymentMethods
		->map(function($method) use($orders, $paymentMethodCount, &$paymentMethodIndex){

			$ordersUsingThisMethod = $orders
			->filter(function($orderItem) use($method){
				if ($orderItem->payment == $method->code)
					return true;
				return false;
			});

			$method->value = $ordersUsingThisMethod->sum('order_total');
			$method->count = $ordersUsingThisMethod->count();
			$method->color = 'hsl(354, 70.5%, '.(80 - floor(53.5 * $paymentMethodIndex++/$paymentMethodCount)).'%)';

			return $method;

		});
		
		$results['orders_by_payment_method_data'] = [
			'datasets' => [
        		[
					'data' => $results['orders_by_payment_method']->map(function($v){ return $v->value; })->values(),
					'backgroundColor' => $results['orders_by_payment_method']->map(function($v){ return $v->color; })->values()
				],
			],
			'labels' => $results['orders_by_payment_method']->map(function($v){ return $v->name. ' ('.currency_format($v->value).' / '.$v->count.')'; })->values(),
		];

		return (object)$results;
	}

}
