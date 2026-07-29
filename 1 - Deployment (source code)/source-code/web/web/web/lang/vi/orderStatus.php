<?php

use App\Enums\OrderStatus;

return [
    OrderStatus::PENDING          => 'Đang chờ',
    OrderStatus::ACCEPT           => 'Chấp nhận',
    OrderStatus::PREPARING        => 'Đang chuẩn bị',
    OrderStatus::PREPARED         => 'Đã chuẩn bị',
    OrderStatus::OUT_FOR_DELIVERY => 'Đang giao hàng',
    OrderStatus::DELIVERED        => 'Đã giao',
    OrderStatus::CANCELED         => 'Đã hủy',
    OrderStatus::REJECTED         => 'Đã từ chối',
    OrderStatus::RETURNED         => 'Đã trả lại',


];
