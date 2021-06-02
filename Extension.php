<?php namespace Thoughtco\Reports;

use DB;
use Event;
use Admin\Widgets\Form;
use System\Classes\BaseExtension;
use Thoughtco\Irtouch\Models\Settings;
use Thoughtco\Irtouch\Classes\LocationRequest;
use Thoughtco\Irtouch\Resources\TouchJsonClient;

/**
 * Extension Information File
**/
class Extension extends BaseExtension
{
    public function boot()
    {
	    	    	    
    }
    
    public function registerFormWidgets()
    {
        return [
            'Thoughtco\Reports\FormWidgets\QueryBuilder' => [
                'label' => 'lang:thoughtco.reports::default.qb.text_title',
                'code' => 'querybuilder',
            ],
            'Thoughtco\Reports\FormWidgets\ReportsTable' => [
                'label' => 'lang:thoughtco.reports::default.qb.text_reportstable',
                'code' => 'reportstable',
            ],
        ];
    }
    
    public function registerNavigation()
    {
        return [
            'reports' => [
                'icon' => 'fa-chart-pie',
                'title' => lang('lang:thoughtco.reports::default.text_title'),
                'priority' => 35,
                'child' => [
                    'dashboard' => [
                        'priority' => 5,
                        'class' => 'pages',
                        'href' => admin_url('thoughtco/reports/dashboard'),
                        'title' => lang('lang:thoughtco.reports::default.text_dashboard_title'),
                        'permission' => 'Thoughtco.Reports.View',
                    ],
                    'builder' => [
                        'priority' => 5,
                        'class' => 'pages',
                        'href' => admin_url('thoughtco/reports/builder'),
                        'title' => lang('lang:thoughtco.reports::default.text_builder_title'),
                        'permission' => 'Thoughtco.Reports.View',
                    ],
                ],
            ],
        ];
    } 
    
    public function registerPermissions()
    {
        return [
            'Thoughtco.Reports.View' => [
                'description' => 'View reports',
                'group' => 'module',
            ],
        ];
    }

}

?>
