// Checkout screenshots: node co.mjs live|local 1440,390 [action]
// action: blank (default) | errors (click place order empty)
import { chromium } from 'playwright-core'; import fs from 'fs';
const [,,tag='live',ws='1440,390',action='blank']=process.argv;
const host=tag==='live'?'https://onyxhealth.net':'http://localhost:8088'; const sl=tag==='live'?'':'/';
const b=await chromium.launch({executablePath:'/usr/bin/google-chrome',args:['--no-sandbox']});
for(const w of ws.split(',').map(Number)){
 const ctx=await b.newContext({viewport:{width:w,height:900}});
 await ctx.addCookies([{name:'onyx_age_verified',value:'1',domain:new URL(host).hostname,path:'/'}]);
 await ctx.addInitScript(()=>{try{localStorage.setItem('ageVerified','true');localStorage.setItem('cookieConsent','accepted');sessionStorage.setItem('onyx_order_delay_notice_dismissed_v1','1')}catch(e){}});
 const p=await ctx.newPage(); await p.goto(host+'/product/rt'+sl,{waitUntil:'load',timeout:60000}); await p.waitForTimeout(2500);
 await p.getByRole('button',{name:/^add to cart$/i}).first().click(); await p.waitForTimeout(3000);
 await p.goto(host+'/checkout'+sl,{waitUntil:'load',timeout:60000}); await p.waitForTimeout(5000);
 let suf='';
 if(action==='errors'){ await p.getByRole('button',{name:/place order/i}).first().click(); await p.waitForTimeout(3000); await p.evaluate(()=>scrollTo(0,0)); suf='-err'; }
 await p.screenshot({path:`/workspace/onyx/cmp/checkout${suf}-${w}-${tag}.png`,fullPage:true});
 if(w===1440&&action==='blank') fs.writeFileSync(`/workspace/onyx/cmp/checkout-${tag}.html`, await p.evaluate(()=>(document.querySelector('main')||document.body).outerHTML));
 await ctx.close();}
await b.close();
