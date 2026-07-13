(function(){
  "use strict";

  // ---- popup automático al final del recorrido (#report) ----
  var report = document.getElementById("report");
  var track  = document.querySelector(".hero-track, .tel-track");
  var TRIGGER = 0.86; // umbral dentro del track (aparece solo al final)

  var atEnd = false, manualReport = false, dismissedEnd = false;
  function applyReport(){ if(report) report.classList.toggle("show", manualReport || (atEnd && !dismissedEnd)); }

  function onScroll(){
    if(!track || !report) return;
    var rect  = track.getBoundingClientRect();
    var total = track.offsetHeight - window.innerHeight;
    var f = -rect.top / total;            // progreso 0..1 dentro del track
    atEnd = f >= TRIGGER;
    if(!atEnd) dismissedEnd = false;      // al salir del final se rearma
    applyReport();
  }
  if(track && report){
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
  var formEl    = document.getElementById("reportForm");
  var formPanel = formEl ? formEl.closest(".report") : null;
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

  // ---- envío del formulario (confidencial) ----
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
