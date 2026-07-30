(function(){
  "use strict";

  var burger   = document.getElementById("navBurger");
  var drawer   = document.getElementById("navDrawer");
  var backdrop = document.getElementById("navBackdrop");
  var closeBtn = document.getElementById("navDrawerClose");
  var linksBox = document.getElementById("navDrawerLinks");
  if(!burger || !drawer || !backdrop || !linksBox) return;

  // clona los enlaces del navbar (excepto el botón de llamada) en el panel
  var links = document.querySelectorAll(".navbar a:not(.nav-call)");
  for(var i = 0; i < links.length; i++){
    var a = document.createElement("a");
    a.href = links[i].getAttribute("href");
    a.textContent = links[i].textContent;
    a.addEventListener("click", close);
    linksBox.appendChild(a);
  }

  // "Ir al cuestionario": en móvil sustituye al botón flotante (que se oculta)
  var quizOpener = document.querySelector(".js-open-quiz");
  if(quizOpener){
    var q = document.createElement("a");
    q.href = "#cuestionario";
    q.textContent = "Ir al cuestionario";
    q.className = "nav-drawer-quiz";
    q.addEventListener("click", function(e){
      e.preventDefault();
      close();
      quizOpener.click();          // reutiliza el manejador del cuestionario
    });
    linksBox.appendChild(q);
  }

  function open(){
    drawer.classList.add("open");
    backdrop.classList.add("open");
    document.body.style.overflow = "hidden";
    burger.setAttribute("aria-expanded", "true");
  }
  function close(){
    drawer.classList.remove("open");
    backdrop.classList.remove("open");
    document.body.style.overflow = "";
    burger.setAttribute("aria-expanded", "false");
  }

  burger.addEventListener("click", function(){
    drawer.classList.contains("open") ? close() : open();
  });

  // ---- speed-dial de los botones flotantes (móvil): icono de persona que despliega los tres ----
  var fabStack  = document.querySelector(".fab-stack");
  var fabToggle = document.getElementById("fabToggle");
  if(fabStack && fabToggle){
    fabToggle.addEventListener("click", function(e){
      e.stopPropagation();
      var isOpen = fabStack.classList.toggle("open");
      fabToggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
    });
    var acts = fabStack.querySelectorAll(".fab-call, .fab-sec");
    for(var f = 0; f < acts.length; f++){
      acts[f].addEventListener("click", function(){ fabStack.classList.remove("open"); fabToggle.setAttribute("aria-expanded","false"); });
    }
    document.addEventListener("click", function(e){
      if(!fabStack.contains(e.target)){ fabStack.classList.remove("open"); fabToggle.setAttribute("aria-expanded","false"); }
    });
  }
  if(closeBtn) closeBtn.addEventListener("click", close);
  backdrop.addEventListener("click", close);
  document.addEventListener("keydown", function(e){
    if(e.key === "Escape" && drawer.classList.contains("open")) close();
  });
})();
