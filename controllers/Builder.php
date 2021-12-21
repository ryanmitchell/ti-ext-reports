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
            'redirectNew' => 'thoughtco/reports/builder/create',
        ],
        'edit' => [
            'title' => 'lang:admin::lang.form.edit_title',
            'redirect' => 'thoughtco/reports/builder/edit/{id}',
            'redirectClose' => 'thoughtco/reports/builder',
            'redirectNew' => 'thoughtco/reports/builder/create',
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

    public function edit($context, $id)
    {
        parent::edit($context, $id);
        $this->addJs('~/extensions/thoughtco/reports/assets/js/editing.js', 'querybuilder-editing-js');
    }

    public function view($context, $id)
    {
        if (!($model = QueryBuilder::find($id)))
            abort(404);

        // some fields require extending the where
        Event::listen('thoughtco.reports.fieldToQuery', function ($controller, $query, $field, $operator, $value, $condition)
        {
            $model = $query->getModel();

            // we only care about certain models by default - this allows others to extend
            if (!in_array(get_class($model), ['Admin\Models\Orders_model', 'Admin\Models\Customers_model']))
                return;

            $tableName = $model->getConnection()->getTablePrefix().$model->getTable();

            $existingJoins = collect($query->getQuery()->joins)->pluck('table');

            if ($field == 'customers.orderdate') {
                return $query->whereHas('orders', function($query) {
                    return $query->where('orders.order_date', $operator, $value);
                }, $condition);
            }

            if ($field == 'date_added_relative') {
                $value = strtotime('-'.$value.' days');
                return $query->where($tableName.'.date_added', $operator, date('Y-m-d H:i:s', $value), $condition);
            }

            if ($field == 'order_date_relative') {
                $value = strtotime('-'.$value.' days');
                return $query->where('orders.order_date', $operator, date('Y-m-d', $value), $condition);
            }

            if ($field == 'orders.categories') {

                $value = \Admin\Models\Menus_model::whereHas('categories', function($query) use ($value) {
                    $query->where('categories.category_id', $value);
                })->pluck('menu_id');

                if (! $existingJoins->contains('order_menus'))
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

                if (! $existingJoins->contains('order_menus'))
                    $query->join('order_menus', 'order_menus.order_id', '=', 'orders.order_id');

                return $query->where('order_menus.menu_id', $operator, $value, $condition);
            }

            // catch-all
            if (stripos($field, '.') === false)
                $field = $model->getTable().'.'.$field;

            return $query->where($field, $operator, $value, $condition);

        });

        // some require extending the overall query
        Event::listen('thoughtco.reports.extendQuery', function($controller, $query, $modelName) {

            // we only care about certain models by default - this allows others to extend
            if (!in_array($modelName, ['\Admin\Models\Orders_model', '\Admin\Models\Customers_model']))
                return;

            $model = $query->getModel();
            $tableName = $model->getConnection()->getTablePrefix().$model->getTable();

            $existingJoins = collect($query->getQuery()->joins)->pluck('table');

            $query->selectRaw($tableName.'.*, CONCAT(first_name, " ", last_name) as customer_name');

            if ($modelName == '\Admin\Models\Customers_model') {

                if (! $existingJoins->contains('order_menus'))
                    $query->join('order_menus', 'order_menus.order_id', '=', 'orders.order_id');


                $query->leftJoin('addresses', 'addresses.address_id', 'customers.address_id');
                $query->selectRaw('CONCAT(address_1, " ", address_2, " ", city, " ", state, " ", postcode) as customer_address');

            }

        });

        // csv export
        if ($csv = request()->input('csv')) {

            $klass = new $model->builderjson['model']();
            $parser = new QueryBuilderParser();

            $table = $klass->newQuery();
            $query = $parser->parse(json_encode($model->builderjson['rules']), $table);

            $this->fireSystemEvent('thoughtco.reports.extendQuery', [$query, $model->builderjson['model']] );

            $data = $query->get();

            $csv_columns = [];
            $csv_headings = [];
            foreach ($model->csv_columns as $list_col) {
                $col = json_decode($list_col['column'], true);
                $csv_columns[] = $col['key'];
                $csv_headings[] = $list_col['label'];
            }

            $data = $data->map(function($row) use ($csv_columns) {
                return $row->only($csv_columns);
            })->sortBy($csv_columns[0]);

            $writer = Writer::createFromString();
            $writer->insertOne($csv_headings);
            $writer->insertAll(new \ArrayIterator($data->toArray()));

            // this will set the file to download properly for most use-cases, but there is a known limitation with Excel on macOS (see https://csv.thephpleague.com/9.0/interoperability/encoding/)
            header('Content-Encoding: UTF-8');
            header('Content-Type: application/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename='.str_replace(' ', '_', $model->title).'.csv');
            header('Pragma: no-cache');

            $writer->setOutputBOM(Writer::BOM_UTF8);
            $writer->addStreamFilter('convert.iconv.ISO-8859-15/UTF-8');

            echo $writer->getContent();
            exit();

        };

        Template::setTitle($model->title);

        $list_columns = [];
        $sort_column = '';
        foreach ($model->list_columns as $list_col) {
            $col = json_decode($list_col['column'], true);
            $list_columns[$col['key']] = [
                'title' => $list_col['label'],
            ];
            if ($sort_column == '')
                $sort_column = $col['key'];
        }

        $this->vars['datatable'] = $this->makeFormWidget('Thoughtco\Reports\FormWidgets\ReportsTable', (object)[
            'fieldName' => 'report',
            'valueFrom' => '',
        ], [
            'attributes' => [
                'model' => $model,
            ],
            'columns' => $list_columns,
            'useAjax' => TRUE,
            'defaultSort' => [$sort_column, 'asc'],
            'searchableFields' => array_keys($list_columns),
            'showRefreshButton' => TRUE,
        ]);

        return $this->makeView('builder/view');

    }
}
