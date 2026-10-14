import { chromium } from 'playwright-core';
const [,,host,tag,...pages]=process.argv;
const b=await chromium.launch({executablePath:'/usr/bin/google-chrome',args:['--no-sandbox']});
for(const w of [1440,390]){
 const ctx=await b.newContext({viewport:{width:w,height:900}});
 const d=new URL(host).hostname;
 await ctx.addCookies([{name:'onyx_age_verified',value:'1',domain:d,path:'/'}]);
 await ctx.addInitScript(()=>{try{localStorage.setItem('ageVerified','true');localStorage.setItem('onyx_age_verified','true')}catch(e){}});
 const p=await ctx.newPage();
 for(const pg of pages){const i=pg.indexOf('='),n=pg.slice(0,i),u=pg.slice(i+1);
  await p.goto(host+u,{waitUntil:'networkidle',timeout:60000}).catch(()=>{});
  await p.waitForTimeout(+process.env.W||1200);
  await p.screenshot({path:`/workspace/onyx/screens4/raw/${n}-${w}-${tag}.png`,fullPage:true});
 }
 await ctx.close();
}
await b.close();
