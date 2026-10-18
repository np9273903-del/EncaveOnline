<?php require 'includes/functions.php';
$items=db()->query('SELECT id,name,category,description,price FROM menu_items WHERE available=1 ORDER BY category,name')->fetchAll();?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Encave Canteen</title><link rel="stylesheet" href="assets/style.css"></head><body>
<header>Encave Canteen · <a href="track.php" style="color:#fff;font-size:14px">Track order</a></header><main>
<div id="menu"></div><h3>Your order</h3><div id="cart">Cart is empty</div>
<p>Pickup only. Collect from the counter when the status shows Ready.</p>
<input id="name" placeholder="Your name"><input id="phone" placeholder="10-digit mobile number" inputmode="numeric" maxlength="10">
<button id="pay" onclick="pay()" style="width:100%;margin-top:8px">Pay &amp; place order</button><p id="msg" class="err"></p></main>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
const MENU=<?=json_encode($items,JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT)?>,cart={};
const esc=s=>String(s).replace(/[&<>"']/g,c=>'&#'+c.charCodeAt(0)+';');
let last='';document.getElementById('menu').innerHTML=MENU.map(m=>(m.category!=last?'<h3>'+esc(last=m.category)+'</h3>':'')+
`<div class="card"><div><b>${esc(m.name)}</b><br><small>${esc(m.description)}</small><br>₹${m.price}</div><button onclick="chg(${m.id},1)">Add</button></div>`).join('');
function chg(id,d){cart[id]=Math.max(0,(cart[id]||0)+d);if(!cart[id])delete cart[id];draw();}
function draw(){let t=0,h='';for(const id in cart){const m=MENU.find(x=>x.id==id);t+=m.price*cart[id];
h+=`<div class="card"><span>${esc(m.name)} ₹${m.price}</span><span><button onclick="chg(${id},-1)">−</button> ${cart[id]} <button onclick="chg(${id},1)">+</button></span></div>`;}
document.getElementById('cart').innerHTML=h?h+'<p class="big">Total: ₹'+t.toFixed(2)+'</p>':'Cart is empty';}
async function pay(){const msg=document.getElementById('msg');msg.textContent='';
const r=await fetch('api/create_order.php',{method:'POST',body:JSON.stringify({name:name.value,phone:phone.value,cart})});const d=await r.json();
if(d.error){msg.textContent=d.error;return;}
new Razorpay({key:d.key,order_id:d.rzp_order,amount:d.amount,currency:'INR',name:'Encave Canteen',prefill:{name:d.name,contact:d.phone},
handler:async res=>{const v=await fetch('api/verify_payment.php',{method:'POST',body:JSON.stringify(res)});
location.href=(await v.json()).ok?'track.php?code='+d.code:'track.php?code='+d.code;}}).open();}
</script></body></html>
