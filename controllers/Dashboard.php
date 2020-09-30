<?php

namespace Thoughtco\Reports\Controllers;

use AdminMenu;
use Admin\Facades\AdminLocation;
use Admin\Models\Locations_model;
use Admin\Models\Orders_model;
use Admin\Models\Menus_model;
use Admin\Models\Categories_model;
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

    public function __construct()
    {
        parent::__construct();

        AdminMenu::setContext('sales', 'reports');
        Template::setTitle(lang('lang:thoughtco.reports::default.text_title'));
        
    }
    
    public function index()
    {
		$this->addJs('/app/system/assets/ui/js/vendor/moment.min.js', 'moment-js');
		$this->addCss('/app/admin/formwidgets/datepicker/assets/vendor/datepicker/bootstrap-datepicker.min.css', 'bootstrap-datepicker-css');
		$this->addJs('/app/admin/formwidgets/datepicker/assets/vendor/datepicker/bootstrap-datepicker.min.js', 'bootstrap-datepicker-js');
		$this->addCss('/app/admin/formwidgets/datepicker/assets/css/datepicker.css', 'datepicker-css');
		$this->addJs('/app/admin/formwidgets/datepicker/assets/js/datepicker.js', 'datepicker-js');	
		
		[$locationParam, $startDate, $endDate] = $this->getParams();
		$this->vars['locationParam'] = $locationParam;
		$this->vars['startDate'] = $startDate;
		$this->vars['endDate'] = $endDate;
		$this->vars['results'] = $this->getResults();
	}
	
	public function getParams(){
		
	    $locations = $this->getLocations();
	    	   
	    $locationParam = Request::get('location', array_keys($locations)[0]);
	    $startDate = Request::get('start_date', strtotime('-7 days'));
	    $endDate = Request::get('end_date', strtotime('today'));
	    
	    return [$locationParam, new Carbon($startDate), new Carbon($endDate)];
	    	    
    }

	public function getLocations()
    {
    
    	if ($this->locations) return $this->locations;
    
    	$locations = []; 
    	
    	foreach (Locations_model::get() as $l){
     
			if (AdminLocation::getId() === NULL || AdminLocation::getId() == $l->location_id){
				
				if ($l->location_status){
				
					$locations[$l->location_id] = $l->location_name;
				
				}
			}
    	
    	}
    	
    	$this->locations = $locations;
    	
    	return $locations;        
        
    }
        
    public function getResults()
    {
	    [$locationParam, $startDate, $endDate] = $this->getParams();
	    
	    $results = [
		    'total_sales' => 0,
		    'total_orders' => 0,
		    'quantity_of_items' => 0,
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
		
		// orders by customer
		$ordersByCustomer = $orders->groupBy('email');
		
		// get orders by customers customers
		$results['orders_by_customer'] = $ordersByCustomer
		->map(function($ordersByCustomer, $key) {
			
			if (!$first = $ordersByCustomer->first())
				return false;			
			
			return (object)[
				'name' => $first->first_name.' '.$first->first()->last_name,
				'email' => $first->first()->email,
				'value' => $ordersByCustomer->sum('order_total'),
			];
		});
		
		$results['top_customers'] = $results['orders_by_customer']
		->sortByDesc('value')
		->slice(0, 10); 
		
		$results['bottom_customers'] = $results['orders_by_customer']
		->sortBy('value')
		->slice(0, 10); 
						
		// get order value and count by day
		$results['orders_by_day'] = collect([0, 1, 2, 3, 4, 5, 6])
		->map(function($dayOfWeek) use($orders) {
			
			$ordersOnDay = $orders->filter(function($order) use($dayOfWeek){
				return $order->order_date->dayOfWeek == $dayOfWeek;
			});
			
			return (object)[
				'value' => $ordersOnDay->sum('order_total'),
				'count' => $ordersOnDay->count(),
			];
						
		});
		
		// get order value and count by hour
		$results['orders_by_hour'] = collect([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 14, 16, 17, 17, 19, 20, 21, 22, 23])
		->map(function($hourOfDay) use($orders) {
			
			$ordersInHour = $orders->filter(function($order) use($hourOfDay) {
				$time = explode(':', $order->order_time);
				return intval(array_shift($time)) == $hourOfDay;
			});
			
			return (object)[
				'value' => $ordersInHour->sum('order_total'),
				'count' => $ordersInHour->count(),
			];
						
		});
												
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
		->map(function($orderItems, $key){
			if (!$first = $orderItems->first())
				return false;
			
			return (object)[
				'subtotal' => $orderItems->sum('subtotal'),
				'quantity' => $orderItems->sum('quantity'),
				'menu_id' => $key,
				'name' => $first->name,
			];
		});
		
		// get best selling items
		$results['top_items'] = $orderItems
		->sortByDesc('quantity')
		->slice(0, 10);
		
		// get worst selling items
		$results['bottom_items'] = $orderItems
		->sortBy('quantity')
		->slice(0, 10);
		
		// get a list of menus vs categories
		$menusCategories = Menus_model::where('menu_status', 1)
		->get()
		->map(function($menu) {
			return (object)[
				'menu_id' => $menu->menu_id,
				'categories' => $menu->categories->pluck('category_id')->toArray()
			];
		})
		->keyBy('menu_id');
		
		// get a list of categories
		$categories = Categories_model::where('status', 1)
		->get()
		->sortBy('name')
		->pluck('name', 'category_id');
		
		// get sales by category
		$results['orders_by_category'] = $categories
		->map(function($category, $categoryKey) use ($orderItems, $menusCategories){
			
			$ordersInThisCategory = $orderItems
			->filter(function($orderItem) use ($categoryKey, $menusCategories){
				if ($categoryList = $menusCategories->get($orderItem->menu_id)) {
					if (in_array($categoryKey, $categoryList->categories)) {
						return true;
					}
				}
				return false;
			});
						
			return (object)[
				'name' => $category,
				'value' => $ordersInThisCategory->sum('subtotal'),
				'count' => $ordersInThisCategory->count(),
			];
			
		});
					    	    	    	    	    
	    return (object)$results;
    }
    
}
