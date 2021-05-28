<?php

namespace Thoughtco\Reports\FormWidgets;

use AdminLocation;
use Admin\Classes\BaseFormWidget;

/**
 * Star Rating
 * Renders a raty star field.
 */
class QueryBuilder extends BaseFormWidget
{
    protected $defaultAlias = 'querybuilder';

    public function initialize()
    {
        $this->fillFromConfig([
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
        $this->vars['filters'] = $this->getFilters();
        $this->vars['fieldOptions'] = [
            'customers' => 'Customers',
            'orders' => 'Orders',
        ];

    }

    public function loadAssets()
    {
        $this->addJs('https://cdn.jsdelivr.net/npm/jQuery-QueryBuilder@2.6.0/dist/js/query-builder.standalone.js', 'jquery-builder-js');
        $this->addJs('js/querybuilder.js', 'querybuilder-js');
        
        $this->addCss('https://cdn.jsdelivr.net/npm/jQuery-QueryBuilder@2.6.0/dist/css/query-builder.default.min.css', 'jquery-builder-css');
    }
    
    public function getFilters()
    {
        return [
            'customers' => [
                'label' => 'Customers',
                'filters' => [
                    [
                        'id' => 'customer.name',
                        'label' => 'Customer name',
                        'type' => 'string',
                    ],
                    [
                        'id' => 'customer.email',
                        'label' => 'Customer email',
                        'type' => 'string',
                    ],  
                ] 
            ],
            'orders' => [
                'label' => 'Orders',
                'filters' => [
                    [
                        'id' => 'orders.location',
                        'label' => 'Location',
                        'type' => 'integer',
                        'input' => 'select',
                        'values' => AdminLocation::listLocations(),
                    ],                    
                ]
            ]
        ];
// [{
//         id: 'name',
//         label: 'Name',
//         type: 'string'
//       }, {
//         id: 'category',
//         label: 'Category',
//         type: 'integer',
//         input: 'select',
//         values: {
//           1: 'Books',
//           2: 'Movies',
//           3: 'Music',
//           4: 'Tools',
//           5: 'Goodies',
//           6: 'Clothes'
//         },
//         operators: ['equal', 'not_equal', 'in', 'not_in', 'is_null', 'is_not_null']
//       }, {
//         id: 'in_stock',
//         label: 'In stock',
//         type: 'integer',
//         input: 'radio',
//         values: {
//           1: 'Yes',
//           0: 'No'
//         },
//         operators: ['equal']
//       }, {
//         id: 'price',
//         label: 'Price',
//         type: 'double',
//         validation: {
//           min: 0,
//           step: 0.01
//         }
//       }, {
//         id: 'id',
//         label: 'Identifier',
//         type: 'string',
//         placeholder: '____-____-____',
//         operators: ['equal', 'not_equal'],
//         validation: {
//           format: /^.{4}-.{4}-.{4}$/
//         }
//       }
    }

    public function getSaveValue($value)
    {
        return json_decode($value, true);
    }
}
