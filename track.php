<?php require 'includes/functions.php';$o=null;$code=strtoupper(trim($_GET['code']??''));
if($code){$s=db()->prepare('SELECT * FROM orders WHERE order_code=?');$s->execute([$code]);$o=$s->fetch();
if($o){$s=db()->prepare('SELECT name,qty FROM order_items WHERE order_id=?');$s->execute([$o['id']]);$it=$s->fetchAll();}}?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Track order</title>
<link rel="stylesheet" href="assets/style.css"><?php if($o)echo '<meta http-equiv="refresh" content="15">'?></head><body><header>Track your order</header><main>
<form><input name="code" placeholder="Order ID e.g. ENC-20261018-A1B2" value="<?=e($code)?>"><button>Check</button></form>
<?php if($code&&!$o)echo '<p class="err">Order not found.</p>';if($o):?>
<div class="card" style="display:block"><p class="big"><?=e($o['order_code'])?></p><p>Status: <b><?=e($o['status'])?></b><br>Payment: <?=e($o['payment_status'])?> · Total ₹<?=e($o['total'])?></p>
<?php foreach($it as $i)echo e($i['name']).' × '.(int)$i['qty'].'<br>';?></div><p>Save your Order ID. Show it at the counter.</p><?php endif;?></main></body></html>
