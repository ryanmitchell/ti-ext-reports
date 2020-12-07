<?php

namespace Thoughtco\Reports\Widgets;

use Admin\Classes\BaseDashboardWidget;
use Carbon\Carbon;
use Request;

/**
 * Date range dashboard widget.
 */
class DateRange extends BaseDashboardWidget
{
    /**
     * @var string A unique alias to identify this widget.
     */
    protected $defaultAlias = 'daterange';

    /**
     * Renders the widget.
     */
    public function render()
    {
        $this->prepareVars();

        return $this->makePartial('daterange/daterange');
    }

    public function defineProperties()
    {
        return [
            'context' => [],
        ];
    }
    
    public function loadAssets()
    {
		$this->addJs('/app/admin/formwidgets/datepicker/assets/vendor/datepicker/bootstrap-datepicker.min.js', 'bootstrap-datepicker-js');
		$this->addJs('/app/admin/formwidgets/datepicker/assets/js/datepicker.js', 'datepicker-js');
        
		$this->addCss('/app/admin/formwidgets/datepicker/assets/vendor/datepicker/bootstrap-datepicker.min.css', 'bootstrap-datepicker-css');
		$this->addCss('/app/admin/formwidgets/datepicker/assets/css/datepicker.css', 'datepicker-css');        
    }

    protected function prepareVars()
    {
		$startDate = Request::get('start_date', strtotime('-1 month'));
		$endDate = Request::get('end_date', strtotime('today'));
		
		$this->vars['startDate'] = new Carbon($startDate);
		$this->vars['endDate'] = new Carbon($endDate);
    }
}
