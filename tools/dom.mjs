import { chromium } from 'playwright-core';
const b=await chromium.launch({executablePath:'/usr/bin/google-chrome',args:['--no-sandbox']});
const ctx=await b.newContext({viewport:{width:1440,height:900}});
await ctx.addInitScript(()=>{localStorage.setItem('ageVerified','true')});
const p=await ctx.newPage();
for(const u of process.argv.slice(2)){await p.goto('https://onyxhealth.net'+u,{waitUntil:'networkidle'});await p.waitForTimeout(4000);
console.log('=====',u);console.log(await p.evaluate(()=>document.querySelector('main')?.outerHTML||document.body.innerHTML.slice(0,20000)));}
await b.close();
