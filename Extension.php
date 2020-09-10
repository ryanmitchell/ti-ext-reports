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
    
    public function registerPermissions()
    {
        return [
            'Thoughtco.Reports.View' => [
                'description' => 'View reports',
                'group' => 'module',
            ],
        ];
    }
    
    public function registerNavigation()
    {
        return [
            'sales' => [
                'child' => [
                    'reports' => [
                        'priority' => 10,
                        'class' => 'pages',
                        'href' => admin_url('thoughtco/reports/dashboard'),
                        'title' => lang('lang:thoughtco.reports::default.text_title'),
                        'permission' => 'Thoughtco.Reports.View',
                    ],
                ],
            ],
        ];
    } 

}

?>
