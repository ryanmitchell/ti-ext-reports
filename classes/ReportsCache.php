<?php

namespace Thoughtco\Reports\Classes;

use Admin\Facades\AdminLocation;
use Admin\Models\Categories_model;
use Admin\Models\Customers_model;
use Admin\Models\Menus_model;
use Admin\Models\Orders_model;
use Admin\Models\Payments_model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportsCache
{
    protected static $cache = [];

    protected static $cancelledOrders;

    protected static $pickupOrders;

    protected static $deliveryOrders;

    protected static $customerSalesWithZero;

    protected static $menuSalesWithZero;

    protected static Collection $orderItems;

    protected static int $totalItems;

    protected static $orders;

    protected static $ordersByCategory;

    protected static $ordersByPaymentMethod;

    public static function getValue($cardCode, $start, $end)
    {
        if (is_null(self::$orders)) {
            static::getQueryForOrders($start, $end);
        }

        switch ($cardCode) {
            case 'total_items':
                return self::$totalItems;
            case 'order_items':
                return self::$orderItems;
            case 'pickup_orders_value':
                return currency_format(self::$pickupOrders->sum('order_total'));
            case 'pickup_orders_count':
                return self::$pickupOrders->count();
            case 'delivery_orders_value':
                return currency_format(self::$deliveryOrders->sum('order_total'));
            case 'delivery_orders_count':
                return self::$deliveryOrders->count();
            case 'cancelled_orders_value':
                return currency_format(self::$cancelledOrders->sum('order_total'));
            case 'cancelled_orders_count':
                return self::$cancelledOrders->count();
            case 'orders_by_day_data':
                return self::getOrdersByDayData();
            case 'orders_by_hour_data':
                return self::getOrdersByHourData();
            case 'orders_by_category_data':
                return self::getOrdersByCategoryData();
            case 'orders_by_payment_method_data':
                return self::getOrdersByPaymentMethodData();
            case 'top_customers':
                return self::$customerSalesWithZero->map(function ($c) {
                    $c->value = currency_format($c->value);

                    return $c;
                });
            case 'bottom_customers':
                return self::$customerSalesWithZero;
            case 'top_items':
            case 'bottom_items':
                return self::$menuSalesWithZero;
            default:
                return 0;
        }
    }

    public static function getQueryForOrders($start, $end)
    {
        $statusesToQuery = setting('completed_order_status');
        $cancelledStatus = setting('canceled_order_status');
        $statusesToQuery[] = $cancelledStatus;

        $locationModel = AdminLocation::current();

        // get order ids for the time period
        $query = Orders_model::query()
            ->whereBetween('order_date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->whereIn('status_id', $statusesToQuery);

        if ($locationModel)
            $query->where('location_id', $locationModel->getKey());

        self::$orders = $orders = $query->get();

        // cancelled order stats
        self::$cancelledOrders = $orders->filter(function ($order) {
            return $order->status_id == setting('canceled_order_status');
        });

        // pickup order stats
        self::$pickupOrders = $orders->filter(function ($order) use ($cancelledStatus) {
            return $order->order_type == 'collection' && $order->status_id != $cancelledStatus;
        });

        // delivery order stats
        self::$deliveryOrders = $orders->filter(function ($order) use ($cancelledStatus) {
            return $order->order_type == 'delivery' && $order->status_id != $cancelledStatus;
        });

        // orders by customer
        $ordersByCustomer = $orders->groupBy('email');

        // build customer items to include zero sales
        self::$customerSalesWithZero = Customers_model::all()
            ->map(function ($customer) use ($ordersByCustomer) {
                if ($el = $ordersByCustomer->firstWhere('customer_id', $customer->customer_id))
                    return $el;

                return (object)[
                    'customer_id' => $customer->customer_id,
                    'name' => $customer->first_name.' '.$customer->last_name,
                    'email' => 0,
                    'value' => 0,
                ];
            })
            ->sortBy('name')
            ->sortByDesc('value')
            ->slice(0, 10);

        // get ids
        $orderIds = $orders->pluck('order_id');

        // get order items
        $orderItems = DB::table('order_menus')
            ->whereIn('order_id', $orderIds)
            ->get();

        self::$totalItems = $orderItems->count();

        self::$orderItems = $orderItems->groupBy('menu_id')
            ->map(function ($orderItems, $key) {
                if (!$first = $orderItems->first())
                    return false;

                return (object)[
                    'value' => $orderItems->sum('subtotal'),
                    'subtotal' => $orderItems->sum('subtotal'),
                    'quantity' => $orderItems->sum('quantity'),
                    'menu_id' => $key,
                    'name' => $first->name,
                ];
            })
            ->filter();

        // get a list of menus vs categories
        $menusCategories = Menus_model::get()
            ->map(function ($menu) {
                return (object)[
                    'menu_id' => $menu->menu_id,
                    'name' => $menu->menu_name,
                    'categories' => $menu->categories->pluck('category_id')->toArray(),
                ];
            })
            ->keyBy('menu_id');

        // build menu items to include zero sales
        self::$menuSalesWithZero = $menusCategories->map(function ($menu) {
            if ($el = self::$orderItems->firstWhere('menu_id', $menu->menu_id))
                return $el;

            return (object)[
                'subtotal' => 0,
                'value' => 0,
                'quantity' => 0,
                'menu_id' => $menu->menu_id,
                'name' => $menu->name,
            ];
        })->sortBy('name')->sortByDesc('quantity')->slice(0, 10);

        // get a list of categories
        $categories = Categories_model::where('status', 1)
            ->get()
            ->sortBy('name')
            ->pluck('name', 'category_id');

        $categoryCount = $categories->count();
        $categoryIndex = 0;

        // get sales by category
        self::$ordersByCategory = $categories
            ->map(function ($category, $categoryKey) use ($orderItems, $menusCategories, $categoryCount, &$categoryIndex) {
                $ordersInThisCategory = $orderItems
                    ->filter(function ($orderItem) use ($categoryKey, $menusCategories) {
                        if ($categoryList = $menusCategories->get($orderItem->menu_id)) {
                            if (in_array($categoryKey, $categoryList->categories))
                                return true;
                        }

                        return false;
                    });

                return (object)[
                    'name' => $category,
                    'value' => $ordersInThisCategory->sum('subtotal'),
                    'count' => $ordersInThisCategory->count(),
                    'color' => 'hsl(134, 61.4%, '.(80 - floor(40.6 * $categoryIndex++ / $categoryCount)).'%)',
                ];
            });

        // get payment methods for this location
        $paymentMethods = ($locationModel ? $locationModel->listAvailablePayments() : Payments_model::all())
            ->map(function ($method) {
                return (object)[
                    'name' => $method->name,
                    'code' => $method->code,
                ];
            });

        $paymentMethodCount = $paymentMethods->count();
        $paymentMethodIndex = 0;

        self::$ordersByPaymentMethod = $paymentMethods
            ->map(function ($method) use ($orders, $paymentMethodCount, &$paymentMethodIndex) {
                $ordersUsingThisMethod = $orders
                    ->filter(function ($orderItem) use ($method) {
                        if ($orderItem->payment == $method->code)
                            return true;

                        return false;
                    });

                $method->value = $ordersUsingThisMethod->sum('order_total');
                $method->count = $ordersUsingThisMethod->count();
                $method->color = 'hsl(354, 70.5%, '.(80 - floor(53.5 * $paymentMethodIndex++ / $paymentMethodCount)).'%)';

                return $method;
            });
    }

    protected static function getOrdersByDayData(): array
    {
        $ordersByDay = collect(range(0, 6))->map(function ($dayOfWeek) {
            $ordersOnDay = self::$orders->filter(function ($order) use ($dayOfWeek) {
                return $order->order_date->dayOfWeek == $dayOfWeek;
            });

            return (object)[
                'day' => $dayOfWeek,
                'value' => $ordersOnDay->sum('order_total'),
                'count' => $ordersOnDay->count(),
                'color' => 'hsl(50, 98.3%, '.(80 - floor(53.5 * $dayOfWeek / 7)).'%)',
            ];
        });

        return [
            'datasets' => [
                [
                    'data' => $ordersByDay->map(function ($v) {
                        return $v->value;
                    }),
                    'backgroundColor' => $ordersByDay->map(function ($v) {
                        return $v->color;
                    }),
                ],
            ],
            'labels' => $ordersByDay->map(function ($v) {
                return date('l', strtotime('Sunday +'.$v->day.' days')).' ('.currency_format($v->value).' / '.$v->count.')';
            }),
        ];
    }

    protected static function getOrdersByHourData(): array
    {
        $ordersByHour = collect(range(0, 23))
            ->map(function ($hourOfDay) {
                $ordersInHour = self::$orders->filter(function ($order) use ($hourOfDay) {
                    $time = explode(':', $order->order_time);

                    return (int)array_shift($time) == $hourOfDay;
                });

                return (object)[
                    'hour' => $hourOfDay,
                    'value' => round($ordersInHour->sum('order_total'), 2),
                    'count' => $ordersInHour->count(),
                    'color' => 'hsl(211, 100%, '.(100 - floor(50 * $hourOfDay / 24)).'%)',
                ];
            });

        return [
            'datasets' => [
                [
                    'data' => $ordersByHour->map(function ($v) {
                        return $v->value;
                    }),
                    'backgroundColor' => $ordersByHour->map(function ($v) {
                        return $v->color;
                    }),
                ],
            ],
            'labels' => $ordersByHour->map(function ($v) {
                return str_pad($v->hour, 2, '0', STR_PAD_LEFT).':00-'.str_pad($v->hour + 1, 2, '0', STR_PAD_LEFT).':00 ('.currency_format($v->value).' / '.$v->count.')';
            }),
        ];
    }

    protected static function getOrdersByCategoryData(): array
    {
        return [
            'datasets' => [
                [
                    'data' => self::$ordersByCategory->map(function ($v) {
                        return $v->value;
                    })->values(),
                    'backgroundColor' => self::$ordersByCategory->map(function ($v) {
                        return $v->color;
                    })->values(),
                ],
            ],
            'labels' => self::$ordersByCategory->map(function ($v) {
                return $v->name.' ('.currency_format($v->value).' / '.$v->count.')';
            })->values(),
        ];
    }

    protected static function getOrdersByPaymentMethodData(): array
    {
        return [
            'datasets' => [
                [
                    'data' => self::$ordersByPaymentMethod->map(function ($v) {
                        return $v->value;
                    })->values(),
                    'backgroundColor' => self::$ordersByPaymentMethod->map(function ($v) {
                        return $v->color;
                    })->values(),
                ],
            ],
            'labels' => self::$ordersByPaymentMethod->map(function ($v) {
                return $v->name.' ('.currency_format($v->value).' / '.$v->count.')';
            })->values(),
        ];
    }

}

?>
