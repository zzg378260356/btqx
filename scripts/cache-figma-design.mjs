// Convert cached Figma reference JSX into Vue-renderable nodes and ordinary CSS.
// No Figma calls are made here. Original asset bytes are downloaded unchanged.
import fs from 'node:fs';
import path from 'node:path';
import vm from 'node:vm';
import { execFileSync } from 'node:child_process';
import { createRequire } from 'node:module';
const require = createRequire(import.meta.url);
const ts = require('../frontend/node_modules/typescript');
const root = path.resolve(import.meta.dirname, '..');
const cache = path.join(root, 'docs/figma-cache/cN8Yayeu6hYGXWjW4f1Zf6');
const target = path.join(root, 'frontend/public/figma/overview');
fs.mkdirSync(target, { recursive: true });
const modules = {
  riskBars: '3034-12146', rainfall: '3055-14070', wind: '3055-14109', temperature: '3055-14117',
  riskPie: '3033-11131', bottomCharts: '3035-12492', rightPanels: '3035-12199',
  observationTable: '3055-13862', redMetric: '3033-11992', blueMetric: '3033-12033', rainMetric: '3033-12041',
};
const unknown = new Set();
const assets = new Map();
const trees = {};
const cssValue = value => value.replace(/_/g, ' ').replace(/calc\(([^)]*)\)/g, (_, expr) => 'calc(' + expr.replace(/(?<=[\d%])([+-])(?=[\d.])/g, ' $1 ') + ')');
const spaced = value => value === 'px' ? '1px' : value === 'full' ? '100%' : value === 'auto' ? 'auto' : value.includes('/') ? (parseFloat(value.split('/')[0]) / parseFloat(value.split('/')[1]) * 100) + '%' : (parseFloat(value) * 4) + 'px';
function styles(classes = '') {
  const out = {};
  let transform = false, gradient = false;
  const direct = {
    absolute: {position:'absolute'}, relative: {position:'relative'}, fixed:{position:'fixed'}, contents:{display:'contents'}, block:{display:'block'}, flex:{display:'flex'}, hidden:{display:'none'}, 'inline-block':{display:'inline-block'},
    'flex-col':{flexDirection:'column'}, 'flex-row':{flexDirection:'row'}, 'flex-none':{flex:'none'}, 'shrink-0':{flexShrink:0}, 'grow':{flexGrow:1}, 'flex-1':{flex:'1 1 0%'},
    'items-start':{alignItems:'flex-start'}, 'items-center':{alignItems:'center'}, 'items-end':{alignItems:'flex-end'}, 'content-stretch':{alignContent:'stretch'},
    'justify-center':{justifyContent:'center'}, 'justify-between':{justifyContent:'space-between'}, 'justify-end':{justifyContent:'flex-end'}, 'justify-start':{justifyContent:'flex-start'},
    'whitespace-nowrap':{whiteSpace:'nowrap'}, 'text-center':{textAlign:'center'}, 'text-right':{textAlign:'right'}, 'text-left':{textAlign:'left'},
    'text-white':{color:'#fff'}, 'text-black':{color:'#000'}, 'not-italic':{fontStyle:'normal'}, italic:{fontStyle:'italic'}, 'font-bold':{fontWeight:700},
    'overflow-clip':{overflow:'clip'}, 'overflow-hidden':{overflow:'hidden'}, 'pointer-events-none':{pointerEvents:'none'},
    'max-w-none':{maxWidth:'none'}, 'object-cover':{objectFit:'cover'}, 'border-solid':{borderStyle:'solid'}, border:{borderStyle:'solid',borderWidth:'1px'},
    'border-b':{borderBottomStyle:'solid',borderBottomWidth:'1px'}, 'rounded-full':{borderRadius:'9999px'},
    'mask-alpha':{maskMode:'alpha'}, 'mask-intersect':{maskComposite:'intersect'}, 'mask-no-clip':{maskClip:'no-clip'}, 'mask-no-repeat':{maskRepeat:'no-repeat'},
    'mix-blend-screen':{mixBlendMode:'screen'}, 'mix-blend-overlay':{mixBlendMode:'overlay'}, 'mix-blend-plus-lighter':{mixBlendMode:'plus-lighter'},
    'bg-clip-text':{backgroundClip:'text',WebkitBackgroundClip:'text'}, 'bg-gradient-to-b':{}, 'bg-gradient-to-r':{}, 'from-white':{'--gradient-from-color':'#fff'},
    'col-1':{gridColumn:'1'}, 'row-1':{gridRow:'1'}, 'inline-grid':{display:'inline-grid'}, 'grid-cols-[max-content]':{gridTemplateColumns:'max-content'}, 'grid-rows-[max-content]':{gridTemplateRows:'max-content'}, 'place-items-start':{placeItems:'start'}, 'aspect-[25/24]':{aspectRatio:'25 / 24'},
  };
  const simple = {w:'width',h:'height','min-w':'minWidth','min-h':'minHeight','max-w':'maxWidth',top:'top',bottom:'bottom',left:'left',right:'right',gap:'gap','gap-x':'columnGap','gap-y':'rowGap',leading:'lineHeight',opacity:'opacity',rounded:'borderRadius'};
  const paddings = { p:['padding'],px:['paddingLeft','paddingRight'],py:['paddingTop','paddingBottom'],pl:['paddingLeft'],pr:['paddingRight'],pt:['paddingTop'],pb:['paddingBottom'],m:['margin'],mx:['marginLeft','marginRight'],my:['marginTop','marginBottom'],ml:['marginLeft'],mr:['marginRight'],mt:['marginTop'],mb:['marginBottom'] };
  for (const cls of classes.split(/\s+/).filter(Boolean)) {
    if (cls in direct) { Object.assign(out,direct[cls]); if (cls.startsWith('bg-gradient')) { gradient=true; out['--gradient-direction']=cls.endsWith('-b')?'to bottom':'to right'; } continue; }
    let m;
    if ((m=cls.match(/^\[([\w-]+):(.+)\]$/))) { out[m[1].replace(/-([a-z])/g,(_,c)=>c.toUpperCase())]=cssValue(m[2]); continue; }
    if ((m=cls.match(/^(size|inset|inset-x|inset-y|w|h|min-w|min-h|max-w|top|bottom|left|right|gap|gap-x|gap-y|leading|rounded|opacity)-\[(.+)\]$/))) {
      const [_,key,v]=m; const value=cssValue(v);
      if(key==='size') Object.assign(out,{width:value,height:value}); else if(key==='inset') out.inset=value; else if(key==='inset-x') Object.assign(out,{left:value,right:value}); else if(key==='inset-y') Object.assign(out,{top:value,bottom:value}); else out[simple[key]]=value; continue;
    }
    if ((m=cls.match(/^(p|px|py|pl|pr|pt|pb|m|mx|my|ml|mr|mt|mb)-\[(.+)\]$/))) { for(const p of paddings[m[1]])out[p]=cssValue(m[2]);continue; }
    if ((m=cls.match(/^(text|bg|border|border-b|border-t|border-l|border-r|shadow|text-shadow|font|mask-size|mask-position|flex)-\[(.+)\]$/))) {
      const key=m[1],value=cssValue(m[2]);
      if(key==='text') out[/^[\d.]+(?:px|rem|em)$/.test(value)?'fontSize':'color']=value;
      else if(key==='bg') out[value.includes('gradient')?'backgroundImage':'backgroundColor']=value;
      else if(key==='font') { const font=value.replace(/^['"]|['"]$/g,'').split(':');out.fontFamily='"'+font[0]+'","Microsoft YaHei","Droid Sans Fallback",sans-serif';out.fontWeight=/75/.test(font[1]||'')?600:/65|Medium/.test(font[1]||'')?500:/45|Light/.test(font[1]||'')?300:400; }
      else if(key==='shadow')out.boxShadow=value;
      else if(key==='text-shadow')out.textShadow=value;
      else if(key==='flex')out.flex=value;
      else if(key==='mask-size')out.maskSize=value;
      else if(key==='mask-position')out.maskPosition=value;
      else if(key.startsWith('border')) {const side=key==='border'?'':key==='border-b'?'Bottom':key==='border-t'?'Top':key==='border-l'?'Left':'Right';out['border'+side+(/^[\d.]+px$/.test(value)?'Width':'Color')]=value;out['border'+side+'Style']='solid';}
      continue;
    }
    if ((m=cls.match(/^(from|to)-\[(.+)\]$/))) { const v=cssValue(m[2]);out['--gradient-'+m[1]+(v.endsWith('%')?'-position':'-color')]=v;continue; }
    if ((m=cls.match(/^(-?)(translate-[xy]|rotate|skew-[xy]|scale-[xy])-\[(.+)\]$/))) {out['--'+m[2]]=(m[1]?'-':'')+cssValue(m[3]);transform=true;continue;}
    if ((m=cls.match(/^(-?)(translate-[xy]|rotate|skew-[xy]|scale-[xy])-(.+)$/))) {const key=m[2],v=m[3];out['--'+key]=(m[1]?'-':'')+(key.startsWith('translate')?spaced(v):key==='rotate'||key.startsWith('skew')?v+'deg':String(Number(v)/100));transform=true;continue;}
    if ((m=cls.match(/^(size|inset|w|h|min-w|min-h|max-w|top|bottom|left|right|gap|leading)-(full|auto|px|0|\d+(?:\.\d+)?(?:\/\d+)?)$/))) {if(m[1]==='size')Object.assign(out,{width:spaced(m[2]),height:spaced(m[2])});else if(m[1]==='inset')out.inset=spaced(m[2]);else out[simple[m[1]]]=m[1]==='leading'&&m[2]==='0'?'0':spaced(m[2]);continue;}
    if ((m=cls.match(/^(p|px|py|pl|pr|pt|pb|m|mx|my|ml|mr|mt|mb)-(\d+(?:\.\d+)?)$/))) {for(const p of paddings[m[1]])out[p]=spaced(m[2]);continue;}
    if ((m=cls.match(/^rounded-(\d+)$/))) {out.borderRadius=spaced(m[1]);continue;}
    unknown.add(cls);
  }
  if(transform) {
    // Each transform establishes its own values; CSS custom properties inherit.
    for(const [key,value]of Object.entries({'--translate-x':'0px','--translate-y':'0px','--rotate':'0deg','--skew-x':'0deg','--skew-y':'0deg','--scale-x':'1','--scale-y':'1'}))if(!(key in out))out[key]=value;
    out.transform='translate(var(--translate-x),var(--translate-y)) rotate(var(--rotate)) skew(var(--skew-x),var(--skew-y)) scale(var(--scale-x),var(--scale-y))';
  }
  if(gradient)out.backgroundImage='linear-gradient(var(--gradient-direction),var(--gradient-from-color,transparent) var(--gradient-from-position,0%),var(--gradient-to-color,transparent) var(--gradient-to-position,100%))';
  return out;
}
function localize(s) {
  if(typeof s!=='string')return s;
  return s.replace(/https:\/\/www\.figma\.com\/api\/mcp\/asset\/[\w-]+\/[\w.-]+\.(?:svg|png|jpg|jpeg|webp)/g,url=>{
    const pieces=url.split('/');const name=pieces.at(-2)+'-'+pieces.at(-1);
    assets.set(url,name);return '/figma/overview/'+name;
  });
}
function h(tag, attrs, ...children) {
  if(typeof tag==='function')return tag({...attrs,children});
  const props={};
  for(const [k,v] of Object.entries(attrs||{})) {
    if(k==='className')continue;
    if(k==='style')continue;
    props[k]=localize(v);
  }
  props.style={...styles(attrs?.className),...Object.fromEntries(Object.entries(attrs?.style||{}).map(([k,v])=>[k,localize(v)]))};
  return {tag,props,children:children.flat(Infinity).filter(c=>c!==null&&c!==undefined&&c!==false)};
}
for(const [name,id]of Object.entries(modules)){
  const input=fs.readFileSync(path.join(cache,id+'.txt'),'utf8').split('SUPER CRITICAL:')[0];
  const code=ts.transpileModule(input,{fileName:'reference.tsx',compilerOptions:{jsx:ts.JsxEmit.React,jsxFactory:'h',module:ts.ModuleKind.CommonJS,target:ts.ScriptTarget.ES2022}}).outputText;
  const sandbox={exports:{},h};vm.runInNewContext(code,sandbox,{timeout:2000});
  trees[name]=sandbox.exports.default();
}
for(const [url,name]of assets){const p=path.join(target,name);if(!fs.existsSync(p)||fs.statSync(p).size===0)execFileSync('curl',['-fsSL','--retry','2','-o',p,url]);}
fs.mkdirSync(path.join(root,'frontend/src/generated'),{recursive:true});
fs.writeFileSync(path.join(root,'frontend/src/generated/overview-design.json'),JSON.stringify(trees,null,2)+'\n');
fs.writeFileSync(path.join(cache,'asset-manifest.json'),JSON.stringify([...assets].map(([source,file])=>({source,file:'/figma/overview/'+file,bytes:fs.statSync(path.join(target,file)).size})),null,2)+'\n');
console.log(JSON.stringify({modules:Object.keys(trees),assetCount:assets.size,unsupportedClasses:[...unknown]}));
