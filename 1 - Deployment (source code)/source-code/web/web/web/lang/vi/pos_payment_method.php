<?php

use App\Enums\PosPaymentMethod;

return [
    PosPaymentMethod::CARD => 'Thẻ',
    PosPaymentMethod::CASH => 'Tiền mặt',
    PosPaymentMethod::OTHER => 'Khác',
    PosPaymentMethod::MOBILE_BANKING => 'Ngân hàng di động'
];
