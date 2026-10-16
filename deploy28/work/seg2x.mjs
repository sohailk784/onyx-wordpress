import {chromium} from 'playwright-core';
// usage: node seg2x.mjs URL out.png width [cookieHeader]
const [url,out,w]=process.argv.slice(2);const b=await chromium.launch({executablePath:'/usr/bin/google-chrome'});
const c=await b.newContext({...(process.env.STATE?{storageState:process.env.STATE}:{}),viewport:{width:+w,height:900},deviceScaleFactor:2,reducedMotion:'reduce'});
await c.addCookies([{name:'onyx_age_verified',value:'1',url:'http://localhost:8088'},{name:'age_gate',value:'1',url:'http://localhost:8088'}]);
const p=await c.newPage();await p.goto(url,{waitUntil:'networkidle',timeout:90000}).catch(()=>{});
for(const t of ['Over 21','Yes','I am 18','Enter','Confirm']){const l=p.getByRole('button',{name:new RegExp(t,'i')}).first();if(await l.isVisible().catch(()=>false)){await l.click().catch(()=>{});await p.waitForTimeout(400);}}
await p.evaluate(async()=>{for(const i of document.images){i.loading='eager';i.decoding='sync'}for(let y=0;y<document.body.scrollHeight;y+=300){scrollTo(0,y);await new Promise(r=>setTimeout(r,90))}scrollTo(0,0)});
await p.waitForTimeout(1500);
await p.evaluate(()=>{for(const el of document.querySelectorAll('p')){if(el.textContent.trim()==='Attention'){let e=el;while(e&&getComputedStyle(e).position!=='fixed')e=e.parentElement;if(e)e.remove()}}});
await p.addStyleTag({content:'header{position:relative!important}'});await p.waitForTimeout(600);
const H=await p.evaluate(()=>document.documentElement.scrollHeight);const seg=7000;
for(let i=0;i*seg<H;i++){await p.screenshot({path:out.replace('.png',`-seg${i}.png`),fullPage:true,clip:{x:0,y:i*seg,width:+w,height:Math.min(seg,H-i*seg)}});}
console.log(H);await b.close();
