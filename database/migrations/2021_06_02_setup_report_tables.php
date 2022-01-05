<?php namespace Thoughtco\Reports\Database\Migrations;

use DB;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReportTables extends Migration
{
    public function up()
    {
        Schema::create('thoughtco_reportbuilder', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->text('title');
            $table->mediumText('builderjson');
            $table->mediumText('list_columns');
            $table->mediumText('csv_columns');
            $table->timestamps();
        });

        $this->seedReports();

    }

    public function down()
    {
        Schema::dropIfExists('thoughtco_reportbuilder');
    }

    protected function seedReports()
    {
        $json = '[{
		        "title": "Orders in the last 30 days",
		        "builderjson": {
			        "model": "\\\\Admin\\\\Models\\\\Orders_model",
			        "rules": {
				        "condition": "AND",
				        "rules": [{
					        "id": "order_date_relative",
					        "field": "order_date_relative",
					        "type": "string",
					        "input": "select",
					        "operator": "greater_or_equal",
					        "value": "30"
				        }],
				        "valid": true
			        }
		        },
		        "list_columns": [{
			        "priority": 0,
			        "column": "{
                        \"key\": \"email\",
                        \"contexts\": [\"\\\\Admin\\\\Models\\\\Customers_model\", \"\\\\Admin\\\\Models\\\\Orders_model\"]
			        }",
			        "label": "Email"
		        }, {
			        "priority": 1,
			        "column": "{
                        \"key\": \"customer_name\",
                        \"contexts\": [\"\\\\Admin\\\\Models\\\\Customers_model\", \"\\\\Admin\\\\Models\\\\Orders_model\"]
			        }",
			        "label": "Name"
		        }, {
			        "priority": 2,
			        "column": "{
                        \"key\": \"order_total\",
                        \"contexts\": [\"\\\\Admin\\\\Models\\\\Orders_model\"]
			        }",
			        "label": "Order Total"
		        }, {
			        "priority": 3,
			        "column": "{
                        \"key\": \"order_date\",
                        \"contexts\": [\"\\\\Admin\\\\Models\\\\Orders_model\"]
			        }",
			        "label": "Date"
		        }],
		        "csv_columns": [{
			        "priority": 0,
			        "column": "{
                        \"key\": \"email\",
                        \"contexts\": [\"\\\\Admin\\\\Models\\\\Customers_model\", \"\\\\Admin\\\\Models\\\\Orders_model\"]
			        }",
			        "label": "Email"
		        }, {
			        "priority": 1,
			        "column": "{
                        \"key\": \"customer_name\",
                        \"contexts\": [\"\\\\Admin\\\\Models\\\\Customers_model\", \"\\\\Admin\\\\Models\\\\Orders_model\"]
			        }",
			        "label": "Name"
		        }, {
			        "priority": 2,
			        "column": "{
                        \"key\": \"order_total\",
                        \"contexts\": [\"\\\\Admin\\\\Models\\\\Orders_model\"]
			        }",
			        "label": "Order Total"
		        }, {
			        "priority": 3,
			        "column": "{
                        \"key\": \"order_date\",
                        \"contexts\": [\"\\\\Admin\\\\Models\\\\Orders_model\"]
			        }",
			        "label": "Date"
		        }]
	        },
	        {
		        "title": "Customers who registered in the last 90 days",
		        "builderjson": {
			        "model": "\\\\Admin\\\\Models\\\\Customers_model",
			        "rules": {
				        "condition": "AND",
				        "rules": [{
					        "id": "date_added_relative",
					        "field": "date_added_relative",
					        "type": "string",
					        "input": "select",
					        "operator": "greater_or_equal",
					        "value": "90"
				        }],
				        "valid": true
			        }
		        },
		        "list_columns": [{
			        "priority": 0,
			        "column": "{
                        \"key\": \"customer_name\",
                        \"contexts\": [\"\\\\Admin\\\\Models\\\\Customers_model\", \"\\\\Admin\\\\Models\\\\Orders_model\"]
			        }",
			        "label": "Name"
		        }, {
			        "priority": 1,
			        "column": "{
                        \"key\": \"email\",
                        \"contexts\": [\"\\\\Admin\\\\Models\\\\Customers_model\", \"\\\\Admin\\\\Models\\\\Orders_model\"]
			        }",
			        "label": "Email"
		        }, {
			        "priority": 2,
			        "column": "{
                        \"key\": \"customer_address\",
                        \"contexts\": [\"\\\\Admin\\\\Models\\\\Customers_model\"]
			        }",
			        "label": "Address"
		        }],
		        "csv_columns": [{
			        "priority": 0,
			        "column": "{
                        \"key\": \"customer_name\",
                        \"contexts\": [\"\\\\Admin\\\\Models\\\\Customers_model\", \"\\\\Admin\\\\Models\\\\Orders_model\"]
			        }",
			        "label": "Name"
		        }, {
			        "priority": 1,
			        "column": "{
                        \"key\": \"email\",
                        \"contexts\": [\"\\\\Admin\\\\Models\\\\Customers_model\", \"\\\\Admin\\\\Models\\\\Orders_model\"]
			        }",
			        "label": "Email"
		        }, {
			        "priority": 2,
			        "column": "{
                        \"key\": \"customer_address\",
                        \"contexts\": [\"\\\\Admin\\\\Models\\\\Customers_model\"]
			        }",
			        "label": "Address"
		        }]
	        }
        ]';

        $json = preg_replace('/[[:cntrl:]]/', '', $json);

        $query_rows = json_decode($json);

        foreach ($query_rows as $i=>$j)
        {
            foreach ($j as $k=>$v) {
                if (!is_string($v))
                    $query_rows[$i]->$k = json_encode($v);
            }
        }
        foreach ($query_rows as $row)
            DB::table('thoughtco_reportbuilder')->insert((array)$row);


    }
}
