import {chromium} from '/workspace/onyx/tools/node_modules/playwright-core/index.mjs';
import fs from 'fs';
const b=await chromium.launch({executablePath:'/usr/bin/google-chrome'});const p=await b.newPage({viewport:{width:400,height:720}});
await p.setContent(`<html><body style="margin:0;background:transparent">${fs.readFileSync('needle.svg','utf8')}</body></html>`);
await p.screenshot({path:'needle@4x.png',omitBackground:true,clip:{x:0,y:0,width:400,height:720}});await b.close();
