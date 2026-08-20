<?php if ( ! defined( 'ABSPATH' ) ) exit; get_header(); ?>
  <style>
    /* ===== Página Prevención ===== */
    #prevHero{ height:120vh; }                                  /* recorrido para llegar al footer/popup (recortado el tramo estático muerto) */

    /* Ventana emergente: título + 3 cards */
    .report-vias{ display:flex; flex-direction:column; justify-content:center; }
    .report-vias .report-left{ text-align:center; max-width:680px; margin:0 auto clamp(10px,1.6vh,20px); }
    .report-vias .report-left p{ margin-left:auto; margin-right:auto; margin-bottom:0; }
    .via-cards{ display:grid; grid-template-columns:repeat(3,1fr); gap:18px; }
    .via-card{ display:flex; flex-direction:column; text-align:left; border:1px solid rgba(11,14,18,.14); border-radius:16px; padding:clamp(22px,2vw,30px); background:rgba(255,255,255,.4); }
    .via-icon{ display:grid; place-items:center; width:50px; height:50px; border-radius:13px; background:#FFD400; color:#0B0E12; margin-bottom:clamp(16px,2.4vh,24px); }
    .via-icon svg{ width:27px; height:27px; }
    .via-card h3{ font-family:var(--font-display); font-weight:600; font-size:clamp(1.25rem,1.8vw,1.6rem); margin-bottom:12px; color:var(--fg); }
    .via-card p{ font-family:var(--font-body); font-size:.95rem; line-height:1.55; color:rgba(11,14,18,.7); }
    @media (max-width:860px){ .via-cards{ grid-template-columns:1fr; } }
    .via-card p{ flex:1; }
    .via-card .btn-hero{ align-self:flex-start; margin-top:22px; }
    /* Modal de recomendaciones */
    .rec-backdrop{ position:fixed; inset:0; z-index:96; background:rgba(11,14,18,.42); backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px); opacity:0; transition:opacity .3s ease; }
    .rec-backdrop.open{ opacity:1; }
    .rec-modal{ position:fixed; z-index:97; left:50%; top:50%; transform:translate(-50%,-46%) scale(.98); opacity:0; pointer-events:none; width:min(760px,92vw); max-height:86vh; overflow-y:auto; background:rgba(255,255,255,.9); backdrop-filter:blur(26px) saturate(150%); -webkit-backdrop-filter:blur(26px) saturate(150%); border:1px solid rgba(255,255,255,.6); border-radius:26px; box-shadow:0 40px 100px rgba(11,14,18,.28); padding:clamp(30px,4vw,52px); transition:opacity .35s ease, transform .35s cubic-bezier(0.22,1,0.36,1); }
    .rec-modal.open{ opacity:1; pointer-events:auto; transform:translate(-50%,-50%) scale(1); }
    .rec-close{ position:absolute; top:18px; right:20px; width:40px; height:40px; border:0; background:transparent; font-size:2rem; line-height:1; color:var(--fg); cursor:pointer; border-radius:10px; }
    .rec-body h2{ font-family:var(--font-display); font-weight:700; font-size:clamp(1.7rem,3vw,2.4rem); letter-spacing:-.02em; margin-bottom:16px; color:var(--fg); }
    .rec-body p{ font-size:clamp(1rem,1.2vw,1.1rem); line-height:1.6; color:rgba(11,14,18,.72); margin-bottom:22px; }
    .rec-body h3{ font-family:var(--font-display); font-weight:700; font-size:clamp(1.05rem,1.3vw,1.2rem); color:#0B0E12; margin:26px 0 12px; }
    .rec-body ul{ list-style:none; margin:0; padding:0; display:flex; flex-direction:column; gap:11px; }
    .rec-body li{ position:relative; padding-left:22px; font-size:clamp(.95rem,1.1vw,1.02rem); line-height:1.55; color:rgba(11,14,18,.72); }
    .rec-body li::before{ content:""; position:absolute; left:2px; top:.6em; width:7px; height:7px; border-radius:50%; background:#FFD400; }
    .rec-body li strong{ color:var(--fg); font-weight:600; }
    /* Ocultar la barra de scroll de los popups (se mantiene el desplazamiento) */
    #report, .rec-modal{ scrollbar-width:none; -ms-overflow-style:none; }
    #report::-webkit-scrollbar, .rec-modal::-webkit-scrollbar{ width:0; height:0; display:none; }
  </style>

  
<?php get_template_part( 'parts/site-nav' ); ?>


  <!-- Portada estática: imagen (vídeo 1) fija; al hacer scroll sube la ventana emergente -->
  <section class="hero-track" id="prevHero" data-static>
    <div class="stage">
      <canvas id="c" aria-hidden="true"></canvas>
      <div class="overlay">
        <div class="copy">
          <span class="eyebrow" id="eyebrow"></span>
          <p class="para" id="para" style="opacity:0"></p>
        </div>
        <div class="intro" id="intro">
          <span class="tag"><span class="dot"></span><?php ff('prev_eyebrow','Prevención'); ?></span>
          <h2><?php ff('prev_titulo','¿Cómo mantenerse a salvo?'); ?></h2>
          <p><?php ff('prev_parrafo','La trata puede comenzar en situaciones cotidianas como la búsqueda de empleo, un viaje o el uso de internet. Conocer los riesgos y saber identificarlos es clave para protegerte.'); ?></p>
          <button class="btn-hero js-open-report" type="button"><?php ff('prev_boton','Ver recomendaciones'); ?></button>
        </div>
      </div>
      <div class="hint" id="hint"><span class="bar"></span>Desplázate</div>
    </div>
  </section>

  <!-- Ventana emergente que sube desde abajo al hacer scroll -->
  <section class="report" id="report">
    <button class="report-close" id="reportClose" type="button" aria-label="Cerrar">&times;</button>
    <div class="report-inner report-vias">
      <div class="report-left">
        <span class="tag"><?php ff('prev_pop_eyebrow','Prevención'); ?></span>
        <h2><?php ff('prev_pop_titulo','Recomendaciones para mantenerte seguro'); ?></h2>
        <p><?php ff('prev_pop_parrafo','La trata puede empezar en un empleo, un viaje o en internet. Estas son las claves para reducir riesgos e identificar señales de alerta en cada situación.'); ?></p>
      </div>
      <div class="via-cards">
        <article class="via-card">
          <span class="via-icon"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20 7h-4V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2zM10 5h4v2h-4V5z"/></svg></span>
          <h3><?php ff('prev_card1_titulo','Empleo Seguro'); ?></h3>
          <p><?php ff('prev_card1_desc','Verifica la oferta y a quien contrata, nunca entregues tus documentos y comparte con alguien de confianza dónde y con quién vas a trabajar.'); ?></p>
          <button class="btn-hero js-rec" data-rec="empleo" type="button"><?php ff('prev_card_boton','Saber más'); ?></button>
        </article>
        <article class="via-card">
          <span class="via-icon"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M21 16v-2l-8-5V3.5a1.5 1.5 0 0 0-3 0V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5L21 16z"/></svg></span>
          <h3><?php ff('prev_card2_titulo','Viaje Seguro'); ?></h3>
          <p><?php ff('prev_card2_desc','Lleva copias de tus documentos, comparte tu itinerario y ten a mano los contactos de tu embajada y de organizaciones de ayuda.'); ?></p>
          <button class="btn-hero js-rec" data-rec="viaje" type="button"><?php ff('prev_card_boton','Saber más'); ?></button>
        </article>
        <article class="via-card">
          <span class="via-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.6 2.7 2.6 15.3 0 18M12 3c-2.6 2.7-2.6 15.3 0 18"/></svg></span>
          <h3><?php ff('prev_card3_titulo','Internet Seguro'); ?></h3>
          <p><?php ff('prev_card3_desc','Protege tus datos personales, desconfía de perfiles desconocidos y extrema la precaución si conciertas una cita con alguien conocido por internet.'); ?></p>
          <button class="btn-hero js-rec" data-rec="internet" type="button"><?php ff('prev_card_boton','Saber más'); ?></button>
        </article>
      </div>
    </div>
  </section>

  <!-- Panel del formulario: se abre en esta misma página (bolígrafo) -->
  <section class="report" id="formPanel" data-form-panel>
    <button class="report-close" id="formClose" type="button" aria-label="Cerrar">&times;</button>
    <?php fiet_report_form_inner(); ?>
  </section>

  <?php get_template_part( 'parts/floating' ); ?>

  <!-- Modal de recomendaciones (se abre desde las cards) -->
  <div class="rec-backdrop" id="recBackdrop" hidden></div>
  <aside class="rec-modal" id="recModal" aria-hidden="true" aria-label="Recomendaciones">
    <button class="rec-close" id="recClose" type="button" aria-label="Cerrar">&times;</button>
    <div class="rec-body" id="recBody"></div>
  </aside>

  <!-- Contenidos de las recomendaciones -->
  <div hidden>
    <div id="rec-empleo"><?php echo fiet_option( 'rec_modal_empleo', fiet_reco_defaults()['empleo'] ); ?></div>
    <div id="rec-viaje"><?php echo fiet_option( 'rec_modal_viaje', fiet_reco_defaults()['viaje'] ); ?></div>
    <div id="rec-internet"><?php echo fiet_option( 'rec_modal_internet', fiet_reco_defaults()['internet'] ); ?></div>
  </div>

    <script>
    (function(){
      "use strict";
      var modal=document.getElementById("recModal"), backdrop=document.getElementById("recBackdrop");
      var body=document.getElementById("recBody"), closeBtn=document.getElementById("recClose");
      if(!modal||!backdrop||!body) return;
      function open(key){
        var src=document.getElementById("rec-"+key); if(!src) return;
        body.innerHTML=src.innerHTML;
        backdrop.hidden=false;
        requestAnimationFrame(function(){ backdrop.classList.add("open"); modal.classList.add("open"); });
        modal.setAttribute("aria-hidden","false");
        document.body.style.overflow="hidden";
        modal.scrollTop=0;
      }
      function close(){
        backdrop.classList.remove("open"); modal.classList.remove("open");
        modal.setAttribute("aria-hidden","true");
        document.body.style.overflow="";
        setTimeout(function(){ backdrop.hidden=true; },350);
      }
      var btns=document.querySelectorAll(".js-rec");
      for(var i=0;i<btns.length;i++){ btns[i].addEventListener("click", function(){ open(this.getAttribute("data-rec")); }); }
      closeBtn.addEventListener("click", close);
      backdrop.addEventListener("click", close);
      document.addEventListener("keydown", function(e){ if(e.key==="Escape" && modal.classList.contains("open")) close(); });
    })();
  </script>

<?php get_footer(); ?>
