import { chromium } from 'playwright-core';
import fs from 'fs';
const routes=process.argv.slice(2);
const b=await chromium.launch({executablePath:'/usr/bin/google-chrome',args:['--no-sandbox']});
const ctx=await b.newContext(); await ctx.addCookies([{name:'onyx_age_verified',value:'1',url:'https://onyxhealth.net'}]);
const out={};
for(const r of routes){ const p=await ctx.newPage(); await p.goto('https://onyxhealth.net/'+r,{waitUntil:'networkidle',timeout:60000}).catch(()=>{}); await p.waitForTimeout(800);
 out[r]=await p.evaluate(()=>({title:document.title,description:document.querySelector('meta[name=description]')?.content||'',robots:document.querySelector('meta[name=robots]')?.content||''})); await p.close(); }
fs.writeFileSync((process.env.ONYX_WORK||'build-work')+'/import/seo.json',JSON.stringify(out,null,1)); await b.close();
