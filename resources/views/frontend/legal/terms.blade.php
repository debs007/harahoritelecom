<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Terms &amp; Conditions — Harahori Telecom LLP</title>
<meta name="description" content="The terms governing orders, payments, delivery, warranty and returns on the Harahori Telecom LLP platform.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600&family=IBM+Plex+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  /* ============ RESET & BASE ============ */
  *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
  html{scroll-behavior:smooth;}
  body{
    font-family:var(--sans);
    background:var(--paper);
    color:var(--text);
    line-height:1.65;
    -webkit-font-smoothing:antialiased;
    text-rendering:optimizeLegibility;
  }
  svg{display:block;}
  a{color:inherit;text-decoration:none;}
  ul,ol{list-style:none;}
  button{font:inherit;cursor:pointer;border:none;background:none;color:inherit;}
  input,select{font:inherit;}

  /* ============ TOKENS ============ */
  :root{
    --ink:#14171A;
    --paper:#EEF0ED;
    --card:#FAFBF9;
    --border:#DADDD6;
    --text:#1B1E1C;
    --muted:#62675F;
    --accent:#C6900F;
    --accent-bright:#E0A82A;
    --accent-soft:#F7ECD3;
    --green:#1F7A4D;
    --green-soft:#DCEEE3;
    --danger:#C1272D;
    --danger-dark:#9E1F24;
    --danger-soft:#FBE2E1;
    --radius-sm:8px;
    --radius-md:14px;
    --radius-lg:22px;
    --shadow-sm:0 1px 2px rgba(20,23,26,.07),0 1px 1px rgba(20,23,26,.06);
    --shadow-md:0 12px 28px rgba(20,23,26,.10);
    --shadow-lg:0 28px 64px rgba(20,23,26,.18);
    --maxw:1180px;
    --header-h:72px;
    --sans:'IBM Plex Sans',-apple-system,BlinkMacSystemFont,sans-serif;
    --mono:'IBM Plex Mono',ui-monospace,monospace;
  }

  h1,h2,h3,h4{font-family:var(--sans);color:var(--ink);font-weight:700;line-height:1.2;}
  .mono{font-family:var(--mono);}

  .container{max-width:var(--maxw);margin:0 auto;padding:0 24px;}

  a:focus-visible,button:focus-visible,input:focus-visible,select:focus-visible,summary:focus-visible{
    outline:2px solid var(--accent);outline-offset:2px;border-radius:4px;
  }

  /* ============ HEADER ============ */
  .site-header{position:sticky;top:0;z-index:50;background:var(--paper);border-bottom:1px solid var(--border);height:var(--header-h);}
  .header-inner{max-width:var(--maxw);margin:0 auto;height:100%;padding:0 24px;display:flex;align-items:center;justify-content:space-between;}
  .brand{display:flex;align-items:center;gap:10px;font-weight:700;font-size:1.05rem;color:var(--ink);}
  .brand-mark{width:36px;height:36px;border-radius:var(--radius-sm);background:var(--ink);color:var(--accent-bright);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
  .brand-mark svg{width:19px;height:19px;}
  .brand-name span{color:var(--muted);font-weight:500;}

  .site-nav{display:none;flex-direction:column;gap:2px;position:absolute;top:var(--header-h);left:0;right:0;background:var(--paper);border-bottom:1px solid var(--border);padding:10px;box-shadow:var(--shadow-md);}
  .site-nav.is-open{display:flex;}
  .site-nav a{position:relative;padding:12px 14px;font-size:.95rem;font-weight:500;border-radius:var(--radius-sm);}
  .site-nav a:hover{background:var(--card);}
  .site-nav a.is-current{color:var(--ink);font-weight:600;background:var(--accent-soft);}

  .nav-toggle{width:40px;height:40px;display:flex;align-items:center;justify-content:center;border-radius:var(--radius-sm);}
  .nav-toggle:hover{background:var(--card);}
  .nav-toggle svg{width:20px;height:20px;}

  @media (min-width:860px){
    .site-nav{display:flex;flex-direction:row;position:static;background:none;border:none;box-shadow:none;padding:0;gap:28px;}
    .site-nav a{padding:6px 0;}
    .site-nav a::after{content:'';position:absolute;left:0;right:0;bottom:0;height:2px;background:var(--accent);transform:scaleX(0);transform-origin:left;transition:transform .18s ease;}
    .site-nav a:hover::after,.site-nav a.is-current::after{transform:scaleX(1);}
    .site-nav a.is-current{background:none;}
    .nav-toggle{display:none;}
  }

  /* ============ HERO ============ */
  .hero{background:var(--ink);color:#fff;position:relative;overflow:hidden;padding:56px 0 72px;}
  .hero::before{content:'';position:absolute;inset:0;background-image:radial-gradient(rgba(255,255,255,.07) 1px,transparent 1px);background-size:24px 24px;opacity:.6;pointer-events:none;}
  .hero-inner{position:relative;z-index:1;display:grid;grid-template-columns:1fr;gap:36px;align-items:end;}
  @media (min-width:860px){.hero-inner{grid-template-columns:1.3fr 1fr;align-items:center;}}
  .path-label{font-family:var(--mono);font-size:.78rem;letter-spacing:.06em;color:var(--accent-bright);margin-bottom:14px;}
  .hero h1{color:#fff;font-size:clamp(2.1rem,4vw + 1rem,3.1rem);}
  .hero .lead{color:rgba(255,255,255,.75);font-size:1.08rem;max-width:52ch;margin-top:14px;}
  .hero-actions{margin-top:26px;display:flex;gap:12px;flex-wrap:wrap;}

  .btn{display:inline-flex;align-items:center;gap:8px;padding:12px 20px;border-radius:var(--radius-sm);font-weight:600;font-size:.92rem;transition:transform .15s ease,box-shadow .15s ease,background .15s ease,border-color .15s ease;}
  .btn svg{width:16px;height:16px;}
  .btn-ghost-invert{background:rgba(255,255,255,.08);color:#fff;border:1px solid rgba(255,255,255,.25);}
  .btn-ghost-invert:hover{background:rgba(255,255,255,.16);}
  .btn-primary{background:var(--accent);color:#fff;box-shadow:var(--shadow-sm);}
  .btn-primary:hover{background:#a97a0c;transform:translateY(-1px);box-shadow:var(--shadow-md);}
  .btn-outline{background:var(--card);border:1.5px solid var(--border);color:var(--ink);}
  .btn-outline:hover{border-color:var(--ink);}

  .spec-card{background:var(--card);color:var(--text);border-radius:var(--radius-md);padding:22px 22px 18px;box-shadow:var(--shadow-lg);position:relative;}
  .spec-card::before,.spec-card::after{content:'';position:absolute;top:-9px;width:18px;height:18px;border-radius:50%;background:var(--ink);}
  .spec-card::before{left:22px;}
  .spec-card::after{right:22px;}
  .spec-title{font-family:var(--mono);font-size:.7rem;letter-spacing:.08em;color:var(--muted);text-transform:uppercase;margin-bottom:14px;padding-bottom:12px;border-bottom:1px dashed var(--border);}
  .spec-row{display:flex;justify-content:space-between;gap:16px;padding:8px 0;font-size:.88rem;}
  .spec-row + .spec-row{border-top:1px solid var(--border);}
  .spec-label{color:var(--muted);font-family:var(--mono);font-size:.78rem;}
  .spec-value{font-weight:600;font-family:var(--mono);text-align:right;}
  .led{display:inline-block;width:7px;height:7px;border-radius:50%;margin-right:7px;position:relative;top:-1px;}
  .led-green{background:var(--green);box-shadow:0 0 0 3px var(--green-soft);}

  /* ============ LAYOUT: TOC + CONTENT ============ */
  .page-shell{max-width:var(--maxw);margin:0 auto;padding:0 24px 90px;}
  .toc-mobile-wrap{margin:28px 0 8px;}
  .toc-mobile-wrap details{background:var(--card);border:1px solid var(--border);border-radius:var(--radius-md);padding:16px 18px;}
  .toc-mobile-wrap summary{font-family:var(--mono);font-size:.82rem;font-weight:600;letter-spacing:.03em;display:flex;align-items:center;justify-content:space-between;cursor:pointer;}
  .toc-mobile-wrap summary::-webkit-details-marker{display:none;}
  .toc-mobile-wrap .chev{width:16px;height:16px;transition:transform .2s ease;}
  .toc-mobile-wrap details[open] .chev{transform:rotate(180deg);}
  .toc-mobile-wrap nav{margin-top:14px;display:flex;flex-direction:column;gap:2px;max-height:340px;overflow-y:auto;}
  .toc-mobile-wrap nav a{padding:8px 10px;border-radius:8px;font-size:.9rem;color:var(--muted);}
  .toc-mobile-wrap nav a:hover{background:var(--paper);color:var(--text);}

  .page-layout{display:grid;grid-template-columns:1fr;gap:40px;margin-top:28px;}
  @media (min-width:1024px){.page-layout{grid-template-columns:250px 1fr;align-items:start;}.toc-mobile-wrap{display:none;}}
  .toc-desktop{display:none;}
  @media (min-width:1024px){
    .toc-desktop{display:block;position:sticky;top:calc(var(--header-h) + 24px);background:var(--card);border:1px solid var(--border);border-radius:var(--radius-md);padding:18px;max-height:calc(100vh - var(--header-h) - 48px);overflow-y:auto;}
    .toc-desktop h4{font-family:var(--mono);font-size:.72rem;letter-spacing:.06em;color:var(--muted);text-transform:uppercase;margin-bottom:10px;}
    .toc-desktop nav{display:flex;flex-direction:column;gap:1px;}
    .toc-desktop a{display:flex;gap:8px;padding:7px 9px;border-radius:7px;font-size:.86rem;color:var(--muted);border-left:2px solid transparent;}
    .toc-desktop a .n{font-family:var(--mono);color:var(--border);font-size:.78rem;flex-shrink:0;}
    .toc-desktop a:hover{background:var(--paper);color:var(--text);}
    .toc-desktop a.is-active{background:var(--accent-soft);color:#8a660b;border-left-color:var(--accent);font-weight:600;}
    .toc-desktop a.is-active .n{color:var(--accent);}
  }

  /* ============ CONTENT ============ */
  .content section{scroll-margin-top:calc(var(--header-h) + 16px);margin-bottom:44px;}
  .content section:first-child{margin-top:0;}
  .intro-block{margin-bottom:40px;padding-bottom:40px;border-bottom:1px dashed var(--border);}
  .intro-block p{color:var(--muted);max-width:70ch;}
  .intro-block p + p{margin-top:12px;}

  .section-head{display:flex;align-items:flex-start;gap:14px;margin-bottom:14px;}
  .section-icon{width:42px;height:42px;border-radius:var(--radius-sm);background:var(--accent-soft);color:#8a660b;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
  .section-icon.green{background:var(--green-soft);color:var(--green);}
  .section-icon svg{width:21px;height:21px;}
  .section-num{font-family:var(--mono);font-size:.78rem;color:var(--muted);display:block;margin-bottom:2px;}
  .content h2{font-size:1.3rem;}
  .content h3{font-size:1.02rem;margin:16px 0 6px;}
  .content p{color:#3a3d38;margin-bottom:12px;max-width:75ch;}
  .content ul,.content ol{margin:8px 0 16px 0;max-width:75ch;}
  .content li{position:relative;padding-left:20px;margin-bottom:9px;color:#3a3d38;}
  .content li::before{content:'';position:absolute;left:0;top:9px;width:5px;height:5px;border-radius:50%;background:var(--accent);}
  .content strong{color:var(--ink);}

  .callout{background:var(--card);border:1px solid var(--border);border-radius:var(--radius-md);padding:18px 20px;margin:16px 0;display:flex;gap:14px;}
  .callout svg{width:20px;height:20px;flex-shrink:0;margin-top:2px;color:var(--accent);}
  .callout.amber-tint{background:var(--accent-soft);border-color:#e9d29e;}
  .callout p{margin:0;font-size:.92rem;}

  .contact-card{background:var(--ink);color:#fff;border-radius:var(--radius-md);padding:26px;margin-top:8px;}
  .contact-card .spec-row{border-color:rgba(255,255,255,.14);}
  .contact-card .spec-row + .spec-row{border-top:1px solid rgba(255,255,255,.14);}
  .contact-card .spec-label{color:rgba(255,255,255,.55);}
  .contact-card .spec-value{color:#fff;}
  .fill-in{border-bottom:1.5px dashed var(--accent-bright);color:var(--accent-bright);cursor:help;font-weight:600;}
  .contact-card .fill-in{color:#f0cf82;border-bottom-color:#f0cf82;}

  /* Definition list for §01 */
  .def-list{border:1px solid var(--border);border-radius:var(--radius-md);overflow:hidden;background:var(--card);margin-top:6px;}
  .def-row{display:grid;grid-template-columns:1fr;gap:4px;padding:14px 18px;}
  .def-row + .def-row{border-top:1px solid var(--border);}
  @media (min-width:640px){.def-row{grid-template-columns:170px 1fr;gap:16px;}}
  .def-term{font-family:var(--mono);font-weight:600;color:var(--ink);font-size:.88rem;}
  .def-desc{color:#3a3d38;font-size:.92rem;}

  /* ============ FOOTER ============ */
  .site-footer{background:var(--ink);color:rgba(255,255,255,.7);padding:52px 0 28px;margin-top:40px;}
  .footer-inner{display:grid;gap:32px;grid-template-columns:1fr;}
  @media (min-width:768px){.footer-inner{grid-template-columns:1.4fr 1fr 1fr;}}
  .footer-inner h5{font-family:var(--mono);font-size:.75rem;letter-spacing:.06em;color:rgba(255,255,255,.4);text-transform:uppercase;margin-bottom:14px;}
  .footer-brand{display:flex;align-items:center;gap:10px;color:#fff;font-weight:700;margin-bottom:10px;}
  .footer-brand .brand-mark{background:rgba(255,255,255,.1);color:var(--accent-bright);}
  .footer-inner p{font-size:.88rem;line-height:1.6;max-width:38ch;color:rgba(255,255,255,.55);}
  .footer-links{display:flex;flex-direction:column;gap:9px;}
  .footer-links a{font-size:.9rem;color:rgba(255,255,255,.7);}
  .footer-links a:hover{color:#fff;}
  .footer-bottom{max-width:var(--maxw);margin:36px auto 0;padding:0 24px;padding-top:22px;border-top:1px solid rgba(255,255,255,.12);display:flex;justify-content:space-between;flex-wrap:wrap;gap:10px;font-size:.8rem;color:rgba(255,255,255,.45);}

  /* ============ BACK TO TOP ============ */
  .back-to-top{position:fixed;bottom:24px;right:24px;width:46px;height:46px;border-radius:50%;background:var(--ink);color:#fff;display:flex;align-items:center;justify-content:center;box-shadow:var(--shadow-md);z-index:40;transition:transform .15s ease,opacity .15s ease;}
  .back-to-top:hover{transform:translateY(-3px);}
  .back-to-top svg{width:18px;height:18px;}

  /* ============ PRINT ============ */
  @media print{
    .site-header,.site-footer,.toc-mobile-wrap,.toc-desktop,.back-to-top,.hero-actions{display:none!important;}
    .hero{background:#fff!important;color:#000!important;padding:20px 0;}
    .hero::before{display:none;}
    .hero h1,.path-label{color:#000!important;}
    .hero .lead{color:#333!important;}
    .spec-card{box-shadow:none;border:1px solid #999;}
    body{background:#fff;}
    .page-layout{grid-template-columns:1fr!important;}
    a{text-decoration:underline;}
  }

  @media (prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important;scroll-behavior:auto!important;}}
</style>
</head>
<body>

<svg style="display:none" aria-hidden="true">
  <symbol id="i-bolt" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 3 14h7l-1 8 10-12h-7l1-8z"/></symbol>
  <symbol id="i-menu" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 6h18M3 12h18M3 18h18"/></symbol>
  <symbol id="i-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M5 5l14 14M19 5 5 19"/></symbol>
  <symbol id="i-print" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V3h12v6"/><rect x="4" y="9" width="16" height="8" rx="1"/><path d="M6 14h12v7H6z"/></symbol>
  <symbol id="i-up" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V6M6 11l6-6 6 6"/></symbol>
  <symbol id="i-chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></symbol>
  <symbol id="i-file" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2.5h8l4 4V21a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1z"/><path d="M14 2.5V7h4"/><path d="M8 12h8M8 15.5h8M8 8.5h3"/></symbol>
  <symbol id="i-check-circle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M8 12.5l2.5 2.5L16 9.5"/></symbol>
  <symbol id="i-lock" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V7.5a4 4 0 0 1 8 0V11"/></symbol>
  <symbol id="i-box" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8l9-5 9 5-9 5-9-5z"/><path d="M3 8v9l9 5 9-5V8"/><path d="M12 13v9"/></symbol>
  <symbol id="i-card" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="2.5" y="6" width="19" height="13" rx="2"/><path d="M2.5 10.5h19"/><path d="M6 15h4"/></symbol>
  <symbol id="i-truck" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="1.5" y="8" width="12" height="9" rx="1"/><path d="M13.5 11h4l3.5 3.5V17h-7.5z"/><circle cx="6" cy="18.5" r="1.6"/><circle cx="17" cy="18.5" r="1.6"/></symbol>
  <symbol id="i-x-circle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M9.5 9.5l5 5M14.5 9.5l-5 5"/></symbol>
  <symbol id="i-refresh-ccw" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 4.5v5.5h5.5"/><path d="M19.5 19.5V14h-5.5"/><path d="M5.6 14.5a7 7 0 0 0 12.3 2.8l1.6-2.3"/><path d="M18.4 9.5A7 7 0 0 0 6.1 6.7L4.5 9"/></symbol>
  <symbol id="i-shield" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v6c0 4.8-3 8.4-7 9.5-4-1.1-7-4.7-7-9.5V6l7-3z"/><path d="M8.7 12l2.3 2.3 4.3-4.6"/></symbol>
  <symbol id="i-percent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 19 19 5"/><circle cx="7.5" cy="7.5" r="2.3"/><circle cx="16.5" cy="16.5" r="2.3"/></symbol>
  <symbol id="i-alert" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3.5 21.5 20h-19L12 3.5z"/><path d="M12 10v4.2"/><path d="M12 17.3h.01"/></symbol>
  <symbol id="i-star" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3.5 14.7 9l6 .9-4.3 4.2 1 6-5.4-2.9L6.6 20l1-6-4.3-4.2 6-.9 2.7-5.5z"/></symbol>
  <symbol id="i-badge" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="9" r="6"/><path d="M8.5 14 7 21l5-2.5L17 21l-1.5-7"/></symbol>
  <symbol id="i-external" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M14 4h6v6"/><path d="M20 4 10 14"/><path d="M18 13v6a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h6"/></symbol>
  <symbol id="i-info" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 11v5.5"/><path d="M12 7.8h.01"/></symbol>
  <symbol id="i-scale" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v18M5 7h14M8.5 7 5 14a3.3 3.3 0 0 0 6.6 0L8.5 7ZM17.5 7 14 14a3.3 3.3 0 0 0 6.6 0L17.5 7Z"/></symbol>
  <symbol id="i-cloud" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M6.5 18.5a4.2 4.2 0 0 1-.7-8.3 5.5 5.5 0 0 1 10.6-2 4.6 4.6 0 0 1-.9 10.3H6.5Z"/></symbol>
  <symbol id="i-message" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a8.5 8.5 0 0 1 13.5-6.9A8.5 8.5 0 0 1 12 20.5a8.4 8.4 0 0 1-4.2-1.1L3 20.5l1.3-4.4A8.4 8.4 0 0 1 3 12Z"/></symbol>
  <symbol id="i-edit" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h4.5L19 9.5a2.1 2.1 0 0 0-3-3L5.5 17z"/><path d="M14 5l5 5"/></symbol>
  <symbol id="i-mail" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="2.5" y="5" width="19" height="14" rx="2"/><path d="M3 6.5 12 13l9-6.5"/></symbol>
</svg>

<header class="site-header">
  <div class="header-inner">
    <a href="index.html" class="brand">
      <span class="brand-mark"><svg width="18" height="18"><use href="#i-bolt"/></svg></span>
      <span class="brand-name">Harahori <span>Telecom</span></span>
    </a>
    <nav class="site-nav" id="siteNav">
      <a href="privacy-policy.html">Privacy Policy</a>
      <a href="terms-and-conditions.html" class="is-current">Terms &amp; Conditions</a>
      <a href="delete-account.html">Delete Account</a>
    </nav>
    <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation menu" aria-expanded="false" aria-controls="siteNav">
      <svg id="navIcon" width="20" height="20"><use href="#i-menu"/></svg>
    </button>
  </div>
</header>

<section class="hero">
  <div class="container hero-inner">
    <div class="hero-text">
      <p class="path-label mono">HARAHORI TELECOM // LEGAL</p>
      <h1>Terms &amp; Conditions</h1>
      <p class="lead">The ground rules for buying phones, refrigerators and other electronics through Harahori Telecom — orders, payments, delivery, warranty and returns.</p>
      <div class="hero-actions">
        <button class="btn btn-ghost-invert" id="printBtn"><svg><use href="#i-print"/></svg> Print these terms</button>
      </div>
    </div>
    <div class="spec-card" aria-label="Document details">
      <div class="spec-title">Document details</div>
      <div class="spec-row"><span class="spec-label">Doc No.</span><span class="spec-value">HT/TC/2026-02</span></div>
      <div class="spec-row"><span class="spec-label">Effective</span><span class="spec-value">27 Jul 2026</span></div>
      <div class="spec-row"><span class="spec-label">Applies to</span><span class="spec-value">All orders &amp; users</span></div>
      <div class="spec-row"><span class="spec-label">Status</span><span class="spec-value"><span class="led led-green"></span>Active</span></div>
    </div>
  </div>
</section>

<div class="page-shell">
  <div class="toc-mobile-wrap">
    <details>
      <summary>Jump to a section <svg class="chev" width="16" height="16"><use href="#i-chev"/></svg></summary>
      <nav id="tocMobile"></nav>
    </details>
  </div>

  <div class="page-layout">
    <aside class="toc-desktop">
      <h4>On this page</h4>
      <nav id="tocDesktop"></nav>
    </aside>

    <main class="content">
      <div class="intro-block">
        <p>These Terms and Conditions ("Terms") govern your access to and use of the website, mobile app and services (the "Platform") operated by <strong>Harahori Telecom LLP</strong>, a limited liability partnership registered in India, for the sale of mobile phones, refrigerators and other consumer electronics and appliances (collectively, "Products").</p>
        <p>By accessing the Platform, placing an Order, or creating an account, you agree to be bound by these Terms. If you don't agree, please don't use the Platform.</p>
      </div>

      <section id="definitions">
        <div class="section-head">
          <span class="section-icon"><svg><use href="#i-file"/></svg></span>
          <div><span class="section-num mono">§ 01</span><h2>Definitions</h2></div>
        </div>
        <div class="def-list">
          <div class="def-row"><span class="def-term">Platform</span><span class="def-desc">The Harahori Telecom website, mobile app, and related services.</span></div>
          <div class="def-row"><span class="def-term">User / you</span><span class="def-desc">Any person accessing or using the Platform.</span></div>
          <div class="def-row"><span class="def-term">Products</span><span class="def-desc">Electronics and appliances listed for sale on the Platform.</span></div>
          <div class="def-row"><span class="def-term">Order</span><span class="def-desc">A request to purchase Products placed through the Platform.</span></div>
          <div class="def-row"><span class="def-term">Seller Partner</span><span class="def-desc">Any authorised third-party seller listed on the Platform, where applicable.</span></div>
        </div>
      </section>

      <section id="eligibility">
        <div class="section-head">
          <span class="section-icon"><svg><use href="#i-check-circle"/></svg></span>
          <div><span class="section-num mono">§ 02</span><h2>Eligibility</h2></div>
        </div>
        <p>You must be at least 18 years old and capable of entering a binding contract under the Indian Contract Act, 1872 to place an Order. If you're a minor, purchases must be made through a parent or legal guardian.</p>
      </section>

      <section id="account">
        <div class="section-head">
          <span class="section-icon"><svg><use href="#i-lock"/></svg></span>
          <div><span class="section-num mono">§ 03</span><h2>Account registration</h2></div>
        </div>
        <p>You're responsible for keeping your login credentials confidential and for all activity under your account. Tell us immediately if you notice any unauthorised use.</p>
      </section>

      <section id="products">
        <div class="section-head">
          <span class="section-icon"><svg><use href="#i-box"/></svg></span>
          <div><span class="section-num mono">§ 04</span><h2>Products &amp; product information</h2></div>
        </div>
        <p>We make reasonable efforts to display accurate descriptions, images, specifications and pricing for every Product, including mobile phones, refrigerators and other appliances. Actual colour, packaging or minor specifications may vary from the images shown, due to manufacturer updates or display settings.</p>
        <p>If a Product is listed with an error in price or description — including an obvious pricing error — we may cancel the affected Order and issue a full refund.</p>
      </section>

      <section id="pricing">
        <div class="section-head">
          <span class="section-icon"><svg><use href="#i-card"/></svg></span>
          <div><span class="section-num mono">§ 05</span><h2>Pricing &amp; payment</h2></div>
        </div>
        <ul>
          <li>All prices are listed in Indian Rupees (₹) and include applicable GST unless stated otherwise.</li>
          <li>Accepted payment methods include debit/credit cards, UPI, net banking, digital wallets, EMI or cardless credit through our financing partners, and Cash on Delivery where available for your location and order value.</li>
          <li>An Order is confirmed only once payment is successfully authorised — or, for Cash on Delivery, once the Order is accepted.</li>
        </ul>
      </section>

      <section id="delivery">
        <div class="section-head">
          <span class="section-icon"><svg><use href="#i-truck"/></svg></span>
          <div><span class="section-num mono">§ 06</span><h2>Shipping &amp; delivery</h2></div>
        </div>
        <ul>
          <li>Estimated delivery timelines shown at checkout aren't guaranteed — they may shift due to location, stock availability, or circumstances beyond our control.</li>
          <li>For large appliances like refrigerators, delivery may be scheduled separately from installation or demonstration, which our service partner will arrange with you directly.</li>
          <li>Risk in the Products passes to you once delivered and accepted at the address provided.</li>
        </ul>
      </section>

      <section id="cancellation">
        <div class="section-head">
          <span class="section-icon"><svg><use href="#i-x-circle"/></svg></span>
          <div><span class="section-num mono">§ 07</span><h2>Cancellation policy</h2></div>
        </div>
        <p>Orders can usually be cancelled free of charge before dispatch, from your account or by contacting support. Once a Product has been dispatched, cancellation may no longer be possible — you can instead request a return once it's delivered, under Section 8.</p>
      </section>

      <section id="returns">
        <div class="section-head">
          <span class="section-icon"><svg><use href="#i-refresh-ccw"/></svg></span>
          <div><span class="section-num mono">§ 08</span><h2>Returns, replacement &amp; refunds</h2></div>
        </div>
        <ul>
          <li>Most Products can be returned within <strong>7 days</strong> of delivery, if unused, in original condition, with all original packaging, accessories and the invoice.</li>
          <li>Some items aren't returnable once opened or activated — including activated SIM-enabled mobile devices, and other items excluded for hygiene, safety or regulatory reasons, as noted on the product page.</li>
          <li>Approved refunds are processed to your original payment method within <strong>5–7 business days</strong> of the return passing quality checks. Cash on Delivery orders are refunded by bank transfer or Platform credit.</li>
          <li>A Product that's damaged, defective, or materially different from its description on delivery can be replaced or refunded at no extra cost, once verified.</li>
        </ul>
      </section>

      <section id="warranty">
        <div class="section-head">
          <span class="section-icon green"><svg><use href="#i-shield"/></svg></span>
          <div><span class="section-num mono">§ 09</span><h2>Warranty</h2></div>
        </div>
        <p>Mobile phones, refrigerators and other electronics are covered by the manufacturer's warranty, honoured through the manufacturer's authorised service network — its terms are provided with the Product.</p>
        <p>Where we or our partners offer an extended warranty or protection plan, its terms are shown separately at the time of purchase. To raise a warranty claim, contact the manufacturer's authorised service centre or our support team with your invoice, and, for mobile phones, the device's IMEI number.</p>
      </section>

      <section id="emi">
        <div class="section-head">
          <span class="section-icon"><svg><use href="#i-percent"/></svg></span>
          <div><span class="section-num mono">§ 10</span><h2>EMI &amp; financing</h2></div>
        </div>
        <p>EMI, cardless credit and similar financing options are provided by third-party banks and NBFCs ("Financing Partners") and are subject to their own terms, interest rates, processing fees and eligibility criteria. Harahori Telecom isn't a party to, and isn't responsible for, the financing agreement between you and your Financing Partner.</p>
      </section>

      <section id="conduct">
        <div class="section-head">
          <span class="section-icon"><svg><use href="#i-alert"/></svg></span>
          <div><span class="section-num mono">§ 11</span><h2>User conduct</h2></div>
        </div>
        <p>You agree not to:</p>
        <ul>
          <li>Place fraudulent Orders or use stolen payment information.</li>
          <li>Resell Products bought at consumer pricing for unauthorised commercial purposes, in breach of manufacturer distribution terms.</li>
          <li>Attempt unauthorised access to the Platform, scrape its data, or interfere with its operation.</li>
          <li>Post false, defamatory or misleading reviews.</li>
        </ul>
      </section>

      <section id="reviews">
        <div class="section-head">
          <span class="section-icon"><svg><use href="#i-star"/></svg></span>
          <div><span class="section-num mono">§ 12</span><h2>Reviews &amp; user content</h2></div>
        </div>
        <p>Any review, rating, image or other content you submit may be displayed publicly. You grant Harahori Telecom a non-exclusive, royalty-free, worldwide licence to use, reproduce and display that content in connection with operating and promoting the Platform. You must own the rights to anything you submit, and it must not violate anyone else's rights.</p>
      </section>

      <section id="ip">
        <div class="section-head">
          <span class="section-icon"><svg><use href="#i-badge"/></svg></span>
          <div><span class="section-num mono">§ 13</span><h2>Intellectual property</h2></div>
        </div>
        <p>The Platform — including its design, logos, trademarks, text and software — is owned by or licensed to Harahori Telecom LLP and is protected by applicable IP laws. You may not copy, modify or use it without our prior written consent.</p>
      </section>

      <section id="thirdparty">
        <div class="section-head">
          <span class="section-icon"><svg><use href="#i-external"/></svg></span>
          <div><span class="section-num mono">§ 14</span><h2>Third-party links &amp; services</h2></div>
        </div>
        <p>The Platform may link to or integrate third-party services — payment gateways, financing partners, manufacturer support pages. We don't control, and aren't responsible for, the content, policies or practices of those third parties.</p>
      </section>

      <section id="disclaimer">
        <div class="section-head">
          <span class="section-icon"><svg><use href="#i-info"/></svg></span>
          <div><span class="section-num mono">§ 15</span><h2>Disclaimer of warranties</h2></div>
        </div>
        <p>The Platform is provided "as is" and "as available." To the fullest extent the law allows, we disclaim all warranties, express or implied, about its uninterrupted or error-free operation — apart from statutory warranties that can't be excluded, and the manufacturer warranties described in Section 9.</p>
      </section>

      <section id="liability">
        <div class="section-head">
          <span class="section-icon"><svg><use href="#i-scale"/></svg></span>
          <div><span class="section-num mono">§ 16</span><h2>Limitation of liability</h2></div>
        </div>
        <p>To the extent the law allows, Harahori Telecom's total liability arising from your use of the Platform or any Order won't exceed the amount you paid for that Order. We won't be liable for indirect, incidental or consequential damages.</p>
      </section>

      <section id="indemnification">
        <div class="section-head">
          <span class="section-icon"><svg><use href="#i-shield"/></svg></span>
          <div><span class="section-num mono">§ 17</span><h2>Indemnification</h2></div>
        </div>
        <p>You agree to indemnify and hold harmless Harahori Telecom, its partners and employees from any claim, loss or expense arising from your breach of these Terms or misuse of the Platform.</p>
      </section>

      <section id="force-majeure">
        <div class="section-head">
          <span class="section-icon"><svg><use href="#i-cloud"/></svg></span>
          <div><span class="section-num mono">§ 18</span><h2>Force majeure</h2></div>
        </div>
        <p>We won't be liable for any delay or failure to perform caused by events beyond our reasonable control, including natural disasters, strikes, transport disruption, or government action.</p>
      </section>

      <section id="governing-law">
        <div class="section-head">
          <span class="section-icon"><svg><use href="#i-scale"/></svg></span>
          <div><span class="section-num mono">§ 19</span><h2>Governing law &amp; jurisdiction</h2></div>
        </div>
        <p>These Terms are governed by the laws of India. Subject to Section 20, the courts at <span class="fill-in" title="Replace with your operating city">[City]</span>, West Bengal have exclusive jurisdiction over any dispute arising from these Terms.</p>
      </section>

      <section id="grievance">
        <div class="section-head">
          <span class="section-icon"><svg><use href="#i-message"/></svg></span>
          <div><span class="section-num mono">§ 20</span><h2>Grievance redressal &amp; dispute resolution</h2></div>
        </div>
        <p>In line with the Consumer Protection (E-Commerce) Rules, 2020 and the Information Technology Act, 2000, complaints can first be raised with our Grievance Officer (see Section 22). We aim to acknowledge complaints within <strong>48 hours</strong> and resolve them within <strong>30 days</strong>. This doesn't affect your right to approach a consumer forum or other competent authority at any time.</p>
      </section>

      <section id="amendments">
        <div class="section-head">
          <span class="section-icon"><svg><use href="#i-edit"/></svg></span>
          <div><span class="section-num mono">§ 21</span><h2>Amendments</h2></div>
        </div>
        <p>We may revise these Terms from time to time. Continuing to use the Platform after a change is posted means you accept the revised Terms — it's worth checking this page occasionally.</p>
      </section>

      <section id="contact">
        <div class="section-head">
          <span class="section-icon"><svg><use href="#i-mail"/></svg></span>
          <div><span class="section-num mono">§ 22</span><h2>Contact us</h2></div>
        </div>
        <div class="contact-card">
          <div class="spec-title" style="color:rgba(255,255,255,.5);border-color:rgba(255,255,255,.14);">Harahori Telecom LLP</div>
          <div class="spec-row"><span class="spec-label">Registered office</span><span class="spec-value fill-in">[Registered Address]</span></div>
          <div class="spec-row"><span class="spec-label">LLPIN</span><span class="spec-value fill-in">[LLPIN Number]</span></div>
          <div class="spec-row"><span class="spec-label">GSTIN</span><span class="spec-value fill-in">[GSTIN Number]</span></div>
          <div class="spec-row"><span class="spec-label">Email</span><span class="spec-value">support@harahoritelecom.com</span></div>
          <div class="spec-row"><span class="spec-label">Phone</span><span class="spec-value fill-in">[Phone Number]</span></div>
        </div>
      </section>
    </main>
  </div>
</div>

<footer class="site-footer">
  <div class="container footer-inner">
    <div>
      <div class="footer-brand">
        <span class="brand-mark"><svg width="18" height="18"><use href="#i-bolt"/></svg></span>
        Harahori Telecom LLP
      </div>
      <p>Phones, refrigerators and electronics, delivered and backed by genuine manufacturer warranty support.</p>
    </div>
    <div>
      <h5>Legal</h5>
      <div class="footer-links">
        <a href="privacy-policy.html">Privacy Policy</a>
        <a href="terms-and-conditions.html">Terms &amp; Conditions</a>
        <a href="delete-account.html">Delete Account</a>
      </div>
    </div>
    <div>
      <h5>Registered office</h5>
      <p class="mono" style="font-size:.82rem;"><span class="fill-in">[Registered Address]</span><br>LLPIN: <span class="fill-in">[LLPIN Number]</span><br>GSTIN: <span class="fill-in">[GSTIN Number]</span></p>
    </div>
  </div>
  <div class="footer-bottom">
    <span>© <span id="year"></span> Harahori Telecom LLP. All rights reserved.</span>
    <span>Registered as a Limited Liability Partnership in India</span>
  </div>
</footer>

<button class="back-to-top" id="backToTop" aria-label="Back to top" hidden>
  <svg width="18" height="18"><use href="#i-up"/></svg>
</button>

<script>
  const navToggle = document.getElementById('navToggle');
  const siteNav = document.getElementById('siteNav');
  const navIcon = document.getElementById('navIcon');
  navToggle.addEventListener('click', () => {
    const isOpen = siteNav.classList.toggle('is-open');
    navToggle.setAttribute('aria-expanded', String(isOpen));
    navIcon.innerHTML = isOpen ? '<use href="#i-close"/>' : '<use href="#i-menu"/>';
  });

  const sections = document.querySelectorAll('main.content > section[id]');
  const tocDesktop = document.getElementById('tocDesktop');
  const tocMobile = document.getElementById('tocMobile');
  sections.forEach((sec, i) => {
    const h2 = sec.querySelector('h2');
    if (!h2) return;
    const num = String(i + 1).padStart(2, '0');
    const linkDesktop = document.createElement('a');
    linkDesktop.href = '#' + sec.id;
    linkDesktop.innerHTML = '<span class="n">' + num + '</span><span>' + h2.textContent + '</span>';
    tocDesktop.appendChild(linkDesktop);

    const linkMobile = document.createElement('a');
    linkMobile.href = '#' + sec.id;
    linkMobile.textContent = num + '  ' + h2.textContent;
    tocMobile.appendChild(linkMobile);
  });

  const tocLinks = tocDesktop.querySelectorAll('a');
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        tocLinks.forEach(l => l.classList.remove('is-active'));
        const active = tocDesktop.querySelector('a[href="#' + entry.target.id + '"]');
        if (active) active.classList.add('is-active');
      }
    });
  }, { rootMargin: '-15% 0px -70% 0px' });
  sections.forEach(sec => observer.observe(sec));

  const backToTop = document.getElementById('backToTop');
  window.addEventListener('scroll', () => { backToTop.hidden = window.scrollY < 600; });
  backToTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

  document.getElementById('printBtn').addEventListener('click', () => window.print());
  document.getElementById('year').textContent = new Date().getFullYear();
</script>
</body>
</html>
