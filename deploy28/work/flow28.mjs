import {chromium} from 'playwright-core';
const B='http://localhost:8088';const b=await chromium.launch({executablePath:'/usr/bin/google-chrome'});
const c=await b.newContext({viewport:{width:1280,height:900}});
await c.addCookies([{name:'onyx_age_verified',value:'1',url:B}]);const p=await c.newPage();const log=(...a)=>console.log(...a);
await p.goto(B+'/peptides-2/',{waitUntil:'networkidle'});
const href=await p.locator('.s2-card:not(:has(.s2-b-soon)):not(:has(.s2-b-oos)) a.s2-title').first().getAttribute('href');log('product link',href);
await p.goto(href,{waitUntil:'networkidle'});log('product body v2:',await p.evaluate(()=>document.body.classList.contains('onyx-v2')));
const btn=p.locator('form.onyx-cart-form button[type=submit]:not([name])').first();
const sel=p.locator('form.onyx-cart-form select');for(let i=0;i<await sel.count();i++){const v=await sel.nth(i).locator('option').nth(1).getAttribute('value');await sel.nth(i).selectOption(v)}
await btn.click();await p.waitForLoadState('networkidle');await p.waitForTimeout(800);log('after add url',p.url());
const cartHref=await p.evaluate(()=>[...document.querySelectorAll('a')].map(a=>a.href).filter(h=>/cart/.test(h)));log('cart links on product',[...new Set(cartHref)].slice(0,4));
await p.goto(B+'/cart-2/',{waitUntil:'networkidle'});log('cart-2 items',await p.locator('.cart_item, [data-cart-item], tr.woocommerce-cart-form__cart-item, .onyx-cart-item').count());
const co=await p.evaluate(()=>[...document.querySelectorAll('a')].map(a=>a.href).filter(h=>/checkout/.test(h)));log('checkout links on cart-2',[...new Set(co)]);
await p.goto(B+'/cart/',{waitUntil:'networkidle'});const co1=await p.evaluate(()=>[...document.querySelectorAll('a')].map(a=>a.href).filter(h=>/checkout/.test(h)));log('checkout links on ORIGINAL cart',[...new Set(co1)]);
await c.storageState({path:'/workspace/onyx/tmp/state-cart.json'});
await p.goto(B+'/checkout-2/',{waitUntil:'networkidle'});log('checkout-2 form',await p.locator('form.checkout').count(),'hidden v2',await p.locator('input[name=onyx_v2]').count());
await p.evaluate(()=>{const v={email:'deploy28-test@example.com',first_name:'Test',last_name:'Local',address_1:'1 Test St',city:'London',postcode:'SW1A 1AA',phone:'7000000000'};
for(const el of document.querySelectorAll('form.checkout input')){if(el.type==='hidden'||el.offsetParent===null)continue;for(const[k,x] of Object.entries(v)){if(el.name.endsWith(k)&&!el.value){el.value=x;el.dispatchEvent(new Event('input',{bubbles:true}));el.dispatchEvent(new Event('change',{bubbles:true}))}}
if(el.type==='checkbox'&&!el.checked&&/terms|confirm|age/i.test(el.name+el.id)){el.click()}}});
{const ph=p.locator('form.checkout input[type=tel]').first();await ph.fill('');await ph.pressSequentially('7911123456');}
console.log('unchecked boxes',await p.locator('form.checkout input[type=checkbox]:not(:checked)').evaluateAll(e=>e.map(x=>x.name)));
await p.waitForTimeout(2500);
await p.locator('#place_order').click();await p.waitForURL(/order-received|order-pay|pay/,{timeout:30000}).catch(()=>{});await p.waitForLoadState('networkidle');
log('after order url',p.url(),'body v2',await p.evaluate(()=>document.body.className.match(/onyx-v2[^ ]*/g)));
await p.screenshot({path:'/workspace/onyx/tmp/after-order.png'});log('errors',await p.locator('.woocommerce-error, .woocommerce-NoticeGroup').allInnerTexts());
await c.storageState({path:'/workspace/onyx/tmp/state-order.json'});
await b.close();
