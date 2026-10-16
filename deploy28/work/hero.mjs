import {chromium} from 'playwright-core';
const [out,...slugs]=process.argv.slice(2);const b=await chromium.launch({executablePath:'/usr/bin/google-chrome'});
for(const [w,t] of [[1440,'desktop'],[390,'mobile']]){const c=await b.newContext({viewport:{width:w,height:900},deviceScaleFactor:2,reducedMotion:'reduce'});
await c.addCookies([{name:'onyx_age_verified',value:'1',url:'http://localhost:8088'}]);
for(const s of slugs){const p=await c.newPage();await p.goto(`http://localhost:8088/${s}/`,{waitUntil:'networkidle'});await p.waitForTimeout(600);
await p.evaluate(()=>{for(const e of document.querySelectorAll('body *')){if(getComputedStyle(e).position==='fixed'&&!e.closest('header'))e.style.setProperty('display','none','important')}});
const h=p.locator('[data-elementor-type="wp-page"] > .e-con').first();await h.screenshot({path:`${out}-${s}-${t}.png`});await p.close()}await c.close()}
await b.close();
