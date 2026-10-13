// Restore stripped inline SVGs in Elementor text widgets by copying the matching live SVG (matched by adjacent text).
import { chromium } from 'playwright-core'; import fs from 'fs';
const map={home:'/',contact:'/contact','privacy-policy':'/privacy-policy','terms-conditions':'/terms-conditions','cookies-policy':'/cookies-policy','refund-policy':'/refund-policy','shipping-policy':'/shipping-policy','accessibility-policy':'/accessibility-policy','payment-delivery':'/payment-delivery','wholesale-application':'/wholesale-application','independent-doctor-consultation':'/independent-doctor-consultation'};
const b=await chromium.launch({executablePath:'/usr/bin/google-chrome',args:['--no-sandbox']});
const ctx=await b.newContext({viewport:{width:1440,height:900}});
await ctx.addCookies([{name:'onyx_age_verified',value:'1',domain:'onyxhealth.net',path:'/'}]);
await ctx.addInitScript(()=>{try{localStorage.setItem('ageVerified','true')}catch(e){}});
const p=await ctx.newPage(); const out={};
for(const [slug,url] of Object.entries(map)){
 const f=`edata/${slug}.json`; const d=JSON.parse(fs.readFileSync(f)); const need=[];
 (function w(x){for(const c of x){for(const [k,v] of Object.entries(c.settings||{})){if(typeof v==='string'&&/<(path|circle|rect|line|polyline)>\s*<\/\1>/.test(v)){need.push(v.replace(/<svg[\s\S]*?<\/svg>/g,'').replace(/<[^>]+>/g,'').replace(/&amp;/g,'&').trim());}} w(c.elements||[]);}})(d);
 if(!need.length) continue;
 await p.goto('https://onyxhealth.net'+url,{waitUntil:'networkidle',timeout:90000}).catch(()=>{}); await p.waitForTimeout(1500);
 out[slug]=await p.evaluate(need=>{const r={};for(const t of need){let best=null;for(const e of document.querySelectorAll('svg')){let a=e.parentElement;for(let i=0;i<3&&a;i++,a=a.parentElement){if(a.innerText&&a.innerText.trim().startsWith(t.slice(0,25))){best=e;break;}} if(best)break;} r[t]=best?best.innerHTML:null;} return r;},need);
 console.log(slug,JSON.stringify(Object.fromEntries(Object.entries(out[slug]).map(([k,v])=>[k.slice(0,30),!!v]))));
}
fs.writeFileSync('/tmp/svgfix.json',JSON.stringify(out)); await b.close();
