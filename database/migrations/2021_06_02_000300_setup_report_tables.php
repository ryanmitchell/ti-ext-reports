<?php namespace Thoughtco\Reports\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SetupReportTables extends Migration
{
    public function up()
    {
        if (Schema::hasTable('thoughtco_reportbuilder'))
            return;

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
        $reports = [
            [
                'title' => 'Orders in the last 30 days',
                'builderjson' => [
                    'model' => \Admin\Models\Orders_model::class,
                    'rules' => [
                        'condition' => 'AND',
                        'rules' => [[
                            'id' => 'order_date_relative',
                            'field' => 'order_date_relative',
                            'type' => 'string',
                            'input' => 'select',
                            'operator' => 'greater_or_equal',
                            'value' => '30',
                        ]],
                        'valid' => true,
                    ],
                ],
                'list_columns' => [
                    [
                        'priority' => '0',
                        'column' => [
                            'key' => 'email',
                            'contexts' => [\Admin\Models\Customers_model::class, \Admin\Models\Orders_model::class],
                        ],
                        'label' => 'Email',
                    ],
                    [
                        'priority' => '1',
                        'column' => [
                            'key' => 'customer_name',
                            'contexts' => [\Admin\Models\Customers_model::class, \Admin\Models\Orders_model::class],
                        ],
                        'label' => 'Name',
                    ],
                    [
                        'priority' => '2',
                        'column' => [
                            'key' => 'order_total',
                            'contexts' => [\Admin\Models\Orders_model::class],
                        ],
                        'label' => 'Order Total',
                    ],
                ],
                'csv_columns' => [
                    [
                        'priority' => '1',
                        'column' => [
                            'key' => 'order_date',
                            'contexts' => [\Admin\Models\Orders_model::class],
                        ],
                        'label' => 'Date',
                    ],
                    [
                        'priority' => '2',
                        'column' => [
                            'key' => 'email',
                            'contexts' => [\Admin\Models\Customers_model::class, \Admin\Models\Orders_model::class],
                        ],
                        'label' => 'Email',
                    ],
                    [
                        'priority' => '3',
                        'column' => [
                            'key' => 'customer_name',
                            'contexts' => [\Admin\Models\Customers_model::class, \Admin\Models\Orders_model::class],
                        ],
                        'label' => 'Name',
                    ],
                    [
                        'priority' => '4',
                        'column' => [
                            'key' => 'order_total',
                            'contexts' => [\Admin\Models\Orders_model::class],
                        ],
                        'label' => 'Order Total',
                    ],
                ],
            ],
            [
                'title' => 'Customers who registered in the last 90 days',
                'builderjson' => [
                    'model' => \Admin\Models\Customers_model::class,
                    'rules' => [
                        'condition' => 'AND',
                        'rules' => [[
                            'id' => 'date_added_relative',
                            'field' => 'date_added_relative',
                            'type' => 'string',
                            'input' => 'select',
                            'operator' => 'greater_or_equal',
                            'value' => '90',
                        ]],
                        'valid' => true,
                    ],
                ],
                'list_columns' => [
                    [
                        'priority' => '0',
                        'column' => [
                            'key' => 'customer_name',
                            'contexts' => [\Admin\Models\Customers_model::class, \Admin\Models\Orders_model::class],
                        ],
                        'label' => 'Name',
                    ],
                    [
                        'priority' => '1',
                        'column' => [
                            'key' => 'email',
                            'contexts' => [\Admin\Models\Customers_model::class, \Admin\Models\Orders_model::class],
                        ],
                        'label' => 'Email',
                    ],
                    [
                        'priority' => '2',
                        'column' => [
                            'key' => 'customer_address',
                            'contexts' => [\Admin\Models\Customers_model::class],
                        ],
                        'label' => 'Address',
                    ],
                ],
                'csv_columns' => [
                    [
                        'priority' => '0',
                        'column' => [
                            'key' => 'customer_name',
                            'contexts' => [\Admin\Models\Customers_model::class, \Admin\Models\Orders_model::class],
                        ],
                        'label' => 'Name',
                    ],
                    [
                        'priority' => '1',
                        'column' => [
                            'key' => 'email',
                            'contexts' => [\Admin\Models\Customers_model::class, \Admin\Models\Orders_model::class],
                        ],
                        'label' => 'Email',
                    ],
                    [
                        'priority' => '2',
                        'column' => [
                            'key' => 'customer_address',
                            'contexts' => [\Admin\Models\Customers_model::class],
                        ],
                        'label' => 'Address',
                    ],
                ],
            ],
        ];

        foreach ($reports as $attributes) {
            foreach ($attributes as $key => $value) {
                if (!is_string($value))
                    $attributes[$key] = json_encode($value);
            }

            DB::table('thoughtco_reportbuilder')->insert($attributes);
        }
    }
}
