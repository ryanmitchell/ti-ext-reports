<?php

namespace Thoughtco\Reports\FormWidgets;

use Admin\Classes\BaseFormWidget;
use AdminLocation;

class QueryBuilder extends BaseFormWidget
{
    protected $defaultAlias = 'querybuilder';

    public function initialize()
    {
        $this->fillFromConfig([
            'filters',
        ]);
    }

    public function render()
    {
        $this->prepareVars();

        return $this->makePartial('querybuilder/querybuilder');
    }

    /**
     * Prepares the list data
     */
    public function prepareVars()
    {
        $this->vars['field'] = $this->formField;
        $this->vars['name'] = $this->formField->getName();
        $this->vars['value'] = $value = $this->getLoadValue();
        $this->vars['filters'] = $this->config['filters'];
    }

    public function loadAssets()
    {
        $this->addJs('~/app/system/assets/ui/js/vendor/moment.min.js', 'moment-js');
        $this->addJs('~/app/admin/formwidgets/datepicker/assets/vendor/datepicker/bootstrap-datepicker.min.js', 'bootstrap-datepicker-js');
        $this->addJs('js/query-builder.standalone.js', 'query-builder.standalone-js');
        $this->addJs('js/querybuilder.js', 'querybuilder-js');

        $this->addCss('~/app/admin/formwidgets/datepicker/assets/vendor/datepicker/bootstrap-datepicker.min.css', 'bootstrap-datepicker-css');
        $this->addCss('css/querybuilder.css', 'jquery-builder-css');
        $this->addCss('~/app/admin/formwidgets/datepicker/assets/css/datepicker.css', 'datepicker-css');
    }

    public function getSaveValue($value)
    {
        return json_decode($value, true);
    }
}
