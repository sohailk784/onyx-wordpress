import { chromium } from 'playwright-core';
const pages=[['home','/'],['shop','/peptides'],['product','/product/rt'],['cart','/cart'],['contact','/contact'],['faq','/faq']];
const b=await chromium.launch({executablePath:'/usr/bin/google-chrome',args:['--no-sandbox']});
for (const [host,tag] of [['http://localhost:8088','local'],['https://onyxhealth.net','live']]){
 const ctx=await b.newContext({viewport:{width:1440,height:900}});
 const d=new URL(host).hostname;
 await ctx.addCookies([{name:'onyx_age_verified',value:'1',domain:d,path:'/'}]);
 await ctx.addInitScript(()=>{try{localStorage.setItem('ageVerified','true');localStorage.setItem('onyx_age_verified','true')}catch(e){}});
 const p=await ctx.newPage();
 for(const [n,u] of pages){
  if(n==='cart'){await p.goto(host+'/product/rt',{waitUntil:'networkidle'}).catch(()=>{});
    try{ if(tag==='local'){await p.click('.single_add_to_cart_button');await p.waitForLoadState('networkidle');}
         else {await p.getByRole('button',{name:/add to cart/i}).first().click();await p.waitForTimeout(1500);} }catch(e){}}
  await p.goto(host+u,{waitUntil:'networkidle',timeout:60000}).catch(()=>{});
  for(let y=0;y<15000;y+=600){await p.evaluate(y=>scrollTo(0,y),y);await p.waitForTimeout(120);}
  await p.evaluate(()=>scrollTo(0,0));await p.waitForTimeout(800);
  await p.screenshot({path:`${process.env.ONYX_WORK||'build-work'}/screens/${n}-${tag}.png`,fullPage:true});
 }
 await ctx.close();
}
await b.close();
