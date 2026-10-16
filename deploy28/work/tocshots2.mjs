import {chromium} from 'playwright-core';
const [out,tag,...slugs]=process.argv.slice(2);const b=await chromium.launch({executablePath:'/usr/bin/google-chrome'});
for(const [w,h,t] of [[1440,900,'1440'],[1280,800,'1280']]){const c=await b.newContext({viewport:{width:w,height:h},deviceScaleFactor:2,reducedMotion:'reduce'});
await c.addCookies([{name:'onyx_age_verified',value:'1',url:'http://localhost:8088'}]);
for(const s of slugs){const p=await c.newPage();await p.goto(`http://localhost:8088/${s}/`,{waitUntil:'networkidle'});await p.waitForTimeout(500);
await p.evaluate(()=>{for(const e of document.querySelectorAll('body *')){if(getComputedStyle(e).position==='fixed'&&!e.closest('header'))e.style.setProperty('display','none','important')}});
const H=await p.evaluate(()=>document.documentElement.scrollHeight);
const y0=await p.evaluate(()=>{const h=[...document.querySelectorAll('h2')].find(e=>/^(on this page|categories)$/i.test(e.textContent.trim()));return h?Math.max(0,h.getBoundingClientRect().top+scrollY-200):600});
for(const [k,y] of [['start',y0],['mid',Math.round(H*0.4)],]){await p.evaluate(y=>scrollTo(0,y),y);await p.waitForTimeout(400);
const vis=await p.evaluate(()=>{const e=document.querySelector('.onyx-v2-sticky')||[...document.querySelectorAll('h2')].find(e=>/^(on this page|categories)$/i.test(e.textContent.trim()))?.closest('.e-con');if(!e)return 'none';const r=e.getBoundingClientRect();return `top=${r.top|0} bottom=${r.bottom|0} vh=${innerHeight} visible=${r.bottom>0&&r.top<innerHeight&&r.height>0}`});
console.log(t,s,k,vis);await p.screenshot({path:`${out}/${tag}-${s}-${t}-${k}.png`});}
await p.close()}await c.close()}await b.close();
