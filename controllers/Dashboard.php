<?php

namespace Thoughtco\Reports\Controllers;

use AdminMenu;
use Admin\Facades\AdminLocation;
use Admin\Models\Locations_model;
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
		    'top_customers' => 0,
		    'top_items' => 0
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
		
		// get top customers
		$results['top_customers'] = $ordersByCustomer
		->map(function($ordersByCustomer, $key) {
			
			if (!$first = $ordersByCustomer->first())
				return false;			
			
			return (object)[
				'name' => $first->first_name.' '.$first->first()->last_name,
				'email' => $first->first()->email,
				'value' => $ordersByCustomer->sum('order_total'),
			];
		})
		->sortByDesc('value')
		->slice(0, 10); 		
				
		// get ids
		$orderIds = $orders->pluck('order_id');
	    
		// get order items
	    $orderItems = DB::table('order_menus')
	    ->whereIn('order_id', $orderIds)
	    ->get();
		
		// get quantity of items
		$results['quantity_of_items'] = $orderItems->count();
	    	    
	    // get quantity of items
		$results['top_items'] = $orderItems
		->groupBy('menu_id')
		->map(function($orderItems, $key){
			if (!$first = $orderItems->first())
				return false;
			
			return (object)[
				'quantity' => $orderItems->sum('quantity'),
				'menu_id' => $key,
				'name' => $first->name,
			];
		})
		->sortByDesc('quantity')
		->slice(0, 10);
	    	    	    	    	    
	    return (object)$results;
    }
    
}
