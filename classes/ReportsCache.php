<?php

namespace Thoughtco\Reports\Classes;

use Admin\Facades\AdminLocation;
use Admin\Models\Categories_model;
use Admin\Models\Customers_model;
use Admin\Models\Locations_model;
use Admin\Models\Menus_model;
use Admin\Models\Orders_model;
use Admin\Models\Payments_model;
use Carbon\Carbon;
use DB;
use Igniter\Flame\Currency;
use Request;

class ReportsCache {
	
	protected static $cache = [];
	
	public static function getAll()
	{
		if (!count(self::$cache))
			self::buildCache();
			
		return self::$cache;
	}
	
	public static function get(String $param, $default = [])
	{
		if (!array_get(self::$cache, $param))
			self::buildCache();
			
		return array_get(self::$cache, $param, $default);
	}

	private static function buildCache()
	{
		
		$locationModel = AdminLocation::getId() ? Locations_model::find(AdminLocation::getId()) : false;
		
		$startDate = Request::get('start_date', strtotime('-1 month'));
		$endDate = Request::get('end_date', strtotime('today'));
		
		$startDate = new Carbon($startDate);
		$endDate = new Carbon($endDate);
		
		$results = [
			'total_sales' => 0,
			'total_orders' => 0,
			'total_items' => 0,
			'cancelled_orders_value',
			'cancelled_orders_count',
			'delivery_orders_value' => 0, 
			'delivery_orders_count' => 0,
			'pickup_orders_value' => 0, 
			'pickup_orders_count' => 0,
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
		
		$statusesToQuery = setting('completed_order_status');
		$cancelledStatus = setting('canceled_order_status');
		$statusesToQuery[] = $cancelledStatus;

		// get order ids for the time period
		$orders = Orders_model::whereBetween('order_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
			->whereIn('status_id', $statusesToQuery);
			
		if ($locationModel)
			$orders->where('location_id', $locationModel->getKey());
			
		$orders = $orders->get();
		
		// cancelled order stats
		$cancelledOrders = $orders->filter(function($order) {
			return $order->status_id == setting('canceled_order_status');
		});

		$results['cancelled_orders_value'] = currency_format($cancelledOrders->sum('order_total'));
		$results['cancelled_orders_count'] = $cancelledOrders->count();
		
		// get total sales
		$results['total_sales'] = currency_format($orders->sum('order_total') - $cancelledOrders->sum('order_total'));

		// get total orders
		$results['total_orders'] = $orders->count() - $cancelledOrders->count();

		// pickup order stats
		$pickupOrders = $orders->filter(function($order) use ($cancelledStatus){
			return $order->order_type == 'collection' && $order->status_id != $cancelledStatus;
		});

		$results['pickup_orders_value'] = currency_format($pickupOrders->sum('order_total'));
		$results['pickup_orders_count'] = $pickupOrders->count();

		// delivery order stats
		$deliveryOrders = $orders->filter(function($order) use ($cancelledStatus) {
			return $order->order_type == 'delivery' && $order->status_id != $cancelledStatus;
		});

		$results['delivery_orders_value'] = currency_format($deliveryOrders->sum('order_total'));
		$results['delivery_orders_count'] = $deliveryOrders->count();

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
		
		$results['top_customers'] = $results['top_customers']->map(function($c){
			$c->value = currency_format($c->value);
			return $c; 
		});

		$results['bottom_customers'] = $customerSalesWithZero
		->sortBy('name')
		->sortBy('value')
		->slice(0, 10);
		
		$results['top_customers'] = $results['top_customers']->map(function($c){
			if (!is_string($c->value)) 
				$c->value = currency_format($c->value);
			return $c; 
		});		

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
		$results['total_items'] = $orderItems->count();

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
		$paymentMethods = ($locationModel ? $locationModel->listAvailablePayments() : Payments_model::all())
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

		self::$cache = $results;
		return $results;
	}

}

?>
