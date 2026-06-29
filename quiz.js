(function(){
  "use strict";

  var QUESTIONS = [
    "¿Tu jefe/a o empleador/a te amenaza?",
    "¿Tienes limitada tu libertad de movimiento (por ejemplo, no puedes salir cuando no trabajas)?",
    "¿Te han quitado el pasaporte u otros documentos personales?",
    "¿Te impiden acceder a atención médica cuando la necesitas?",
    "¿Trabajas más de 8 horas al día sin descanso?",
    "¿Trabajas en condiciones inseguras o insalubres?",
    "¿Te obliga a trabajar incluso cuando estás enfermo/a?",
    "¿Te obliga a hacer actividades con las que no te sientes cómodo/a?",
    "¿Te dicen que tienes una deuda que debes pagar?",
    "¿Recibes el salario tarde, incompleto o variable sin explicación?",
    "¿Cobras menos del salario mínimo legal?"
  ];

  var openBtn   = document.getElementById("openQuiz");
  var drawer    = document.getElementById("quizDrawer");
  var backdrop  = document.getElementById("quizBackdrop");
  var closeBtn  = document.getElementById("quizClose");
  var list      = document.getElementById("quizList");
  var resultBtn = document.getElementById("quizResultBtn");
  var resultBox = document.getElementById("quizResult");

  if(!drawer || !list) return;

  // ---- construir las preguntas ----
  var answers = new Array(QUESTIONS.length); // "si" | "no" | undefined
  QUESTIONS.forEach(function(text, i){
    var q = document.createElement("div");
    q.className = "q";

    var num = document.createElement("span");
    num.className = "q-num";
    num.textContent = (i + 1 < 10 ? "0" : "") + (i + 1);

    var p = document.createElement("p");
    p.className = "q-text";
    p.textContent = text;

    var opts = document.createElement("div");
    opts.className = "q-opts";

    ["si", "no"].forEach(function(v){
      var b = document.createElement("button");
      b.type = "button";
      b.className = "opt";
      b.setAttribute("data-v", v);
      b.textContent = v === "si" ? "Sí" : "No";
      b.addEventListener("click", function(){
        answers[i] = v;
        opts.querySelectorAll(".opt").forEach(function(o){ o.classList.remove("sel"); });
        b.classList.add("sel");
      });
      opts.appendChild(b);
    });

    q.appendChild(num);
    q.appendChild(p);
    q.appendChild(opts);
    list.appendChild(q);
  });

  // ---- abrir / cerrar ----
  var lastFocus = null;
  function open(){
    lastFocus = document.activeElement;
    backdrop.hidden = false;
    requestAnimationFrame(function(){
      backdrop.classList.add("open");
      drawer.classList.add("open");
    });
    drawer.setAttribute("aria-hidden", "false");
    document.body.style.overflow = "hidden";
    closeBtn.focus();
  }
  function close(){
    backdrop.classList.remove("open");
    drawer.classList.remove("open");
    drawer.setAttribute("aria-hidden", "true");
    document.body.style.overflow = "";
    setTimeout(function(){ backdrop.hidden = true; }, 420);
    if(lastFocus) lastFocus.focus();
  }

  if(openBtn) openBtn.addEventListener("click", function(e){ e.preventDefault(); open(); });
  closeBtn.addEventListener("click", close);
  backdrop.addEventListener("click", close);
  document.addEventListener("keydown", function(e){
    if(e.key === "Escape" && drawer.classList.contains("open")) close();
  });

  // ---- resultado orientativo ----
  resultBtn.addEventListener("click", function(){
    var yes = answers.filter(function(a){ return a === "si"; }).length;
    var level, label, color, title, msg;
    if(yes === 0){
      level = "bajo"; label = "Riesgo bajo"; color = "#1FAE5A";
      title = "Riesgo bajo";
      msg = "No has marcado señales de alerta. Por lo que has indicado, no aparecen indicios claros de trata. Aun así, si algo te preocupa, puedes hablar con el Teléfono de Ayuda de forma confidencial y gratuita.";
    } else if(yes <= 3){
      level = "medio"; label = "Riesgo medio"; color = "#E0A100";
      title = "Riesgo medio";
      msg = "Has marcado entre 1 y 3 señales. Algunas de tus respuestas pueden indicar una situación de riesgo. Te recomendamos contactar con el Teléfono de Ayuda para valorarlo con profesionales. Es confidencial y gratuito.";
    } else {
      level = "alto"; label = "Riesgo alto"; color = "#D7263D";
      title = "Riesgo alto";
      msg = "Has marcado 4 o más señales, que coinciden con indicios de trata. No estás sola: contacta cuanto antes con el Teléfono de Ayuda. Puedes permanecer en el anonimato.";
    }

    resultBox.setAttribute("data-level", level);
    resultBox.innerHTML =
      '<span class="risk-badge" style="background:' + color + '">' + label + '</span>' +
      '<h3>' + title + '</h3>' +
      '<p>' + msg + '</p>' +
      '<a class="call" href="tel:900759759"><span class="dot" style="width:9px;height:9px;border-radius:50%;background:#1FAE5A;display:inline-block"></span>Llamar 900 759 759</a>' +
      '<div><button class="redo" type="button">Volver a responder</button></div>';

    list.hidden = true;
    resultBtn.hidden = true;
    resultBox.hidden = false;
    resultBox.scrollIntoView({ behavior: "smooth", block: "start" });

    resultBox.querySelector(".redo").addEventListener("click", function(){
      resultBox.hidden = true;
      list.hidden = false;
      resultBtn.hidden = false;
      drawer.scrollTo({ top: 0, behavior: "smooth" });
    });
  });
})();
