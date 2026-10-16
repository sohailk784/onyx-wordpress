import {chromium} from 'playwright-core';import fs from 'fs';
// node clip.mjs WIDTH OUTDIR TAG
const [w,out,tag]=process.argv.slice(2);fs.mkdirSync(out,{recursive:true});
const b=await chromium.launch({executablePath:'/usr/bin/google-chrome'});
const c=await b.newContext({viewport:{width:+w,height:1000},deviceScaleFactor:2,reducedMotion:'reduce'});
await c.addCookies([{name:'onyx_age_verified',value:'1',url:'http://localhost:8088'}]);
const p=await c.newPage();const res=[];
for(let pg=1;pg<=7;pg++){await p.goto(`http://localhost:8088/peptides-2/?s2_pg=${pg}`,{waitUntil:'networkidle'});
 const n=await p.locator('.s2-card').count();if(!n)break;
 await p.evaluate(async()=>{for(const i of document.images){i.loading='eager'}for(let y=0;y<document.body.scrollHeight;y+=400){scrollTo(0,y);await new Promise(r=>setTimeout(r,60))}});
 await p.waitForTimeout(800);await p.evaluate(()=>{for(const e of document.querySelectorAll('body *')){const cs=getComputedStyle(e);if(cs.position==='fixed'&&!e.closest('header'))e.style.setProperty('display','none','important')}});
 await p.addStyleTag({content:'.s2-wish,.s2-badges,.ht-ctc,#ht-ctc-chat,.ht_ctc_chat_area{visibility:hidden!important}'});await p.evaluate(()=>{for(const el of document.querySelectorAll('p')){if(el.textContent.trim()==='Attention'){let e=el;while(e&&getComputedStyle(e).position!=='fixed')e=e.parentElement;if(e)e.remove()}}});
 for(let i=0;i<n;i++){const card=p.locator('.s2-card').nth(i);const name=(await card.locator('.s2-title').innerText()).trim();
  const prem=await card.evaluate(e=>e.classList.contains('s2-premium'));
  const m=card.locator('.s2-media');await m.scrollIntoViewIfNeeded();
  const f=`${out}/${tag}-p${pg}-${i}.png`;await m.screenshot({path:f});res.push({pg,i,name,prem,f});}
}
fs.writeFileSync(`${out}/${tag}.json`,JSON.stringify(res));console.log(res.length);await b.close();
