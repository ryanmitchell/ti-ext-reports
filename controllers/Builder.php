<?php

namespace Thoughtco\Reports\Controllers;

use AdminMenu;
use Admin\Facades\AdminLocation;
use ApplicationException;

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
}
