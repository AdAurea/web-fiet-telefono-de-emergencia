(function(){
  "use strict";

  // ---- popup automático al llegar al footer (#report) ----
  // Emerge cuando el footer entra en el viewport (es decir, al hacer scroll hasta
  // el final de la página). Si no hubiera footer, cae al 86% del recorrido.
  var report = document.getElementById("report");
  var footer = document.querySelector(".site-footer");
  var track  = document.querySelector(".hero-track, .tel-track");
  var TRIGGER = 0.86; // umbral de reserva dentro del track (si no hay footer)

  var atEnd = false, manualReport = false, dismissedEnd = false;
  function applyReport(){ if(report) report.classList.toggle("show", manualReport || (atEnd && !dismissedEnd)); }

  function computeAtEnd(){
    if(footer){
      return footer.getBoundingClientRect().top <= window.innerHeight; // el footer ha entrado en el viewport
    }
    if(track){
      var rect  = track.getBoundingClientRect();
      var total = track.offsetHeight - window.innerHeight;
      return (-rect.top / total) >= TRIGGER;
    }
    return false;
  }
  function onScroll(){
    if(!report) return;
    atEnd = computeAtEnd();
    if(!atEnd) dismissedEnd = false;      // al salir del final se rearma
    applyReport();
  }
  if(report && (footer || track)){
    window.addEventListener("scroll", onScroll, { passive: true });
    window.addEventListener("resize", onScroll);
    onScroll();
  }
  var reportClose = document.getElementById("reportClose");
  if(reportClose) reportClose.addEventListener("click", function(){ manualReport = false; dismissedEnd = true; applyReport(); });
  // abrir el popup automático a demanda (p. ej. botón "Ver recomendaciones")
  var reportOpeners = document.querySelectorAll(".js-open-report");
  for(var ro = 0; ro < reportOpeners.length; ro++){
    reportOpeners[ro].addEventListener("click", function(e){ e.preventDefault(); manualReport = true; dismissedEnd = false; applyReport(); });
  }

  // ---- panel del formulario ----
  // En la home el formulario ES el propio #report; en la página del teléfono
  // vive en un panel aparte (#formPanel). Detectamos su contenedor real.
  // El panel se marca con [data-form-panel]: en "Qué es la trata" es el propio
  // #report (compartido con el popup automático); en el resto, #formPanel.
  var formPanel = document.querySelector("[data-form-panel]");
  var formShared = (formPanel && formPanel === report); // comparte overlay con el popup automático

  function openForm(){
    if(!formPanel) return;
    if(formShared){ manualReport = true; dismissedEnd = false; applyReport(); }
    else formPanel.classList.add("show");
  }
  function closeForm(){
    if(!formPanel) return;
    if(formShared){ manualReport = false; applyReport(); }
    else formPanel.classList.remove("show");
  }

  // abre el formulario: bolígrafo flotante (#fabForm) y botón "Abrir formulario" (.js-open-form)
  var openers = document.querySelectorAll("#fabForm, .js-open-form");
  for(var i = 0; i < openers.length; i++){
    openers[i].addEventListener("click", function(e){ e.preventDefault(); openForm(); });
  }
  var formClose = document.getElementById("formClose");
  if(formClose) formClose.addEventListener("click", closeForm);

  // abrir desde otra página (enlace que_es_la_trata.html#informar)
  if(location.hash === "#informar" && formPanel) openForm();

  document.addEventListener("keydown", function(e){
    if(e.key === "Escape"){ manualReport = false; applyReport(); closeForm(); }
  });

  // ---- Contact Form 7: al enviarse, sustituye el formulario por el mensaje de
  // gracias (mismo comportamiento que el formulario original) ----
  document.addEventListener("wpcf7mailsent", function(e){
    var panel = e.target.closest(".report") || (formPanel || document);
    var right = panel.querySelector(".report-right");
    if(right){
      right.innerHTML =
        '<div class="report-thanks">' +
          '<span class="tag">Comunicación confidencial</span>' +
          '<h3>Gracias por tu mensaje.</h3>' +
          '<p>Hemos recibido tu información. Si has facilitado datos de contacto, un miembro de nuestro equipo podrá ponerse en contacto contigo de forma confidencial.</p>' +
        '</div>';
    }
  }, false);

  // ---- envío del formulario estático de reserva (si NO se usa Contact Form 7) ----
  var formEl = document.getElementById("reportForm");
  if(formEl){
    formEl.addEventListener("submit", function(e){
      e.preventDefault();
      formEl.innerHTML =
        '<div class="report-thanks">' +
          '<span class="tag">Comunicación confidencial</span>' +
          '<h3>Gracias por tu mensaje.</h3>' +
          '<p>Hemos recibido tu información. Si has facilitado datos de contacto, un miembro de nuestro equipo podrá ponerse en contacto contigo de forma confidencial.</p>' +
        '</div>';
    });
  }
})();
