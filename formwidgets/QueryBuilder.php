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
        $this->addJs('https://cdn.jsdelivr.net/npm/jQuery-QueryBuilder@2.6.0/dist/js/query-builder.standalone.js', 'jquery-builder-js');
        $this->addJs('js/querybuilder.js', 'querybuilder-js');
        
        $this->addCss('https://cdn.jsdelivr.net/npm/jQuery-QueryBuilder@2.6.0/dist/css/query-builder.default.min.css', 'jquery-builder-css');
    }

    public function getSaveValue($value)
    {
        return json_decode($value, true);
    }
}
