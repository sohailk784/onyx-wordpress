import {chromium} from 'playwright-core';
const [,,url,prefix]=process.argv;const b=await chromium.launch({executablePath:'/usr/bin/google-chrome'});
for (const [w,name] of [[1440,'desktop'],[390,'mobile']]){
 const p=await b.newPage({viewport:{width:w,height:900},deviceScaleFactor:2});
 await p.goto(url,{waitUntil:'networkidle'});
 for(const t of ['Over 21','Yes','I am 18','Enter','Confirm']){const l=p.getByRole('button',{name:new RegExp(t,'i')}).first();if(await l.isVisible().catch(()=>false)){await l.click().catch(()=>{});await p.waitForTimeout(500);}}
 await p.waitForTimeout(2500); await p.evaluate(()=>{for(const el of document.querySelectorAll('p')){if(el.textContent.trim()==='Attention'){let e=el;while(e&&getComputedStyle(e).position!=='fixed')e=e.parentElement;if(e)e.remove()}}});
 await p.addStyleTag({content:'.hv2-logos .hv2-lg-track{animation-delay:-3s!important;animation-play-state:paused!important}'});
 const el=p.locator('.hv2-logos').first(); await el.scrollIntoViewIfNeeded(); await p.waitForTimeout(1500);
 await p.evaluate(()=>document.querySelectorAll('.ht-ctc,[class*=whatsapp]').forEach(e=>e.style.display='none'));
 await p.evaluate(()=>{for(const e of document.querySelectorAll('body *')){const cs=getComputedStyle(e);if(cs.position==='fixed'&&!e.closest('header'))e.style.setProperty('display','none','important')}document.querySelectorAll('[class*=ctc],[id*=ctc]').forEach(e=>e.style.setProperty('display','none','important'));document.body.style.overflow='auto';document.documentElement.style.overflow='auto'}); await p.waitForTimeout(300);
 await el.screenshot({path:`${prefix}-${name}@2x.png`});
 await p.close();}
await b.close();
