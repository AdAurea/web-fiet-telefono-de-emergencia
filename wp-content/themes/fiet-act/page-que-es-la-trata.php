<?php if ( ! defined( 'ABSPATH' ) ) exit; get_header(); ?>
  
<?php get_template_part( 'parts/site-nav' ); ?>


  <section class="hero-track">
    <div class="stage">
      <canvas id="c" aria-hidden="true"></canvas>
      <div class="overlay">
        <div class="copy">
          <span class="eyebrow" id="eyebrow"><span class="dot"></span><?php ff('qet_eyebrow','¿Qué es la trata?'); ?></span>
          <p class="para" id="para" style="opacity:0"><?php ff('qet_def','La trata de personas Es un delito que consiste en la captación, traslado y explotación de personas mediante engaño, abuso de vulnerabilidad o violencia, con fines como la explotación sexual, laboral u otras formas de explotación.'); ?></p>
        </div>
        <div class="copy2" id="copy2">
          <span class="eyebrow" id="eyebrow2"><span class="dot"></span><?php ff('qet_esp_eyebrow','La trata en España'); ?></span>
          <h2 class="para" id="para2" style="opacity:0"><?php ff('qet_esp_titulo','España es un país de origen, tránsito y destino de la trata de seres humanos.'); ?></h2>
          <div class="copy2-body" id="copy2body" style="opacity:0">
            <p><?php ff('qet_esp_parrafo','Se han detectado casos en todas las comunidades autónomas y, además, el país se sitúa entre los mayores consumidores de prostitución del mundo.'); ?></p>
          </div>
        </div>
        <div class="copy2 copy2-alt" id="copy2b" style="opacity:0">
          <span class="eyebrow show" id="eyebrow4"><span class="dot"></span><?php ff('qet_esp_eyebrow','La trata en España'); ?></span>
          <p class="para" id="para4"><?php ff('qet_esp_parrafo2','España es además uno de los países europeos con mayor demanda de prostitución, un factor que favorece la explotación sexual. Según el Ministerio del Interior, en 2024 el 56 % de las víctimas detectadas fueron mujeres y el 44 % hombres.'); ?></p>
        </div>
        <div class="intro" id="intro">
          <span class="tag"><span class="dot"></span><?php ff('qet_intro_tag','Confidencial · Gratuito · Disponible 24/7'); ?></span>
          <h2><?php ff('qet_intro_titulo','No estás sola'); ?></h2>
          <p><?php ff( 'qet_intro_parrafo0', 'El 900 759 759 es el Teléfono de Ayuda Contra la Trata en España. Está disponible 24/7 y es atendido por profesionales especializados que ofrecen una respuesta rápida, segura y confidencial.' ); ?></p>
          <p><?php ff( 'qet_intro_parrafo', 'Si crees que tú o alguien que conoces puede estar en una situación de trata, contacta. Puedes permanecer en el anonimato.' ); ?></p>
          <a href="tel:<?php echo esc_attr( fiet_option('telefono_tel','900759759') ); ?>" class="btn-hero"><?php ff('qet_intro_boton','Línea de asistencia 24h'); ?></a>
        </div>
        <div class="quiz" id="quiz">
          <span class="tag"><span class="dot"></span><?php ff('qet_quiz_tag','Autoevaluación confidencial'); ?></span>
          <h2><?php ff('qet_quiz_titulo','¿Podrías estar en una situación de trata?'); ?></h2>
          <p><?php ff('qet_quiz_parrafo','Responde a estas preguntas para identificar posibles señales de alerta. El resultado es orientativo y no sustituye el asesoramiento profesional.'); ?></p>
          <a href="#cuestionario" class="btn-hero" id="openQuiz"><?php ff('qet_quiz_boton','Comprueba tu situación'); ?></a>
        </div>
        <div class="orb-cover" id="orbCover"></div>
        <div class="elements" id="elements">
          <span class="eyebrow" id="eyebrow3"><span class="dot"></span><?php ff('qet_el_eyebrow','Los tres elementos del delito'); ?></span>
          <h2 class="para el-title" id="eltitle"><?php ff('qet_el_titulo','La existencia de estos tres elementos constituye el delito de trata.'); ?></h2>
          <div class="el-cards">
            <article class="el-card"><h3><?php ff('qet_el1_titulo','La acción'); ?></h3><p><?php ff('qet_el1_desc','Captación, transporte, traslado, acogida o recepción de personas.'); ?></p></article>
            <article class="el-card"><h3><?php ff('qet_el2_titulo','Los medios'); ?></h3><p><?php ff('qet_el2_desc','Engaño, abuso de una situación de vulnerabilidad, coacción o violencia.'); ?></p></article>
            <article class="el-card"><h3><?php ff('qet_el3_titulo','El fin'); ?></h3><p><?php ff('qet_el3_desc','La explotación de la persona para obtener un beneficio económico.'); ?></p></article>
          </div>
        </div>
        <div class="tipos" id="tipos">
          <h2 class="para el-title" id="tipostitle"><?php ff('qet_tipos_titulo','Existen diferentes tipos de trata'); ?></h2>
          <div class="tipo-cards">
            <article class="tipo-card">
              <span class="tipo-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-6.5 8-6.5s8 2.5 8 6.5"/></svg></span>
              <h3><?php ff('qet_tipo1','Explotación sexual'); ?></h3>
            </article>
            <article class="tipo-card">
              <span class="tipo-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M3 12h18"/></svg></span>
              <h3><?php ff('qet_tipo2','Explotación laboral'); ?></h3>
            </article>
            <article class="tipo-card">
              <span class="tipo-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M6 21v-5a4 4 0 0 1 4-4h1l4-3 1.5 1.5L14 13"/><path d="M8 12V6a1.5 1.5 0 0 1 3 0v4"/><circle cx="17" cy="5" r="2"/></svg></span>
              <h3><?php ff('qet_tipo3','Mendicidad forzada'); ?></h3>
            </article>
            <article class="tipo-card">
              <span class="tipo-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="8.5" cy="14" r="5"/><circle cx="15.5" cy="14" r="5"/><path d="M6.5 8 8.5 5h2M17.5 8 15.5 5h-2"/></svg></span>
              <h3><?php ff('qet_tipo4','Matrimonio forzado'); ?></h3>
            </article>
            <article class="tipo-card">
              <span class="tipo-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v5c0 4.4-3 8-7 10-4-2-7-5.6-7-10V6l7-3z"/><path d="M12 8v4M12 15.5v.5"/></svg></span>
              <h3><?php ff('qet_tipo5','Criminalidad forzada'); ?></h3>
            </article>
            <article class="tipo-card">
              <span class="tipo-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M20.8 6.6a4.6 4.6 0 0 0-7.8-2.5L12 5l-1-.9A4.6 4.6 0 0 0 3.2 6.6c-.9 2.9 1.2 5.4 3.3 7.2"/><path d="M3 15h4l1.5-2.5L11 18l2-9 1.8 4H21"/></svg></span>
              <h3><?php ff('qet_tipo6','Extracción de órganos'); ?></h3>
            </article>
          </div>
        </div>
        <div class="caras" id="caras">
          <h2 class="caras-title">La trata tiene muchas caras</h2>
          <div class="cara-cards">
            <article class="cara-card"><h4>Explotación sexual</h4></article>
            <article class="cara-card"><h4>Explotación laboral</h4></article>
            <article class="cara-card"><h4>Mendicidad forzada</h4></article>
            <article class="cara-card"><h4>Matrimonio forzado</h4></article>
            <article class="cara-card"><h4>Criminalidad forzada</h4></article>
            <article class="cara-card"><h4>Servidumbre doméstica</h4></article>
            <article class="cara-card"><h4>Extracción de órganos</h4></article>
            <article class="cara-card"><h4>Explotación agrícola</h4></article>
            <article class="cara-card"><h4>Trata de menores</h4></article>
          </div>
        </div>
      </div>
      <div class="hint" id="hint"><span class="bar"></span>Desplázate</div>
    </div>
  </section>

  <!-- Sección "Informar una sospecha": entra deslizando desde abajo sobre la anterior -->
  <section class="report" id="report" data-form-panel>
    <button class="report-close" id="reportClose" type="button" aria-label="Cerrar">&times;</button>
    <?php fiet_report_form_inner(); ?>
  </section>

  <?php get_template_part( 'parts/floating' ); ?>

<?php get_footer(); ?>
