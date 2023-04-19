<?php

namespace app\components\interfaces;

interface PaymentInterface 
{
    const TYPE_MONEY = 1;
    const TYPE_POINTS = 2;

    const PAYMENT_SUCCESS = 1;
    const PAYMENT_FAILURE = 2;

    const BILL_SUCCESS = 1;
    const BILL_REJECT = 2;
    const BILL_AWAIT = 3;

    const INCOME = 1;
    const OUTCOME = 2;

    const APPOINTMENT_INVOICE = 1;
    const APPOINTMENT_PAY = 2;
}