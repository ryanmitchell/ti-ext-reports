<?php

namespace Thoughtco\Reports\Widgets;

use Admin\Classes\BaseDashboardWidget;
use Thoughtco\Reports\Classes\ReportsCache;

/**
 * Statistic dashboard widget.
 */
class Statistics extends BaseDashboardWidget
{
    /**
     * @var string A unique alias to identify this widget.
     */
    protected $defaultAlias = 'statistics';

    /**
     * Renders the widget.
     */
    public function render()
    {
        $this->prepareVars();

        return $this->makePartial('statistics/statistics');
    }

    public function defineProperties()
    {
        return [
            'context' => [
                'label' => 'admin::lang.dashboard.text_context',
                'default' => 'sale',
                'type' => 'select',
                'options' => $this->getContextOptions(),
            ],
        ];
    }

    public function listContext()
    {
        return [
            'total_sales' => [
                'label' => 'lang:thoughtco.reports::default.text_total_sales',
                'icon' => ' bg-green text-white fa fa-money-bill',
            ],
            'total_orders' => [
                'label' => 'lang:thoughtco.reports::default.text_total_orders',
                'icon' => ' bg-blue text-white fa fa-shopping-basket',
            ],            
            'total_items' => [
                'label' => 'lang:thoughtco.reports::default.text_total_items',
                'icon' => ' bg-warning text-white fa fa-line-chart',
            ],
            'pickup_orders_value' => [
                'label' => 'lang:thoughtco.reports::default.text_collection_orders',
                'icon' => ' bg-blue text-white fa fa-store',
            ],  
            'pickup_orders' => [
                'label' => 'lang:thoughtco.reports::default.text_collection_orders_count',
                'icon' => ' bg-blue text-white fa fa-store',
            ],             
            'delivery_orders_value' => [
                'label' => 'lang:thoughtco.reports::default.text_delivery_orders',
                'icon' => ' bg-blue text-white fa fa-shipping-fast',
            ],
            'delivery_orders_count' => [
                'label' => 'lang:thoughtco.reports::default.text_delivery_orders_count',
                'icon' => ' bg-blue text-white fa fa-shipping-fast',
            ],            
            'cancelled_orders_value' => [
                'label' => 'lang:thoughtco.reports::default.text_cancelled_orders',
                'icon' => ' bg-danger text-white fa fa-exclamation-circle',
            ],   
            'cancelled_orders_count' => [
                'label' => 'lang:thoughtco.reports::default.text_cancelled_orders_count',
                'icon' => ' bg-danger text-white fa fa-exclamation-circle',
            ],                       
        ];
    }

    public function getContextOptions()
    {
        return array_map(function ($context) {
            return array_get($context, 'label');
        }, $this->listContext());
    }

    public function getContextLabel($context)
    {
        return array_get(array_get($this->listContext(), $context, []), 'label', '--');
    }

    public function getContextColor($context)
    {
        return array_get(array_get($this->listContext(), $context, []), 'color', 'success');
    }

    public function getContextIcon($context)
    {
        return array_get(array_get($this->listContext(), $context, []), 'icon', 'fa fa-bar-chart-o');
    }

    public function loadAssets()
    {
        $this->addCss('css/statistics.css', 'statistics-css');
    }

    protected function prepareVars()
    {
        $this->vars['statsContext'] = $context = $this->property('context');
        $this->vars['statsLabel'] = $this->getContextLabel($context);
        $this->vars['statsIcon'] = $this->getContextIcon($context);
        $this->vars['statsCount'] = $this->callContextCountMethod($context);
    }

    protected function callContextCountMethod($context)
    {
        return ReportsCache::get($context, 0);
    }
}
