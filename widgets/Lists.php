<?php

namespace Thoughtco\Reports\Widgets;

use Admin\Classes\BaseDashboardWidget;
use Thoughtco\Reports\Classes\ReportsCache;

/**
 * Lists widget.
 */
class Lists extends BaseDashboardWidget
{
    /**
     * @var string A unique alias to identify this widget.
     */
    protected $defaultAlias = 'lists';

    /**
     * Renders the widget.
     */
    public function render()
    {
        $this->prepareVars();

        return $this->makePartial('lists/lists');
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
            'top_customers' => [
                'label' => 'lang:thoughtco.reports::default.text_top_customers',
                'icon' => ' fa fa-users',
            ],
            'bottom_customers' => [
                'label' => 'lang:thoughtco.reports::default.text_bottom_customers',
                'icon' => ' fa fa-users',
            ],            
            'top_items' => [
                'label' => 'lang:thoughtco.reports::default.text_best_selling_items',
                'icon' => ' fa fa-shopping-bag',
            ],
            'bottom_items' => [
                'label' => 'lang:thoughtco.reports::default.text_worst_selling_items',
                'icon' => ' fa fa-shopping-bag',
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

    protected function prepareVars()
    {
        $this->vars['listContext'] = $context = $this->property('context');
        $this->vars['listLabel'] = $this->getContextLabel($context);
        $this->vars['listIcon'] = $this->getContextIcon($context);
        $this->vars['listItems'] = $this->callContextCountMethod($context);
    }

    protected function callContextCountMethod($context)
    {
        return ReportsCache::get($context, []);
    }
}
