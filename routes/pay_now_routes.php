<?php
$pay_now_routes = [
    '/pay-now-token-pay'=>'Pay_now_ctrl@pay@name.payNow',
    '/pay-now-token-test'=>'Pay_now_ctrl@test@name.payNowTest',
    '/pay-now-token-verify-v7'=>'Pay_now_ctrl@verify_payment_v7@name.payNowVerify',
    '/pay-now-token-verify'=>'Pay_now_ctrl@verify_test@name.payNowVerifyTest',
    '/pay-now'=>'Pay2play_ctrl@pay@name.pay',
    '/pay-now-status'=>'Pay2play_ctrl@check_status@name.payStatusAjax',
    '/pay-now-status-admin'=>'Pay2play_ctrl@check_status_admin@name.payStatusAjaxAdmin',
];