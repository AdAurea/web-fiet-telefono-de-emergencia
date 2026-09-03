<?php if ( ! defined( 'ABSPATH' ) ) exit; get_header(); ?>
  <style>
    /* ===== Página Recursos ===== */
    #recHero{ height:150vh; }                                   /* portada estática + sección de servicios + popup (recortado el tramo muerto tras la sección amarilla) */

    /* Sección amarilla de servicios (se funde a pantalla completa al hacer scroll) */
    .rec-services{ position:absolute; inset:0; z-index:3; background:#FFD400; opacity:0; pointer-events:none; overflow-y:auto; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:clamp(64px,9vh,110px) clamp(20px,5vw,60px); text-align:center; will-change:opacity; }
    .rec-services h2{ font-family:var(--font-display); font-weight:600; font-size:clamp(1.7rem,3.6vw,3rem); line-height:1.1; letter-spacing:-.02em; color:#0B0E12; margin-bottom:clamp(26px,4.5vh,46px); max-width:22ch; }
    .serv-cards{ display:grid; grid-template-columns:repeat(3,1fr); gap:14px; max-width:1040px; width:100%; }
    .serv-card{ background:rgba(255,255,255,.6); border:1px solid rgba(11,14,18,.12); border-radius:16px; padding:clamp(16px,1.4vw,24px); text-align:left; }
    .serv-num{ font-family:var(--font-body); font-size:.75rem; letter-spacing:.1em; color:rgba(11,14,18,.5); margin-bottom:14px; }
    .serv-card h3{ font-family:var(--font-display); font-weight:600; font-size:clamp(1rem,1.2vw,1.25rem); line-height:1.2; margin-bottom:10px; color:#0B0E12; }
    .serv-card p{ font-family:var(--font-body); font-size:.85rem; line-height:1.5; color:rgba(11,14,18,.72); }
    @media (max-width:1000px){ .serv-cards{ grid-template-columns:repeat(2,1fr); max-width:640px; } .rec-services{ justify-content:flex-start; } }
    @media (max-width:560px){
      /* móvil: 6 tarjetas en 2 columnas (3 filas) y todo compacto para que quepan en pantalla */
      .rec-services{ padding:104px 15px 16px; justify-content:flex-start; }   /* padding superior para que el título no quede bajo el menú/logo fijo */
      .rec-services h2{ font-size:clamp(1.35rem,6vw,1.8rem); line-height:1.15; margin-bottom:clamp(12px,2.5vh,20px); max-width:20ch; }
      .serv-cards{ grid-template-columns:repeat(2,1fr); gap:8px; max-width:none; }
      .serv-card{ padding:12px 12px; border-radius:12px; }
      .serv-num{ margin-bottom:6px; font-size:.66rem; }
      .serv-card h3{ font-size:.95rem; line-height:1.15; margin-bottom:5px; }
      .serv-card p{ font-size:.74rem; line-height:1.35; }
    }

    /* Ventana emergente: formación especializada + cards por sector */
    .report-vias{ display:flex; flex-direction:column; justify-content:center; }
    .report-vias .report-left{ text-align:center; max-width:900px; margin:0 auto 0; }
    .report-vias .report-left p{ margin-left:auto; margin-right:auto; max-width:none; }   /* usa el ancho de la cabecera (menos líneas) */
    .report-vias .report-left p:last-child{ margin-bottom:clamp(2px,0.6vh,8px); }
    .via-cards{ display:flex; flex-wrap:wrap; justify-content:center; gap:16px; }
    .via-card{ flex:0 1 calc(33.333% - 11px); min-width:200px; display:flex; flex-direction:column; align-items:flex-start; text-align:left; border:1px solid rgba(11,14,18,.14); border-radius:16px; padding:clamp(18px,1.6vw,24px); background:rgba(255,255,255,.4); }
    .via-icon{ display:grid; place-items:center; width:46px; height:46px; border-radius:12px; background:#FFD400; color:#0B0E12; margin-bottom:16px; }
    .via-icon svg{ width:25px; height:25px; }
    .via-card h3{ font-family:var(--font-display); font-weight:600; font-size:clamp(1.05rem,1.4vw,1.25rem); margin-bottom:16px; color:var(--fg); }
    .via-mat{ margin-top:auto; display:inline-flex; align-items:center; gap:7px; font-family:var(--font-body); font-weight:600; font-size:.92rem; color:var(--fg); text-decoration:none; background:none; border:0; padding:0; cursor:pointer; }
    .via-mat span{ transition:transform .2s ease; }
    .via-mat:hover span{ transform:translateX(4px); }
    /* En responsive, las cards de sectores en dos columnas */
    @media (max-width:720px){
      .via-card{ flex:0 1 calc(50% - 8px); min-width:0; }
    }

    .via-foot{ text-align:center; max-width:680px; margin:clamp(2px,0.6vh,8px) auto 0; font-family:var(--font-body); font-size:.82rem; line-height:1.55; color:rgba(11,14,18,.62); }
    #report{ scrollbar-width:none; -ms-overflow-style:none; }
    #report::-webkit-scrollbar{ width:0; height:0; display:none; }
  </style>

  


  <!-- Portada estática: imagen (vídeo 1) fija -->
  <section class="hero-track" id="recHero" data-static>
    <div class="stage">
      <canvas id="c" aria-hidden="true"></canvas>
      <div class="overlay">
        <div class="copy">
          <span class="eyebrow" id="eyebrow"></span>
          <p class="para" id="para" style="opacity:0"></p>
        </div>
        <div class="intro" id="intro">
          <span class="tag"><span class="dot"></span><?php ff('rec_eyebrow','Recursos'); ?></span>
          <h2><?php ff('rec_titulo','Recursos y servicios.'); ?></h2>
          <p><?php ff('rec_parrafo','El Teléfono ACT ofrece formaciones gratuitas a profesionales y sectores con mayor riesgo de detectar situaciones de trata, además de una amplia red de derivación y materiales especializados.'); ?></p>
          <button class="btn-hero js-open-report" type="button"><?php ff('rec_boton','Ver formaciones'); ?></button>
        </div>
      </div>
      <div class="rec-services" id="recServices">
        <h2><?php ff('rec_serv_titulo','Una amplia gama de servicios a tu disposición'); ?></h2>
        <div class="serv-cards">
          <article class="serv-card">
            <span class="serv-num">01 / 06</span>
            <h3><?php ff('rec_serv1_titulo','Derivaciones'); ?></h3>
            <p><?php ff('rec_serv1_desc','Conectamos a quien llama con servicios especializados: gestión de casos, alojamiento seguro, transporte, asistencia legal y apoyo psicológico y de salud mental.'); ?></p>
          </article>
          <article class="serv-card">
            <span class="serv-num">02 / 06</span>
            <h3><?php ff('rec_serv2_titulo','Formación a profesionales'); ?></h3>
            <p><?php ff('rec_serv2_desc','Formación y asistencia técnica a fuerzas de seguridad, profesionales sanitarios, personal aeroportuario y organismos públicos. Fortalecemos protocolos locales y nacionales.'); ?></p>
          </article>
          <article class="serv-card">
            <span class="serv-num">03 / 06</span>
            <h3><?php ff('rec_serv3_titulo','Informar una sospecha'); ?></h3>
            <p><?php ff('rec_serv3_desc','Recibimos información sobre posibles situaciones de trata. Todas las comunicaciones son confidenciales y la persona puede permanecer en el anonimato.'); ?></p>
          </article>
          <article class="serv-card">
            <span class="serv-num">04 / 06</span>
            <h3><?php ff('rec_serv4_titulo','Asistencia a víctimas'); ?></h3>
            <p><?php ff('rec_serv4_desc','Apoyo en crisis mediante planes de seguridad, acompañamiento emocional y conexión con servicios de emergencia y entidades especializadas.'); ?></p>
          </article>
          <article class="serv-card">
            <span class="serv-num">05 / 06</span>
            <h3><?php ff('rec_serv5_titulo','Verificación de empleo'); ?></h3>
            <p><?php ff('rec_serv5_desc','Servicio gratuito de verificación de ofertas de empleo. Revisamos el registro de la empresa, antecedentes y opiniones, y elaboramos una evaluación de riesgo.'); ?></p>
          </article>
          <article class="serv-card">
            <span class="serv-num">06 / 06</span>
            <h3><?php ff('rec_serv6_titulo','Servicio de interpretación'); ?></h3>
            <p><?php ff('rec_serv6_desc','Atención en más de 200 idiomas para facilitar la comunicación y la intervención de profesionales especializados en trata de personas.'); ?></p>
          </article>
        </div>
      </div>
      <div class="hint" id="hint"><span class="bar"></span>Desplázate</div>
    </div>
  </section>

  <!-- Ventana emergente: formación especializada -->
  <section class="report" id="report">
    <button class="report-close" id="reportClose" type="button" aria-label="Cerrar">&times;</button>
    <div class="report-inner report-vias">
      <div class="report-left">
        <span class="tag"><?php ff('rec_pop_eyebrow','Recursos'); ?></span>
        <h2><?php ff('rec_pop_titulo','Formación especializada.'); ?></h2>
        <p><?php ff('rec_pop_p1','Proporcionamos conocimientos clave sobre la magnitud y las formas de la trata, los indicadores específicos según cada ámbito profesional y los protocolos de actuación necesarios en situaciones de sospecha o identificación.'); ?></p>
        <p><?php ff('rec_pop_p2','Contamos con una amplia base de datos de organizaciones que imparten formaciones, campañas y charlas en todo el territorio, además de un catálogo de materiales especializados.'); ?></p>
      </div>
      <div class="via-cards">
        <article class="via-card">
          <span class="via-icon"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20 8h-4V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v4H4a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h4v4a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2v-4h4a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2z"/></svg></span>
          <h3><?php ff('rec_sec1_titulo','Sector sanitario'); ?></h3>
          <?php if ( fiet_option( 'rec_url_sanitario', '' ) ) : ?><button class="via-mat js-descarga" type="button" data-sector="sanitario"><?php ff('rec_mat_boton','Descarga el material'); ?> <span>&rarr;</span></button><?php endif; ?>
        </article>
        <article class="via-card">
          <span class="via-icon"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M2 8a1 1 0 0 1 2 0v3h7V9a2 2 0 0 1 2-2h5a3 3 0 0 1 3 3v8a1 1 0 0 1-2 0v-2H4v2a1 1 0 0 1-2 0V8z"/></svg></span>
          <h3><?php ff('rec_sec2_titulo','Sector hostelero'); ?></h3>
          <?php if ( fiet_option( 'rec_url_hostelero', '' ) ) : ?><button class="via-mat js-descarga" type="button" data-sector="hostelero"><?php ff('rec_mat_boton','Descarga el material'); ?> <span>&rarr;</span></button><?php endif; ?>
        </article>
        <article class="via-card">
          <span class="via-icon"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M2 5a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v3h3.2a1 1 0 0 1 .8.4l2.4 3.2a1 1 0 0 1 .2.6V16a1 1 0 0 1-1 1h-1.2a2.5 2.5 0 0 1-4.9 0H8.9a2.5 2.5 0 0 1-4.9 0H3a1 1 0 0 1-1-1V5zm14 5h4.2L18.4 8H16v2z"/><circle cx="6.5" cy="17" r="1.4"/><circle cx="16.5" cy="17" r="1.4"/></svg></span>
          <h3><?php ff('rec_sec3_titulo','Sector transporte'); ?></h3>
          <?php if ( fiet_option( 'rec_url_transporte', '' ) ) : ?><button class="via-mat js-descarga" type="button" data-sector="transporte"><?php ff('rec_mat_boton','Descarga el material'); ?> <span>&rarr;</span></button><?php endif; ?>
        </article>
        <article class="via-card">
          <span class="via-icon"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M5 3h9a1 1 0 0 1 1 1v16H4V4a1 1 0 0 1 1-1zm12 6h3a1 1 0 0 1 1 1v10h-4V9zM7 6h2v2H7V6zm4 0h2v2h-2V6zM7 10h2v2H7v-2zm4 0h2v2h-2v-2zM7 14h2v2H7v-2zm4 0h2v2h-2v-2z"/></svg></span>
          <h3><?php ff('rec_sec4_titulo','Sector consular'); ?></h3>
          <?php if ( fiet_option( 'rec_url_consular', '' ) ) : ?><button class="via-mat js-descarga" type="button" data-sector="consular"><?php ff('rec_mat_boton','Descarga el material'); ?> <span>&rarr;</span></button><?php endif; ?>
        </article>
        <article class="via-card">
          <span class="via-icon"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 3 1 8l11 5 9-4.09V15h2V8L12 3zM5 13.18V16.5c0 1.66 3.13 3 7 3s7-1.34 7-3v-3.32l-7 3.18-7-3.18z"/></svg></span>
          <h3><?php ff('rec_sec5_titulo','Sector educativo'); ?></h3>
          <?php if ( fiet_option( 'rec_url_educativo', '' ) ) : ?><button class="via-mat js-descarga" type="button" data-sector="educativo"><?php ff('rec_mat_boton','Descarga el material'); ?> <span>&rarr;</span></button><?php endif; ?>
        </article>
        <article class="via-card">
          <span class="via-icon"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a5 5 0 0 1 5 5 5 5 0 0 1-3 4.58V13h1a4 4 0 0 1 4 4v3H5v-3a4 4 0 0 1 4-4h1v-1.42A5 5 0 0 1 7 7a5 5 0 0 1 5-5z"/></svg></span>
          <h3><?php ff('rec_sec6_titulo','Tercer Sector'); ?></h3>
          <?php if ( fiet_option( 'rec_url_tercer_sector', '' ) ) : ?><button class="via-mat js-descarga" type="button" data-sector="tercer_sector"><?php ff('rec_mat_boton','Descarga el material'); ?> <span>&rarr;</span></button><?php endif; ?>
        </article>
      </div>
      <p class="via-foot"><?php ff('rec_pop_foot','Nuestro equipo de especialistas ofrece orientación para la elaboración e implementación de protocolos de actuación. Contáctanos para más información o asesoramiento específico.'); ?></p>
    </div>
  </section>

  <!-- Panel del formulario: se abre en esta misma página (bolígrafo) -->
  <section class="report" id="formPanel" data-form-panel>
    <button class="report-close" id="formClose" type="button" aria-label="Cerrar">&times;</button>
    <?php fiet_report_form_inner(); ?>
  </section>

  <?php get_template_part( 'parts/floating' ); ?>

  <script>
    (function(){
      "use strict";
      var track=document.getElementById("recHero"), serv=document.getElementById("recServices");
      if(!track || !serv) return;
      function clamp(v,a,b){return v<a?a:v>b?b:v;}
      function remap(v,a,b){return clamp((v-a)/(b-a),0,1);}
      function onScroll(){
        var rect=track.getBoundingClientRect();
        var total=track.offsetHeight-window.innerHeight;
        var p=total>0?clamp(-rect.top/total,0,1):0;
        var so=remap(p,0.15,0.30);                 // la sección amarilla se funde a pantalla completa
        serv.style.opacity=so.toFixed(3);
        serv.style.pointerEvents=so>0.5?"auto":"none";
      }
      window.addEventListener("scroll",onScroll,{passive:true});
      window.addEventListener("resize",onScroll);
      onScroll();
    })();
  </script>

  <?php get_template_part( 'parts/descargas-modal' ); ?>

<?php get_footer(); ?>
