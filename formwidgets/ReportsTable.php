<?php

namespace Thoughtco\Reports\FormWidgets;

use Event;
use Thoughtco\Reports\Parser\QueryBuilderParser;

class ReportsTable extends \Admin\FormWidgets\DataTable
{
    protected $defaultAlias = 'reportstable';

    public function getDataTableRecords($offset, $limit, $search)
    {
        if (!($model = $this->config['attributes']['model']))
            abort(404);   
        
        $klass = new $model->builderjson['model']();
        
        // some fields require extending query
        Event::listen('thoughtco.reports.fieldToQuery', function ($controller, $query, $field, $operator, $value, $condition) 
        {
            if ($field == 'customers.orderdate') {
                return $query->whereHas('orders', function($query) {
                    return $query->where('orders.order_date', $operator, $value);
                }, $condition);                
            }
            
            if ($field == 'orders.categories') {
                
                $value = \Admin\Models\Menus_model::whereHas('categories', function($query) use ($value) {
                    $query->where('categories.category_id', $value);
                })->pluck('menu_id');
                               
                $query->join('order_menus', 'order_menus.order_id', '=', 'orders.order_id');
                return $query->where(function($query) use ($operator, $value) {
                    foreach ($value as $val)
                        $query->orWhere('order_menus.menu_id', $operator, $val);
                }, $condition);

            }
            
            if ($field == 'orders.customer_group') {
                return $query->whereHas('customer', function($query) {
                    return $query->where('customer_group_id', $operator, $value);
                }, $condition);
            }
            
            if ($field == 'orders.customer_name' || $field == 'customers.name') {
                return $query->whereRaw('CONCAT(first_name, " ", last_name) '.$operator.' ?', [$value], $condition);    
            }
            
            if ($field == 'orders.delivery_address') {
                return $query->whereHas('address', function($query) {
                    return $query->whereRaw('CONCAT(address_1, " ", address_2, " ", city, " ", state, " ", postcode) '.$operator.' ?', [$value], $condition);
                }, $condition);
            }            
            
            if ($field == 'orders.menus') {
                $query->join('order_menus', 'order_menus.order_id', '=', 'orders.order_id');
                return $query->where('order_menus.menu_id', $operator, $value, $condition);
            }
            
        });
        
        $parser = new QueryBuilderParser();
        
        $table = $klass->newQuery();
        $query = $parser->parse(json_encode($model->builderjson['rules']), $table);
        
        if (strlen($search)) {
            $query->search($search, $this->searchableFields);
        }

        if (is_array($this->defaultSort)) {
            [$sortColumn, $sortBy] = $this->defaultSort;
            $query->orderBy($sortColumn, $sortBy);
        }

        $page = ($offset / $limit) + 1;

        return $query->paginate($limit, ['*'], 'page', $page);
    }
}
