<?php require '../includes/functions.php';$pdo=db();$err='';
$none=!$pdo->query('SELECT COUNT(*) c FROM admins')->fetch()['c'];
if($_SERVER['REQUEST_METHOD']==='POST'){check_csrf();$u=trim($_POST['u']);$p=$_POST['p'];
if($none){if(strlen($p)<8)$err='Password must be 8+ characters';else{$pdo->prepare('INSERT INTO admins(username,password_hash) VALUES(?,?)')->execute([$u,password_hash($p,PASSWORD_DEFAULT)]);$ok=1;}}
else{$s=$pdo->prepare('SELECT * FROM admins WHERE username=?');$s->execute([$u]);$a=$s->fetch();if($a&&password_verify($p,$a['password_hash']))$ok=1;else $err='Wrong username or password';}
if(!empty($ok)){session_regenerate_id(true);$_SESSION['admin']=1;header('Location: index.php');exit;}}?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/style.css"></head><body><header>Admin <?=$none?'· create first admin':'login'?></header><main>
<form method="post"><input type="hidden" name="csrf" value="<?=csrf()?>"><input name="u" placeholder="Username" required><input name="p" type="password" placeholder="Password" required><button>Continue</button><p class="err"><?=e($err)?></p></form></main></body></html>
