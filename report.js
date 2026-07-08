(function(){
  "use strict";

  var report = document.getElementById("report");
  if(!report) return;
  var track  = document.querySelector(".hero-track, .tel-track");
  var TRIGGER = 0.86; // umbral dentro del track (aparece sola al final)

  var atEnd = false, manual = false;
  function apply(){ report.classList.toggle("show", manual || atEnd); }

  // ---- entrada automática al final del recorrido ----
  function onScroll(){
    if(!track) return;
    var rect  = track.getBoundingClientRect();
    var total = track.offsetHeight - window.innerHeight;
    var f = -rect.top / total;            // progreso 0..1 dentro del track
    atEnd = f >= TRIGGER;
    apply();
  }
  if(track){
    window.addEventListener("scroll", onScroll, { passive: true });
    window.addEventListener("resize", onScroll);
    onScroll();
  }

  // ---- abrir/cerrar a demanda (botón flotante del bolígrafo) ----
  var opener = document.getElementById("fabForm");
  if(opener) opener.addEventListener("click", function(){ manual = true; apply(); });
  var closer = document.getElementById("reportClose");
  if(closer) closer.addEventListener("click", function(){ manual = false; apply(); });
  document.addEventListener("keydown", function(e){ if(e.key === "Escape" && manual){ manual = false; apply(); } });

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
