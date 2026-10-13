import { chromium } from 'playwright-core';
const P=[['home','/','/'],['shop','/peptides','/peptides/'],['category','/peptides/weight-loss','/peptides/weight-loss/'],['product-rt','/product/rt','/product/rt/'],['product-glow','/product/glow','/product/glow/'],['product-cjcipam','/product/cjcipam','/product/cjcipam/'],['plans','/peptide-plans','/peptide-plans/'],['plan','/peptide-plans/anti-aging-program','/peptide-plans/anti-aging-program/'],['faq','/faq','/faq/'],['contact','/contact','/contact/'],['wap','/what-are-peptides','/what-are-peptides/'],['wholesale','/wholesale-application','/wholesale-application/'],['doctor','/independent-doctor-consultation','/independent-doctor-consultation/'],['payment','/payment-delivery','/payment-delivery/'],['returns','/product-returns','/product-returns/'],['checkcode','/check-code','/check-code/'],['calc','/analytical-reference-calculator','/analytical-reference-calculator/'],['privacy','/privacy-policy','/privacy-policy/'],['terms','/terms-conditions','/terms-conditions/'],['cart','/cart','/cart/']];
const only=process.argv[2]?process.argv[2].split(','):null; const which=process.argv[3]||'live,local'; const widths=(process.argv[4]||'1440,390').split(',').map(Number);
const H={live:'https://onyxhealth.net',local:'http://localhost:8088'};
const b=await chromium.launch({executablePath:'/usr/bin/google-chrome',args:['--no-sandbox']});
for(const tag of which.split(','))for(const w of widths){
 const host=H[tag];const ctx=await b.newContext({viewport:{width:w,height:900}});
 await ctx.addCookies([{name:'onyx_age_verified',value:'1',domain:new URL(host).hostname,path:'/'}]);
 await ctx.addInitScript(()=>{try{localStorage.setItem('ageVerified','true');localStorage.setItem('onyx_age_verified','true');localStorage.setItem('cookieConsent','accepted');sessionStorage.setItem('onyx_order_delay_notice_dismissed_v1','1')}catch(e){}});
 const p=await ctx.newPage();
 for(const [n,lu,wu] of P){ if(only&&!only.includes(n))continue;
  if(n==='cart'){await p.goto(host+(tag==='live'?'/product/rt':'/product/rt/'),{waitUntil:'networkidle'}).catch(()=>{});
   try{ if(tag==='local'){await p.getByRole('button',{name:/^add to cart$/i}).first().click();await p.waitForLoadState('load');} else {await p.getByRole('button',{name:/add to cart/i}).first().click();await p.waitForTimeout(1500);} }catch(e){}}
  await p.goto(host+(tag==='live'?lu:wu),{waitUntil:'load',timeout:60000}).catch(()=>{});
  for(let y=0;y<20000;y+=700){await p.evaluate(y=>scrollTo(0,y),y);await p.waitForTimeout(100);}
  await p.evaluate(()=>scrollTo(0,0));await p.waitForTimeout(tag==='live'?2500:1000);
  await p.screenshot({path:`/workspace/onyx/cmp/${n}-${w}-${tag}.png`,fullPage:true}).catch(e=>console.log(n,e.message));
 }
 await ctx.close();}
await b.close();
