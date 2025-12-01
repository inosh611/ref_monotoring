<?php

use Modules\Orders\Entities\Order;

if (! function_exists('findOrderId')) {
    function findOrderId($order_number) {
        $order = Order::where('order_number', $order_number)->first();
        return $order ? $order->id : null;
    }
}

if (!function_exists('findUserId')){
    function findUserId($reg_number) {
        $user = \App\Models\User::where('reg_number', $reg_number)->first();
        return $user ? $user->id : null;
    }
}

?>