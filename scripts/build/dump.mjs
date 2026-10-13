import { chromium } from 'playwright-core';
import fs from 'fs';
const [,,url,out,sel='main'] = process.argv;
const b = await chromium.launch({executablePath:'/usr/bin/google-chrome',args:['--no-sandbox']});
const ctx = await b.newContext({viewport:{width:1440,height:900}});
await ctx.addCookies([{name:'onyx_age_verified',value:'1',domain:'onyxhealth.net',path:'/'}]);
const p = await ctx.newPage();
await p.goto(url,{waitUntil:'networkidle',timeout:90000});
await p.waitForTimeout(2500);
// scroll to trigger lazy/animations
await p.evaluate(async()=>{for(let y=0;y<document.body.scrollHeight;y+=600){window.scrollTo(0,y);await new Promise(r=>setTimeout(r,120));}window.scrollTo(0,0);});
await p.waitForTimeout(1500);
const collect = (sel)=>{
  const root=document.querySelector(sel); if(!root) return null;
  let i=0; const all=[root,...root.querySelectorAll('*')];
  const res={};
  for(const el of all){ if(!el.dataset.ix) el.dataset.ix=String(i); i++;
    const cs=getComputedStyle(el); const r=el.getBoundingClientRect();
    const k=['display','visibility','opacity','flexDirection','flexWrap','justifyContent','alignItems','columnGap','rowGap','gridTemplateColumns','paddingTop','paddingRight','paddingBottom','paddingLeft','marginTop','marginRight','marginBottom','marginLeft','backgroundColor','backgroundImage','backgroundSize','backgroundPosition','borderTopWidth','borderRightWidth','borderBottomWidth','borderLeftWidth','borderTopColor','borderStyle','borderRadius','boxShadow','color','fontSize','fontWeight','lineHeight','letterSpacing','textTransform','textAlign','maxWidth','minHeight','width','height','position','flexGrow','flexShrink','objectFit','fontFamily','fontStyle','textDecorationLine','overflow','top','left','right','bottom','zIndex','transform'];
    const s={}; for(const x of k) s[x]=cs[x];
    s.w=r.width; s.h=r.height; s.x=r.left; s.y=r.top+window.scrollY;
    const pr=el.parentElement?el.parentElement.getBoundingClientRect():r; s.pw=pr.width;
    res[el.dataset.ix]=s;
  }
  return res;
};
const out1={};
for(const [name,w] of [['d',1440],['t',820],['m',390]]){
  await p.setViewportSize({width:w,height:900}); await p.waitForTimeout(800);
  out1[name]=await p.evaluate(collect,sel);
}
await p.setViewportSize({width:1440,height:900}); await p.waitForTimeout(500);
const tree = await p.evaluate((sel)=>{
  const root=document.querySelector(sel);
  const ser=(el)=>{
    const o={ix:el.dataset.ix,tag:el.tagName.toLowerCase(),cls:typeof el.className==='string'?el.className:''};
    const a={}; for(const at of ['href','src','alt','type','placeholder','name','poster','role','aria-label','currentSrc']) { if(at==='currentSrc'){ if(el.currentSrc) a.src=el.currentSrc; continue;} if(el.getAttribute(at)!=null) a[at]=el.getAttribute(at); }
    if(el.tagName==='A') a.href=el.href;
    o.attrs=a;
    if(o.tag==='svg'){ const c=el.cloneNode(true); const cs=getComputedStyle(el); c.setAttribute('width',el.getBoundingClientRect().width||cs.width); c.setAttribute('height',el.getBoundingClientRect().height||cs.height); c.removeAttribute('class'); c.setAttribute('style','color:'+cs.color+';flex-shrink:0;vertical-align:middle'); o.html=c.outerHTML; return o;}
    if(o.tag==='video'){ o.attrs.src=el.currentSrc||el.querySelector('source')?.src; }
    o.inner=el.innerHTML.length<20000?el.innerHTML:'';
    o.text=el.innerText;
    o.kids=[];
    for(const n of el.childNodes){
      if(n.nodeType===3){ if(n.textContent.trim()) o.kids.push({tag:'#text',text:n.textContent}); }
      else if(n.nodeType===1) o.kids.push(ser(n));
    }
    return o;
  };
  return ser(root);
},sel);
fs.writeFileSync(out,JSON.stringify({url,tree,st:out1}));
await b.close();
