<?php
require '../includes/functions.php';header('Content-Type: application/json');
$in=json_decode(file_get_contents('php://input'),true);
$o=$in['razorpay_order_id']??'';$p=$in['razorpay_payment_id']??'';$s=$in['razorpay_signature']??'';
if(!hash_equals(hash_hmac('sha256',$o.'|'.$p,RZP_KEY_SECRET),$s)){http_response_code(400);exit(json_encode(['error'=>'Payment could not be verified']));}
db()->prepare("UPDATE orders SET payment_status='Paid',status='Confirmed',razorpay_payment_id=? WHERE razorpay_order_id=? AND payment_status='Pending'")->execute([$p,$o]);
echo json_encode(['ok'=>1]);
