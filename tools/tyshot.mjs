import { chromium } from 'playwright-core';
const [,,url,out]=process.argv;
const b=await chromium.launch({executablePath:'/usr/bin/google-chrome',args:['--no-sandbox']});
for(const w of [1440,390]){const c=await b.newContext({viewport:{width:w,height:900}});await c.addCookies([{name:'onyx_age_verified',value:'1',domain:'localhost',path:'/'}]);await c.addInitScript(()=>{try{localStorage.setItem('ageVerified','true');localStorage.setItem('cookieConsent','accepted')}catch(e){}});
const p=await c.newPage();await p.goto(url,{timeout:60000});await p.waitForTimeout(2000);await p.screenshot({path:`../cmp/${out}-${w}-local.png`,fullPage:true});await c.close();}
await b.close();
