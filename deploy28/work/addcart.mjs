import {chromium} from 'playwright-core';
const B='http://localhost:8088';const b=await chromium.launch({executablePath:'/usr/bin/google-chrome'});const c=await b.newContext();
await c.addCookies([{name:'onyx_age_verified',value:'1',url:B}]);const p=await c.newPage();
for(const slug of ['5amino1mq']){await p.goto(B+'/product/'+slug+'/?v2=1');await p.locator('form.onyx-cart-form button[type=submit]:not([name])').click();await p.waitForLoadState('networkidle');}
await c.storageState({path:process.argv[2]});await b.close();
