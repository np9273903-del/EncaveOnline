<?php
require '../includes/functions.php';header('Content-Type: application/json');
$in=json_decode(file_get_contents('php://input'),true);
$name=trim($in['name']??'');$phone=trim($in['phone']??'');$cart=$in['cart']??[];
if(!$name||!preg_match('/^[6-9]\d{9}$/',$phone)||!$cart){http_response_code(400);exit(json_encode(['error'=>'Enter your name, a valid 10-digit phone and at least one item']));}
$pdo=db();
try{
$pdo->beginTransaction();$total=0;$lines=[];
foreach($cart as $id=>$q){$q=(int)$q;if($q<1||$q>20)continue;
$s=$pdo->prepare('SELECT * FROM menu_items WHERE id=? AND available=1');$s->execute([(int)$id]);$m=$s->fetch();
if(!$m)throw new Exception('An item is no longer available');
$total+=$m['price']*$q;$lines[]=[$m,$q];}
if(!$lines)throw new Exception('Cart is empty');
$code='ENC-'.date('Ymd').'-'.strtoupper(bin2hex(random_bytes(2)));
$pdo->prepare('INSERT INTO orders(order_code,customer_name,phone,total) VALUES(?,?,?,?)')->execute([$code,$name,$phone,$total]);
$oid=$pdo->lastInsertId();
$i=$pdo->prepare('INSERT INTO order_items(order_id,menu_item_id,name,price,qty) VALUES(?,?,?,?,?)');
foreach($lines as [$m,$q])$i->execute([$oid,$m['id'],$m['name'],$m['price'],$q]);
$r=rzp(['amount'=>(int)round($total*100),'currency'=>'INR','receipt'=>$code]);
if(empty($r['id']))throw new Exception('Payment gateway error, try again');
$pdo->prepare('UPDATE orders SET razorpay_order_id=? WHERE id=?')->execute([$r['id'],$oid]);
$pdo->commit();
echo json_encode(['key'=>RZP_KEY_ID,'rzp_order'=>$r['id'],'amount'=>$r['amount'],'code'=>$code,'name'=>$name,'phone'=>$phone]);
}catch(Exception $x){if($pdo->inTransaction())$pdo->rollBack();http_response_code(400);echo json_encode(['error'=>$x->getMessage()]);}
