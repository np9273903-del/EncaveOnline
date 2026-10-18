<?php require '../includes/functions.php';require_admin();$pdo=db();
$st=['Confirmed','Preparing','Ready','Completed','Cancelled'];
if($_SERVER['REQUEST_METHOD']==='POST'){check_csrf();if(in_array($_POST['status'],$st))$pdo->prepare('UPDATE orders SET status=? WHERE id=?')->execute([$_POST['status'],(int)$_POST['id']]);header('Location: index.php');exit;}
$orders=$pdo->query("SELECT o.*,(SELECT GROUP_CONCAT(CONCAT(name,' × ',qty) SEPARATOR ', ') FROM order_items WHERE order_id=o.id) items FROM orders o WHERE payment_status='Paid' ORDER BY id DESC LIMIT 100")->fetchAll();?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta http-equiv="refresh" content="30"><link rel="stylesheet" href="../assets/style.css"></head><body>
<header>Orders · <a href="menu.php" style="color:#fff">Menu</a> · <a href="logout.php" style="color:#fff">Logout</a></header><main style="max-width:900px">
<div id="qr"></div><small><?=e(BASE_URL)?> (right-click QR to save and print)</small>
<table><tr><th>ID</th><th>Customer</th><th>Items</th><th>₹</th><th>Status</th></tr>
<?php foreach($orders as $o):?><tr><td><?=e($o['order_code'])?></td><td><?=e($o['customer_name'])?><br><?=e($o['phone'])?></td><td><?=e($o['items'])?></td><td><?=e($o['total'])?></td>
<td><form method="post"><input type="hidden" name="csrf" value="<?=csrf()?>"><input type="hidden" name="id" value="<?=$o['id']?>"><select name="status" onchange="this.form.submit()"><?php foreach($st as $s)echo '<option'.($s==$o['status']?' selected':'').'>'.$s.'</option>';?></select></form></td></tr><?php endforeach;?></table></main>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script><script>new QRCode(document.getElementById('qr'),{text:<?=json_encode(BASE_URL)?>,width:160,height:160});</script></body></html>
