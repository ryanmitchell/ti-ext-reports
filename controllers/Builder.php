<?php

namespace Thoughtco\Reports\Controllers;

use AdminMenu;
use Admin\Facades\AdminLocation;
use ApplicationException;
use Event;
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
        
        $klass = new $model->builderjson['model']();
        
        // some fields require extending query
        Event::listen('thoughtco.reports.fieldToQuery', function ($controller, $query, $field, $operator, $value, $condition) 
        {
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
            
            if ($field == 'orders.customer_name') {
                return $query->whereRaw('CONCAT(first_name, " ", last_name) '.$operator.' ?', [$value], $condition);    
            }
            
            if ($field == 'orders.menus') {
                $query->join('order_menus', 'order_menus.order_id', '=', 'orders.order_id');
                return $query->where('order_menus.menu_id', $operator, $value, $condition);
            }
            
        });
        
        $parser = new QueryBuilderParser();
        
        $table = $klass->newQuery();
        $query = $parser->parse(json_encode($model->builderjson['rules']), $table);

        dd($rows = $query->get());
        exit();
        
    }
}
