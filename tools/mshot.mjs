import {chromium} from 'playwright-core';
const [url,out,w]=process.argv.slice(2);const b=await chromium.launch({executablePath:'/usr/bin/google-chrome'});
const c=await b.newContext({viewport:{width:+w,height:900}});
await c.addCookies([{name:'onyx_age_ok',value:'1',url:'http://localhost:8088'},{name:'age_gate',value:'1',url:'http://localhost:8088'}]).catch(()=>{});
const p=await c.newPage();await p.goto(url,{waitUntil:'networkidle',timeout:60000}).catch(()=>{});
for(const t of ['Over 21','Yes','I am 18','Enter','Confirm']){const l=p.getByRole('button',{name:new RegExp(t,'i')}).first();if(await l.isVisible().catch(()=>false)){await l.click().catch(()=>{});await p.waitForTimeout(500);}}
await p.evaluate(async()=>{for(let y=0;y<document.body.scrollHeight;y+=500){scrollTo(0,y);await new Promise(r=>setTimeout(r,250))}scrollTo(0,0);for(const i of document.images){i.loading='eager'}await Promise.all([...document.images].map(i=>i.complete?0:new Promise(r=>{i.onload=i.onerror=r;setTimeout(r,4000)})));if(location.search.includes('notoast')||1){for(const el of document.querySelectorAll('p')){if(el.textContent.trim()==='Attention'){let e=el;while(e&&getComputedStyle(e).position!=='fixed')e=e.parentElement;if(e)e.style.display='none'}}}const h=document.querySelector('header');}).catch(()=>{});
await p.waitForTimeout(2500);await p.evaluate(()=>{for(const el of document.querySelectorAll('p')){if(el.textContent.trim()==='Attention'){let e=el;while(e&&getComputedStyle(e).position!=='fixed')e=e.parentElement;if(e)e.remove()}}});await p.addStyleTag({content:'header{position:relative!important}'});await p.waitForTimeout(1200);
await p.screenshot({path:out,fullPage:true});
console.log(await p.evaluate(()=>[document.documentElement.scrollWidth,document.body.scrollHeight]));await b.close();
