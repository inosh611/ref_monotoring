<?php

use Modules\Orders\Entities\Order;

if (! function_exists('findOrderId')) {
    function findOrderId($order_number) {
        $order = Order::where('order_number', $order_number)->first();
        return $order ? $order->id : null;
    }
}


?>