(function(){
  "use strict";

  var heroTrack = document.querySelector(".hero-track");
  var report    = document.getElementById("report");
  if(!heroTrack || !report) return;

  // ---- entrada cover: al cruzar el umbral (vídeo/copy ya en su estado final y FIJO por el
  //      sticky), la sección sube ENTERA con una sola transición, sin mover lo de detrás ----
  var TRIGGER = 0.86; // umbral dentro del hero-track (tras el fin de la animación al 80%)
  function onScroll(){
    var rect  = heroTrack.getBoundingClientRect();
    var total = heroTrack.offsetHeight - window.innerHeight;
    var f = -rect.top / total;            // progreso 0..1 dentro del hero-track
    report.classList.toggle("show", f >= TRIGGER);
  }
  window.addEventListener("scroll", onScroll, { passive: true });
  window.addEventListener("resize", onScroll);
  onScroll();

  // ---- envío del formulario (confidencial) ----
  var form = document.getElementById("reportForm");
  if(form){
    form.addEventListener("submit", function(e){
      e.preventDefault();
      form.innerHTML =
        '<div class="report-thanks">' +
          '<span class="tag">Comunicación confidencial</span>' +
          '<h3>Gracias por tu mensaje.</h3>' +
          '<p>Hemos recibido tu información. Si has facilitado datos de contacto, un miembro de nuestro equipo podrá ponerse en contacto contigo de forma confidencial.</p>' +
        '</div>';
    });
  }
})();
