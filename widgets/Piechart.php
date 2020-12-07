<?php

namespace Thoughtco\Reports\Widgets;

use Admin\Classes\BaseDashboardWidget;
use Thoughtco\Reports\Classes\ReportsCache;

/**
 * Piechart widget.
 */
class Piechart extends BaseDashboardWidget
{
    /**
     * @var string A unique alias to identify this widget.
     */
    protected $defaultAlias = 'piechart';

    /**
     * Renders the widget.
     */
    public function render()
    {
        $this->prepareVars();

        return $this->makePartial('piechart/piechart');
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
            'orders_by_day' => [
                'label' => 'lang:thoughtco.reports::default.text_orders_by_day',
                'icon' => ' fa fa-calendar',
            ],   
            'orders_by_hour' => [
                'label' => 'lang:thoughtco.reports::default.text_orders_by_hour',
                'icon' => ' fa fa-clock',
            ],              
            'orders_by_category' => [
                'label' => 'lang:thoughtco.reports::default.text_orders_by_category',
                'icon' => ' fa fa-stream',
            ],    
            'orders_by_payment_method' => [
                'label' => 'lang:thoughtco.reports::default.text_orders_by_payment_type',
                'icon' => ' fa fa-money-check-alt',
            ],
        ];
    }
    
    public function loadAssets()
    {
		$this->addJs('/app/system/assets/ui/js/vendor/moment.min.js', 'moment-js');
		$this->addJs('/app/admin/dashboardwidgets/charts/assets/vendor/chartjs/Chart.min.js', 'chart-js');
        $this->addJs('js/charts.js', 'reports-js');
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
        $this->vars['chartContext'] = $context = $this->property('context');
        $this->vars['chartLabel'] = $this->getContextLabel($context);
        $this->vars['chartIcon'] = $this->getContextIcon($context);
        $this->vars['chartData'] = $this->callContextCountMethod($context);
    }

    protected function callContextCountMethod($context)
    {
        return ReportsCache::get($context.'_data', []);
    }
}
