import {chromium} from 'playwright-core';
const b=await chromium.launch({executablePath:'/usr/bin/google-chrome'});
for(const w of [1025,1100,1200,1280,1366,1440]){const c=await b.newContext({viewport:{width:w,height:900}});await c.addCookies([{name:'onyx_age_verified',value:'1',url:'http://localhost:8088'}]);
for(const s of process.argv.slice(2)){const p=await c.newPage();await p.goto(`http://localhost:8088/${s}/`,{waitUntil:'networkidle'});
const r=await p.evaluate(()=>{const col=document.querySelector('.onyx-v2-sticky');if(!col)return 'no sticky';const t=col.querySelector('.onyx-v2-toc-title');const cr=col.getBoundingClientRect();
const sib=[...col.parentElement.children].find(k=>k!==col&&k.offsetHeight>0);const sr=sib.getBoundingClientRect();
// visible content card inside sibling: first child with bg
let card=[...sib.querySelectorAll('*')].find(e=>{const c=getComputedStyle(e);return c.backgroundColor!=='rgba(0, 0, 0, 0)'&&e.offsetWidth>200});const kr=(card||sib).getBoundingClientRect();
const vc=[col,...col.querySelectorAll('*')].find(e=>getComputedStyle(e).backgroundColor!=='rgba(0, 0, 0, 0)'&&e.offsetWidth>150)||col;const vr=vc.getBoundingClientRect();const tr=t.getBoundingClientRect();const cs=getComputedStyle(t);
return `gap=${Math.round(kr.left-vr.right)} colgap=${Math.round(sr.left-cr.right)} label=${cs.fontSize} inside=${tr.left>=cr.left+8&&tr.right<=cr.right-8} labelW=${tr.width|0} colW=${cr.width|0}`});
console.log(w,s,r);await p.close()}await c.close()}await b.close();
