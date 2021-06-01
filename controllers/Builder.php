<?php

namespace Thoughtco\Reports\Controllers;

use AdminMenu;
use Admin\Facades\AdminLocation;
use ApplicationException;
use Event;
use League\Csv\Writer;
use Template;
use Thoughtco\Reports\Models\QueryBuilder;
use Thoughtco\Reports\Parser\QueryBuilderParser;

class Builder extends \Admin\Classes\AdminController
{    
    public $implement = [
        'Admin\Actions\FormController',
        'Admin\Actions\ListController',
    ];

    public $listConfig = [
        'list' => [
            'model' => 'Thoughtco\Reports\Models\QueryBuilder',
            'title' => 'lang:thoughtco.reports::default.text_builder_title',
            'emptyMessage' => 'lang:thoughtco.reports::default.text_builder_empty',
            'defaultSort' => ['title', 'ASC'],
            'configFile' => 'querybuilder',
        ],
    ];

    public $formConfig = [
        'name' => 'lang:thoughtco.reports::default.text_builder_form',
        'model' => 'Thoughtco\Reports\Models\QueryBuilder',
        'create' => [
            'title' => 'lang:admin::lang.form.edit_title',
            'redirect' => 'thoughtco/reports/builder/edit/{id}',
            'redirectClose' => 'thoughtco/reports/builder',
        ],
        'edit' => [
            'title' => 'lang:admin::lang.form.edit_title',
            'redirect' => 'thoughtco/reports/builder/edit/{id}',
            'redirectClose' => 'thoughtco/reports/builder',
        ],
        'preview' => [
            'title' => 'lang:admin::lang.form.preview_title',
            'redirect' => 'thoughtco/reports/builder',
        ],
        'delete' => [
            'redirect' => 'thoughtco/reports/builder',
        ],
        'configFile' => 'querybuilder',
    ];

    protected $requiredPermissions = 'Thoughtco.Reports.*';

    public function __construct()
    {
        parent::__construct();
        AdminMenu::setContext('reports', 'builder');
    }
    
    public function view($context, $id)
    {
        if (!($model = QueryBuilder::find($id)))
            abort(404);  
            
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
            
        if ($csv = request()->input('csv')) {
            
            $klass = new $model->builderjson['model']();
            $parser = new QueryBuilderParser();
            
            $table = $klass->newQuery();
            $query = $parser->parse(json_encode($model->builderjson['rules']), $table);
            
            $writer = Writer::createFromString();
            $writer->insertAll(new \ArrayIterator($query->get()->toArray()));
            
            echo $writer->getContent();
            exit();
        
        };
            
        Template::setTitle($model->title);
        
        $this->vars['datatable'] = $this->makeFormWidget('Thoughtco\Reports\FormWidgets\ReportsTable', (object)[
            'fieldName' => 'report',
            'valueFrom' => '',
        ], [
            'attributes' => [
                'model' => $model,
            ],
            'columns' => [
                'order_id' => [
                    'title' => 'lang:admin::lang.orders.column_time_date',
                ],
                'first_name' => [
                    'title' => 'lang:admin::lang.label_status',
                ],
                'last_name' => [
                    'title' => 'lang:admin::lang.orders.column_comment',
                ],
            ],
            'useAjax' => TRUE,
            'defaultSort' => ['orders.order_id', 'desc'],
            'searchableFields' => ['first_name'],
            'showRefreshButton' => TRUE,
        ]);
        
        return $this->makeView('builder/view');
        
    }
}
