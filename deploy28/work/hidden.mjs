import {chromium} from 'playwright-core';
const b=await chromium.launch({executablePath:'/usr/bin/google-chrome'});const c=await b.newContext({viewport:{width:1440,height:900}});
await c.addCookies([{name:'onyx_age_verified',value:'1',url:'http://localhost:8088'}]);
for(const s of process.argv.slice(2)){const p=await c.newPage();await p.goto(`http://localhost:8088/${s}-2/`,{waitUntil:'networkidle'});
const r=await p.evaluate(()=>[...document.querySelectorAll('[data-elementor-type="wp-page"] svg')].filter(v=>getComputedStyle(v).display==='none').map(v=>v.closest('.elementor-widget')?.textContent.trim().slice(0,30)));
if(r.length)console.log(s,'hidden:',JSON.stringify(r));await p.close()}await b.close();
