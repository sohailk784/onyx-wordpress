import {chromium} from 'playwright-core';
const b=await chromium.launch({executablePath:'/usr/bin/google-chrome'});const c=await b.newContext({viewport:{width:1440,height:900}});
await c.addCookies([{name:'onyx_age_verified',value:'1',url:'http://localhost:8088'}]);
for(const s of process.argv.slice(2)){const p=await c.newPage();await p.goto(`http://localhost:8088/${s}-2/`,{waitUntil:'networkidle'});
const r=await p.evaluate(()=>{const o=[];for(const row of document.querySelectorAll('[data-elementor-type="wp-page"] .e-con, [data-elementor-type="wp-page"] .e-con-inner, #content .grid, #content aside')){const k=[...row.children].filter(x=>x.offsetHeight>0);if(k.length<2)continue;
const rr=k.map(x=>x.getBoundingClientRect());const sameRow=rr.every(q=>Math.abs(q.top-rr[0].top)<40);if(!sameRow)continue;
for(let i=0;i<k.length;i++){const w=rr[i].width,h=k[i].scrollHeight;const other=Math.max(...rr.filter((_,j)=>j!==i).map(q=>q.height));const cont=k[i].getBoundingClientRect().height;
if(w<400&&w>150&&other>Math.max(h*1.6,900)){o.push(`${k[i].className.slice(0,60)} w=${w|0} contentH=${h} colH=${cont|0} siblingH=${other|0} text="${k[i].innerText.trim().slice(0,40).replace(/\n/g,' | ')}"`)}}}return o});
console.log(s,r.length?'\n  '+r.join('\n  '):'-');await p.close()}await b.close();
