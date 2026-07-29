<?php

namespace App\Services;


use App\Events\SendOrderGotMail;
use App\Events\SendOrderGotSms;
use Exception;
use App\Models\Tax;
use App\Models\Item;
use App\Enums\TaxType;
use App\Models\Address;
use App\Enums\OrderType;
use App\Models\OrderItem;
use App\Enums\OrderStatus;
use App\Models\OrderCoupon;
use App\Events\SendOrderSms;
use App\Models\OrderAddress;
use App\Events\SendOrderMail;
use App\Events\SendOrderPush;
use App\Libraries\AppLibrary;
use App\Models\FrontendOrder;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\OrderRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PaginateRequest;
use Smartisan\Settings\Facades\Settings;
use App\Http\Requests\OrderStatusRequest;
use App\Events\SendOrderGotPush;

class FrontendOrderService
{
    use HandlesOrderItems;

    public object $frontendOrder;
    protected array $frontendOrderFilter = [
        'order_serial_no',
        'user_id',
        'branch_id',
        'total',
        'order_type',
        'order_datetime',
        'payment_method',
        'payment_status',
        'status',
        'delivery_boy_id'
    ];

    protected array $exceptFilter = [
        'excepts'
    ];

    /**
     * @throws Exception
     */
    public function myOrder(PaginateRequest $request)
    {
        try {
            $requests = $request->all();
            $method = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $frontendOrderColumn = $request->get('order_column') ?? 'id';
            $frontendOrderType = $request->get('order_by') ?? 'desc';

            return FrontendOrder::with('transaction', 'orderItems', 'branch', 'user')->where('order_type', "!=", OrderType::POS)->where(function ($query) use ($requests) {
                $query->where('user_id', auth()->user()->id);
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->frontendOrderFilter)) {
                        if ($key === "status") {
                            $query->where($key, (int)$request);
                        } else {
                            $query->where($key, 'like', '%' . $request . '%');
                        }
                    }
                    if (in_array($key, $this->exceptFilter)) {
                        $explodes = explode('|', $request);
                        if (is_array($explodes)) {
                            foreach ($explodes as $explode) {
                                $query->where('status', '!=', $explode);
                            }
                        }
                    }
                }
            })->orderBy($frontendOrderColumn, $frontendOrderType)->$method(
                $methodValue
            );
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function myOrderStore(OrderRequest $request): object
    {
        try {
            DB::transaction(function () use ($request) {
                $this->frontendOrder = FrontendOrder::create(
                    $request->validated() + [
                        'user_id'          => Auth::user()->id,
                        'status'           => OrderStatus::PENDING,
                        'order_datetime'   => date('Y-m-d H:i:s'),
                        'preparation_time' => Settings::group('order_setup')->get('order_setup_food_preparation_time')
                    ]
                );

                $processed = $this->processOrderItems($this->frontendOrder, json_decode($request->items));

                $this->setOrderSerial($this->frontendOrder);
                $this->frontendOrder->total_tax = $processed['totalTax'];
                $this->frontendOrder->save();

                $this->saveOrderAddress($this->frontendOrder, $request->address_id, Auth::user()->id);

                $this->saveOrderCoupon($this->frontendOrder, $request->coupon_id, Auth::user()->id, $request->discount);

                SendOrderMail::dispatch(['order_id' => $this->frontendOrder->id, 'status' => OrderStatus::PENDING]);
                SendOrderSms::dispatch(['order_id' => $this->frontendOrder->id, 'status' => OrderStatus::PENDING]);
                SendOrderPush::dispatch(['order_id' => $this->frontendOrder->id, 'status' => OrderStatus::PENDING]);

                SendOrderGotMail::dispatch(['order_id' => $this->frontendOrder->id]);
                SendOrderGotSms::dispatch(['order_id' => $this->frontendOrder->id]);
                SendOrderGotPush::dispatch(['order_id' => $this->frontendOrder->id]);
            });
            return $this->frontendOrder;
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(FrontendOrder $frontendOrder): FrontendOrder|array
    {
        try {
            if ($frontendOrder->user_id == Auth::user()->id) {
                return $frontendOrder;
            }
            return [];
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function changeStatus(FrontendOrder $frontendOrder, OrderStatusRequest $request): FrontendOrder
    {
        try {
            if ($frontendOrder->user_id == Auth::user()->id) {
                if ($request->status == OrderStatus::CANCELED) {
                    if ($frontendOrder->status >= OrderStatus::ACCEPT) {
                        throw new Exception(trans('all.message.order_accept'), 422);
                    } else {
                        if ($frontendOrder->transaction) {
                            app(PaymentService::class)->cashBack(
                                $frontendOrder,
                                'credit',
                                rand(111111111111111, 99999999999999)
                            );
                        }
                        SendOrderMail::dispatch(['order_id' => $frontendOrder->id, 'status' => $request->status]);
                        SendOrderSms::dispatch(['order_id' => $frontendOrder->id, 'status' => $request->status]);
                        SendOrderPush::dispatch(['order_id' => $frontendOrder->id, 'status' => $request->status]);
                        $frontendOrder->status = $request->status;
                        $frontendOrder->save();
                    }
                }
            }
            return $frontendOrder;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }
}
