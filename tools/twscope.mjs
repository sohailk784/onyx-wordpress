import fs from 'fs'; import postcss from 'postcss'; import prefixer from 'postcss-prefix-selector';
const src=fs.readFileSync('/workspace/onyx/live/index-CdWduHBa.css','utf8');
const root=postcss.parse(src);
// unwrap utilities/base/components layers; keep properties/theme
root.walkAtRules('layer',at=>{ if(['utilities','base','components'].includes(at.params)&&at.nodes){ at.replaceWith(at.nodes);} else if(at.params==='components'&&!at.nodes) at.remove(); });
const out=postcss([prefixer({prefix:'.onyx-tw',transform(prefix,sel,prefixed){
  if(sel.startsWith(':root')||sel.startsWith(':host')) return sel;
  if(/^(html|body)\b/.test(sel)) return sel.replace(/^(html|body)/,prefix);
  if(sel==='*') return `${prefix} *, ${prefix}`;
  if(sel.startsWith('.dark')) return sel;
  return `${prefix} ${sel}`;}})]).process(root.toString(),{from:undefined}).css;
fs.writeFileSync(process.argv[2],out);console.log(out.length);
