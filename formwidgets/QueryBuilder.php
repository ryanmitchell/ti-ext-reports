<?php

namespace Thoughtco\Reports\FormWidgets;

use AdminLocation;
use Admin\Classes\BaseFormWidget;

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
        $this->addCss('~/app/admin/formwidgets/datepicker/assets/vendor/datepicker/bootstrap-datepicker.min.css', 'bootstrap-datepicker-css');
        $this->addJs('~/app/admin/formwidgets/datepicker/assets/vendor/datepicker/bootstrap-datepicker.min.js', 'bootstrap-datepicker-js');
        $this->addCss('~/app/admin/formwidgets/datepicker/assets/css/datepicker.css', 'datepicker-css');

        $this->addJs('https://cdn.jsdelivr.net/npm/jQuery-QueryBuilder@2.6.0/dist/js/query-builder.standalone.js', 'jquery-builder-js');
        $this->addCss('css/querybuilder.css', 'jquery-builder-css');

        $this->addJs('js/querybuilder.js', 'querybuilder-js');
    }

    public function getSaveValue($value)
    {
        return json_decode($value, true);
    }
}
