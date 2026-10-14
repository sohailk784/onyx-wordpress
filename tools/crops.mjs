import {chromium} from 'playwright-core';
const b=await chromium.launch({executablePath:'/usr/bin/google-chrome'});
for (const [w,sel,name] of [[1440,'.hv2-hero2','hero'],[1440,'.hv2-best','bestsellers'],[1440,'.hv2-testing','how-we-test'],[1440,'.hv2-lab','video'],[390,'.hv2-hero2','hero-mobile'],[390,'.hv2-best','bestsellers-mobile']]){
 const p=await b.newPage({viewport:{width:w,height:900},deviceScaleFactor:2});
 await p.goto('http://localhost:8088/home-v2/',{waitUntil:'networkidle'});
 for(const t of ['Over 21']){const l=p.getByRole('button',{name:new RegExp(t,'i')}).first();if(await l.isVisible().catch(()=>false))await l.click();}
 await p.evaluate(async()=>{for(let y=0;y<document.body.scrollHeight;y+=400){scrollTo(0,y);await new Promise(r=>setTimeout(r,150))}});
 await p.waitForTimeout(2500);
 await p.evaluate(()=>{for(const el of document.querySelectorAll('p')){if(el.textContent.trim()==='Attention'){let e=el;while(e&&getComputedStyle(e).position!=='fixed')e=e.parentElement;if(e)e.remove()}}});
 await p.addStyleTag({content:'header,.elementor-sticky,[data-elementor-type=header] *{position:static!important}'});await p.waitForTimeout(300);await p.locator(sel).first().screenshot({path:`../homev2/crop-${name}@2x.png`});await p.close();}
await b.close();
