<?php

namespace App\Services;

use App\Models\Tax;
use App\Models\Item;
use App\Enums\TaxType;
use App\Models\OrderItem;
use App\Models\OrderCoupon;
use App\Models\OrderAddress;
use App\Models\Address;
use App\Libraries\AppLibrary;
use Carbon\Carbon;
use Smartisan\Settings\Facades\Settings;

trait HandlesOrderItems
{
    protected function processOrderItems($order, $requestItems, $orderModel = null)
    {
        $i = 0;
        $totalTax = 0;
        $itemsArray = [];
        $items = Item::get()->pluck('tax_id', 'id');
        $taxes = AppLibrary::pluck(Tax::get(), 'obj', 'id');

        if (!blank($requestItems)) {
            foreach ($requestItems as $item) {
                $taxId = isset($items[$item->item_id]) ? $items[$item->item_id] : 0;
                $taxName = isset($taxes[$taxId]) ? $taxes[$taxId]->name : null;
                $taxRate = isset($taxes[$taxId]) ? $taxes[$taxId]->tax_rate : 0;
                $taxType = isset($taxes[$taxId]) ? $taxes[$taxId]->type : TaxType::FIXED;
                $taxPrice = $taxType === TaxType::FIXED ? $taxRate : ($item->total_price * $taxRate) / 100;
                $itemsArray[$i] = [
                    'order_id'             => $order->id,
                    'branch_id'            => $item->branch_id,
                    'item_id'              => $item->item_id,
                    'quantity'             => $item->quantity,
                    'discount'             => (float)$item->discount,
                    'tax_name'             => $taxName,
                    'tax_rate'             => $taxRate,
                    'tax_type'             => $taxType,
                    'tax_amount'           => $taxPrice,
                    'price'                => $item->item_price,
                    'item_variations'      => json_encode($item->item_variations),
                    'item_extras'          => json_encode($item->item_extras),
                    'instruction'          => $item->instruction,
                    'item_variation_total' => $item->item_variation_total,
                    'item_extra_total'     => $item->item_extra_total,
                    'total_price'          => $item->total_price,
                ];
                $totalTax = $totalTax + $taxPrice;
                $i++;
            }
        }

        if (!blank($itemsArray)) {
            OrderItem::insert($itemsArray);
        }

        return ['totalTax' => $totalTax];
    }

    protected function setOrderSerial($order)
    {
        $order->order_serial_no = date('dmy') . $order->id;
    }

    protected function saveOrderAddress($order, $addressId, $userId)
    {
        if ($addressId) {
            $address = Address::find($addressId);
            if ($address) {
                OrderAddress::create([
                    'order_id'  => $order->id,
                    'user_id'   => $userId,
                    'label'     => $address->label,
                    'address'   => $address->address,
                    'apartment' => $address->apartment,
                    'latitude'  => $address->latitude,
                    'longitude' => $address->longitude
                ]);
            }
        }
    }

    protected function saveOrderCoupon($order, $couponId, $userId, $discount)
    {
        if ($couponId > 0) {
            OrderCoupon::create([
                'order_id'  => $order->id,
                'coupon_id' => $couponId,
                'user_id'   => $userId,
                'discount'  => $discount
            ]);
        }
    }

    protected function calculateDeliveryTime($order)
    {
        $currentTime = Carbon::now();
        $endTime = $currentTime->copy()->addMinutes(Settings::group('order_setup')->get('order_setup_schedule_order_slot_duration'));
        $start = $currentTime->format('H:i');
        $end = $endTime->format('H:i');
        $order->delivery_time = "$start - $end";
    }
}
