<?php require '../includes/functions.php';require_admin();$pdo=db();
if($_SERVER['REQUEST_METHOD']==='POST'){check_csrf();$a=$_POST['a'];
if($a==='add')$pdo->prepare('INSERT INTO menu_items(name,category,description,price) VALUES(?,?,?,?)')->execute([$_POST['name'],$_POST['category'],$_POST['description'],(float)$_POST['price']]);
if($a==='save')$pdo->prepare('UPDATE menu_items SET price=?,available=? WHERE id=?')->execute([(float)$_POST['price'],isset($_POST['available'])?1:0,(int)$_POST['id']]);
if($a==='del')$pdo->prepare('DELETE FROM menu_items WHERE id=?')->execute([(int)$_POST['id']]);
header('Location: menu.php');exit;}
$items=$pdo->query('SELECT * FROM menu_items ORDER BY category,name')->fetchAll();?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/style.css"></head><body><header>Menu · <a href="index.php" style="color:#fff">Orders</a></header><main>
<form method="post"><input type="hidden" name="csrf" value="<?=csrf()?>"><input type="hidden" name="a" value="add"><input name="name" placeholder="Item name" required><input name="category" placeholder="Category" required><input name="description" placeholder="Description"><input name="price" type="number" step="0.01" min="1" placeholder="Price ₹" required><button>Add item</button></form>
<?php foreach($items as $m):?><form method="post" class="card"><input type="hidden" name="csrf" value="<?=csrf()?>"><input type="hidden" name="id" value="<?=$m['id']?>"><b><?=e($m['name'])?></b>
<input name="price" type="number" step="0.01" value="<?=e($m['price'])?>" style="width:80px"><label><input type="checkbox" name="available" style="width:auto" <?=$m['available']?'checked':''?>>Avail</label>
<button name="a" value="save">Save</button><button name="a" value="del" onclick="return confirm('Delete?')">✕</button></form><?php endforeach;?></main></body></html>
