<?php

namespace Thoughtco\Reports\Controllers;

use AdminMenu;
use Admin\Facades\AdminLocation;
use ApplicationException;
use Thoughtco\Reports\Models\QueryBuilder;

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
        ]);
        
        return $this->makeView('builder/view');
        
    }
}
