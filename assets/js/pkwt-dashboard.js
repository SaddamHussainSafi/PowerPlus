
/* PowerPlus Dashboard — React UI v3.5.8.1 */
const { useState, useEffect, useRef } = React;

/* ── TAILWIND CONFIG (must be set before first render) ── */
try {
  tailwind.config = {
    theme: {
      extend: {
        fontFamily: { sans:['Inter','system-ui','sans-serif'], mono:['JetBrains Mono','ui-monospace','monospace'] },
        colors: {
          bg:       'rgb(var(--c-bg) / <alpha-value>)',
          card:     'rgb(var(--c-card) / <alpha-value>)',
          border:   'rgb(var(--c-border) / <alpha-value>)',
          sidebar:  'rgb(var(--c-sidebar) / <alpha-value>)',
          sub:      'rgb(var(--c-sub) / <alpha-value>)',
          fg:       'rgb(var(--c-fg) / <alpha-value>)',
          brand:    '#FF6500',
          brandDark:'#cc5200',
          ok:       '#3fb950',
        },
        boxShadow: {
          card: 'var(--shadow-card)',
          glow: '0 0 0 1px rgba(255,101,0,.4),0 0 32px rgba(255,101,0,.18)',
        },
      },
    },
  };
} catch(e) {}

/* ── ICONS ── */
const I = ({ children, size=18, stroke=2, className='' }) => (
  <svg xmlns="http://www.w3.org/2000/svg" width={size} height={size} viewBox="0 0 24 24"
       fill="none" stroke="currentColor" strokeWidth={stroke}
       strokeLinecap="round" strokeLinejoin="round" className={className} aria-hidden="true">
    {children}
  </svg>
);

const IconDashboard  = p=><I {...p}><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></I>;
const IconUser       = p=><I {...p}><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></I>;
const IconCopy       = p=><I {...p}><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></I>;
const IconShield     = p=><I {...p}><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></I>;
const IconBolt       = p=><I {...p}><path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"/></I>;
const IconPalette    = p=><I {...p}><circle cx="13.5" cy="6.5" r=".5" fill="currentColor"/><circle cx="17.5" cy="10.5" r=".5" fill="currentColor"/><circle cx="8.5" cy="7.5" r=".5" fill="currentColor"/><circle cx="6.5" cy="12.5" r=".5" fill="currentColor"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2z"/></I>;
const IconSettings   = p=><I {...p}><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></I>;
const IconSearch     = p=><I {...p}><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></I>;
const IconBell       = p=><I {...p}><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></I>;
const IconHelp       = p=><I {...p}><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></I>;
const IconChevronRight = p=><I {...p}><path d="m9 18 6-6-6-6"/></I>;
const IconPlus       = p=><I {...p}><path d="M12 5v14M5 12h14"/></I>;
const IconImport     = p=><I {...p}><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5"/><path d="M12 15V3"/></I>;
const IconScan       = p=><I {...p}><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><path d="M3 12h18"/></I>;
const IconCheck      = p=><I {...p}><path d="M20 6 9 17l-5-5"/></I>;
const IconX          = p=><I {...p}><path d="M18 6 6 18M6 6l12 12"/></I>;
const IconActivity   = p=><I {...p}><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></I>;
const IconLogin      = p=><I {...p}><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><path d="m10 17 5-5-5-5"/><path d="M15 12H3"/></I>;
const IconKey        = p=><I {...p}><circle cx="7.5" cy="15.5" r="5.5"/><path d="m21 2-9.6 9.6"/><path d="m15.5 7.5 3 3L22 7l-3-3"/></I>;
const IconLock       = p=><I {...p}><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></I>;
const IconEye        = p=><I {...p}><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></I>;
const IconTrash      = p=><I {...p}><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></I>;
const IconEdit       = p=><I {...p}><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5z"/></I>;
const IconExternal   = p=><I {...p}><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><path d="M15 3h6v6"/><path d="M10 14 21 3"/></I>;
const IconRefresh    = p=><I {...p}><path d="M21 12a9 9 0 1 1-3-6.7L21 8"/><path d="M21 3v5h-5"/></I>;
const IconSparkles   = p=><I {...p}><path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M5.6 18.4l2.1-2.1M16.3 7.7l2.1-2.1"/></I>;
const IconLayers     = p=><I {...p}><path d="m12 2 9 5-9 5-9-5z"/><path d="m3 17 9 5 9-5"/><path d="m3 12 9 5 9-5"/></I>;
const IconGrid       = p=><I {...p}><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></I>;
const IconBox        = p=><I {...p}><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><path d="m3.27 6.96 8.73 5.05 8.73-5.05"/><path d="M12 22.08V12"/></I>;
const IconArrowRight = p=><I {...p}><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></I>;
const IconArrowUp    = p=><I {...p}><path d="m18 15-6-6-6 6"/></I>;
const IconFile       = p=><I {...p}><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></I>;
const IconUpload     = p=><I {...p}><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m17 8-5-5-5 5"/><path d="M12 3v12"/></I>;
const IconMail       = p=><I {...p}><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/></I>;
const IconGlobe      = p=><I {...p}><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15 15 0 0 1 0 20a15 15 0 0 1 0-20z"/></I>;
const IconHash       = p=><I {...p}><path d="M4 9h16M4 15h16M10 3 8 21M16 3l-2 18"/></I>;
const IconWand       = p=><I {...p}><path d="m3 21 18-18"/><path d="M14 7l3 3"/><path d="M5 6V3M19 19v3M3.5 4.5h3M17.5 20.5h3"/></I>;
const IconRocket     = p=><I {...p}><path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"/><path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/><path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"/><path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"/></I>;
const IconClock      = p=><I {...p}><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></I>;
const IconSun        = p=><I {...p}><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></I>;
const IconMoon       = p=><I {...p}><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></I>;

/* ── LOGO MARK — the real PowerPlus logo (orange square + white P-bolt) ── */
const LogoMark = ({ size=22 }) => (
  <img src={(window.pkwtDashboard||{}).iconUrl||''} alt="PowerPlus" width={size} height={size}
       className="inline-block flex-shrink-0"
       style={{ width:size, height:size, borderRadius:Math.max(4,size*0.22), boxShadow:'0 4px 18px rgba(255,101,0,.45)' }}/>
);

/* ── ADMIN URL HELPERS ── */
const D = window.pkwtDashboard || {};
const adminUrl  = (path='') => (D.adminUrl||'/wp-admin/') + path;
const siteUrl   = (path='') => (D.siteUrl||'/') + path;
const ppPage    = (page)    => adminUrl('admin.php?page=pkwt-settings' + (page ? '-'+page : ''));

/* ── REAL SETTINGS PERSISTENCE ──
   POSTs changed fields to wp_ajax_pkwt_dash_save; the server merges + sanitizes
   and returns the full saved settings object. */
async function ajaxPost(action, fields) {
  const body = new URLSearchParams();
  body.set('action', action);
  Object.entries(fields).forEach(([k, v]) => body.set(k, v));
  const res  = await fetch(D.ajaxUrl, { method: 'POST', credentials: 'same-origin', body });
  const json = await res.json();
  if (!json.success) throw new Error((json.data && json.data.message) || 'Request failed');
  return json.data;
}
const persistSettings = (patch) =>
  ajaxPost('pkwt_dash_save', { nonce: D.nonce, settings: JSON.stringify(patch) })
    .then((data) => data.settings);

/* One-click: install + activate the latest Elementor without leaving the dashboard. */
const installElementor = () =>
  ajaxPost('pkwt_install_elementor', { nonce: D.nonce });

/* Onboarding wizard apply / reset. */
const applyOnboarding = (choices) =>
  ajaxPost('pkwt_apply_onboarding', { nonce: D.nonce, choices: JSON.stringify(choices) });
const resetOnboarding = () =>
  ajaxPost('pkwt_reset_onboarding', { nonce: D.nonce });

/* Module option groups (ghost / svg / classic / duplicator) — separate WP options */
const persistModule = (group, patch) =>
  ajaxPost('pkwt_dash_save', { nonce: D.nonce, group, settings: JSON.stringify(patch) })
    .then((data) => data.settings);

function useModuleSettings(group, notify) {
  const [vals, setVals] = useState(() => (D.modules && D.modules[group]) || {});
  const saveMod = async (patch) => {
    if (!D.canManage) {
      notify && notify('You have view-only access — ask an administrator to change settings.', 'error');
      return;
    }
    setVals(v => ({ ...v, ...patch }));
    try {
      const fresh = await persistModule(group, patch);
      setVals(fresh);
      notify && notify('Settings saved');
    } catch (e) {
      notify && notify(e.message || 'Save failed', 'error');
    }
  };
  return [vals, saveMod];
}

/* ── HOOKS ── */
function useCountUp(target, duration=1200, start=true) {
  const [val,setVal] = useState(0);
  useEffect(()=>{
    if(!start) return;
    let raf, t0;
    const tick=(t)=>{
      if(t0==null) t0=t;
      const p=Math.min(1,(t-t0)/duration);
      setVal(target*(1-Math.pow(1-p,3)));
      if(p<1) raf=requestAnimationFrame(tick);
    };
    raf=requestAnimationFrame(tick);
    return()=>cancelAnimationFrame(raf);
  },[target,duration,start]);
  return val;
}

/* ── SHARED COMPONENTS ── */

function Toggle({ on, onChange, size='md' }) {
  const w=size==='sm'?36:44, h=size==='sm'?20:24, thumb=h-6;
  return (
    <button onClick={()=>onChange?.(!on)} className="toggle-track relative rounded-full flex-shrink-0"
      style={{ width:w, height:h,
               background:on?'#FF6500':'rgb(var(--c-border))',
               boxShadow:on?'0 0 16px rgba(255,101,0,.4),inset 0 0 0 1px rgba(255,255,255,.06)':'inset 0 0 0 1px rgba(0,0,0,.08)' }}
      aria-pressed={on}>
      <span className="toggle-thumb absolute top-1/2 -translate-y-1/2 rounded-full bg-white"
        style={{ width:thumb, height:thumb, left:on?w-thumb-3:3,
                 boxShadow:on?'0 0 8px rgba(255,255,255,.4),0 2px 4px rgba(0,0,0,.3)':'0 2px 4px rgba(0,0,0,.2)' }}/>
    </button>
  );
}

function Badge({ children, variant='orange' }) {
  const map={
    orange:{bg:'rgba(255,101,0,.12)',color:'#FF6500',border:'rgba(255,101,0,.3)'},
    green :{bg:'rgba(63,185,80,.12)', color:'#3fb950',border:'rgba(63,185,80,.3)'},
    grey  :{bg:'rgba(139,148,158,.10)',color:'#8b949e',border:'rgba(139,148,158,.22)'},
    red   :{bg:'rgba(248,81,73,.12)', color:'#f85149',border:'rgba(248,81,73,.3)'},
    purple:{bg:'rgba(139,92,246,.12)',color:'#8b5cf6',border:'rgba(139,92,246,.3)'},
  };
  const s=map[variant]||map.orange;
  return (
    <span className="inline-flex items-center gap-1 text-[11px] font-medium px-2 py-0.5 rounded-full label"
      style={{background:s.bg,color:s.color,border:`1px solid ${s.border}`}}>
      {children}
    </span>
  );
}

function Button({ children, variant='primary', icon:Icon, onClick, type='button', className='' }) {
  const ref=useRef(null);
  const handle=(e)=>{
    const el=ref.current;
    if(el){
      const rect=el.getBoundingClientRect();
      const dot=document.createElement('span');
      const sz=Math.max(rect.width,rect.height);
      dot.className='ripple';
      dot.style.width=dot.style.height=sz+'px';
      dot.style.left=(e.clientX-rect.left-sz/2)+'px';
      dot.style.top=(e.clientY-rect.top-sz/2)+'px';
      el.appendChild(dot);
      setTimeout(()=>dot.remove(),650);
    }
    onClick?.(e);
  };
  return (
    <button ref={ref} type={type} onClick={handle}
      className={`btn btn-${variant} inline-flex items-center justify-center gap-2 ${className}`}>
      {Icon && <Icon size={16}/>}{children}
    </button>
  );
}

function ElementorInstallButton({ notify, className='', label='Install Elementor' }) {
  const [busy,setBusy] = useState(false);
  const go = async () => {
    setBusy(true);
    try {
      const r = await installElementor();
      notify && notify((r && r.message) || 'Elementor installed', 'ok');
      setTimeout(()=>window.location.reload(), 700);
    } catch (e) {
      notify && notify(e.message || 'Elementor install failed', 'error');
      setBusy(false);
    }
  };
  return (
    <button onClick={go} disabled={busy}
      className={`btn btn-primary inline-flex items-center justify-center gap-2 ${className}`}
      style={{ opacity: busy?0.7:1 }}>
      {busy ? <IconRefresh size={14} className="animate-spin"/> : <IconImport size={14}/>}
      {busy ? 'Installing…' : label}
    </button>
  );
}

function Card({ title, subtitle, action, children, className='', delay=0 }) {
  return (
    <section className={`border border-border rounded-xl shadow-card anim-slide-up ${className}`}
      style={{ background:'rgb(var(--c-card))', animationDelay:`${delay}ms` }}>
      {(title||action)&&(
        <header className="flex items-center justify-between px-5 pt-5 pb-3">
          <div>
            {title&&<h3 className="text-[15px] font-semibold tracking-tight">{title}</h3>}
            {subtitle&&<p className="text-xs text-sub mt-0.5">{subtitle}</p>}
          </div>
          {action}
        </header>
      )}
      <div className="px-5 pb-5">{children}</div>
    </section>
  );
}

function Sparkline({ data, color='#FF6500', w=70, h=28 }) {
  const max=Math.max(...data), min=Math.min(...data), range=max-min||1;
  const stepX=w/(data.length-1);
  const pts=data.map((d,i)=>`${i*stepX},${h-((d-min)/range)*h}`).join(' ');
  const path=`M ${pts.replaceAll(' ',' L ')}`;
  return (
    <svg className="spark" width={w} height={h} viewBox={`0 0 ${w} ${h}`}>
      <defs>
        <linearGradient id="sg" x1="0" x2="0" y1="0" y2="1">
          <stop offset="0%" stopColor={color} stopOpacity=".3"/>
          <stop offset="100%" stopColor={color} stopOpacity="0"/>
        </linearGradient>
      </defs>
      <path d={`${path} L ${w},${h} L 0,${h} Z`} fill="url(#sg)" stroke="none"/>
      <path d={path} fill="none" stroke={color} strokeWidth="1.5" strokeLinejoin="round" strokeLinecap="round"/>
    </svg>
  );
}

function StatCard({ idx=0, icon:Icon, label, value, suffix='', spark }) {
  const [mounted,setMounted]=useState(false);
  useEffect(()=>{ const t=setTimeout(()=>setMounted(true),80+idx*100); return()=>clearTimeout(t); },[idx]);
  const v=useCountUp(value,1200,mounted);
  return (
    <div className="stat-card relative border border-border rounded-xl p-5 shadow-card anim-slide-up overflow-hidden"
         style={{ background:'rgb(var(--c-card))', animationDelay:`${idx*100}ms` }}>
      <div className="flex items-start justify-between mb-4">
        <div className="w-10 h-10 rounded-full flex items-center justify-center"
             style={{ background:'rgba(255,101,0,.12)', color:'#FF6500', boxShadow:'inset 0 0 0 1px rgba(255,101,0,.25)' }}>
          <Icon size={18}/>
        </div>
        {spark&&<Sparkline data={spark}/>}
      </div>
      <div className="text-[11px] label text-sub mb-1">{label}</div>
      <div className="text-3xl font-bold tracking-tight tabular-nums">
        {Number.isInteger(value)?Math.round(v):v.toFixed(1)}{suffix}
      </div>
    </div>
  );
}

function PageHeader({ title, subtitle, crumbs=[], actions }) {
  return (
    <header className="mb-6 anim-slide-up">
      {crumbs.length>0&&(
        <div className="flex items-center gap-1.5 text-xs text-sub mb-3">
          {crumbs.map((c,i)=>(
            <React.Fragment key={i}>
              <span className={i===crumbs.length-1?'font-medium text-fg':''}>{c}</span>
              {i<crumbs.length-1&&<IconChevronRight size={12}/>}
            </React.Fragment>
          ))}
        </div>
      )}
      <div className="flex items-end justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold tracking-tight">{title}</h1>
          {subtitle&&<p className="text-sm text-sub mt-1">{subtitle}</p>}
        </div>
        {actions&&<div className="flex items-center gap-2 flex-shrink-0">{actions}</div>}
      </div>
    </header>
  );
}

function ComingSoonOverlay({ label='Coming Soon', desc='This feature is under active development.' }) {
  return (
    <div className="coming-soon-overlay">
      <div className="text-center px-6">
        <div className="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4"
             style={{ background:'rgba(255,101,0,.12)', border:'1px solid rgba(255,101,0,.28)' }}>
          <IconRocket size={24} style={{ color:'#FF6500' }}/>
        </div>
        <div className="text-base font-bold mb-1">{label}</div>
        <div className="text-sm text-sub max-w-xs mx-auto leading-relaxed">{desc}</div>
        <div className="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold"
             style={{ background:'rgba(255,101,0,.12)', color:'#FF6500', border:'1px solid rgba(255,101,0,.28)' }}>
          <IconClock size={12}/> Coming Soon
        </div>
      </div>
    </div>
  );
}

/* ── THEME TOGGLE ── */
function ThemeToggle({ theme, onToggle }) {
  const dark = theme === 'dark';
  return (
    <button
      onClick={onToggle}
      title={dark ? 'Switch to light mode' : 'Switch to dark mode'}
      className="ease-out-soft relative w-14 h-7 rounded-full border border-border flex items-center"
      style={{
        background: dark ? 'rgba(255,101,0,.15)' : 'rgba(71,85,105,.08)',
        padding: '0 3px',
      }}
      aria-label="Toggle theme"
    >
      <span
        className="absolute rounded-full flex items-center justify-center transition-all duration-300"
        style={{
          width: 22, height: 22,
          background: dark ? '#FF6500' : '#FF6500',
          left: dark ? 'calc(100% - 25px)' : '3px',
          boxShadow: '0 2px 6px rgba(255,101,0,.45)',
        }}
      >
        {dark
          ? <IconMoon size={12} style={{ color:'#fff' }}/>
          : <IconSun  size={12} style={{ color:'#fff' }}/>
        }
      </span>
    </button>
  );
}

/* ── TOP BAR ── */
function TopBar({ onSearchClick, theme, onThemeChange }) {
  return (
    <div className="border-b border-border"
         style={{ background:'rgb(var(--c-bg))', position:'sticky', top:0, zIndex:20 }}>
      <div className="flex items-center h-12 px-4">
        {/* Left: WP nav links */}
        <div className="flex items-center gap-1 text-sub">
          <a href={adminUrl()} className="ease-out-soft hover:text-fg w-8 h-8 rounded-md flex items-center justify-center" title="WordPress Dashboard">
            <svg viewBox="0 0 20 20" width="18" height="18" fill="currentColor">
              <path d="M10 0a10 10 0 1 0 10 10A10 10 0 0 0 10 0zM1.43 10A8.57 8.57 0 0 1 2.94 5.2L7.05 16.5A8.58 8.58 0 0 1 1.43 10zm8.57 8.57a8.6 8.6 0 0 1-2.4-.34l2.55-7.4L12.77 18a.8.8 0 0 0 .06.12 8.59 8.59 0 0 1-2.83.45zm1.18-12.6c.51-.03.97-.08.97-.08.46-.05.4-.73-.05-.7 0 0-1.36.1-2.25.1-.83 0-2.23-.1-2.23-.1-.46-.03-.51.67-.05.7 0 0 .44.05.89.08l1.3 3.56-1.83 5.5L4.88 5.97c.5-.03.96-.08.96-.08.46-.05.4-.73-.05-.7 0 0-1.36.1-2.24.1l-.55-.01a8.57 8.57 0 0 1 12.95-1.6c-.03 0-.07 0-.1-.01-.83 0-1.42.72-1.42 1.5 0 .69.4 1.28.83 1.97.32.56.7 1.27.7 2.3 0 .72-.27 1.55-.63 2.7l-.82 2.75-2.97-8.84zM10 18.57a8.58 8.58 0 0 1-2.4-.34l2.55-7.4 2.65 7.27a.78.78 0 0 0 .07.14 8.61 8.61 0 0 1-2.87.33z"/>
            </svg>
          </a>
          <a href={siteUrl()} target="_blank" rel="noopener" className="ease-out-soft hover:text-fg hidden sm:flex items-center gap-1 text-xs text-sub px-1" title="View site">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 0 1 0 20 15 15 0 0 1 0-20z"/></svg>
            My Site
          </a>
        </div>
        <div className="flex-1"/>
        {/* Center: Logo */}
        <div className="hidden md:flex items-center gap-2 absolute left-1/2 -translate-x-1/2">
          <LogoMark size={20}/>
          <span className="font-semibold text-sm tracking-tight">PowerPlus</span>
          <span className="text-[10px] label text-sub border border-border rounded-full px-2 py-0.5">v3.5.8.1</span>
        </div>
        {/* Right: actions */}
        <div className="flex items-center gap-1.5">
          <button onClick={onSearchClick}
            className="ease-out-soft hover:text-fg text-sub hidden sm:flex items-center gap-2 border border-border rounded-md px-2.5 h-8 mr-1"
            style={{ background:'rgb(var(--c-card))' }}>
            <IconSearch size={14}/><span className="text-xs">Search</span>
            <span className="text-[10px] px-1 py-0.5 border border-border rounded text-sub/80">⌘K</span>
          </button>
          <ThemeToggle theme={theme} onToggle={()=>onThemeChange(t=>t==='dark'?'light':'dark')}/>
          <button className="ease-out-soft hover:text-fg text-sub w-8 h-8 rounded-md flex items-center justify-center relative ml-0.5">
            <IconBell size={16}/><span className="absolute top-1.5 right-1.5 w-1.5 h-1.5 bg-brand rounded-full anim-pulse"/>
          </button>
          <div className="w-px h-5 bg-border mx-1"/>
          <div className="flex items-center gap-2 pl-1 pr-2 h-8 rounded-md hover:bg-card ease-out-soft cursor-pointer">
            <div className="text-xs text-sub hidden sm:block">Howdy, <span className="font-medium text-fg">Admin</span></div>
            <div className="w-6 h-6 rounded-full bg-gradient-to-br from-brand to-brandDark text-white text-[10px] font-bold flex items-center justify-center">A</div>
          </div>
        </div>
      </div>
      <div className="h-px w-full relative overflow-hidden">
        <div className="absolute inset-0 topbar-pulse"
             style={{ background:'linear-gradient(90deg,transparent 0%,rgba(255,101,0,.3) 30%,#FF6500 50%,rgba(255,101,0,.3) 70%,transparent 100%)' }}/>
      </div>
    </div>
  );
}

/* ── SIDEBAR ── */
const NAV = [
  { id:'dashboard',     label:'Dashboard',         icon:IconDashboard },
  // group: Login & Design
  { id:'login',         label:'Login Pages',       icon:IconLogin,   group:'Login & Design' },
  { id:'redirects',     label:'Login URL',         icon:IconLock,    group:'Login & Design' },
  { id:'branding',      label:'Branding',          icon:IconPalette, group:'Login & Design' },
  // group: Features
  { id:'duplicator',    label:'Page Duplicator',   icon:IconCopy,               group:'Features' },
  { id:'widgets',       label:'Elementor Widgets', icon:IconBolt,    badge:'12', group:'Features' },
  { id:'security',      label:'Security',          icon:IconShield,  badge:'NEW',badgeVariant:'green', group:'Features' },
  { id:'svg-upload',    label:'SVG Upload',        icon:IconUpload,             group:'Features' },
  { id:'ghost-mode',    label:'Ghost Mode',        icon:IconEye,                group:'Features' },
  { id:'classic-editor',label:'Classic Editor',    icon:IconEdit,               group:'Features' },
  // group: System
  { id:'settings',      label:'Settings',          icon:IconSettings,           group:'System' },
  { id:'compatibility', label:'Compatibility',     icon:IconGrid,               group:'System' },
  { id:'import-export', label:'Import / Export',   icon:IconImport,             group:'System' },
];

function Sidebar({ current, onNavigate, collapsed }) {
  /* Build ordered groups from NAV */
  const groupOrder = [];
  const groups = {};
  NAV.forEach(n => {
    const g = n.group || '';
    if (!groups[g]) { groups[g] = []; groupOrder.push(g); }
    groups[g].push(n);
  });

  return (
    <aside className="border-r border-border flex flex-col flex-shrink-0"
      style={{ width:collapsed?64:232, transition:'width .25s cubic-bezier(.4,0,.2,1)', minHeight:'calc(100vh - 81px)', background:'rgb(var(--c-sidebar))' }}>
      {/* Header */}
      <div className="p-4 flex items-center gap-2.5 flex-shrink-0">
        <LogoMark size={32}/>
        {!collapsed&&(
          <div className="leading-tight min-w-0">
            <div className="text-sm font-bold tracking-tight truncate">PowerPlus</div>
            <div className="text-[10px] label text-sub truncate">All-in-One Toolkit</div>
          </div>
        )}
      </div>
      {/* Nav — scrollable */}
      <nav className="px-2 flex flex-col overflow-y-auto flex-1" style={{ gap:0 }}>
        {groupOrder.map(g => (
          <div key={g||'main'}>
            {g && !collapsed && (
              <div className="px-2 pt-3 pb-1 text-[10px] label text-sub tracking-widest">{g.toUpperCase()}</div>
            )}
            {g && collapsed && <div className="my-1 mx-2 border-t border-border opacity-40"/>}
            {groups[g].map(n => {
              const active = current === n.id;
              const Icon = n.icon;
              return (
                <button key={n.id} onClick={() => onNavigate(n.id)}
                  className={`nav-item ease-out-soft relative flex items-center gap-2.5 rounded-lg ${collapsed?'h-10 justify-center':'h-9 px-3'} text-[13px] font-medium ${active?'text-fg':'text-sub hover:text-fg'}`}
                  style={{ background:active?'rgba(255,101,0,.09)':'transparent', marginBottom:2 }}
                  title={collapsed ? n.label : undefined}>
                  {active && <span key={n.id+'-bar'} className="nav-active-bar"/>}
                  <span className={`nav-icon flex-shrink-0 ${active?'text-brand':''}`}><Icon size={17}/></span>
                  {!collapsed && (
                    <>
                      <span className="flex-1 text-left truncate">{n.label}</span>
                      {n.badge && <Badge variant={n.badgeVariant||'orange'}>{n.badge}</Badge>}
                    </>
                  )}
                </button>
              );
            })}
          </div>
        ))}
      </nav>
      {/* Footer */}
      <div className="border-t border-border px-3 py-3 relative flex-shrink-0">
        {!collapsed?(
          <div className="flex items-center gap-2 relative z-10">
            <LogoMark size={22}/>
            <div className="text-[11px] text-sub leading-tight">
              <div className="font-medium text-fg">PowerPlus</div>
              <div>v{(window.pkwtDashboard||{}).version||'3.5.8.1'} · Free</div>
            </div>
          </div>
        ):(
          <div className="flex justify-center"><LogoMark size={22}/></div>
        )}
        <div className="absolute -bottom-6 -left-6 w-32 h-32 rounded-full pointer-events-none"
             style={{ background:'radial-gradient(circle,rgba(255,101,0,.12),transparent 70%)' }}/>
      </div>
    </aside>
  );
}

/* ── PAGES ── */

function DashboardPage({ settings={}, save, onNavigate, notify }) {
  const n = (k) => Number(settings[k]) || 0;
  const pages = D.pages || {};
  const authPagesSet = Object.values(pages).filter(p=>p&&p.id).length;
  const score = Math.max(0, Math.min(100, 40
    + (n('enable_rate_limiting') ? 20 : 0)
    + (n('block_default_wp_auth') ? 20 : 0)
    + (settings.pkwt_custom_login_url ? 10 : 0)
    + (n('settings_activity_log') ? 10 : 0)
    - (n('admin_test_mode') ? 10 : 0)));
  const stats=[
    { icon:IconUser,   label:'Auth Pages',     value:authPagesSet, suffix:' / 4 Set', spark:[0,1,1,2,2,3,3,authPagesSet||1] },
    { icon:IconShield, label:'Security Score', value:score,        suffix:'/100',     spark:[40,45,55,60,70,80,90,score||40] },
    { icon:IconBolt,   label:'Widgets Ready',  value:12,           suffix:' Widgets', spark:[8,8,9,10,10,11,12,12] },
    { icon:IconLock,   label:'Max Attempts',   value:n('max_attempts')||5, suffix:` / ${n('lockout_minutes')||15}min lock`, spark:[5,5,5,5,5,5,5,5] },
  ];
  const quickActions=[
    { icon:IconLogin,  label:'Edit Login Page',   hint:'Open your login page in Elementor.', href: (pages.login&&pages.login.editUrl)||'' , go: (pages.login&&pages.login.editUrl)?null:'login' },
    { icon:IconLock,   label:'Login URL',         hint:'Hide login + redirect blocked visitors.', go:'redirects' },
    { icon:IconShield, label:'Security Settings', hint:'Rate limiting & endpoint blocking.', go:'security' },
    { icon:IconCopy,   label:'Duplicate a Page',  hint:'One-click clone from the Pages list.', href: adminUrl('edit.php?post_type=page') },
  ];
  const healthy = D.elementor && n('enabled');
  return (
    <div className="anim-page">
      <PageHeader title="Dashboard" subtitle="Overview of every PowerPlus module — at a glance."
        crumbs={['PowerPlus','Dashboard']}
        actions={<Button variant="primary" icon={IconLogin} onClick={()=>onNavigate&&onNavigate('login')}>Login Pages</Button>}/>
      {/* Health banner — real state */}
      <div className="relative overflow-hidden border border-border rounded-xl px-5 py-4 mb-6 anim-slide-up flex items-center gap-4"
           style={{ background: healthy?'linear-gradient(90deg,rgba(63,185,80,.10),rgba(255,101,0,.05) 60%,transparent)':'linear-gradient(90deg,rgba(248,81,73,.10),transparent 60%)', animationDelay:'20ms' }}>
        <div className="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0"
             style={{ background:healthy?'rgba(63,185,80,.15)':'rgba(248,81,73,.15)', color:healthy?'#3fb950':'#f85149' }}>
          {healthy?<IconCheck size={18}/>:<IconX size={18}/>}
        </div>
        <div className="flex-1 min-w-0">
          <div className="text-sm font-semibold">{healthy ? 'All systems ready' : (!D.elementor ? 'Elementor is not active' : 'PowerPlus modules are disabled')}</div>
          <div className="text-xs text-sub">
            {D.elementor ? 'Elementor detected' : 'Widgets & templates need Elementor'} · PowerPlus v{D.version||''}
            {!n('enabled') && ' · master switch is off'}
          </div>
        </div>
        {!D.elementor && (
          <ElementorInstallButton notify={notify} className="text-xs flex-shrink-0" />
        )}
      </div>
      {/* Flagship: auto-update all plugins */}
      <div className="relative overflow-hidden border rounded-xl px-5 py-4 mb-6 anim-slide-up flex items-center gap-4"
           style={{ borderColor:'rgba(255,101,0,.35)', background:'linear-gradient(90deg,rgba(255,101,0,.10),rgba(255,101,0,.02) 70%,transparent)', animationDelay:'30ms' }}>
        <div className="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 text-white" style={{ background:'linear-gradient(135deg,#FF6500,#cc5200)', boxShadow:'0 4px 14px rgba(255,101,0,.4)' }}>
          <IconRefresh size={20}/>
        </div>
        <div className="flex-1 min-w-0">
          <div className="text-sm font-semibold flex items-center gap-2">Auto-update all plugins <Badge variant="orange">Flagship</Badge></div>
          <div className="text-xs text-sub mt-0.5">One switch to keep every plugin on this site automatically updated to its latest version.</div>
        </div>
        <Toggle on={!!n('auto_update_all_plugins')} onChange={v=>save&&save({ auto_update_all_plugins: v?1:0 })}/>
      </div>
      {/* Stats */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {stats.map((s,i)=><StatCard key={s.label} idx={i} {...s}/>)}
      </div>
      {/* Quick Actions + Status */}
      <div className="grid grid-cols-1 lg:grid-cols-5 gap-4 mb-6">
        <Card title="Quick Actions" subtitle="Common workflows, one tap away." className="lg:col-span-3" delay={500}>
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
            {quickActions.map((a)=>{
              const inner = (
                <>
                  <span className="qa-fill"/>
                  <span className="relative w-1 h-9 rounded-full bg-brand/60"/>
                  <span className="relative w-9 h-9 rounded-md flex items-center justify-center text-brand flex-shrink-0" style={{ background:'rgba(255,101,0,.10)' }}><a.icon size={18}/></span>
                  <span className="relative flex-1 min-w-0">
                    <span className="block text-sm font-semibold text-fg">{a.label}</span>
                    <span className="block text-xs text-sub mt-0.5">{a.hint}</span>
                  </span>
                  <span className="relative text-sub flex-shrink-0"><IconArrowRight size={16}/></span>
                </>
              );
              const cls = "qa relative overflow-hidden rounded-lg border border-border px-4 py-3.5 flex items-center gap-3 ease-out-soft text-left hover:border-brand/40 no-underline w-full";
              const sty = { background:'rgb(var(--c-bg))', textDecoration:'none' };
              return a.go
                ? <button key={a.label} onClick={()=>onNavigate&&onNavigate(a.go)} className={cls} style={sty}>{inner}</button>
                : <a key={a.label} href={a.href} className={cls} style={sty}>{inner}</a>;
            })}
          </div>
        </Card>
        <Card title="Plugin Status" subtitle="Real module switches — saved instantly." className="lg:col-span-2" delay={600}>
          <ul className="divide-y divide-border -mx-1">
            {[
              { k:'enabled',                label:'PowerPlus Modules', sub:'Master switch for the whole plugin.' },
              { k:'enable_rate_limiting',   label:'Rate Limiting',     sub:'Throttle brute-force login attempts.' },
              { k:'block_default_wp_auth',  label:'Endpoint Blocking', sub:'Turn away wp-login.php for guests.' },
              { k:'woocommerce_mode',       label:'WooCommerce Mode',  sub:'Use Woo account pages where present.' },
              { k:'hide_plugins_list',      label:'Stealth Listing',   sub:'Hide PowerPlus from the plugins list.' },
            ].map((row)=>(
              <li key={row.k} className="flex items-center justify-between gap-3 py-3 px-1">
                <div className="min-w-0">
                  <div className="text-sm font-medium">{row.label}</div>
                  <div className="text-xs text-sub truncate">{row.sub}</div>
                </div>
                <Toggle on={!!n(row.k)} onChange={v=>save&&save({ [row.k]: v?1:0 })}/>
              </li>
            ))}
          </ul>
        </Card>
      </div>
    </div>
  );
}

function LoginFormsPage({ onNavigate, notify }) {
  const pages = D.pages || {};
  const CARDS = [
    { type:'login',    title:'Login Page',          desc:'Your sign-in page.',               hue:['#0d1117','#FF6500'], icon:IconLogin },
    { type:'register', title:'Register Page',        desc:'New user sign-up page.',           hue:['#1a1f2e','#6366f1'], icon:IconUser },
    { type:'lost',     title:'Lost Password Page',   desc:'Password recovery request form.',  hue:['#13181f','#3fb950'], icon:IconKey },
    { type:'reset',    title:'Reset Password Page',  desc:'New-password form (token-gated).', hue:['#2a1f1a','#cc5200'], icon:IconRefresh },
  ];
  return (
    <div className="anim-page">
      <PageHeader title="Login Pages" subtitle="One page each for login, register, lost &amp; reset password — design them in Elementor."
        crumbs={['PowerPlus','Login Pages']}
        actions={<Button variant="primary" icon={IconLock} onClick={()=>onNavigate&&onNavigate('redirects')}>Login URL &amp; Security</Button>}/>

      {!D.elementor && (
        <div className="mb-5 p-4 rounded-lg text-sm flex items-center gap-3"
             style={{ background:'rgba(248,81,73,.08)', border:'1px solid rgba(248,81,73,.3)' }}>
          <IconShield size={16} style={{ color:'#f85149', flexShrink:0 }}/>
          <span>Elementor is needed to design these pages.</span>
          <span className="ml-auto"><ElementorInstallButton notify={notify} className="text-xs"/></span>
        </div>
      )}

      <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
        {CARDS.map((c,i)=>{
          const p = pages[c.type] || {};
          const exists = !!p.id;
          return (
            <div key={c.type} className="border border-border rounded-xl overflow-hidden shadow-card anim-slide-up ease-out-soft hover:border-brand/50 hover:-translate-y-1 relative"
                 style={{ background:'rgb(var(--c-card))', animationDelay:`${i*70}ms` }}>
              <div className="h-28 relative overflow-hidden flex items-center justify-center" style={{ background:`linear-gradient(135deg,${c.hue[0]},${c.hue[1]})` }}>
                <c.icon size={32} className="text-white/80"/>
                {exists && p.status==='publish' && <div className="absolute top-3 right-3"><Badge variant="green"><span className="w-1.5 h-1.5 rounded-full bg-ok inline-block anim-pulse"/> Live</Badge></div>}
                {exists && p.status!=='publish' && <div className="absolute top-3 right-3"><Badge variant="orange">{p.status}</Badge></div>}
              </div>
              <div className="p-4">
                <div className="text-sm font-semibold">{c.title}</div>
                <div className="text-xs text-sub mt-0.5">{exists ? <>Page: <span className="text-fg font-medium">{p.title}</span></> : c.desc}</div>
                <div className="flex items-center gap-2 mt-4">
                  {exists && D.elementor && (
                    <a className="btn btn-ghost text-xs flex-1 inline-flex items-center justify-center gap-1.5" style={{ padding:'7px 10px' }} href={p.editUrl}>
                      <IconEdit size={13}/> Edit in Elementor
                    </a>
                  )}
                  {exists && (
                    <a className="btn btn-quiet text-xs inline-flex items-center justify-center gap-1.5" style={{ padding:'7px 10px' }} href={p.viewUrl} target="_blank" rel="noopener">
                      <IconExternal size={13}/> View
                    </a>
                  )}
                  {!exists && (
                    <span className="text-xs text-sub">Not created yet — activate the plugin or run onboarding to create it.</span>
                  )}
                </div>
              </div>
            </div>
          );
        })}
      </div>
      <div className="mt-4 text-xs rounded-md p-3 leading-relaxed" style={{ background:'rgb(var(--c-card))', border:'1px solid rgb(var(--c-border))' }}>
        <span className="font-semibold">Tip: </span>
        each page already contains the matching PowerPlus widget — open it in Elementor and style it to match your brand. Set your secret login address on the <button className="text-brand font-medium" onClick={()=>onNavigate&&onNavigate('redirects')}>Login URL</button> page.
      </div>
    </div>
  );
}

function DuplicatorPage({ notify }) {
  const [m, saveMod] = useModuleSettings('duplicator', notify);
  const [suffix,setSuffix]=useState(m.title_suffix || '(Copy)');
  useEffect(()=>{ setSuffix(m.title_suffix || '(Copy)'); }, [m.title_suffix]);
  return (
    <div className="anim-page">
      <PageHeader title="Page Duplicator" subtitle="Clone posts, pages and products with a single click — settings save instantly."
        crumbs={['PowerPlus','Page Duplicator']}
        actions={<a className="btn btn-primary inline-flex items-center gap-2 text-sm font-semibold" style={{ padding:'10px 16px', textDecoration:'none' }} href={adminUrl('edit.php?post_type=page')}><IconCopy size={16}/> Go to Pages</a>}/>
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        <Card title="Module" subtitle="Master switch." delay={0}>
          <div className="flex items-start justify-between gap-3">
            <div>
              <div className="text-sm font-medium">Enable post duplicator</div>
              <div className="text-xs text-sub mt-0.5">Adds a "Duplicate" row action across posts, pages, products and custom post types.</div>
            </div>
            <Toggle on={!!Number(m.enabled)} onChange={v=>saveMod({ enabled: v?1:0 })}/>
          </div>
          <div className="mt-4 text-xs rounded-md p-3 leading-relaxed" style={{ background:'rgba(255,101,0,.07)', border:'1px solid rgba(255,101,0,.2)' }}>
            <span className="font-semibold text-brand">How to use: </span>
            hover any post or page in its list table and click <span className="font-medium">Duplicate</span>.
          </div>
        </Card>
        <Card title="Behavior" subtitle="What happens when you click Duplicate." delay={80}>
          <label className="block text-[11px] label text-sub mb-1">Title suffix</label>
          <input className="ip" value={suffix} onChange={(e)=>setSuffix(e.target.value)} onBlur={()=>saveMod({ title_suffix: suffix })}/>
          <div className="flex items-center justify-between mt-4"><span className="text-sm">Show row action link</span><Toggle on={!!Number(m.enable_row_action)} onChange={v=>saveMod({ enable_row_action: v?1:0 })}/></div>
          <div className="flex items-center justify-between mt-2"><span className="text-sm">Show Elementor editor button</span><Toggle on={!!Number(m.enable_elementor_button)} onChange={v=>saveMod({ enable_elementor_button: v?1:0 })}/></div>
        </Card>
      </div>
    </div>
  );
}

function ScoreRing({ value }) {
  const size=140, r=56, cir=2*Math.PI*r;
  const v=useCountUp(value,1500);
  const off=cir-(v/100)*cir;
  return (
    <div className="relative">
      <svg width={size} height={size} viewBox={`0 0 ${size} ${size}`}>
        <circle cx={size/2} cy={size/2} r={r} stroke="rgb(var(--c-border))" strokeWidth="10" fill="none"/>
        <circle cx={size/2} cy={size/2} r={r} stroke="url(#g1)" strokeWidth="10" fill="none"
                strokeLinecap="round" strokeDasharray={cir} strokeDashoffset={off}
                transform={`rotate(-90 ${size/2} ${size/2})`} style={{ transition:'stroke-dashoffset .5s' }}/>
        <defs><linearGradient id="g1" x1="0" x2="1"><stop offset="0" stopColor="#3fb950"/><stop offset="1" stopColor="#FF6500"/></linearGradient></defs>
      </svg>
      <div className="absolute inset-0 flex flex-col items-center justify-center">
        <div className="text-3xl font-bold tabular-nums">{Math.round(v)}</div>
        <div className="text-[10px] label text-sub">/ 100</div>
      </div>
    </div>
  );
}

function SecurityPage({ settings={}, save }) {
  const n = (k) => Number(settings[k]) || 0;
  const [attempts,setAttempts] = useState(String(settings.max_attempts ?? 5));
  const [lockout,setLockout]   = useState(String(settings.lockout_minutes ?? 15));
  const [allowlist,setAllowlist] = useState(settings.ip_allowlist || '');
  useEffect(()=>{ setAttempts(String(settings.max_attempts ?? 5)); }, [settings.max_attempts]);
  useEffect(()=>{ setLockout(String(settings.lockout_minutes ?? 15)); }, [settings.lockout_minutes]);
  useEffect(()=>{ setAllowlist(settings.ip_allowlist || ''); }, [settings.ip_allowlist]);

  const saveLimits = () => save({ max_attempts: parseInt(attempts,10)||5, lockout_minutes: parseInt(lockout,10)||15 });

  /* Rough live score from real config */
  const score = 40
    + (n('enable_rate_limiting') ? 20 : 0)
    + (n('block_default_wp_auth') ? 20 : 0)
    + (settings.pkwt_custom_login_url ? 10 : 0)
    + (n('settings_activity_log') ? 10 : 0)
    - (n('admin_test_mode') ? 10 : 0);

  return (
    <div className="anim-page">
      <PageHeader title="Security" subtitle="Brute-force protection and login hardening — changes save instantly."
        crumbs={['PowerPlus','Security']}/>
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div className="lg:col-span-1 border border-border rounded-xl p-6 shadow-card anim-slide-up flex flex-col items-center justify-center relative overflow-hidden"
             style={{ background:'rgb(var(--c-card))' }}>
          <div className="absolute -top-12 -right-12 w-48 h-48 rounded-full" style={{ background:'radial-gradient(circle,rgba(63,185,80,.15),transparent 65%)' }}/>
          <ScoreRing value={Math.max(0,Math.min(100,score))}/>
          <div className="mt-3 text-sm font-semibold">Security Score</div>
          <div className="text-xs text-sub">{score>=80?'Excellent — keep it up.':score>=60?'Good — room to harden.':'Enable protections below.'}</div>
          <div className="flex gap-2 mt-4">
            {n('enable_rate_limiting')
              ? <Badge variant="green"><IconCheck size={10}/> Lockout armed</Badge>
              : <Badge variant="red">Rate limiting off</Badge>}
            {n('admin_test_mode') ? <Badge variant="orange">Test mode on</Badge> : null}
          </div>
        </div>
        <Card title="Login Protection" subtitle="Throttle brute-force attempts." className="lg:col-span-1" delay={80}>
          <div className="flex items-center justify-between mb-4">
            <span className="text-sm">Enable rate limiting</span>
            <Toggle on={!!n('enable_rate_limiting')} onChange={v=>save({ enable_rate_limiting: v?1:0 })}/>
          </div>
          <div className="grid grid-cols-2 gap-3">
            <div><label className="block text-[11px] label text-sub mb-1">Max attempts (1–20)</label><input className="ip" value={attempts} onChange={e=>setAttempts(e.target.value)} onBlur={saveLimits} inputMode="numeric"/></div>
            <div><label className="block text-[11px] label text-sub mb-1">Lockout min. (5–1440)</label><input className="ip" value={lockout} onChange={e=>setLockout(e.target.value)} onBlur={saveLimits} inputMode="numeric"/></div>
          </div>
          <div className="mt-4 text-[11px] text-sub border border-border rounded-md p-3" style={{ background:'rgb(var(--c-bg))' }}>
            Values save automatically when you leave a field.
          </div>
        </Card>
        <Card title="Endpoint Blocking" subtitle="Turn away unauthenticated wp-login.php requests." className="lg:col-span-1" delay={160}>
          <div className="flex items-center justify-between mb-4">
            <span className="text-sm">Block native endpoints</span>
            <Toggle on={!!n('block_default_wp_auth')} onChange={v=>save({ block_default_wp_auth: v?1:0 })}/>
          </div>
          <div className="text-xs rounded-md p-3 leading-relaxed" style={{ background:'rgba(255,101,0,.08)', border:'1px solid rgba(255,101,0,.25)' }}>
            <div className="font-semibold text-brand mb-1 label">Heads up</div>
            Make sure your custom login page works before enabling this, or you may lock yourself out.
          </div>
        </Card>
      </div>
      <Card title="IP Allow-list" subtitle="These IPs are never rate-limited or locked out — your escape hatch." delay={220}>
        <label className="block text-[11px] label text-sub mb-1">Allowed IPs / CIDR ranges (one per line or comma-separated)</label>
        <textarea className="ip" rows={3} value={allowlist} onChange={e=>setAllowlist(e.target.value)}
          onBlur={()=>save({ ip_allowlist: allowlist })}
          placeholder={"203.0.113.4\n198.51.100.0/24"} style={{ fontFamily:'monospace', resize:'vertical' }}/>
        <div className="mt-2 text-[11px] text-sub">Add your office/admin IP here so a lockout can never lock you out. Saves when you leave the field.</div>
      </Card>
      <Card title="Security Operations" subtitle="Defensive features — each toggle saves immediately." delay={240}>
        <ul className="divide-y divide-border">
          {[
            { k:'security_dashboard_enabled', label:'Enable security dashboard',      sub:'Pin the security widget to the WP Dashboard.' },
            { k:'settings_activity_log',      label:'Settings activity logging',      sub:'Audit every settings change with IP + user.' },
            { k:'admin_test_mode',            label:'Test mode (administrators only)',sub:'Disable lockouts for site administrators while testing.' },
          ].map((r)=>(
            <li key={r.k} className="flex items-center justify-between gap-3 py-3.5">
              <div><div className="text-sm font-medium">{r.label}</div><div className="text-xs text-sub">{r.sub}</div></div>
              <Toggle on={!!n(r.k)} onChange={v=>save({ [r.k]: v?1:0 })}/>
            </li>
          ))}
        </ul>
      </Card>
    </div>
  );
}

/* ── WIDGETS PAGE — exact widget names from plugin PHP files ── */
function WidgetsPage() {
  /* These names match get_title() in each class-widget-*.php exactly */
  const widgets=[
    { name:'PowerPlus Login Form',     icon:IconLogin,    cat:'Forms',    desc:'Full custom login form with redirect, styling & validation.' },
    { name:'PowerPlus Register Form',  icon:IconUser,     cat:'Forms',    desc:'User registration with custom fields & role assignment.' },
    { name:'PowerPlus Auth Tabs',      icon:IconLayers,   cat:'Layout',   desc:'Tabbed Login / Register switcher in one widget.' },
    { name:'PowerPlus Auth Logo',      icon:IconBolt,     cat:'Branding', desc:'Branded logo area for login & register pages.' },
    { name:'PowerPlus Auth Message',   icon:IconActivity, cat:'UX',       desc:'Flash notice widget for auth success / error states.' },
    { name:'PowerPlus Lost Password',  icon:IconKey,      cat:'Forms',    desc:'Password recovery request form with email trigger.' },
    { name:'PowerPlus Reset Password', icon:IconRefresh,  cat:'Forms',    desc:'New password entry form (token-gated).' },
    { name:'PowerPlus CAPTCHA',        icon:IconShield,   cat:'Security', desc:'reCAPTCHA v2 / v3 on any auth form.' },
    { name:'PowerPlus Redirect Timer', icon:IconClock,    cat:'UX',       desc:'Countdown widget that auto-redirects after login.' },
    { name:'PowerPlus Social Login',   icon:IconGlobe,    cat:'Forms',    desc:'OAuth social login buttons (Google, Facebook, etc.).' },
    { name:'PowerPlus Terms & Privacy',icon:IconLock,     cat:'Legal',    desc:'Terms & Privacy acceptance checkbox for registration.' },
    { name:'PowerPlus Divider Text',   icon:IconHash,     cat:'Layout',   desc:'Styled "or" / "and" divider for auth forms.' },
  ];
  const [filter,setFilter]=useState('All');
  const cats=['All','Forms','Layout','Branding','UX','Security','Legal'];
  const shown=filter==='All'?widgets:widgets.filter(w=>w.cat===filter);
  const [widgetState,setWidgetState]=useState(()=>Object.fromEntries(widgets.map(w=>[w.name,true])));
  const openElementor = () => { window.location.href = adminUrl('post-new.php?post_type=elementor_library'); };
  return (
    <div className="anim-page">
      <PageHeader title="Elementor Widgets" subtitle="12 production-ready widgets — drop them into any Elementor page."
        crumbs={['PowerPlus','Elementor Widgets']}
        actions={<><Button variant="quiet" icon={IconGrid}>Categories</Button><Button variant="primary" icon={IconExternal} onClick={openElementor}>Open Elementor</Button></>}/>
      <div className="border border-border rounded-xl p-2 mb-6 anim-slide-up flex items-center gap-1 overflow-x-auto" style={{ background:'rgb(var(--c-card))' }}>
        {cats.map((c)=>(
          <button key={c} onClick={()=>setFilter(c)}
            className={`px-3 h-8 rounded-md text-xs font-medium ease-out-soft whitespace-nowrap ${filter===c?'bg-brand text-white':'text-sub hover:text-fg hover:bg-bg'}`}>
            {c}{c==='All'&&<span className="opacity-75 ml-1">12</span>}
          </button>
        ))}
      </div>
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        {shown.map((w,i)=>{
          const on=widgetState[w.name]??true;
          return (
            <div key={w.name} className="stat-card relative border border-border rounded-xl p-5 anim-slide-up overflow-hidden"
                 style={{ background:'rgb(var(--c-card))', animationDelay:`${i*60}ms` }}>
              <div className="flex items-start justify-between mb-4">
                <div className="w-11 h-11 rounded-lg flex items-center justify-center text-brand flex-shrink-0" style={{ background:'rgba(255,101,0,.10)' }}><w.icon size={20}/></div>
                {on?<Badge variant="green"><IconCheck size={10}/>Active</Badge>:<Badge variant="grey">Off</Badge>}
              </div>
              <div className="text-sm font-semibold">{w.name}</div>
              <div className="text-xs text-sub mt-0.5">{w.desc}</div>
              <div className="mt-4 flex items-center gap-2">
                <Toggle on={on} onChange={(v)=>setWidgetState(s=>({...s,[w.name]:v}))} size="sm"/>
                <span className="text-[11px] text-sub flex-1">{on?'Available in Elementor':'Hidden from panel'}</span>
                <button className="text-sub hover:text-fg ease-out-soft"><IconSettings size={14}/></button>
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
}

function BrandingPage({ settings={}, save }) {
  const b0 = settings.branding || {};
  const [b,setB] = useState({
    enabled: !!Number(b0.enabled),
    style_login: b0.style_login===undefined?true:!!Number(b0.style_login),
    logo_id: b0.logo_id||0, logo_link: b0.logo_link||'', logo_title: b0.logo_title||'',
    bg_color: b0.bg_color||'', form_bg: b0.form_bg||'', accent_color: b0.accent_color||'#FF6500',
    welcome_message: b0.welcome_message||'', hide_login_errors: !!Number(b0.hide_login_errors),
    admin_footer_text: b0.admin_footer_text||'',
    hide_admin_footer_version: !!Number(b0.hide_admin_footer_version), hide_wp_logo: !!Number(b0.hide_wp_logo),
  });
  const [logoUrl,setLogoUrl] = useState(b0.logo_url||'');

  const toPayload = (st) => ({
    enabled: st.enabled?1:0, style_login: st.style_login?1:0, logo_id: st.logo_id||0,
    logo_link: st.logo_link, logo_title: st.logo_title, bg_color: st.bg_color, form_bg: st.form_bg,
    accent_color: st.accent_color, welcome_message: st.welcome_message,
    hide_login_errors: st.hide_login_errors?1:0, admin_footer_text: st.admin_footer_text,
    hide_admin_footer_version: st.hide_admin_footer_version?1:0, hide_wp_logo: st.hide_wp_logo?1:0,
  });
  const saveAll = () => save({ branding: toPayload(b) });

  // Autosave: toggles/swatches/logo commit immediately; text inputs update locally and
  // commit on blur (debounced) so we don't POST on every keystroke.
  const commit = (next) => { setB(next); save({ branding: toPayload(next) }); };
  const set = (k)=>(v)=>commit({ ...b, [k]:v });            // toggles & swatches autosave now
  const setVal = (k)=>(e)=>setB(s=>({ ...s, [k]:e.target.value })); // text: local only…
  const blurSave = ()=>save({ branding: toPayload(b) });    // …committed on blur

  const pickLogo = () => {
    if (!(window.wp && window.wp.media)) { save({ branding: toPayload(b) }); return; }
    const frame = window.wp.media({ title:'Select login logo', button:{text:'Use logo'}, multiple:false, library:{ type:'image' } });
    frame.on('select', ()=>{
      const att = frame.state().get('selection').first().toJSON();
      commit({ ...b, logo_id: att.id });
      setLogoUrl((att.sizes&&att.sizes.medium&&att.sizes.medium.url)||att.url);
    });
    frame.open();
  };

  const SWATCHES=['#FF6500','#3fb950','#6366f1','#ec4899','#06b6d4','#0d1117'];

  return (
    <div className="anim-page">
      <PageHeader title="Branding" subtitle="White-label the native login screen and the WP admin chrome."
        crumbs={['PowerPlus','Branding']}
        actions={<Button variant="primary" icon={IconCheck} onClick={saveAll}>Save branding</Button>}/>

      <Card title="Branding" subtitle="Master switch for all white-label features." delay={0} className="mb-4">
        <div className="flex items-center justify-between">
          <div><div className="text-sm font-medium">Enable branding</div><div className="text-xs text-sub mt-0.5">Applies the login &amp; admin options below.</div></div>
          <Toggle on={b.enabled} onChange={set('enabled')}/>
        </div>
      </Card>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <Card title="Login Screen" subtitle="Style the native wp-login.php page." delay={80}>
          <div className="flex items-center justify-between mb-4">
            <span className="text-sm">Style the login screen</span>
            <Toggle on={b.style_login} onChange={set('style_login')} size="sm"/>
          </div>
          <label className="block text-[11px] label text-sub mb-1">Login logo</label>
          <div className="flex items-center gap-3 mb-3">
            <div className="w-16 h-12 rounded-md border border-border flex items-center justify-center overflow-hidden flex-shrink-0" style={{ background:'rgb(var(--c-bg))' }}>
              {logoUrl ? <img src={logoUrl} alt="" style={{ maxWidth:'100%', maxHeight:'100%' }}/> : <IconBox size={18} className="text-sub"/>}
            </div>
            <Button variant="quiet" icon={IconUpload} onClick={pickLogo}>Choose logo</Button>
            {b.logo_id ? <button className="text-xs text-sub hover:text-fg" onClick={()=>{commit({...b,logo_id:0});setLogoUrl('');}}>Remove</button> : null}
          </div>
          <label className="block text-[11px] label text-sub mb-1">Logo link URL</label>
          <input className="ip mb-3" value={b.logo_link} onChange={setVal('logo_link')} onBlur={blurSave} placeholder={siteUrl()}/>
          <div className="grid grid-cols-2 gap-3">
            <div>
              <label className="block text-[11px] label text-sub mb-1">Page background</label>
              <input className="ip" value={b.bg_color} onChange={setVal('bg_color')} onBlur={blurSave} placeholder="#0d1117"/>
            </div>
            <div>
              <label className="block text-[11px] label text-sub mb-1">Form background</label>
              <input className="ip" value={b.form_bg} onChange={setVal('form_bg')} onBlur={blurSave} placeholder="#ffffff"/>
            </div>
          </div>
          <label className="block text-[11px] label text-sub mb-1 mt-3">Accent color</label>
          <div className="flex items-center gap-2">
            <input className="ip" style={{ maxWidth:130 }} value={b.accent_color} onChange={setVal('accent_color')} onBlur={blurSave}/>
            <div className="flex gap-1.5">
              {SWATCHES.map(c=>(
                <button key={c} onClick={()=>commit({...b,accent_color:c})} title={c}
                  className="w-6 h-6 rounded-full flex-shrink-0 ease-out-soft" style={{ background:c, outline:b.accent_color===c?'2px solid rgb(var(--c-fg))':'none', outlineOffset:'1px' }}/>
              ))}
            </div>
          </div>
          <label className="block text-[11px] label text-sub mb-1 mt-3">Welcome message (shown above the form)</label>
          <textarea className="ip" rows={2} value={b.welcome_message} onChange={setVal('welcome_message')} onBlur={blurSave} placeholder="Welcome back — sign in to continue." style={{ resize:'vertical' }}/>
          <div className="flex items-center justify-between mt-4 pt-3 border-t border-border">
            <div><div className="text-sm">Generic login errors</div><div className="text-xs text-sub">Hide whether the username or password was wrong.</div></div>
            <Toggle on={b.hide_login_errors} onChange={set('hide_login_errors')} size="sm"/>
          </div>
        </Card>

        <div className="flex flex-col gap-4">
          <Card title="Live Preview" subtitle="Approximate login appearance." delay={160}>
            <div className="rounded-lg border border-border overflow-hidden" style={{ background: b.bg_color||'#0d1117' }}>
              <div className="p-6 flex flex-col items-center">
                <div className="mb-3 h-10 flex items-center justify-center">
                  {logoUrl ? <img src={logoUrl} alt="" style={{ maxHeight:40 }}/> : <LogoMark size={32}/>}
                </div>
                <div className="w-full max-w-[220px] rounded-md p-4" style={{ background: b.form_bg||'#ffffff' }}>
                  {b.welcome_message ? <div className="text-[11px] mb-2" style={{ color:'#374151' }}>{b.welcome_message}</div> : null}
                  <div className="h-7 rounded border mb-2" style={{ borderColor:'#e5e7eb', background:'#f9fafb' }}/>
                  <div className="h-7 rounded border mb-3" style={{ borderColor:'#e5e7eb', background:'#f9fafb' }}/>
                  <div className="h-8 rounded text-center text-[11px] font-semibold text-white flex items-center justify-center" style={{ background:b.accent_color||'#FF6500' }}>Log In</div>
                </div>
              </div>
            </div>
          </Card>
          <Card title="Admin Chrome" subtitle="White-label the WP admin." delay={240}>
            <label className="block text-[11px] label text-sub mb-1">Admin footer text (left)</label>
            <input className="ip mb-3" value={b.admin_footer_text} onChange={setVal('admin_footer_text')} onBlur={blurSave} placeholder="Maintained by Your Agency"/>
            <div className="flex items-center justify-between py-2.5 border-t border-border">
              <div><div className="text-sm">Hide WordPress version</div><div className="text-xs text-sub">Clears the version in the admin footer.</div></div>
              <Toggle on={b.hide_admin_footer_version} onChange={set('hide_admin_footer_version')} size="sm"/>
            </div>
            <div className="flex items-center justify-between py-2.5">
              <div><div className="text-sm">Hide WordPress logo</div><div className="text-xs text-sub">Removes the W logo from the admin bar.</div></div>
              <Toggle on={b.hide_wp_logo} onChange={set('hide_wp_logo')} size="sm"/>
            </div>
          </Card>
        </div>
      </div>
    </div>
  );
}

function SettingsPage({ settings={}, save, onNavigate }) {
  const n = (k) => Number(settings[k]) || 0;
  const [menuName,setMenuName]   = useState(settings.plugin_menu_name || '');
  const [supportUrl,setSupportUrl] = useState(settings.support_url || '');
  useEffect(()=>{ setMenuName(settings.plugin_menu_name || ''); }, [settings.plugin_menu_name]);
  useEffect(()=>{ setSupportUrl(settings.support_url || ''); }, [settings.support_url]);

  return (
    <div className="anim-page">
      <PageHeader title="Settings" subtitle="General plugin preferences — saved to the real plugin options."
        crumbs={['PowerPlus','Settings']}
        actions={<Button variant="primary" icon={IconCheck} onClick={()=>save({ plugin_menu_name:menuName, support_url:supportUrl })}>Save</Button>}/>
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        <Card title="Identity" subtitle="How PowerPlus appears in this WP admin." delay={0}>
          <label className="block text-[11px] label text-sub mb-1">Admin menu name</label>
          <input className="ip mb-3" value={menuName} onChange={e=>setMenuName(e.target.value)} placeholder="PowerPlus"/>
          <label className="block text-[11px] label text-sub mb-1">Support URL</label>
          <input className="ip" value={supportUrl} onChange={e=>setSupportUrl(e.target.value)} placeholder="https://…"/>
        </Card>
        <Card title="Modules" subtitle="Core behavior switches — saved instantly." delay={80}>
          <ul className="divide-y divide-border">
            {[
              { k:'enabled',           label:'Enable PowerPlus modules', sub:'Master switch for all functionality.' },
              { k:'woocommerce_mode',  label:'WooCommerce mode',         sub:'Defer to Woo account pages when present.' },
              { k:'hide_plugins_list', label:'Hide from plugins list',   sub:'Stealth-mode listing for client sites.' },
            ].map((r)=>(
              <li key={r.k} className="flex items-center justify-between py-3">
                <div><div className="text-sm font-medium">{r.label}</div><div className="text-xs text-sub">{r.sub}</div></div>
                <Toggle on={!!n(r.k)} onChange={v=>save({ [r.k]: v?1:0 })}/>
              </li>
            ))}
          </ul>
        </Card>
      </div>
      <Card title="Setup Wizard" subtitle="Re-run the guided onboarding any time." delay={160} className="mb-4">
        <div className="flex items-center justify-between gap-4">
          <div className="text-sm text-sub">Walk through the feature setup again, regenerate the login pages, or change which modules are enabled.</div>
          <Button variant="primary" icon={IconRocket} onClick={()=>window.pkwtOpenWizard&&window.pkwtOpenWizard()}>Run Setup Wizard</Button>
        </div>
      </Card>
      <Card title="Backup & Restore" subtitle="Everything import/export lives on its own page." delay={240}>
        <div className="flex items-center justify-between gap-4">
          <div className="text-sm text-sub">Export a JSON snapshot of all settings, restore one, or factory-reset the plugin.</div>
          <Button variant="ghost" icon={IconImport} onClick={()=>onNavigate&&onNavigate('import-export')}>Open Import / Export</Button>
        </div>
      </Card>
    </div>
  );
}

/* ── APP SHELL ── */
function TemplatesPage({ notify }) {
  const sets = D.templates || [];
  const PAGE_LABELS = { login:'Login', register:'Register', lost:'Lost Password', reset:'Reset Password' };
  /* importState[setSlug:pageType] = 'busy' | {edit_url, view_url} | {error} */
  const [imp,setImp] = useState({});

  const doImport = async (setSlug, pageType) => {
    const key = setSlug + ':' + pageType;
    setImp(s=>({ ...s, [key]:'busy' }));
    try {
      const { nonce } = await ajaxPost('pkwt_get_import_nonce', {});
      const data = await ajaxPost('pkwt_ajax_import_template', { nonce, set_slug:setSlug, page_type:pageType });
      setImp(s=>({ ...s, [key]:{ edit_url:data.edit_url, view_url:data.view_url } }));
      notify && notify(data.message || 'Template imported');
    } catch (e) {
      setImp(s=>({ ...s, [key]:{ error: e.message } }));
      notify && notify(e.message || 'Import failed', 'error');
    }
  };

  return (
    <div className="anim-page">
      <PageHeader title="Page Templates" subtitle="One-click import onto your auth pages — no reloads, instant edit links."
        crumbs={['PowerPlus','Page Templates']}/>
      {!D.elementor && (
        <div className="mb-5 p-4 rounded-lg text-sm flex items-center gap-3"
             style={{ background:'rgba(248,81,73,.08)', border:'1px solid rgba(248,81,73,.3)' }}>
          <IconShield size={16} style={{ color:'#f85149', flexShrink:0 }}/>
          <span>Elementor is not active — install &amp; activate it to import and edit templates.</span>
          <span className="ml-auto"><ElementorInstallButton notify={notify} className="text-xs" /></span>
        </div>
      )}
      <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
        {sets.map((t,i)=>(
          <div key={t.slug} className="border border-border rounded-xl overflow-hidden shadow-card anim-slide-up"
               style={{ background:'rgb(var(--c-card))', animationDelay:`${i*60}ms` }}>
            <div className="h-32 relative overflow-hidden" style={{ background:`linear-gradient(135deg,${t.color},${t.accent})` }}>
              <div className="absolute inset-4 grid grid-cols-2 gap-3">
                <div className="rounded-md bg-black/25 backdrop-blur-sm"/>
                <div className="rounded-md bg-white/15 backdrop-blur-sm p-3 flex flex-col gap-1.5">
                  <div className="h-2 w-12 rounded bg-white/50"/>
                  <div className="h-1.5 w-20 rounded bg-white/25 mt-1"/>
                  <div className="h-1.5 w-16 rounded bg-white/25"/>
                  <div className="mt-auto h-3 w-14 rounded" style={{ background:t.accent }}/>
                </div>
              </div>
            </div>
            <div className="p-4">
              <div className="text-sm font-semibold">{t.label}</div>
              <div className="text-xs text-sub mt-0.5 leading-relaxed">{t.description}</div>
              <div className="grid grid-cols-2 gap-2 mt-4">
                {(t.pages||[]).map(pt=>{
                  const key = t.slug + ':' + pt;
                  const st  = imp[key];
                  if (st && st.edit_url !== undefined) {
                    return (
                      <div key={pt} className="flex items-center gap-1.5 text-xs rounded-md px-2 py-2"
                           style={{ background:'rgba(63,185,80,.10)', border:'1px solid rgba(63,185,80,.3)' }}>
                        <IconCheck size={12} style={{ color:'#3fb950', flexShrink:0 }}/>
                        <span className="font-medium">{PAGE_LABELS[pt]}</span>
                        {st.edit_url && <a className="text-brand hover:underline ml-auto" href={st.edit_url}>Edit</a>}
                        {st.view_url && <a className="text-sub hover:text-fg" href={st.view_url} target="_blank" rel="noopener">View</a>}
                      </div>
                    );
                  }
                  return (
                    <button key={pt} disabled={st==='busy' || !D.elementor}
                      onClick={()=>doImport(t.slug, pt)}
                      className="btn btn-quiet text-xs justify-center"
                      style={{ padding:'8px 10px', opacity:(!D.elementor)?0.5:1 }}>
                      {st==='busy' ? 'Importing…' : <>{PAGE_LABELS[pt]}{st&&st.error?' — retry':''}</>}
                    </button>
                  );
                })}
              </div>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}

/* Reduce a stored URL/path to the part AFTER the install's home path.
   Critical for subdirectory / WordPress-Playground installs whose home URL carries a path
   prefix (e.g. /scope:.../) — without stripping it the slug accumulates that prefix on
   every save. `base` is the home URL (with trailing slash). */
function stripBase(v, base) {
  if (!v) return '';
  let s = String(v).replace(/^https?:\/\/[^/]+/i, '');   // drop scheme + host -> path
  s = s.replace(/^\/+|\/+$/g, '');
  if (base) {
    const bp = String(base).replace(/^https?:\/\/[^/]+/i, '').replace(/^\/+|\/+$/g, ''); // base path only
    if (bp && (s === bp || s.indexOf(bp + '/') === 0)) s = s.slice(bp.length);
  }
  return s.replace(/^\/+|\/+$/g, '');
}
/* After-login redirect may be multi-level — keep the whole relative path. */
function urlToSlug(v, base) { return stripBase(v, base); }
/* Login slug is single-level — take the LAST segment (also auto-heals contaminated values). */
function urlToLoginSlug(v, base) {
  const p = stripBase(v, base);
  const segs = p.split('/').filter(Boolean);
  return segs.length ? segs[segs.length - 1] : '';
}

/* WPS-style slug field: [ base url ][ slug input ][ / ].
   IMPORTANT: defined at MODULE scope (not inside a page component). If it were declared
   inside the component, every keystroke would create a new component identity and React
   would remount the <input>, destroying the cursor — the "loses focus after one char" bug. */
function SlugField({ base, value, onChange, onBlur, placeholder }) {
  return (
    <div className="flex items-stretch rounded-lg overflow-hidden border border-border" style={{ background:'rgb(var(--c-bg))' }}>
      <span className="flex items-center px-3 text-xs text-sub font-mono whitespace-nowrap" style={{ background:'rgba(0,0,0,.04)', borderRight:'1px solid rgb(var(--c-border))' }}>{base}</span>
      <input className="flex-1 min-w-0 bg-transparent px-3 py-2.5 text-sm" style={{ outline:'none', border:0, color:'rgb(var(--c-fg))' }}
        value={value} onChange={onChange} onBlur={onBlur} placeholder={placeholder} spellCheck={false}/>
      <span className="flex items-center px-3 text-xs text-sub font-mono" style={{ borderLeft:'1px solid rgb(var(--c-border))' }}>/</span>
    </div>
  );
}

function RedirectsPage({ settings={}, save, notify }) {
  const base = (siteUrl()||'/').replace(/\/+$/,'') + '/';
  const [loginSlug,setLoginSlug]   = useState(urlToLoginSlug(settings.pkwt_custom_login_url, base));
  const [redirect,setRedirect]     = useState(settings.login_blocked_redirect || '');
  const [afterLogin,setAfterLogin] = useState(urlToSlug(settings.after_login_redirect, base));
  const [saving,setSaving] = useState(false);

  useEffect(()=>{ setLoginSlug(urlToLoginSlug(settings.pkwt_custom_login_url, base)); }, [settings.pkwt_custom_login_url]);
  useEffect(()=>{ setRedirect(settings.login_blocked_redirect || ''); }, [settings.login_blocked_redirect]);
  useEffect(()=>{ setAfterLogin(urlToSlug(settings.after_login_redirect, base)); }, [settings.after_login_redirect]);

  const blocking = !!Number(settings.block_default_wp_auth);

  const saveAll = async () => {
    setSaving(true);
    await save({
      pkwt_custom_login_url: loginSlug.trim(),
      login_blocked_redirect: redirect.trim(),
      after_login_redirect: afterLogin.trim() ? base + afterLogin.trim().replace(/^\/+/,'') : '',
    });
    setSaving(false);
  };

  const liveLoginUrl = loginSlug.trim() ? base + loginSlug.trim().replace(/^\/+/,'') + '/' : '';

  return (
    <div className="anim-page">
      <PageHeader title="Login URL" subtitle="Hide your login behind a secret URL and control where blocked visitors go."
        crumbs={['PowerPlus','Login URL']}
        actions={<Button variant="primary" icon={IconCheck} onClick={saveAll}>{saving?'Saving…':'Save Changes'}</Button>}/>

      <Card title="Hide Login" subtitle="Change the login URL and block wp-login.php + wp-admin for visitors who aren't logged in." delay={0} className="mb-4">
        <label className="block text-[11px] label text-sub mb-1.5">Login URL</label>
        <SlugField base={base} value={loginSlug} onChange={e=>setLoginSlug(e.target.value)} onBlur={saveAll} placeholder="login"/>
        <div className="text-xs text-sub mt-1.5">
          {liveLoginUrl
            ? <>Your login is at <a className="text-brand" href={liveLoginUrl} target="_blank" rel="noopener">{liveLoginUrl}</a></>
            : <>Set a slug to move your login away from <code className="font-mono">wp-login.php</code>.</>}
        </div>

        <label className="block text-[11px] label text-sub mb-1.5 mt-5">Redirection URL</label>
        <SlugField base={base} value={redirect} onChange={e=>setRedirect(e.target.value)} onBlur={saveAll} placeholder="404"/>
        <div className="text-xs text-sub mt-1.5">Where to send anyone who hits <code className="font-mono">wp-login.php</code> or <code className="font-mono">wp-admin</code> while logged out. Leave as <code className="font-mono">404</code> to show a Not Found page.</div>

        <div className="flex items-center justify-between gap-3 mt-5 pt-4 border-t border-border">
          <div>
            <div className="text-sm font-medium">Protect wp-login.php &amp; wp-admin</div>
            <div className="text-xs text-sub mt-0.5">Turn away non-connected visitors from the default login &amp; admin URLs.</div>
          </div>
          <Toggle on={blocking} onChange={v=>save({ block_default_wp_auth: v?1:0 })}/>
        </div>

        <div className="mt-4 text-xs rounded-md p-3 leading-relaxed" style={{ background:'rgba(255,101,0,.08)', border:'1px solid rgba(255,101,0,.25)' }}>
          <span className="font-semibold text-brand">Heads up: </span>
          keep this login URL somewhere safe. If you forget it you can still recover by defining
          <code className="font-mono mx-1 px-1 rounded" style={{ background:'rgba(0,0,0,.08)' }}>POWERPLUS_RECOVERY_MODE</code>
          as <code className="font-mono">true</code> in <code className="font-mono">wp-config.php</code>.
        </div>
      </Card>

      <Card title="After-Login Redirect" subtitle="Where users land after a successful login (optional)." delay={120}>
        <label className="block text-[11px] label text-sub mb-1.5">Redirect URL</label>
        <SlugField base={base} value={afterLogin} onChange={e=>setAfterLogin(e.target.value)} onBlur={saveAll} placeholder="dashboard"/>
        <div className="text-xs text-sub mt-1.5">Leave blank to use the WordPress default (admin dashboard).</div>
      </Card>
    </div>
  );
}

function CompatibilityPage() {
  const features=[
    { id:'elementor',   label:'Elementor',          desc:'Registers PowerPlus widgets in the Elementor panel.',          on:true },
    { id:'woo',         label:'WooCommerce',         desc:'Duplicator support for product post types.',                   on:true },
    { id:'polylang',    label:'Polylang',            desc:'Clone pages with translated versions intact.',                 on:false },
    { id:'wpml',        label:'WPML',                desc:'Duplicate multi-language pages with WPML.',                    on:false },
    { id:'rankmath',    label:'Rank Math SEO',       desc:'Copy SEO meta when duplicating.',                              on:true },
    { id:'yoast',       label:'Yoast SEO',           desc:'Copy Yoast meta when duplicating.',                            on:false },
    { id:'classic',     label:'Classic Editor',      desc:'Enable Classic Editor compatibility mode.',                    on:false },
  ];
  const [state,setState]=useState(()=>Object.fromEntries(features.map(f=>[f.id,f.on])));
  return (
    <div className="anim-page">
      <PageHeader title="Compatibility" subtitle="Fine-tune how PowerPlus co-exists with other active plugins."
        crumbs={['PowerPlus','Compatibility']}
        actions={<Button variant="primary" icon={IconCheck}>Save</Button>}/>
      <Card title="Plugin Compatibility Layer" subtitle="Toggle integrations for plugins you have installed." delay={0}>
        <ul className="divide-y divide-border">
          {features.map(f=>(
            <li key={f.id} className="flex items-center justify-between gap-4 py-3.5">
              <div>
                <div className="text-sm font-medium">{f.label}</div>
                <div className="text-xs text-sub mt-0.5">{f.desc}</div>
              </div>
              <Toggle on={state[f.id]} onChange={v=>setState(s=>({...s,[f.id]:v}))}/>
            </li>
          ))}
        </ul>
      </Card>
    </div>
  );
}

function SvgUploadPage({ notify }) {
  const [m, saveMod] = useModuleSettings('svg', notify);
  const b = (k) => !!Number(m[k]);
  const [maxKb,setMaxKb] = useState(String(m.dpp_svg_max_size_kb || 512));
  useEffect(()=>{ setMaxKb(String(m.dpp_svg_max_size_kb || 512)); }, [m.dpp_svg_max_size_kb]);
  return (
    <div className="anim-page">
      <PageHeader title="SVG Upload" subtitle="Sanitized SVG uploads for the media library — settings save instantly."
        crumbs={['PowerPlus','SVG Upload']}/>
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <Card title="Enable SVG Uploads" subtitle="Disabled by default for security." delay={0}>
          <div className="flex items-start justify-between gap-3 mb-4">
            <div>
              <div className="text-sm font-medium">Allow SVG uploads</div>
              <div className="text-xs text-sub mt-0.5">Adds SVG to the allowed media MIME types. Every upload is sanitized server-side.</div>
            </div>
            <Toggle on={b('dpp_svg_enabled')} onChange={v=>saveMod({ dpp_svg_enabled: v?1:0 })}/>
          </div>
          <div className="flex items-start justify-between gap-3 mb-4">
            <div>
              <div className="text-sm font-medium">Media library previews</div>
              <div className="text-xs text-sub mt-0.5">Render SVG thumbnails in the media grid.</div>
            </div>
            <Toggle on={b('dpp_svg_preview')} onChange={v=>saveMod({ dpp_svg_preview: v?1:0 })}/>
          </div>
          <div className="flex items-start justify-between gap-3">
            <div>
              <div className="text-sm font-medium">Log blocked uploads</div>
              <div className="text-xs text-sub mt-0.5">Keep a record of SVGs rejected by the sanitizer.</div>
            </div>
            <Toggle on={b('dpp_svg_blocked_log')} onChange={v=>saveMod({ dpp_svg_blocked_log: v?1:0 })}/>
          </div>
        </Card>
        <Card title="Limits" subtitle="Upload constraints." delay={80}>
          <label className="block text-[11px] label text-sub mb-1">Max file size (KB)</label>
          <input className="ip" value={maxKb} onChange={e=>setMaxKb(e.target.value)} inputMode="numeric"
                 onBlur={()=>saveMod({ dpp_svg_max_size_kb: parseInt(maxKb,10)||512 })}/>
          <div className="mt-3 text-xs text-sub">Saves when you leave the field.</div>
        </Card>
        <Card title="Security notice" subtitle="" className="lg:col-span-2" delay={160}>
          <div className="flex items-start gap-3 p-4 rounded-lg" style={{ background:'rgba(255,101,0,.07)', border:'1px solid rgba(255,101,0,.2)' }}>
            <IconShield size={18} style={{ color:'#FF6500', flexShrink:0, marginTop:2 }}/>
            <div className="text-sm text-sub leading-relaxed">SVG files can contain embedded scripts. PowerPlus strips scripts, event handlers and external references on upload, but always review SVG files from untrusted sources before uploading.</div>
          </div>
        </Card>
      </div>
    </div>
  );
}

function GhostModePage({ notify }) {
  const [m, saveMod] = useModuleSettings('ghost', notify);
  const b = (k) => !!Number(m[k]);
  return (
    <div className="anim-page">
      <PageHeader title="Ghost Mode" subtitle="Hide WordPress fingerprints from your public source code — saves instantly."
        crumbs={['PowerPlus','Ghost Mode']}/>
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <Card title="Ghost Mode" subtitle="Master switch for all stealth features." delay={0}>
          <div className="flex items-start justify-between gap-3 mb-5">
            <div>
              <div className="text-sm font-medium">Enable Ghost Mode</div>
              <div className="text-xs text-sub mt-0.5">Activates the selected stealth options below.</div>
            </div>
            <Toggle on={b('dpp_ghost_enabled')} onChange={v=>saveMod({ dpp_ghost_enabled: v?1:0 })}/>
          </div>
          <ul className="divide-y divide-border">
            {[
              { k:'dpp_ghost_remove_generator',        l:'Remove generator meta',      s:'Strips the WordPress version tag from <head>.' },
              { k:'dpp_ghost_strip_version_urls',      l:'Strip ?ver= query strings',  s:'Removes version numbers from asset URLs.' },
              { k:'dpp_ghost_remove_emoji',            l:'Remove emoji scripts',       s:'Drops the wp-emoji loader and styles.' },
              { k:'dpp_ghost_disable_xmlrpc',          l:'Disable XML-RPC',            s:'Blocks the xmlrpc.php attack surface.' },
              { k:'dpp_ghost_hide_rest_users',         l:'Hide REST user listing',     s:'Blocks /wp-json/wp/v2/users enumeration.' },
              { k:'dpp_ghost_disable_author_archives', l:'Disable author archives',    s:'Prevents username discovery via /?author=N.' },
            ].map(r=>(
              <li key={r.k} className="flex items-center justify-between gap-4 py-3">
                <div><div className="text-sm font-medium">{r.l}</div><div className="text-xs text-sub">{r.s}</div></div>
                <Toggle on={b(r.k)} onChange={v=>saveMod({ [r.k]: v?1:0 })} size="sm"/>
              </li>
            ))}
          </ul>
        </Card>
        <Card title="What gets hidden" subtitle="Overview of active stealth rules." delay={80}>
          {[
            { icon:IconHash,  on:b('dpp_ghost_remove_generator'),        text:'WordPress version from meta generator tags' },
            { icon:IconGlobe, on:b('dpp_ghost_strip_version_urls'),      text:'Plugin asset version query strings (?ver=)' },
            { icon:IconEye,   on:b('dpp_ghost_hide_rest_users'),         text:'User enumeration via the REST API' },
            { icon:IconLock,  on:b('dpp_ghost_disable_xmlrpc'),          text:'XML-RPC remote access endpoint' },
            { icon:IconUser,  on:b('dpp_ghost_disable_author_archives'), text:'Author archive username discovery' },
          ].map(r=>(
            <div key={r.text} className="flex items-center gap-3 py-2.5 border-b border-border last:border-0">
              <span className="flex-shrink-0" style={{ color: r.on?'#3fb950':'rgb(var(--c-sub))' }}><r.icon size={15}/></span>
              <span className="text-sm text-sub flex-1">{r.text}</span>
              {r.on ? <Badge variant="green">on</Badge> : <Badge variant="grey">off</Badge>}
            </div>
          ))}
          <div className="mt-4 text-xs rounded-md p-3 leading-relaxed" style={{ background:'rgba(255,101,0,.07)', border:'1px solid rgba(255,101,0,.2)' }}>
            <span className="font-semibold text-brand">Note: </span>
            fingerprint hiding raises the bar for automated scanners but is not a substitute for keeping WordPress and plugins updated.
          </div>
        </Card>
      </div>
    </div>
  );
}

function ClassicEditorPage({ notify }) {
  const [m, saveMod] = useModuleSettings('classic', notify);
  const b = (k) => !!Number(m[k]);
  return (
    <div className="anim-page">
      <PageHeader title="Classic Editor" subtitle="Force the Classic Editor — settings save instantly."
        crumbs={['PowerPlus','Classic Editor']}/>
      <Card title="Classic Editor settings" subtitle="PowerPlus's built-in Classic Editor compatibility layer." delay={0}>
        <ul className="divide-y divide-border">
          {[
            { k:'dpp_classic_enabled',            l:'Enable Classic Editor mode',  s:'Replaces the block editor with the classic editing experience.' },
            { k:'dpp_classic_allow_user_choice',  l:'Allow users to switch',       s:'Let each user pick classic or block editor.' },
            { k:'dpp_classic_allow_admin_bypass', l:'Allow admin bypass',          s:'Administrators can still open the block editor when needed.' },
          ].map(r=>(
            <li key={r.k} className="flex items-center justify-between gap-4 py-3.5">
              <div><div className="text-sm font-medium">{r.l}</div><div className="text-xs text-sub mt-0.5">{r.s}</div></div>
              <Toggle on={b(r.k)} onChange={v=>saveMod({ [r.k]: v?1:0 })}/>
            </li>
          ))}
        </ul>
        <div className="mt-4 text-xs rounded-md p-3" style={{ background:'rgba(255,101,0,.07)', border:'1px solid rgba(255,101,0,.2)' }}>
          <span className="font-semibold text-brand">Tip: </span>If you use Elementor for page building you generally do not need Classic Editor enabled — Elementor has its own editor environment.
        </div>
      </Card>
    </div>
  );
}

function ImportExportPage() {
  const fileRef = useRef(null);
  const formRef = useRef(null);
  const [fileName,setFileName] = useState('');

  const pickFile = () => fileRef.current && fileRef.current.click();
  const onFile = (e) => {
    const f = e.target.files && e.target.files[0];
    if (f) setFileName(f.name);
  };
  const submitImport = () => { if (fileName && formRef.current) formRef.current.submit(); };
  const doReset = () => {
    if (window.confirm('Reset ALL PowerPlus settings to factory defaults? This cannot be undone.')) {
      window.location.href = D.resetUrl;
    }
  };

  return (
    <div className="anim-page">
      <PageHeader title="Import / Export" subtitle="Back up and restore all PowerPlus settings as a JSON file."
        crumbs={['PowerPlus','Import / Export']}
        actions={<a className="btn btn-primary inline-flex items-center gap-2 text-sm font-semibold" style={{ padding:'10px 16px', textDecoration:'none' }} href={D.exportUrl}><IconImport size={16}/> Export JSON</a>}/>
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <Card title="Export settings" subtitle="Download everything as a single JSON snapshot." delay={0}>
          <div className="text-xs text-sub mb-4 leading-relaxed">Includes all module toggles, security settings, redirect rules and branding preferences.</div>
          <a className="btn btn-primary inline-flex items-center gap-2 text-sm font-semibold" style={{ padding:'10px 16px', textDecoration:'none' }} href={D.exportUrl}>
            <IconImport size={16}/> Download JSON backup
          </a>
        </Card>
        <Card title="Import settings" subtitle="Upload a previously exported JSON file." delay={80}>
          {/* Real multipart form posted to admin-post.php */}
          <form ref={formRef} method="post" action={D.importAction} encType="multipart/form-data">
            <input type="hidden" name="action" value="pkwt_import_settings"/>
            <input type="hidden" name="_wpnonce" value={D.importNonce}/>
            <input ref={fileRef} type="file" name="pkwt_import_file" accept="application/json,.json" className="hidden" style={{ display:'none' }} onChange={onFile}/>
            <div onClick={pickFile} className="rounded-xl border-2 border-dashed p-8 text-center ease-out-soft cursor-pointer hover:border-brand/40"
                 style={{ borderColor:'rgb(var(--c-border))' }}>
              <IconUpload size={24} className="mx-auto mb-2" style={{ color:'#FF6500' }}/>
              <div className="text-sm font-medium">{fileName || <>Click to choose a JSON file</>}</div>
              <div className="text-xs text-sub mt-1">pkwt-settings-*.json</div>
            </div>
            {fileName && (
              <div className="mt-3">
                <Button variant="primary" icon={IconCheck} onClick={submitImport}>Import "{fileName}"</Button>
              </div>
            )}
          </form>
        </Card>
        <Card title="Reset to defaults" subtitle="Wipe all settings — cannot be undone." className="lg:col-span-2" delay={160}>
          <div className="flex items-center justify-between gap-4">
            <div className="text-sm text-sub">This will delete all PowerPlus settings and restore factory defaults. Export a backup first.</div>
            <Button variant="ghost" icon={IconTrash} onClick={doReset}>Reset all settings</Button>
          </div>
        </Card>
      </div>
    </div>
  );
}

const PAGES = {
  dashboard:      DashboardPage,
  login:          LoginFormsPage,
  duplicator:     DuplicatorPage,
  security:       SecurityPage,
  widgets:        WidgetsPage,
  branding:       BrandingPage,
  settings:       SettingsPage,
  redirects:      RedirectsPage,
  compatibility:  CompatibilityPage,
  'svg-upload':   SvgUploadPage,
  'ghost-mode':   GhostModePage,
  'classic-editor':ClassicEditorPage,
  'import-export':ImportExportPage,
};

/* ───────────────────────── ONBOARDING WIZARD ───────────────────────── */

const WIZARD_STEPS = [
  {
    key:'login_pages', icon:IconLogin, title:'Login Customization', emoji:'🔐',
    blurb:"Let's give your members a beautiful, on-brand sign-in experience — built on WordPress's own secure login.",
    master:'login_customization', masterLabel:'Enable Login Customization',
    items:[
      { key:'login_page',    label:'Custom Login Page',    sub:'A branded sign-in page (username & password).' },
      { key:'register_page', label:'Custom Register Page',  sub:'On-brand sign-up with the native fields.' },
      { key:'lost_page',     label:'Lost Password Page',    sub:'Styled “forgot password” request form.' },
      { key:'reset_page',    label:'Reset Password Page',   sub:'Token-gated new-password form.' },
    ],
  },
  {
    key:'branding', icon:IconPalette, title:'Branding', emoji:'🎨',
    blurb:'Make every screen feel like yours — logo, background and tone.',
    items:[
      { key:'login_logo',              label:'Login Logo',                 sub:'Replace the WordPress logo with yours.' },
      { key:'login_background',         label:'Login Background',           sub:'Custom colors / image behind the form.' },
      { key:'disable_default_styling',  label:'Disable default wp-login styling', sub:'Take full control of the native login look.' },
      { key:'custom_errors',            label:'Custom Error Messages',      sub:'Friendly, enumeration-safe error text.' },
      { key:'typography',               label:'Typography & Colors',        sub:'Fonts and colors for the login form.' },
      { key:'button_styling',           label:'Button Styling',             sub:'Style the submit buttons.' },
    ],
  },
  {
    key:'behavior', icon:IconArrowRight, title:'Behavior', emoji:'⚡',
    blurb:'Smooth out what happens before and after sign-in.',
    items:[
      { key:'redirect_login',  label:'Redirect After Login',  sub:'Send members to a chosen page after login.' },
      { key:'redirect_logout', label:'Redirect After Logout', sub:'Where users land after signing out.' },
      { key:'remember_me',     label:'Remember Me',           sub:'Keep users signed in.' },
      { key:'password_toggle', label:'Password Visibility Toggle', sub:'Show/hide password eye icon.' },
      { key:'role_redirects',  label:'User Role-Based Redirects', sub:'Different landing pages per role.' },
    ],
  },
  {
    key:'security', icon:IconShield, title:'Security', emoji:'🛡️',
    blurb:'Keep the bad bots out without getting in your members’ way.',
    items:[
      { key:'anti_spam',  label:'reCAPTCHA / Anti-Spam',  sub:'Block automated login & signup abuse.' },
      { key:'hide_login', label:'Hide Login URL',         sub:'Move login off wp-login.php to a secret URL.' },
      { key:'rate_limit', label:'Brute-force Protection', sub:'Throttle & lock out repeated failures.' },
    ],
  },
  {
    key:'integrations', icon:IconBox, title:'Integrations', emoji:'🧩',
    blurb:'Play nicely with the tools you already use.',
    items:[
      { key:'woocommerce',      label:'WooCommerce Login Compatibility', sub:'Use Woo account flows where present.' },
      { key:'elementor_import', label:'Elementor Template Import',       sub:'Import ready-made login designs.' },
      { key:'email_templates',  label:'Email Template Customization',    sub:'Brand the auth emails.' },
    ],
  },
];

function WizardToggleRow({ label, sub, on, onChange }) {
  return (
    <div className="flex items-center justify-between gap-4 py-3 px-1 border-b border-border last:border-0">
      <div className="min-w-0">
        <div className="text-sm font-medium">{label}</div>
        <div className="text-xs text-sub mt-0.5">{sub}</div>
      </div>
      <Toggle on={on} onChange={onChange} size="sm"/>
    </div>
  );
}

function OnboardingWizard({ onClose, notify }) {
  // step 0 = welcome; 1..N = feature groups; N+1 = summary; N+2 = applying/success
  const N = WIZARD_STEPS.length;
  const SUMMARY = N + 1, DONE = N + 2;
  const [step,setStep] = useState(0);
  const [choices,setChoices] = useState(() => {
    const init = { ...(D.onboardingChoices||{}) };
    // sensible defaults on first run
    if (!Object.keys(init).length) {
      ['login_customization','login_page','register_page','lost_page','reset_page',
       'login_logo','login_background','disable_default_styling','custom_errors',
       'remember_me','password_toggle','anti_spam','rate_limit','elementor_import'].forEach(k=>{ init[k]=true; });
    }
    return init;
  });
  const [applying,setApplying] = useState(false);
  const [result,setResult] = useState(null);

  /* Full-screen takeover: hide the WP admin chrome while the wizard is open. */
  useEffect(()=>{
    document.body.classList.add('pkwt-wizard-open');
    return ()=>document.body.classList.remove('pkwt-wizard-open');
  },[]);

  const set = (k,v) => setChoices(c=>({ ...c, [k]: v }));
  const total = N + 2; // welcome + groups + summary (progress denominator before done)
  const pct = Math.min(100, Math.round((step / (SUMMARY)) * 100));

  const apply = async () => {
    setApplying(true);
    setStep(DONE);
    try {
      const data = await applyOnboarding(choices);
      setResult(data);
    } catch (e) {
      notify && notify(e.message || 'Setup failed', 'error');
      setResult({ error: e.message });
    } finally { setApplying(false); }
  };

  const enabledList = () => {
    const out = [];
    WIZARD_STEPS.forEach(s=>{
      if (s.master && !choices[s.master]) return;
      s.items.forEach(it=>{ if (choices[it.key]) out.push(it.label); });
    });
    return out;
  };
  const skippedList = () => {
    const out = [];
    WIZARD_STEPS.forEach(s=>{
      s.items.forEach(it=>{ if (!choices[it.key]) out.push(it.label); });
    });
    return out;
  };

  /* ── shell ── */
  return (
    <div className="fixed inset-0 overflow-auto" style={{ zIndex:100000, background:'rgb(var(--c-bg))' }}>
      {/* premium animated background */}
      <div className="pkwt-wiz-bg">
        <div className="pkwt-orb o1"/><div className="pkwt-orb o2"/><div className="pkwt-orb o3"/>
      </div>
      <div className="relative min-h-full flex flex-col">
        {/* top bar */}
        <div className="flex items-center gap-3 px-6 py-4">
          <LogoMark size={28}/>
          <div className="text-sm font-bold tracking-tight">PowerPlus Setup</div>
          {step>0 && step<DONE && (
            <div className="flex-1 mx-4 h-1.5 rounded-full overflow-hidden" style={{ background:'rgb(var(--c-border))', maxWidth:380 }}>
              <div className="h-full rounded-full" style={{ width:pct+'%', background:'linear-gradient(90deg,#FF6500,#cc5200)', transition:'width .4s cubic-bezier(.4,0,.2,1)' }}/>
            </div>
          )}
          <div className="flex-1"/>
          {step<DONE && <button onClick={onClose} className="text-xs text-sub hover:text-fg">Skip setup →</button>}
        </div>

        <div className="flex-1 flex items-center justify-center px-6 pb-12">
          <div className="w-full" style={{ maxWidth: step===0?780:(step===DONE?560:620) }}>

            {/* WELCOME */}
            {step===0 && (
              <div className="text-center anim-slide-up">
                <div className="mx-auto mb-8 inline-flex"><LogoMark size={104}/></div>
                <div className="text-[11px] tracking-[0.25em] font-semibold text-brand mb-3">ALL-IN-ONE TOOLKIT</div>
                <h1 className="font-extrabold tracking-tight leading-[1.05]" style={{ fontSize:'clamp(40px, 6vw, 68px)' }}>Welcome to PowerPlus</h1>
                <p className="text-sub mt-5 leading-relaxed mx-auto" style={{ fontSize:'clamp(16px, 1.6vw, 20px)', maxWidth:620 }}>
                  Your all-in-one toolkit for a beautiful, secure WordPress login — plus duplication, SVG, Ghost Mode and more. Let's set it up together in under a minute. ✨
                </p>
                <button onClick={()=>setStep(1)} className="btn btn-primary mt-10 inline-flex items-center gap-2.5" style={{ padding:'17px 40px', fontSize:18, borderRadius:14 }}>
                  Start Setup <IconArrowRight size={19}/>
                </button>
                <div className="mt-5"><button onClick={onClose} className="text-sm text-sub hover:text-fg">I'll explore on my own</button></div>
              </div>
            )}

            {/* FEATURE GROUPS */}
            {step>=1 && step<=N && (()=>{
              const s = WIZARD_STEPS[step-1];
              const masterOff = s.master && !choices[s.master];
              return (
                <div key={s.key} className="anim-page">
                  <div className="text-center mb-6">
                    <div className="text-4xl mb-2">{s.emoji}</div>
                    <div className="text-[11px] label text-sub">Step {step} of {N}</div>
                    <h2 className="text-2xl font-bold tracking-tight mt-1">{s.title}</h2>
                    <p className="text-sub text-sm mt-2 max-w-md mx-auto leading-relaxed">{s.blurb}</p>
                  </div>
                  <div className="border border-border rounded-2xl shadow-card p-5" style={{ background:'rgb(var(--c-card))' }}>
                    {s.master && (
                      <div className="flex items-center justify-between gap-4 pb-3 mb-2 border-b border-border">
                        <div className="flex items-center gap-2">
                          <span className="text-brand"><s.icon size={18}/></span>
                          <span className="text-sm font-semibold">{s.masterLabel}</span>
                        </div>
                        <Toggle on={!!choices[s.master]} onChange={v=>set(s.master,v)}/>
                      </div>
                    )}
                    <div style={{ opacity: masterOff?0.45:1, pointerEvents: masterOff?'none':'auto', transition:'opacity .2s' }}>
                      {s.items.map(it=>(
                        <WizardToggleRow key={it.key} label={it.label} sub={it.sub} on={!!choices[it.key]} onChange={v=>set(it.key,v)}/>
                      ))}
                    </div>
                  </div>
                  <div className="flex items-center justify-between mt-6">
                    <button onClick={()=>setStep(step-1)} className="btn btn-quiet inline-flex items-center gap-1.5"><IconArrowRight size={14} style={{ transform:'rotate(180deg)' }}/> Back</button>
                    <div className="flex items-center gap-2">
                      <button onClick={()=>setStep(step+1)} className="btn btn-quiet">Skip</button>
                      <button onClick={()=>setStep(step+1)} className="btn btn-primary inline-flex items-center gap-1.5">Next <IconArrowRight size={14}/></button>
                    </div>
                  </div>
                </div>
              );
            })()}

            {/* SUMMARY */}
            {step===SUMMARY && (
              <div className="anim-page">
                <div className="text-center mb-6">
                  <div className="text-4xl mb-2">📋</div>
                  <h2 className="text-2xl font-bold tracking-tight">Review your setup</h2>
                  <p className="text-sub text-sm mt-2">Here's what we'll turn on. You can change anything later.</p>
                </div>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div className="border border-border rounded-2xl p-5 shadow-card" style={{ background:'rgb(var(--c-card))' }}>
                    <div className="flex items-center gap-2 mb-3 text-sm font-semibold"><IconCheck size={15} style={{ color:'#3fb950' }}/> Enabled <Badge variant="green">{enabledList().length}</Badge></div>
                    <ul className="space-y-1.5 max-h-60 overflow-auto">
                      {enabledList().map(l=><li key={l} className="text-sm text-sub flex items-center gap-2"><span className="w-1.5 h-1.5 rounded-full bg-ok inline-block"/>{l}</li>)}
                      {!enabledList().length && <li className="text-sm text-sub">Nothing selected yet.</li>}
                    </ul>
                  </div>
                  <div className="border border-border rounded-2xl p-5 shadow-card" style={{ background:'rgb(var(--c-card))' }}>
                    <div className="flex items-center gap-2 mb-3 text-sm font-semibold"><IconX size={15} style={{ color:'#8b949e' }}/> Skipped <Badge variant="grey">{skippedList().length}</Badge></div>
                    <ul className="space-y-1.5 max-h-60 overflow-auto">
                      {skippedList().map(l=><li key={l} className="text-sm text-sub flex items-center gap-2"><span className="w-1.5 h-1.5 rounded-full inline-block" style={{ background:'rgb(var(--c-border))' }}/>{l}</li>)}
                      {!skippedList().length && <li className="text-sm text-sub">You enabled everything! 🎉</li>}
                    </ul>
                  </div>
                </div>
                <div className="flex items-center justify-between mt-6">
                  <button onClick={()=>setStep(N)} className="btn btn-quiet inline-flex items-center gap-1.5"><IconArrowRight size={14} style={{ transform:'rotate(180deg)' }}/> Back</button>
                  <button onClick={apply} className="btn btn-primary inline-flex items-center gap-2" style={{ padding:'11px 22px' }}><IconRocket size={16}/> Apply &amp; Finish</button>
                </div>
              </div>
            )}

            {/* APPLYING / SUCCESS */}
            {step===DONE && (
              <div className="text-center anim-slide-up">
                {applying ? (
                  <>
                    <IconRefresh size={40} className="animate-spin mx-auto mb-4 text-brand"/>
                    <div className="text-lg font-semibold">Setting everything up…</div>
                    <div className="text-sub text-sm mt-1">Creating pages, applying settings, wiring up your login.</div>
                  </>
                ) : result && !result.error ? (
                  <>
                    <div className="mx-auto mb-5 w-16 h-16 rounded-full flex items-center justify-center" style={{ background:'rgba(63,185,80,.15)', color:'#3fb950' }}><IconCheck size={32}/></div>
                    <h2 className="text-2xl font-bold tracking-tight">You're all set! 🎉</h2>
                    <p className="text-sub text-sm mt-2 max-w-sm mx-auto">Your login system is live and ready. Here's where to go next.</p>
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-2.5 mt-6 text-left">
                      {result.login_view && <a className="qa-btn" href={result.login_view} target="_blank" rel="noopener"><IconExternal size={15}/> View Login Page</a>}
                      {result.login_edit && <a className="qa-btn" href={result.login_edit}><IconEdit size={15}/> Edit Login Template</a>}
                      <button className="qa-btn" onClick={onClose}><IconDashboard size={15}/> Go to Dashboard</button>
                      <button className="qa-btn" onClick={()=>{ onClose&&onClose('redirects'); }}><IconLock size={15}/> Login URL &amp; Security</button>
                      <button className="qa-btn sm:col-span-2" onClick={()=>{ setResult(null); setStep(0); }}><IconRefresh size={15}/> Restart Setup Wizard</button>
                    </div>
                  </>
                ) : (
                  <>
                    <div className="mx-auto mb-5 w-16 h-16 rounded-full flex items-center justify-center" style={{ background:'rgba(248,81,73,.15)', color:'#f85149' }}><IconX size={32}/></div>
                    <h2 className="text-xl font-bold">Something went wrong</h2>
                    <p className="text-sub text-sm mt-2">{(result&&result.error)||'Please try again.'}</p>
                    <button onClick={()=>setStep(SUMMARY)} className="btn btn-primary mt-5">Back to summary</button>
                  </>
                )}
              </div>
            )}
          </div>
        </div>
      </div>
      <style>{`#pkwt-dashboard-root .qa-btn{display:flex;align-items:center;gap:8px;padding:12px 14px;border:1px solid rgb(var(--c-border));border-radius:10px;background:rgb(var(--c-card));font-size:13px;font-weight:600;color:rgb(var(--c-fg));text-decoration:none;transition:all .18s}#pkwt-dashboard-root .qa-btn:hover{border-color:#FF6500;color:#FF6500}`}</style>
    </div>
  );
}

function App() {
  const [route,setRoute]    = useState((window.pkwtDashboard||{}).currentPage || 'dashboard');
  const [collapsed,setCol]  = useState(window.innerWidth < 900);
  const [settings,setSettings] = useState(D.settings || {});
  const [toast,setToast]    = useState(null);
  const [showWizard,setShowWizard] = useState(!D.wizardComplete);
  const toastTimer = useRef(null);

  // Closing the wizard optionally navigates to a route (e.g. from a success quick-action).
  const closeWizard = (gotoRoute) => {
    setShowWizard(false);
    if (typeof gotoRoute === 'string') setRoute(gotoRoute);
  };

  const notify = (msg, tone='ok') => {
    clearTimeout(toastTimer.current);
    setToast({ msg, tone });
    toastTimer.current = setTimeout(()=>setToast(null), 2800);
  };

  /* Optimistic save: update UI immediately, then reconcile with the server's
     sanitized copy (so e.g. a normalized login URL shows its final form). */
  const save = async (patch) => {
    if (!D.canManage) {
      notify('You have view-only access — ask an administrator to change settings.', 'error');
      return false;
    }
    setSettings(s => ({ ...s, ...patch }));
    try {
      const fresh = await persistSettings(patch);
      setSettings(fresh);
      notify('Settings saved');
      return true;
    } catch (e) {
      notify(e.message || 'Save failed', 'error');
      return false;
    }
  };
  const [theme,setThemeRaw] = useState(()=>{
    try { const t=localStorage.getItem('pp-theme'); if(t==='dark'||t==='light') return t; } catch(e){}
    return 'light';
  });

  const setTheme = (updater) => setThemeRaw(prev => {
    const next = typeof updater === 'function' ? updater(prev) : updater;
    try { localStorage.setItem('pp-theme', next); } catch(e){}
    return next;
  });

  /* Sync dark class to root element */
  useEffect(()=>{
    const el = document.getElementById('pkwt-dashboard-root');
    if (!el) return;
    if (theme === 'dark') el.classList.add('dark');
    else el.classList.remove('dark');
  }, [theme]);

  /* Toast notices coming back from admin-post redirects (e.g. settings import) */
  useEffect(()=>{
    const MSG = {
      import_ok:     ['Settings imported successfully.', 'ok'],
      import_failed: ['Settings import failed — check the file and try again.', 'error'],
      reset_ok:      ['Settings were reset to defaults.', 'ok'],
    };
    if (D.notice && MSG[D.notice]) notify(MSG[D.notice][0], MSG[D.notice][1]);
  }, []);

  /* Let any page re-open the wizard (e.g. Settings → "Run Setup Wizard"). */
  useEffect(()=>{ window.pkwtOpenWizard = ()=>setShowWizard(true); return ()=>{ delete window.pkwtOpenWizard; }; }, []);

  /* Keyboard shortcut Shift+D toggles theme */
  useEffect(()=>{
    const onKey=(e)=>{
      if(e.shiftKey && e.key.toLowerCase()==='d' && !e.metaKey && !e.ctrlKey && !e.altKey
         && document.activeElement && !['INPUT','TEXTAREA','SELECT'].includes(document.activeElement.tagName)) {
        setTheme(t=>t==='dark'?'light':'dark');
      }
    };
    window.addEventListener('keydown',onKey);
    return()=>window.removeEventListener('keydown',onKey);
  },[]);

  /* Collapse sidebar on mobile resize */
  useEffect(()=>{
    const onResize=()=>{ if(window.innerWidth<768) setCol(true); };
    window.addEventListener('resize',onResize);
    return()=>window.removeEventListener('resize',onResize);
  },[]);

  const Page = PAGES[route] || DashboardPage;

  if (showWizard) {
    return (
      <div className="pp-dotbg" style={{ minHeight:'100vh', background:'rgb(var(--c-bg))', color:'rgb(var(--c-fg))' }}>
        <OnboardingWizard onClose={closeWizard} notify={notify}/>
        {toast && (
          <div className="fixed bottom-6 right-6 z-[110] flex items-center gap-2 px-4 py-3 rounded-lg shadow-card text-sm font-medium anim-slide-up"
               style={{ background:'rgb(var(--c-card))', border:`1px solid ${toast.tone==='error'?'rgba(248,81,73,.5)':'rgba(63,185,80,.5)'}`, color:'rgb(var(--c-fg))' }}>
            {toast.tone==='error' ? <IconX size={15} style={{color:'#f85149'}}/> : <IconCheck size={15} style={{color:'#3fb950'}}/>}
            {toast.msg}
          </div>
        )}
      </div>
    );
  }

  return (
    <div className="pp-dotbg" style={{ minHeight:'100vh', background:'rgb(var(--c-bg))', color:'rgb(var(--c-fg))' }}>
      <TopBar onSearchClick={()=>setCol(c=>!c)} theme={theme} onThemeChange={setTheme}/>
      <div className="flex">
        <Sidebar current={route} onNavigate={setRoute} collapsed={collapsed}/>
        <main className="flex-1 min-w-0 p-8 overflow-auto" key={route}>
          <Page onNavigate={setRoute} settings={settings} save={save} notify={notify}/>
          <footer className="mt-12 pt-6 border-t border-border flex flex-wrap items-center justify-between gap-2 text-xs text-sub">
            <div>© 2026 PowerPlus — All-in-One Powerful Toolkit · Developed by Saddam Hussain Safi</div>
            <div className="flex items-center gap-3">
              <a className="hover:text-brand ease-out-soft" href="https://saddamhussain.com.np/" target="_blank" rel="noopener">Portfolio</a>
              <span>·</span>
              <a className="hover:text-brand ease-out-soft" href="https://wordpress.org/plugins/powerplus-toolkit/" target="_blank" rel="noopener">WordPress.org</a>
              <span>·</span>
              <a className="hover:text-brand ease-out-soft" href="https://wordpress.org/support/plugin/powerplus-toolkit/" target="_blank" rel="noopener">Support</a>
            </div>
          </footer>
        </main>
      </div>
      {toast && (
        <div className="fixed bottom-6 right-6 z-50 flex items-center gap-2 px-4 py-3 rounded-lg shadow-card text-sm font-medium anim-slide-up"
             style={{ background:'rgb(var(--c-card))', border:`1px solid ${toast.tone==='error'?'rgba(248,81,73,.5)':'rgba(63,185,80,.5)'}`, color:'rgb(var(--c-fg))' }}>
          {toast.tone==='error' ? <IconX size={15} style={{color:'#f85149'}}/> : <IconCheck size={15} style={{color:'#3fb950'}}/>}
          {toast.msg}
        </div>
      )}
    </div>
  );
}

ReactDOM.createRoot(document.getElementById('pkwt-dashboard-root')).render(<App/>);
