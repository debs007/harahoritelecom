<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Privacy Policy — Harahori Telecom LLP</title>
<meta name="description" content="How Harahori Telecom LLP collects, uses and protects your data when you shop for phones, refrigerators and electronics with us.">
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

  /* ============ FOCUS STATES ============ */
  a:focus-visible,button:focus-visible,input:focus-visible,select:focus-visible,summary:focus-visible{
    outline:2px solid var(--accent);outline-offset:2px;border-radius:4px;
  }

  /* ============ HEADER ============ */
  .site-header{
    position:sticky;top:0;z-index:50;
    background:var(--paper);
    border-bottom:1px solid var(--border);
    height:var(--header-h);
  }
  .header-inner{
    max-width:var(--maxw);margin:0 auto;height:100%;padding:0 24px;
    display:flex;align-items:center;justify-content:space-between;
  }
  .brand{display:flex;align-items:center;gap:10px;font-weight:700;font-size:1.05rem;color:var(--ink);}
  .brand-mark{
    width:36px;height:36px;border-radius:var(--radius-sm);
    background:var(--ink);color:var(--accent-bright);
    display:flex;align-items:center;justify-content:center;flex-shrink:0;
  }
  .brand-mark svg{width:19px;height:19px;}
  .brand-name span{color:var(--muted);font-weight:500;}

  .site-nav{
    display:none;flex-direction:column;gap:2px;
    position:absolute;top:var(--header-h);left:0;right:0;
    background:var(--paper);border-bottom:1px solid var(--border);
    padding:10px;box-shadow:var(--shadow-md);
  }
  .site-nav.is-open{display:flex;}
  .site-nav a{position:relative;padding:12px 14px;font-size:.95rem;font-weight:500;border-radius:var(--radius-sm);}
  .site-nav a:hover{background:var(--card);}
  .site-nav a.is-current{color:var(--ink);font-weight:600;background:var(--accent-soft);}

  .nav-toggle{
    width:40px;height:40px;display:flex;align-items:center;justify-content:center;
    border-radius:var(--radius-sm);
  }
  .nav-toggle:hover{background:var(--card);}
  .nav-toggle svg{width:20px;height:20px;}

  @media (min-width:860px){
    .site-nav{
      display:flex;flex-direction:row;position:static;background:none;border:none;
      box-shadow:none;padding:0;gap:28px;
    }
    .site-nav a{padding:6px 0;}
    .site-nav a::after{
      content:'';position:absolute;left:0;right:0;bottom:0;height:2px;
      background:var(--accent);transform:scaleX(0);transform-origin:left;
      transition:transform .18s ease;
    }
    .site-nav a:hover::after,.site-nav a.is-current::after{transform:scaleX(1);}
    .site-nav a.is-current{background:none;}
    .nav-toggle{display:none;}
  }

  /* ============ HERO ============ */
  .hero{background:var(--ink);color:#fff;position:relative;overflow:hidden;padding:56px 0 72px;}
  .hero::before{
    content:'';position:absolute;inset:0;
    background-image:radial-gradient(rgba(255,255,255,.07) 1px,transparent 1px);
    background-size:24px 24px;opacity:.6;pointer-events:none;
  }
  .hero-inner{
    position:relative;z-index:1;display:grid;grid-template-columns:1fr;gap:36px;align-items:end;
  }
  @media (min-width:860px){
    .hero-inner{grid-template-columns:1.3fr 1fr;align-items:center;}
  }
  .path-label{
    font-family:var(--mono);font-size:.78rem;letter-spacing:.06em;
    color:var(--accent-bright);margin-bottom:14px;
  }
  .hero h1{color:#fff;font-size:clamp(2.1rem,4vw + 1rem,3.1rem);}
  .hero .lead{color:rgba(255,255,255,.75);font-size:1.08rem;max-width:52ch;margin-top:14px;}
  .hero-actions{margin-top:26px;display:flex;gap:12px;flex-wrap:wrap;}

  .btn{
    display:inline-flex;align-items:center;gap:8px;padding:12px 20px;
    border-radius:var(--radius-sm);font-weight:600;font-size:.92rem;
    transition:transform .15s ease,box-shadow .15s ease,background .15s ease,border-color .15s ease;
  }
  .btn svg{width:16px;height:16px;}
  .btn-ghost-invert{background:rgba(255,255,255,.08);color:#fff;border:1px solid rgba(255,255,255,.25);}
  .btn-ghost-invert:hover{background:rgba(255,255,255,.16);}
  .btn-primary{background:var(--accent);color:#fff;box-shadow:var(--shadow-sm);}
  .btn-primary:hover{background:#a97a0c;transform:translateY(-1px);box-shadow:var(--shadow-md);}
  .btn-outline{background:var(--card);border:1.5px solid var(--border);color:var(--ink);}
  .btn-outline:hover{border-color:var(--ink);}

  /* Spec card — signature element */
  .spec-card{
    background:var(--card);color:var(--text);border-radius:var(--radius-md);
    padding:22px 22px 18px;box-shadow:var(--shadow-lg);position:relative;
  }
  .spec-card::before,.spec-card::after{
    content:'';position:absolute;top:-9px;width:18px;height:18px;border-radius:50%;
    background:var(--ink);
  }
  .spec-card::before{left:22px;}
  .spec-card::after{right:22px;}
  .spec-title{
    font-family:var(--mono);font-size:.7rem;letter-spacing:.08em;color:var(--muted);
    text-transform:uppercase;margin-bottom:14px;padding-bottom:12px;border-bottom:1px dashed var(--border);
  }
  .spec-row{display:flex;justify-content:space-between;gap:16px;padding:8px 0;font-size:.88rem;}
  .spec-row + .spec-row{border-top:1px solid var(--border);}
  .spec-label{color:var(--muted);font-family:var(--mono);font-size:.78rem;}
  .spec-value{font-weight:600;font-family:var(--mono);text-align:right;}
  .led{display:inline-block;width:7px;height:7px;border-radius:50%;margin-right:7px;position:relative;top:-1px;}
  .led-green{background:var(--green);box-shadow:0 0 0 3px var(--green-soft);}

  /* ============ LAYOUT: TOC + CONTENT ============ */
  .page-shell{max-width:var(--maxw);margin:0 auto;padding:0 24px 90px;}
  .toc-mobile-wrap{margin:28px 0 8px;}
  .toc-mobile-wrap details{
    background:var(--card);border:1px solid var(--border);border-radius:var(--radius-md);padding:16px 18px;
  }
  .toc-mobile-wrap summary{
    font-family:var(--mono);font-size:.82rem;font-weight:600;letter-spacing:.03em;
    display:flex;align-items:center;justify-content:space-between;cursor:pointer;
  }
  .toc-mobile-wrap summary::-webkit-details-marker{display:none;}
  .toc-mobile-wrap .chev{width:16px;height:16px;transition:transform .2s ease;}
  .toc-mobile-wrap details[open] .chev{transform:rotate(180deg);}
  .toc-mobile-wrap nav{margin-top:14px;display:flex;flex-direction:column;gap:2px;}
  .toc-mobile-wrap nav a{padding:8px 10px;border-radius:8px;font-size:.9rem;color:var(--muted);}
  .toc-mobile-wrap nav a:hover{background:var(--paper);color:var(--text);}

  .page-layout{display:grid;grid-template-columns:1fr;gap:40px;margin-top:28px;}
  @media (min-width:1024px){
    .page-layout{grid-template-columns:250px 1fr;align-items:start;}
    .toc-mobile-wrap{display:none;}
  }
  .toc-desktop{display:none;}
  @media (min-width:1024px){
    .toc-desktop{
      display:block;position:sticky;top:calc(var(--header-h) + 24px);
      background:var(--card);border:1px solid var(--border);border-radius:var(--radius-md);
      padding:18px;max-height:calc(100vh - var(--header-h) - 48px);overflow-y:auto;
    }
    .toc-desktop h4{font-family:var(--mono);font-size:.72rem;letter-spacing:.06em;color:var(--muted);
      text-transform:uppercase;margin-bottom:10px;}
    .toc-desktop nav{display:flex;flex-direction:column;gap:1px;}
    .toc-desktop a{
      display:flex;gap:8px;padding:7px 9px;border-radius:7px;font-size:.86rem;color:var(--muted);
      border-left:2px solid transparent;
    }
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
  .section-icon{
    width:42px;height:42px;border-radius:var(--radius-sm);background:var(--accent-soft);color:#8a660b;
    display:flex;align-items:center;justify-content:center;flex-shrink:0;
  }
  .section-icon.green{background:var(--green-soft);color:var(--green);}
  .section-icon svg{width:21px;height:21px;}
  .section-num{font-family:var(--mono);font-size:.78rem;color:var(--muted);display:block;margin-bottom:2px;}
  .content h2{font-size:1.35rem;}
  .content h3{font-size:1.05rem;margin:18px 0 6px;}
  .content p{color:#3a3d38;margin-bottom:12px;max-width:75ch;}
  .content ul,.content ol{margin:8px 0 16px 0;max-width:75ch;}
  .content li{position:relative;padding-left:20px;margin-bottom:9px;color:#3a3d38;}
  .content li::before{
    content:'';position:absolute;left:0;top:9px;width:5px;height:5px;border-radius:50%;background:var(--accent);
  }
  .content strong{color:var(--ink);}

  .callout{
    background:var(--card);border:1px solid var(--border);border-radius:var(--radius-md);
    padding:18px 20px;margin:16px 0;display:flex;gap:14px;
  }
  .callout svg{width:20px;height:20px;flex-shrink:0;margin-top:2px;color:var(--accent);}
  .callout.amber-tint{background:var(--accent-soft);border-color:#e9d29e;}
  .callout p{margin:0;font-size:.92rem;}

  .contact-card{
    background:var(--ink);color:#fff;border-radius:var(--radius-md);padding:26px;margin-top:8px;
  }
  .contact-card .spec-row{border-color:rgba(255,255,255,.14);}
  .contact-card .spec-row + .spec-row{border-top:1px solid rgba(255,255,255,.14);}
  .contact-card .spec-label{color:rgba(255,255,255,.55);}
  .contact-card .spec-value{color:#fff;}
  .fill-in{border-bottom:1.5px dashed var(--accent-bright);color:var(--accent-bright);cursor:help;font-weight:600;}
  .contact-card .fill-in{color:#f0cf82;border-bottom-color:#f0cf82;}

  /* ============ FOOTER ============ */
  .site-footer{background:var(--ink);color:rgba(255,255,255,.7);padding:52px 0 28px;margin-top:40px;}
  .footer-inner{display:grid;gap:32px;grid-template-columns:1fr;}
  @media (min-width:768px){.footer-inner{grid-template-columns:1.4fr 1fr 1fr;}}
  .footer-inner h5{font-family:var(--mono);font-size:.75rem;letter-spacing:.06em;color:rgba(255,255,255,.4);
    text-transform:uppercase;margin-bottom:14px;}
  .footer-brand{display:flex;align-items:center;gap:10px;color:#fff;font-weight:700;margin-bottom:10px;}
  .footer-brand .brand-mark{background:rgba(255,255,255,.1);color:var(--accent-bright);}
  .footer-inner p{font-size:.88rem;line-height:1.6;max-width:38ch;color:rgba(255,255,255,.55);}
  .footer-links{display:flex;flex-direction:column;gap:9px;}
  .footer-links a{font-size:.9rem;color:rgba(255,255,255,.7);}
  .footer-links a:hover{color:#fff;}
  .footer-bottom{
    max-width:var(--maxw);margin:36px auto 0;padding:0 24px;padding-top:22px;
    border-top:1px solid rgba(255,255,255,.12);display:flex;justify-content:space-between;
    flex-wrap:wrap;gap:10px;font-size:.8rem;color:rgba(255,255,255,.45);
  }

  /* ============ BACK TO TOP ============ */
  .back-to-top{
    position:fixed;bottom:24px;right:24px;width:46px;height:46px;border-radius:50%;
    background:var(--ink);color:#fff;display:flex;align-items:center;justify-content:center;
    box-shadow:var(--shadow-md);z-index:40;transition:transform .15s ease,opacity .15s ease;
  }
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

  /* ============ REDUCED MOTION ============ */
  @media (prefers-reduced-motion:reduce){
    *{animation:none!important;transition:none!important;scroll-behavior:auto!important;}
  }
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
  <symbol id="i-database" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5.5" rx="8" ry="3"/><path d="M4 5.5v6c0 1.7 3.6 3 8 3s8-1.3 8-3v-6"/><path d="M4 11.5v6c0 1.7 3.6 3 8 3s8-1.3 8-3v-6"/></symbol>
  <symbol id="i-gear" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M12 2.5v3M12 18.5v3M4.2 4.2l2.1 2.1M17.7 17.7l2.1 2.1M2.5 12h3M18.5 12h3M4.2 19.8l2.1-2.1M17.7 6.3l2.1-2.1"/></symbol>
  <symbol id="i-cookie" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><circle cx="9" cy="9.5" r="1" fill="currentColor" stroke="none"/><circle cx="14.5" cy="8.5" r="1" fill="currentColor" stroke="none"/><circle cx="15" cy="13.5" r="1" fill="currentColor" stroke="none"/><circle cx="9.5" cy="15" r="1" fill="currentColor" stroke="none"/><circle cx="13" cy="17" r="1" fill="currentColor" stroke="none"/></symbol>
  <symbol id="i-users" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="8.5" cy="8" r="3"/><path d="M2.5 20c0-3.6 2.7-6.5 6-6.5s6 2.9 6 6.5"/><circle cx="17" cy="8.5" r="2.3"/><path d="M15.5 13.6c2.4.5 4 2.9 4 6.4"/></symbol>
  <symbol id="i-shield" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v6c0 4.8-3 8.4-7 9.5-4-1.1-7-4.7-7-9.5V6l7-3z"/><path d="M8.7 12l2.3 2.3 4.3-4.6"/></symbol>
  <symbol id="i-clock" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5.5l4 2.3"/></symbol>
  <symbol id="i-check-circle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M8 12.5l2.5 2.5L16 9.5"/></symbol>
  <symbol id="i-alert" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3.5 21.5 20h-19L12 3.5z"/><path d="M12 10v4.2"/><path d="M12 17.3h.01"/></symbol>
  <symbol id="i-external" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M14 4h6v6"/><path d="M20 4 10 14"/><path d="M18 13v6a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h6"/></symbol>
  <symbol id="i-globe" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M3 12h18"/><path d="M12 3c2.5 2.5 3.8 5.7 3.8 9s-1.3 6.5-3.8 9c-2.5-2.5-3.8-5.7-3.8-9s1.3-6.5 3.8-9Z"/></symbol>
  <symbol id="i-refresh" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 4.5v5.5h5.5"/><path d="M19.5 19.5V14h-5.5"/><path d="M5.6 14.5a7 7 0 0 0 12.3 2.8l1.6-2.3"/><path d="M18.4 9.5A7 7 0 0 0 6.1 6.7L4.5 9"/></symbol>
  <symbol id="i-mail" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="2.5" y="5" width="19" height="14" rx="2"/><path d="M3 6.5 12 13l9-6.5"/></symbol>
</svg>

<header class="site-header">
  <div class="header-inner">
    <a href="index.html" class="brand">
      <span class="brand-mark"><svg width="18" height="18"><use href="#i-bolt"/></svg></span>
      <span class="brand-name">Harahori <span>Telecom</span></span>
    </a>
    <nav class="site-nav" id="siteNav">
      <a href="privacy-policy.html" class="is-current">Privacy Policy</a>
      <a href="terms-and-conditions.html">Terms &amp; Conditions</a>
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
      <h1>Privacy Policy</h1>
      <p class="lead">What we collect when you shop for phones, refrigerators and electronics with us, how it's used, and the choices you have.</p>
      <div class="hero-actions">
        <button class="btn btn-ghost-invert" id="printBtn"><svg><use href="#i-print"/></svg> Print this policy</button>
      </div>
    </div>
    <div class="spec-card" aria-label="Document details">
      <div class="spec-title">Document details</div>
      <div class="spec-row"><span class="spec-label">Doc No.</span><span class="spec-value">HT/PP/2026-03</span></div>
      <div class="spec-row"><span class="spec-label">Effective</span><span class="spec-value">27 Jul 2026</span></div>
      <div class="spec-row"><span class="spec-label">Applies to</span><span class="spec-value">Web, App &amp; In-store</span></div>
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
        <p><strong>Harahori Telecom LLP</strong> ("Harahori Telecom", "we", "us", "our") operates an online platform for purchasing electronics and home appliances — including smartphones, refrigerators, televisions and other consumer electronics (the "Platform"). This Policy explains what personal information we collect, how we use and share it, and the choices you have.</p>
        <p>By using our website, mobile app, or services, you agree to the practices described here. If anything is unclear, our contact details are at the end of this page.</p>
      </div>

      <section id="collect">
        <div class="section-head">
          <span class="section-icon"><svg><use href="#i-database"/></svg></span>
          <div><span class="section-num mono">§ 01</span><h2>Information we collect</h2></div>
        </div>
        <h3>Information you provide</h3>
        <ul>
          <li><strong>Account details</strong> — name, email, mobile number and password.</li>
          <li><strong>Delivery details</strong> — shipping address, billing address, landmark and pin code.</li>
          <li><strong>Order details</strong> — products purchased (mobile handsets, refrigerators, other appliances) and, where relevant, device IMEI or serial numbers for warranty registration.</li>
          <li><strong>Payment details</strong> — we never store your full card number or CVV; payments are processed by our RBI-authorised payment partners. We may retain the last four digits of a card, a UPI ID, or a payment token for order history and refunds.</li>
          <li><strong>Communications</strong> — messages to customer support, reviews and ratings, survey responses.</li>
        </ul>
        <h3>Information collected automatically</h3>
        <ul>
          <li>Device information — model, operating system, unique device identifiers.</li>
          <li>Log data — IP address, browser type, pages viewed, time on page, referring URL.</li>
          <li>Location — approximate location from your IP, or, with your permission, precise GPS location in our app to estimate delivery times.</li>
          <li>Cookies and similar technologies — see Section 3.</li>
        </ul>
        <h3>Information from third parties</h3>
        <ul>
          <li>Payment gateways and banks — transaction status and fraud-risk signals.</li>
          <li>Logistics and delivery partners — delivery status updates.</li>
          <li>EMI and financing partners, where you choose to pay via EMI or cardless credit.</li>
          <li>Manufacturers and authorised service centres, for warranty and after-sales coordination.</li>
        </ul>
      </section>

      <section id="use">
        <div class="section-head">
          <span class="section-icon"><svg><use href="#i-gear"/></svg></span>
          <div><span class="section-num mono">§ 02</span><h2>How we use your information</h2></div>
        </div>
        <ul>
          <li>Process, confirm and deliver your orders — including scheduling installation or demonstration for large appliances such as refrigerators.</li>
          <li>Register product warranties and coordinate after-sales service and repairs.</li>
          <li>Provide customer support and respond to queries or complaints.</li>
          <li>Personalise your shopping experience and recommend relevant products.</li>
          <li>Send order updates, service alerts and — with your consent — promotional communications.</li>
          <li>Detect and prevent fraud, unauthorised transactions and misuse of the Platform.</li>
          <li>Meet our tax, accounting, consumer-protection and other legal obligations.</li>
          <li>Improve our website, app and services through analytics.</li>
        </ul>
      </section>

      <section id="cookies">
        <div class="section-head">
          <span class="section-icon"><svg><use href="#i-cookie"/></svg></span>
          <div><span class="section-num mono">§ 03</span><h2>Cookies &amp; tracking technologies</h2></div>
        </div>
        <ul>
          <li><strong>Essential cookies</strong> keep you logged in and remember your cart — the Platform won't work properly without these.</li>
          <li><strong>Performance cookies</strong> help us understand how the Platform is used, so we can fix issues and improve it.</li>
          <li><strong>Marketing cookies</strong> show you relevant offers on our Platform and elsewhere, only with consent where required by law.</li>
        </ul>
        <p>You can manage cookie preferences through your browser settings or our cookie-consent banner. Disabling essential cookies may affect how the Platform works.</p>
      </section>

      <section id="share">
        <div class="section-head">
          <span class="section-icon"><svg><use href="#i-users"/></svg></span>
          <div><span class="section-num mono">§ 04</span><h2>How we share your information</h2></div>
        </div>
        <ul>
          <li>Delivery and logistics partners, so they can ship and deliver your order.</li>
          <li>Payment gateways, banks and EMI/NBFC partners, to process payments and financing.</li>
          <li>Installation and after-sales technicians, and manufacturers, for warranty support.</li>
          <li>Cloud hosting and IT service providers who process data on our behalf, under confidentiality obligations.</li>
          <li>Government or regulatory authorities, where required by law, court order, or to protect the rights and safety of Harahori Telecom, our users, or the public.</li>
        </ul>
        <div class="callout amber-tint">
          <svg><use href="#i-check-circle"/></svg>
          <p>We do not sell your personal information to third parties for their own independent marketing purposes.</p>
        </div>
      </section>

      <section id="security">
        <div class="section-head">
          <span class="section-icon green"><svg><use href="#i-shield"/></svg></span>
          <div><span class="section-num mono">§ 05</span><h2>Data security</h2></div>
        </div>
        <ul>
          <li>Industry-standard encryption (TLS) for data in transit.</li>
          <li>Access controls that limit employee access to personal data on a need-to-know basis.</li>
          <li>PCI-DSS compliant payment processing through our payment partners.</li>
          <li>Regular security reviews of our systems and vendors.</li>
        </ul>
        <p>No method of transmission or storage is completely secure, and we can't guarantee absolute security — but protecting your data is something we take seriously at every layer of the Platform.</p>
      </section>

      <section id="retention">
        <div class="section-head">
          <span class="section-icon"><svg><use href="#i-clock"/></svg></span>
          <div><span class="section-num mono">§ 06</span><h2>Data retention</h2></div>
        </div>
        <p>We keep your account and order information for as long as your account is active, and afterwards for as long as required to meet tax, accounting, warranty and consumer-protection obligations — typically up to <strong>8 years</strong> from your last transaction — or to resolve disputes and enforce our agreements.</p>
      </section>

      <section id="rights">
        <div class="section-head">
          <span class="section-icon"><svg><use href="#i-check-circle"/></svg></span>
          <div><span class="section-num mono">§ 07</span><h2>Your rights &amp; choices</h2></div>
        </div>
        <ul>
          <li>Access and update your profile from your account settings.</li>
          <li>Opt out of promotional email, SMS or push notifications at any time.</li>
          <li>Request a copy of the personal data we hold about you.</li>
          <li>Request deletion of your account and personal data, subject to our legal retention duties — visit our <a href="delete-account.html" style="text-decoration:underline;">Delete Account</a> page to start this.</li>
          <li>Withdraw consent for optional uses (like location access or marketing cookies) at any time.</li>
        </ul>
      </section>

      <section id="children">
        <div class="section-head">
          <span class="section-icon"><svg><use href="#i-alert"/></svg></span>
          <div><span class="section-num mono">§ 08</span><h2>Children's privacy</h2></div>
        </div>
        <p>The Platform is intended for people aged 18 or over, or purchases made with a parent or guardian's involvement. We don't knowingly collect personal information from children. If we learn we've done so inadvertently, we'll delete it.</p>
      </section>

      <section id="thirdparty">
        <div class="section-head">
          <span class="section-icon"><svg><use href="#i-external"/></svg></span>
          <div><span class="section-num mono">§ 09</span><h2>Third-party links</h2></div>
        </div>
        <p>The Platform may link to third-party sites, such as manufacturer support pages. We aren't responsible for their privacy practices, and we encourage you to review their policies before sharing information with them.</p>
      </section>

      <section id="international">
        <div class="section-head">
          <span class="section-icon"><svg><use href="#i-globe"/></svg></span>
          <div><span class="section-num mono">§ 10</span><h2>International data transfers</h2></div>
        </div>
        <p>Your information may be stored and processed on servers outside your state or country, including for cloud-hosting purposes. Where this happens, we take steps to keep your data protected to the standard described in this Policy.</p>
      </section>

      <section id="changes">
        <div class="section-head">
          <span class="section-icon"><svg><use href="#i-refresh"/></svg></span>
          <div><span class="section-num mono">§ 11</span><h2>Changes to this policy</h2></div>
        </div>
        <p>We may update this Policy from time to time. We'll flag material changes on the Platform or by email, and update the "Effective" date at the top of this page. Using the Platform after changes take effect means you accept the update.</p>
      </section>

      <section id="contact">
        <div class="section-head">
          <span class="section-icon"><svg><use href="#i-mail"/></svg></span>
          <div><span class="section-num mono">§ 12</span><h2>Grievance officer &amp; contact us</h2></div>
        </div>
        <p>In line with applicable Indian data-protection and IT regulations, our Grievance Officer handles privacy-related complaints.</p>
        <div class="contact-card">
          <div class="spec-title" style="color:rgba(255,255,255,.5);border-color:rgba(255,255,255,.14);">Grievance officer</div>
          <div class="spec-row"><span class="spec-label">Name</span><span class="spec-value fill-in">[Grievance Officer Name]</span></div>
          <div class="spec-row"><span class="spec-label">Email</span><span class="spec-value">privacy@harahoritelecom.com</span></div>
          <div class="spec-row"><span class="spec-label">Address</span><span class="spec-value fill-in">[Registered Office Address]</span></div>
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
  // Mobile nav toggle
  const navToggle = document.getElementById('navToggle');
  const siteNav = document.getElementById('siteNav');
  const navIcon = document.getElementById('navIcon');
  navToggle.addEventListener('click', () => {
    const isOpen = siteNav.classList.toggle('is-open');
    navToggle.setAttribute('aria-expanded', String(isOpen));
    navIcon.innerHTML = isOpen ? '<use href="#i-close"/>' : '<use href="#i-menu"/>';
  });

  // Build TOC (desktop + mobile) from sections
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

  // Highlight active TOC link on scroll
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

  // Back to top
  const backToTop = document.getElementById('backToTop');
  window.addEventListener('scroll', () => {
    backToTop.hidden = window.scrollY < 600;
  });
  backToTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

  // Print
  document.getElementById('printBtn').addEventListener('click', () => window.print());

  // Year
  document.getElementById('year').textContent = new Date().getFullYear();
</script>
</body>
</html>
