<?php if ( ! defined( 'ABSPATH' ) ) exit; get_header(); ?>
  <style>
    /* ===== Página El teléfono ===== */
    .tel-track{ position:relative; height:1100vh; background:var(--bg); }   /* recorrido más corto (animación más rápida) + margen tras el último texto antes del footer */
    .tel-stage{ position:sticky; top:0; height:100vh; overflow:hidden; display:grid; place-items:center; }

    /* Número gigante con la imagen a través de los dígitos */
    .tel-frag{ position:absolute; inset:0; width:100%; height:100%; z-index:2; pointer-events:none; }
    .tel-cover{ position:absolute; z-index:2; left:0; right:0; top:43%; transform:translateY(-50%); display:flex; flex-direction:column; align-items:center; text-align:center; padding:0 24px; }
    .tel-number{ font-family:var(--font-body); font-weight:900;
      font-size:clamp(3.5rem,14vw,14.5rem); line-height:.95; letter-spacing:-.04em; white-space:nowrap;
      filter:drop-shadow(0 7px 13px rgba(11,14,18,.26)); will-change:transform,opacity,filter; }   /* pequeña sombra debajo del número */
    .tel-number .tn-a{ padding-right:.06em; background:linear-gradient(180deg,#3a3e46 0%,#212428 55%,#15171b 100%); -webkit-background-clip:text; background-clip:text; color:transparent; }   /* 900: mismo negro con degradado (profundidad); padding-right evita que se recorte el último 0 */
    .tel-number .tn-b{ padding-right:.06em; background:linear-gradient(180deg,#FFE45C 0%,#FFD400 52%,#E3B100 100%); -webkit-background-clip:text; background-clip:text; color:transparent; }   /* 759: amarillo corporativo con degradado */
    .tel-eyebrow{ font-size:.8rem; letter-spacing:.2em; text-transform:uppercase; color:rgba(11,14,18,.55); margin-bottom:clamp(16px,2.6vh,30px); will-change:opacity; }
    .tel-sub{ width:min(720px,90vw); font-size:clamp(1.15rem,1.8vw,1.3rem); line-height:1.6; color:rgba(11,14,18,.6); margin-top:clamp(18px,3vh,34px); will-change:opacity; }
    .tel-hint{ position:absolute; z-index:2; bottom:6vh; left:0; right:0; text-align:center;
      font-size:.78rem; letter-spacing:.18em; text-transform:uppercase; color:var(--mist); display:flex;
      flex-direction:column; align-items:center; gap:10px; will-change:opacity; }
    .tel-hint .bar{ width:1px; height:34px; background:linear-gradient(var(--fg),transparent); animation:slide 1.8s ease-in-out infinite; }

    /* Carruseles de idiomas (marquee infinito, direcciones alternas) */
    .tel-marquees{ position:absolute; z-index:2; left:0; right:0; top:70vh; display:flex; flex-direction:column; gap:4px; background:#F4F2EC; padding:18px 0; will-change:opacity; }
    .marquee{ overflow:hidden; -webkit-mask-image:linear-gradient(90deg,transparent,#000 12%,#000 88%,transparent); mask-image:linear-gradient(90deg,transparent,#000 12%,#000 88%,transparent); }
    .marquee-track{ display:inline-flex; align-items:center; width:max-content; animation:mLeft 78s linear infinite; }
    .marquee.rev .marquee-track{ animation-name:mRight; }
    .marquee-track span{ font-family:var(--font-display); font-weight:600; font-size:clamp(.85rem,1.4vw,1.25rem); color:rgba(51,54,60,.55); white-space:nowrap; display:inline-flex; align-items:center; }
    .marquee-track span::after{ content:"•"; margin:0 .9em; color:rgba(51,54,60,.25); }
    @keyframes mLeft{ from{transform:translateX(0)} to{transform:translateX(-50%)} }
    @keyframes mRight{ from{transform:translateX(-50%)} to{transform:translateX(0)} }
    @media (prefers-reduced-motion:reduce){ .marquee-track{ animation:none } }

    /* Barra de pasos (se rellena acompasada con los textos) */
    .tel-steps{ position:absolute; z-index:2; bottom:11vh; left:50%; transform:translateX(-50%); width:min(72vw,1000px); opacity:0; will-change:opacity; }
    .steps-track{ position:relative; height:3px; border-radius:3px; background:rgba(11,14,18,.12); }
    .steps-fill{ position:absolute; left:0; top:0; height:100%; width:0; border-radius:3px; background:#FFD400; }
    .steps-labels{ display:flex; justify-content:space-between; gap:16px; margin-top:16px; }
    .steps-labels .step:last-child{ text-align:right; }
    .steps-labels .step{ font-family:var(--font-body); font-weight:500; font-size:clamp(.82rem,1.1vw,1.05rem); color:rgba(11,14,18,.3); }
    .steps-labels .num{ font-weight:700; }
    @media (max-width:820px){ .tel-steps{ top:12vh; bottom:auto; width:88vw; } .steps-labels{ justify-content:center; gap:0; margin-top:12px; } .steps-labels .step{ font-size:.98rem; text-align:center; } .steps-labels .step:last-child{ text-align:center; } }

    /* Píldora amarilla encima del móvil */
    .tel-pill{ display:none; position:absolute; z-index:3; left:50%; top:50%; background:#FFD400; color:#0B0E12; font-family:var(--font-body); font-weight:600; font-size:1rem; padding:11px 22px; border-radius:999px; white-space:nowrap; box-shadow:0 12px 30px rgba(11,14,18,.2); opacity:0; will-change:transform,opacity; }

    /* Título que aparece a la derecha cuando el móvil se va a la izquierda */
    .tel-topic{ position:absolute; z-index:2; top:28vh; left:45vw; right:auto; width:min(54ch,52vw); text-align:left; opacity:0; will-change:opacity; }
    .tel-topic-eye{ display:inline-flex; align-items:center; gap:10px; }
    .tel-topic-eye .dot{ width:9px; height:9px; border-radius:50%; background:var(--fg); opacity:0; flex:0 0 auto; }
    .tel-topic-label{ font-family:var(--font-display); font-weight:300; text-transform:uppercase; letter-spacing:.16em; line-height:1.15; color:var(--fg); white-space:nowrap; font-size:2.1rem; }
    .tel-topic-paras{ position:relative; margin-top:24px; }
    .tel-topic-paras .para{ position:absolute; top:0; left:0; right:0; opacity:0; }
    @media (max-width:820px){ .tel-topic{ top:64vh; bottom:auto; left:0; right:0; width:auto; text-align:center; padding:0 22px; }
      .tel-topic-eye{ justify-content:center; } .tel-topic-paras{ margin-top:14px; }
      .tel-topic-label{ white-space:normal; text-align:center; line-height:1.25; letter-spacing:.1em; } }

    /* Teléfono Android */
    .tel-phone{ position:absolute; z-index:1; width:300px; height:620px; border-radius:44px; background:#0a0a0b;
      padding:9px; box-shadow:0 50px 110px rgba(11,14,18,.35); opacity:0; will-change:transform,opacity; }
    .tel-screen{ position:relative; width:100%; height:100%; border-radius:36px; overflow:hidden;
      background:linear-gradient(165deg,#23262c 0%,#141519 60%,#0e0f12 100%); color:#fff; display:flex; flex-direction:column;
      font-family:var(--font-body); }
    .tel-screen .notch{ position:absolute; top:9px; left:50%; transform:translateX(-50%); width:14px; height:14px; border-radius:50%; background:#000; }
    .statusbar{ display:flex; justify-content:space-between; align-items:center; padding:12px 22px 0; font-size:.72rem; color:#e6e8ec; font-weight:500; }
    .statusbar .icons{ display:flex; gap:6px; align-items:center; }
    .statusbar .icons svg{ width:15px; height:15px; }
    .call-top{ flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:0 24px; }
    .call-label{ font-size:.82rem; color:#9aa0aa; margin-bottom:24px; letter-spacing:.04em; }
    .avatar{ width:104px; height:104px; border-radius:50%; background:#fff url(<?php echo esc_url( get_template_directory_uri() ); ?>/logo_fondo_blanco.png) center / cover no-repeat; position:relative; margin-bottom:22px; }
    .avatar::after{ content:""; position:absolute; inset:-10px; border-radius:50%; border:2px solid rgba(31,174,90,.7); animation:ring 1.7s ease-out infinite; }
    @keyframes ring{ 0%{transform:scale(1);opacity:.75} 100%{transform:scale(1.28);opacity:0} }
    .call-name{ font-size:1.45rem; font-weight:600; line-height:1.2; }
    .call-number{ font-size:1.02rem; color:#c8ccd4; margin-top:6px; letter-spacing:.02em; }
    .call-status{ margin-top:16px; font-size:.98rem; color:#aeb4be; }
    .call-status .dots span{ animation:blink 1.4s infinite both; }
    .call-status .dots span:nth-child(2){ animation-delay:.2s } .call-status .dots span:nth-child(3){ animation-delay:.4s }
    @keyframes blink{ 0%,70%,100%{opacity:.2} 35%{opacity:1} }
    .call-controls{ padding:0 26px 44px; display:flex; flex-direction:column; align-items:center; gap:30px; }
    .ctrl-row{ display:flex; gap:24px; }
    .ctrl{ width:56px; height:56px; border-radius:50%; background:rgba(255,255,255,.1); border:0; color:#e6e8ec; display:grid; place-items:center; }
    .ctrl svg{ width:24px; height:24px; }
    .ctrl span{ position:absolute; }
    .end-call{ width:64px; height:64px; border-radius:50%; background:#ea2b3c; border:0; display:grid; place-items:center; box-shadow:0 10px 24px rgba(234,43,60,.5); }
    .end-call svg{ width:30px; height:30px; transform:rotate(135deg); fill:#fff; }


    @media (max-width:560px){
      .tel-phone{ width:270px; height:560px; }
      .tel-number{ font-size:clamp(2.6rem,17vw,5rem); }
      .navbar a:not(.nav-call){ display:none; }
    }

    /* Ventana emergente: 3 vías de contacto */
    .report-vias{ display:flex; flex-direction:column; justify-content:center; }
    .report-vias .report-left{ text-align:center; max-width:640px; margin:0 auto 0; }
    .report-vias .report-left p{ margin-left:auto; margin-right:auto; margin-bottom:0; }
    .via-cards{ display:grid; grid-template-columns:repeat(3,1fr); gap:16px; }
    .via-card{ display:flex; flex-direction:column; text-align:left; border:1px solid rgba(11,14,18,.14); border-radius:14px; padding:clamp(18px,1.6vw,24px); background:rgba(255,255,255,.35); }
    .via-num{ font-size:.74rem; letter-spacing:.12em; color:rgba(11,14,18,.45); margin-bottom:clamp(12px,2.2vh,20px); }
    .via-card h3{ font-family:var(--font-display); font-weight:600; font-size:clamp(1.2rem,1.7vw,1.55rem); margin-bottom:10px; color:var(--fg); }
    .via-card p{ font-family:var(--font-body); font-size:.9rem; line-height:1.5; color:rgba(11,14,18,.7); margin-bottom:18px; flex:1; }
    .via-btn{ display:inline-flex; align-items:center; justify-content:center; align-self:flex-start; font-family:var(--font-body); font-weight:600; font-size:.95rem; padding:13px 20px; border-radius:10px; text-decoration:none; border:1px solid var(--fg); color:var(--fg); background:transparent; transition:background .2s ease, color .2s ease; }
    .via-btn:hover{ background:var(--fg); color:#fff; }
    .via-btn-primary{ background:#FFD400; border-color:#FFD400; color:#0B0E12; }
    .via-btn-primary:hover{ background:#F5C400; color:#0B0E12; }
    .via-card .btn-hero{ align-self:flex-start; } /* mismo botón que la portada */
    #formPanel{ z-index:56; } /* el formulario se muestra por encima de las cards */
    @media (max-width:860px){ .via-cards{ grid-template-columns:1fr; } }
  </style>

<?php get_template_part( 'parts/site-nav' ); ?>

  <section class="tel-track" id="telTrack">
    <div class="tel-stage">
      <canvas class="tel-frag" id="telFrag" aria-hidden="true"></canvas>
      <div class="tel-cover" id="telCover">
        <span class="tel-eyebrow" id="telEyebrow"><?php ff('tel_eyebrow','Confidencial · Gratuito · Disponible 24/7 · Sin rastro en la factura'); ?></span>
        <div class="tel-number" id="telNum"><?php
          $tel_disp = fiet_option('telefono_display','900 759 759');
          $tel_sp   = strpos( $tel_disp, ' ' );
          if ( $tel_sp === false ) {
            echo '<span class="tn-a">' . esc_html( $tel_disp ) . '</span>';
          } else {
            echo '<span class="tn-a">' . esc_html( substr( $tel_disp, 0, $tel_sp ) ) . '</span> '
               . '<span class="tn-b">' . esc_html( ltrim( substr( $tel_disp, $tel_sp ) ) ) . '</span>';
          }
        ?></div>
        <p class="tel-sub" id="telSub"><?php ff('tel_sub','El Teléfono de Ayuda Contra la Trata funciona 24/7 y está atendido por profesionales especializados que siguen protocolos internacionales para responder con rapidez y seguridad. Financiado y operado por la ONG FIET.'); ?></p>
      </div>

      <div class="tel-marquees" id="telMarquees">
        <div class="marquee rev"><div class="marquee-track"></div></div>
        <div class="marquee"><div class="marquee-track"></div></div>
        <div class="marquee rev"><div class="marquee-track"></div></div>
      </div>

      <div class="tel-phone" id="telPhone">
        <div class="tel-screen">
          <span class="notch"></span>
          <div class="statusbar">
            <span>9:41</span>
            <span class="icons">
              <svg viewBox="0 0 24 24" fill="#e6e8ec"><path d="M2 16h3v4H2zM7 12h3v8H7zM12 8h3v12h-3zM17 4h3v16h-3z"/></svg>
              <svg viewBox="0 0 24 24" fill="#e6e8ec"><path d="M12 4a9 9 0 0 1 9 9h-2a7 7 0 0 0-7-7zm0 4a5 5 0 0 1 5 5h-2a3 3 0 0 0-3-3z"/></svg>
              <svg viewBox="0 0 24 26" fill="none" stroke="#e6e8ec" stroke-width="2"><rect x="1" y="6" width="18" height="12" rx="2"/><rect x="21" y="10" width="2" height="4" fill="#e6e8ec" stroke="none"/><rect x="3" y="8" width="13" height="8" rx="1" fill="#e6e8ec"/></svg>
            </span>
          </div>

          <div class="call-top">
            <div class="call-label"><?php ff('tel_call_label','Llamada saliente'); ?></div>
            <div class="avatar"></div>
            <div class="call-name"><?php ff('tel_call_name','Teléfono ACT'); ?></div>
            <div class="call-number"><?php echo esc_html( fiet_option('telefono_display','900 759 759') ); ?></div>
            <div class="call-status">Llamando<span class="dots"><span>.</span><span>.</span><span>.</span></span></div>
          </div>

          <div class="call-controls">
            <div class="ctrl-row">
              <button class="ctrl" aria-label="Silenciar"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 14a3 3 0 0 0 3-3V5a3 3 0 0 0-6 0v6a3 3 0 0 0 3 3zm5-3a5 5 0 0 1-10 0H5a7 7 0 0 0 6 6.9V21h2v-3.1A7 7 0 0 0 19 11z"/></svg></button>
              <button class="ctrl" aria-label="Teclado"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M6 5a1.6 1.6 0 1 0 0 3.2A1.6 1.6 0 0 0 6 5zm6 0a1.6 1.6 0 1 0 0 3.2A1.6 1.6 0 0 0 12 5zm6 0a1.6 1.6 0 1 0 0 3.2A1.6 1.6 0 0 0 18 5zM6 10.8a1.6 1.6 0 1 0 0 3.2 1.6 1.6 0 0 0 0-3.2zm6 0a1.6 1.6 0 1 0 0 3.2 1.6 1.6 0 0 0 0-3.2zm6 0a1.6 1.6 0 1 0 0 3.2 1.6 1.6 0 0 0 0-3.2zM6 16.6a1.6 1.6 0 1 0 0 3.2 1.6 1.6 0 0 0 0-3.2zm6 0a1.6 1.6 0 1 0 0 3.2 1.6 1.6 0 0 0 0-3.2zm6 0a1.6 1.6 0 1 0 0 3.2 1.6 1.6 0 0 0 0-3.2z"/></svg></button>
              <button class="ctrl" aria-label="Altavoz"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M4 9v6h4l5 5V4L8 9H4zm12 3a4 4 0 0 0-2-3.5v7A4 4 0 0 0 16 12z"/></svg></button>
            </div>
            <button class="end-call" aria-label="Colgar"><svg viewBox="0 0 24 24"><path d="M6.62 10.79a15.53 15.53 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.02-.24 11.36 11.36 0 0 0 3.57.57 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1 11.36 11.36 0 0 0 .57 3.57 1 1 0 0 1-.24 1.02l-2.21 2.2z"/></svg></button>
          </div>
        </div>
      </div>

      <div class="tel-pill" id="telPill"><?php ff('tel_pill','Traducción automática'); ?></div>
      <div class="tel-topic" id="telTopic">
        <span class="tel-topic-eye"><span class="dot" id="telDot"></span><span class="tel-topic-label" id="telTopicLabel"><?php ff('tel_topic_label','¿Qué pasa cuando llamas?'); ?></span></span>
        <div class="tel-topic-paras">
          <p class="para" id="telTopicText"><?php ff('tel_topic1','Una víctima o testigo contacta con el 900 759 759 a cualquier hora y en cualquier momento. Puede contactar en su idioma nativo si quiere...'); ?></p>
          <p class="para" id="telTopicText2"><?php ff('tel_topic2','Fiet incorpora un sistema de traducción automática de la llamada que permite a un profesional especializado entender a la víctima y comunicarse con ella en su idioma en tiempo real.'); ?></p>
          <p class="para" id="telTopicText3"><?php ff('tel_topic3','Una vez atendida a la víctima, se revisa en la base de datos si hubiese casos conectados.'); ?></p>
          <p class="para" id="telTopicText4"><?php ff('tel_topic4','Con esta información se procede a dar la respuesta más adecuada y segura.'); ?></p>
        </div>
      </div>
      <div class="tel-steps" id="telSteps">
        <div class="steps-track"><div class="steps-fill" id="stepsFill"></div></div>
        <div class="steps-labels">
          <span class="step"><span class="num">01</span> · <?php ff('tel_step1','Llamada de ayuda'); ?></span>
          <span class="step"><span class="num">02</span> · <?php ff('tel_step2','Atención especializada'); ?></span>
          <span class="step"><span class="num">03</span> · <?php ff('tel_step3','Revisión de datos'); ?></span>
          <span class="step"><span class="num">04</span> · <?php ff('tel_step4','Plan de acción'); ?></span>
        </div>
      </div>
      <div class="tel-hint" id="telHint"><span class="bar"></span><?php ff('tel_hint','Desplázate para llamar'); ?></div>
    </div>
  </section>


  <script>
    (function(){
      "use strict";
      var track=document.getElementById("telTrack");
      var num=document.getElementById("telNum");
      var phone=document.getElementById("telPhone");
      var hint=document.getElementById("telHint");
      var eyebrow=document.getElementById("telEyebrow");
      var sub=document.getElementById("telSub");
      var marquees=document.getElementById("telMarquees");
      var topic=document.getElementById("telTopic");
      var pill=document.getElementById("telPill");
      var topicLabel=document.getElementById("telTopicLabel");
      var topicDot=document.getElementById("telDot");
      var topicText=document.getElementById("telTopicText");
      var topicText2=document.getElementById("telTopicText2");
      var tchars=[], tchars2=[];
      function buildChars(el,arr){ if(!el) return; var raw=el.textContent; el.textContent=""; for(var i=0;i<raw.length;i++){ var sp=document.createElement("span"); sp.className="ch"; sp.textContent=raw[i]; el.appendChild(sp); arr.push(sp); } }
      var topicText3=document.getElementById("telTopicText3");
      var topicText4=document.getElementById("telTopicText4");
      var tchars3=[], tchars4=[];
      buildChars(topicText,tchars); buildChars(topicText2,tchars2); buildChars(topicText3,tchars3); buildChars(topicText4,tchars4);
      var steps=document.getElementById("telSteps"), stepsFill=document.getElementById("stepsFill"), stepEls=document.querySelectorAll(".step");
      // "Podemos ayudarte" en varios idiomas
      var LANGS=["We can help you","Podemos ayudarte","Nous pouvons t'aider","我们可以帮助你","يمكننا مساعدتك","Мы можем помочь тебе","Te putem ajuta","Possiamo aiutarti","ਅਸੀਂ ਤੁਹਾਡੀ ਮਦਦ ਕਰ ਸਕਦੇ ਹਾਂ","Noo la mën a dimbali","نقدرو نعاونوك"];
      var tracks=document.querySelectorAll(".marquee-track");
      for(var ti=0;ti<tracks.length;ti++){
        var rot=LANGS.slice(ti*3).concat(LANGS.slice(0,ti*3));   // rota el orden por fila
        var html=rot.map(function(x){return "<span>"+x+"</span>";}).join("");
        tracks[ti].innerHTML=html+html;                          // duplicado -> bucle continuo
      }
      function clamp(v,a,b){return v<a?a:v>b?b:v;}
      function lerp(a,b,t){return a+(b-a)*t;}
      function ease(t){return t<0.5?4*t*t*t:1-Math.pow(-2*t+2,3)/2;}

      // ===== Fragmentación del número en partículas (estilo "qué es la trata") =====
      var cv=document.getElementById("telFrag"), fctx=cv.getContext("2d");
      var particles=[], pReady=false, pStep=3, pCenterX=0, pCenterY=0;

      function sizeCanvas(){
        var dpr=Math.min(window.devicePixelRatio||1, 2);
        cv.width=Math.round(window.innerWidth*dpr);
        cv.height=Math.round(window.innerHeight*dpr);
        fctx.setTransform(dpr,0,0,dpr,0,0);
      }
      function buildParticles(){
        var prevT=num.style.transform, prevO=num.style.opacity;      // medir el número en tamaño natural
        num.style.transform="none"; num.style.opacity="1";
        var r=num.getBoundingClientRect();
        num.style.transform=prevT; num.style.opacity=prevO;
        var W=Math.ceil(r.width), H=Math.ceil(r.height);
        if(W<2||H<2) return;
        var cs=getComputedStyle(num), fpx=parseFloat(cs.fontSize);
        // Línea base real del número en el DOM (para colocar las partículas exactamente encima)
        var baseIn;
        (function(){
          var probe=document.createElement("span");
          probe.style.cssText="position:absolute;left:-9999px;top:0;visibility:hidden;white-space:nowrap";
          probe.style.fontFamily=cs.fontFamily; probe.style.fontWeight=cs.fontWeight;
          probe.style.fontSize=cs.fontSize; probe.style.lineHeight=cs.lineHeight; probe.style.letterSpacing=cs.letterSpacing;
          probe.textContent=num.textContent;
          var mk=document.createElement("span");
          mk.style.cssText="display:inline-block;width:0;height:0;vertical-align:baseline";
          probe.appendChild(mk);
          document.body.appendChild(probe);
          baseIn=mk.getBoundingClientRect().top-probe.getBoundingClientRect().top;
          document.body.removeChild(probe);
        })();
        if(!(baseIn>0)) baseIn=H*0.5+0.35*fpx;
        var padY=Math.ceil(fpx*0.7), CW=W, CH=H+padY*2;              // lienzo con margen para no recortar los dígitos
        var off=document.createElement("canvas"); off.width=CW; off.height=CH;
        var o=off.getContext("2d");
        o.textAlign="left"; o.textBaseline="alphabetic";
        try{ o.letterSpacing=(-0.04*fpx)+"px"; }catch(e){}
        o.font=cs.fontWeight+" "+cs.fontSize+" "+cs.fontFamily;
        // Dos tramos con degradado (900 negro / 759 amarillo), igual que el DOM; las partículas lo heredan
        var _full=num.textContent, _sp=_full.indexOf(" ");
        var _A=(_sp<0)?_full:_full.slice(0,_sp), _B=(_sp<0)?"":_full.slice(_sp+1);
        var _tW=o.measureText(_full).width, _x0=(CW-_tW)/2;
        var _gT=padY+baseIn-fpx*0.72, _gB=padY+baseIn+fpx*0.06;
        var _gA=o.createLinearGradient(0,_gT,0,_gB); _gA.addColorStop(0,"#3a3e46"); _gA.addColorStop(0.55,"#212428"); _gA.addColorStop(1,"#15171b");
        o.fillStyle=_gA; o.fillText(_A, _x0, padY+baseIn);
        if(_B){
          var _wA=o.measureText(_A+" ").width;
          var _gY=o.createLinearGradient(0,_gT,0,_gB); _gY.addColorStop(0,"#FFE45C"); _gY.addColorStop(0.52,"#FFD400"); _gY.addColorStop(1,"#E3B100");
          o.fillStyle=_gY; o.fillText(_B, _x0+_wA, padY+baseIn);
        }
        var d; try{ d=o.getImageData(0,0,CW,CH).data; }catch(e){ return; }
        // Centro real de los píxeles de los dígitos en el lienzo
        var minX=CW,minY=CH,maxX=0,maxY=0,any=false;
        for(var yy=0;yy<CH;yy++){ var row=yy*CW; for(var xx=0;xx<CW;xx++){ if(d[(row+xx)*4+3]>=128){ any=true; if(xx<minX)minX=xx; if(xx>maxX)maxX=xx; if(yy<minY)minY=yy; if(yy>maxY)maxY=yy; } } }
        if(!any) return;
        var gcx=(minX+maxX)/2, gcy=(minY+maxY)/2;
        // El lienzo mapea al DOM así: col x -> r.left + x ; fila y -> r.top + (y - padY)
        pCenterX=r.left+gcx; pCenterY=r.top+(gcy-padY);
        particles=[];
        for(var y=0;y<CH;y+=pStep){ for(var x=0;x<CW;x+=pStep){
          var i=(y*CW+x)*4; if(d[i+3]<128) continue;
          var ang=Math.random()*Math.PI*2, spd=0.4+Math.random();
          particles.push({ hx:x-gcx, hy:y-gcy,                       // origen relativo al centro real de los dígitos
            col:"rgba("+d[i]+","+d[i+1]+","+d[i+2]+","+(d[i+3]/255).toFixed(2)+")",
            vx:Math.cos(ang)*spd, vy:Math.sin(ang)*spd-0.25, spread:60+Math.random()*220 });
        }}
        pReady=particles.length>0;
      }
      function drawParticles(cloudScale, ty, frag, alpha){
        fctx.clearRect(0,0,window.innerWidth,window.innerHeight);
        if(!pReady || alpha<=0) return;
        fctx.globalAlpha=alpha;
        var cx=pCenterX, cy=pCenterY+ty, sz=Math.max(1, pStep*cloudScale*(1.5-0.7*frag)), ef=frag*frag;
        for(var k=0;k<particles.length;k++){
          var pt=particles[k];
          fctx.fillStyle=pt.col;
          fctx.fillRect(cx+pt.hx*cloudScale+pt.vx*pt.spread*ef, cy+pt.hy*cloudScale+pt.vy*pt.spread*ef, sz, sz);
        }
        fctx.globalAlpha=1;
      }
      sizeCanvas(); buildParticles(); onScroll();
      if(document.fonts&&document.fonts.ready){ document.fonts.ready.then(function(){ buildParticles(); onScroll(); }); }
      window.addEventListener("load", function(){ buildParticles(); onScroll(); });

      function onScroll(){
        var rect=track.getBoundingClientRect();
        var total=track.offsetHeight-window.innerHeight;
        var p=clamp(-rect.top/total,0,1)*0.87;   // el contenido acaba al 84%: con factor 0.87 el último texto se revela al ~97% del track y queda un margen corto (~3%) antes de que aparezca el footer.
        // Número -> (fragmentación en partículas) -> teléfono
        var ns=ease(clamp(p/0.30,0,1));
        var nTy=lerp(0,-window.innerHeight*0.04,ns), nScale=lerp(1,0.135,ns);
        num.style.transform="translateY("+nTy+"px) scale("+nScale+")";
        if(pReady){
          num.style.opacity=(1-clamp((p-0.06)/0.05,0,1)).toFixed(3);      // el número se apaga y las partículas toman el relevo
          var frag=ease(clamp((p-0.07)/0.15,0,1));                         // el número se fragmenta y se dispersa (empieza antes)
          var pAlpha=clamp((p-0.03)/0.05,0,1)*(1-clamp((p-0.24)/0.06,0,1));// partículas presentes antes (bajo el número) y luego se desvanecen
          drawParticles(nScale, nTy, frag, pAlpha);
        } else {
          num.style.opacity=(1-clamp((p-0.24)/0.06,0,1)).toFixed(3);      // reserva si aún no hay partículas
        }
        eyebrow.style.opacity=(1-clamp(p/0.10,0,1)).toFixed(3);
        sub.style.opacity=(1-clamp(p/0.10,0,1)).toFixed(3);
        marquees.style.opacity=(1-clamp(p/0.10,0,1)).toFixed(3);
        hint.style.opacity=(1-clamp(p/0.07,0,1)).toFixed(3);
        var pp=ease(clamp((p-0.18)/0.12,0,1));
        var shift=ease(clamp((p-0.31)/0.05,0,1));
        var wide=window.innerWidth>820;
        var px=wide?lerp(0,-window.innerWidth*0.26,shift):0;
        var py=wide?0:lerp(0,-window.innerHeight*0.14,shift);          // móvil: el teléfono sube (bajo la línea de pasos)
        var restShrink=wide?lerp(1,0.72,shift):lerp(1,0.48,shift);     // móvil: encoge más para dejar hueco al texto
        phone.style.opacity=pp.toFixed(3);
        phone.style.transform="translate("+px+"px,"+(lerp(70,0,pp)+py)+"px) scale("+(lerp(0.92,1,pp)*restShrink)+")";
        topic.style.opacity=clamp((p-0.37)/0.03,0,1).toFixed(3);
        var conv=ease(clamp((p-0.41)/0.04,0,1));
        var bigPx=wide?Math.min(Math.max(1.3*16, window.innerWidth*0.024), 2.1*16):1.1*16; // móvil: título más pequeño para que no se salga
        topicLabel.style.fontSize=lerp(bigPx,0.78*16,conv).toFixed(2)+"px";
        topicDot.style.opacity=conv.toFixed(3);
        var reveal=clamp((p-0.46)/0.06,0,1);
        topicText.style.opacity=(clamp((p-0.44)/0.02,0,1)*(1-clamp((p-0.55)/0.025,0,1))).toFixed(3);
        var litN=Math.floor(reveal*tchars.length);
        for(var ci=0;ci<tchars.length;ci++){ var onc=ci<litN; if(onc!==tchars[ci]._on){ tchars[ci]._on=onc; tchars[ci].classList.toggle("lit",onc); } }
        var reveal2=clamp((p-0.57)/0.06,0,1);
        var v2app=clamp((p-0.55)/0.02,0,1);
        topicText2.style.opacity=(v2app*(1-clamp((p-0.66)/0.025,0,1))).toFixed(3);
        var litN2=Math.floor(reveal2*tchars2.length);
        for(var cj=0;cj<tchars2.length;cj++){ var onc2=cj<litN2; if(onc2!==tchars2[cj]._on){ tchars2[cj]._on=onc2; tchars2[cj].classList.toggle("lit",onc2); } }
        var pillY=wide?-267:window.innerHeight*0.08;                   // móvil: la píldora justo bajo el teléfono (encima del texto)
        pill.style.opacity=v2app.toFixed(3);
        pill.style.transform="translate(-50%,-50%) translate("+px+"px,"+pillY+"px) scale("+lerp(0.85,1,ease(v2app))+")";
        var reveal3=clamp((p-0.68)/0.06,0,1);
        topicText3.style.opacity=(clamp((p-0.66)/0.02,0,1)*(1-clamp((p-0.765)/0.025,0,1))).toFixed(3);
        var litN3=Math.floor(reveal3*tchars3.length);
        for(var ck=0;ck<tchars3.length;ck++){ var onc3=ck<litN3; if(onc3!==tchars3[ck]._on){ tchars3[ck]._on=onc3; tchars3[ck].classList.toggle("lit",onc3); } }
        var reveal4=clamp((p-0.78)/0.06,0,1);
        topicText4.style.opacity=clamp((p-0.76)/0.02,0,1).toFixed(3);
        var litN4=Math.floor(reveal4*tchars4.length);
        for(var cl=0;cl<tchars4.length;cl++){ var onc4=cl<litN4; if(onc4!==tchars4[cl]._on){ tchars4[cl]._on=onc4; tchars4[cl].classList.toggle("lit",onc4); } }
        steps.style.opacity=clamp((p-0.40)/0.03,0,1).toFixed(3);
        var storyP=(reveal+reveal2+reveal3+reveal4)/4;
        stepsFill.style.width=(storyP*100).toFixed(1)+"%";
        var rr=[reveal,reveal2,reveal3,reveal4];
        if(wide){
          for(var si=0;si<stepEls.length;si++){ stepEls[si].style.display=""; stepEls[si].style.color="rgba(11,14,18,"+(0.3+0.7*rr[si]).toFixed(3)+")"; }
        } else {
          var cur=0; for(var ss=0;ss<4;ss++){ if(rr[ss]>0) cur=ss; }   // móvil: solo el paso actual, centrado
          for(var sm=0;sm<stepEls.length;sm++){ stepEls[sm].style.display=(sm===cur)?"":"none"; stepEls[sm].style.color="rgba(11,14,18,1)"; }
        }
      }
      window.addEventListener("scroll",onScroll,{passive:true});
      window.addEventListener("resize",function(){ sizeCanvas(); buildParticles(); onScroll(); });
      onScroll();
    })();
  </script>
  <!-- Ventana emergente que sube desde abajo al final -->
  <section class="report" id="report">
    <button class="report-close" id="reportClose" type="button" aria-label="Cerrar">&times;</button>
    <div class="report-inner report-vias">
      <div class="report-left">
        <span class="tag"><?php ff('tel_pop_tag','Cómo contactar'); ?></span>
        <h2><?php ff('tel_pop_titulo','Tres vías. Todas confidenciales.'); ?></h2>
        <p><?php ff('tel_pop_parrafo','Hay una persona al otro lado. No te juzga, no comparte nada sin tu consentimiento (salvo obligación legal) y puedes permanecer en el anonimato. Elige el canal que te resulte más seguro.'); ?></p>
      </div>
      <div class="via-cards">
        <article class="via-card">
          <span class="via-num">01 / 03</span>
          <h3><?php ff('tel_c1_titulo','Teléfono'); ?></h3>
          <p><?php ff('tel_c1_desc','Marca el 900 759 759. Gratuito, 24/7. Profesionales que hablan español e inglés, con interpretación en más de 200 idiomas.'); ?></p>
          <a class="btn-hero" href="tel:<?php echo esc_attr( fiet_option('telefono_tel','900759759') ); ?>"><?php ff('tel_c1_boton','Llamar'); ?></a>
        </article>
        <article class="via-card">
          <span class="via-num">02 / 03</span>
          <h3><?php ff('tel_c2_titulo','Formulario'); ?></h3>
          <p><?php ff('tel_c2_desc','Describe una situación de sospecha a través del formulario de contacto. Puedes hacerlo de forma anónima o dejar un contacto para que te llamen.'); ?></p>
          <button class="btn-hero js-open-form" type="button"><?php ff('tel_c2_boton','Abrir formulario'); ?></button>
        </article>
        <article class="via-card">
          <span class="via-num">03 / 03</span>
          <h3><?php ff('tel_c3_titulo','Correo'); ?></h3>
          <p><?php ff('tel_c3_desc','Escríbenos con los detalles de tu situación o tu consulta. Te responderá el equipo especializado.'); ?></p>
          <a class="btn-hero" href="mailto:<?php echo esc_attr( fiet_option('email_contacto','informacion@fiet.ong') ); ?>"><?php ff('tel_c3_boton','Enviar correo'); ?></a>
        </article>
      </div>
    </div>
  </section>

  <!-- Panel del formulario: se abre en esta misma página (bolígrafo o "Abrir formulario") -->
  <section class="report" id="formPanel" data-form-panel>
    <button class="report-close" id="formClose" type="button" aria-label="Cerrar">&times;</button>
    <?php fiet_report_form_inner(); ?>
  </section>

<?php get_template_part( 'parts/floating' ); ?>

<?php get_footer(); ?>
