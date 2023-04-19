<?php

namespace app\components\payment;

use app\components\payment\Payment;

/**
 * This is the class for execute bills payments.
 *
 * @property int $type money or point
 * @property int $direction default is 1 (income payment)
 */
class BillPayment extends Payment
{
    public $appointment = 'Пополнение кошелька по счёту';

    public $direction = self::INCOME;

    public $type = self::TYPE_MONEY;
}