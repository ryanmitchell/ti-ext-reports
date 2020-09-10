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
	    
	    // get total sales
	    $results['total_sales'] = currency_format(Orders_model::where([
		    ['order_date', '>=', $startDate->format('Y-m-d')],
		    ['order_date', '<=', $endDate->format('Y-m-d')],
	    ])
	    ->whereIn('status_id', setting('completed_order_status'))
	    ->sum('order_total'));
	    
	    // get total orders
	    $results['total_orders'] = Orders_model::where([
		    ['order_date', '>=', $startDate->format('Y-m-d')],
		    ['order_date', '<=', $endDate->format('Y-m-d')],
	    ])
	    ->whereIn('status_id', setting('completed_order_status'))
	    ->count();
	    
	    // get top customers
	    $results['top_customers'] = DB::table('orders')
	    ->where([
		    ['order_date', '>=', $startDate->format('Y-m-d')],
		    ['order_date', '<=', $endDate->format('Y-m-d')],
	    ])
	    ->select(DB::raw('sum(order_total) as value, order_id, concat(first_name, " ", last_name) as name'))
	    ->whereIn('status_id', setting('completed_order_status'))
	    ->groupBy('email')
	    ->orderBy('value', 'desc')
	    ->limit(10)	    
	    ->get();	 
	    	    
	    // get order ids for the time period
	    $orderIds = Orders_model::where([
		    ['order_date', '>=', $startDate->format('Y-m-d')],
		    ['order_date', '<=', $endDate->format('Y-m-d')],
	    ])
	    ->whereIn('status_id', setting('completed_order_status'))
	    ->pluck('order_id');
	    
	    // get quantity of items
	    $results['quantity_of_items'] = DB::table('order_menus')
	    ->whereIn('order_id', $orderIds)
	    ->count();
	    	    
	    // get quantity of items
	    $results['top_items'] = DB::table('order_menus')
	    ->select(DB::raw('sum(quantity) as quantity, menu_id, name'))
	    ->whereIn('order_id', $orderIds)
	    ->groupBy('menu_id')
	    ->orderBy('quantity', 'desc')
	    ->limit(10)
	    ->get();
	    	    	    	    	    
	    return (object)$results;
    }
    
}
