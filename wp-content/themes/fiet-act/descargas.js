(function(){
  "use strict";

  var CFG = window.FIET_DL || {};
  var modal    = document.getElementById("dlModal");
  var backdrop = document.getElementById("dlBackdrop");
  var form     = document.getElementById("dlForm");
  var done     = document.getElementById("dlDone");
  var sectorIn = document.getElementById("dlSector");
  var nameIn   = document.getElementById("dlNombre");
  var mailIn   = document.getElementById("dlCorreo");
  var submit   = document.getElementById("dlSubmit");
  var msg      = document.getElementById("dlMsg");
  var link     = document.getElementById("dlLink");
  var closeBtn = document.getElementById("dlClose");

  if(!modal || !form || !backdrop) return;

  function open(sector){
    sectorIn.value = sector;
    form.reset();
    sectorIn.value = sector;
    msg.hidden = true; msg.textContent = "";
    submit.disabled = false; submit.textContent = "Descargar";
    form.hidden = false; done.hidden = true;
    backdrop.hidden = false;
    requestAnimationFrame(function(){ backdrop.classList.add("open"); modal.classList.add("open"); });
    modal.setAttribute("aria-hidden", "false");
    document.body.style.overflow = "hidden";
    if(nameIn) nameIn.focus();
  }
  function close(){
    backdrop.classList.remove("open");
    modal.classList.remove("open");
    modal.setAttribute("aria-hidden", "true");
    document.body.style.overflow = "";
    setTimeout(function(){ backdrop.hidden = true; }, 350);
  }

  var btns = document.querySelectorAll(".js-descarga");
  for(var i=0;i<btns.length;i++){
    btns[i].addEventListener("click", function(){ open(this.getAttribute("data-sector")); });
  }
  closeBtn.addEventListener("click", close);
  backdrop.addEventListener("click", close);
  document.addEventListener("keydown", function(e){
    if(e.key === "Escape" && modal.classList.contains("open")) close();
  });

  form.addEventListener("submit", function(e){
    e.preventDefault();
    msg.hidden = true;
    submit.disabled = true; submit.textContent = "Enviando…";

    var data = new FormData();
    data.append("action", "fiet_descarga");
    data.append("nonce",  CFG.nonce || "");
    data.append("sector", sectorIn.value);
    data.append("nombre", nameIn.value);
    data.append("correo", mailIn.value);

    fetch(CFG.ajax, { method: "POST", credentials: "same-origin", body: data })
      .then(function(r){ return r.json(); })
      .then(function(res){
        if(res && res.success && res.data && res.data.url){
          link.href = res.data.url;
          form.hidden = true;
          done.hidden = false;
        } else {
          submit.disabled = false; submit.textContent = "Descargar";
          msg.hidden = false;
          msg.className = "dl-msg err";
          msg.textContent = (res && res.data && res.data.msg) ? res.data.msg : "No se pudo completar la descarga. Inténtalo de nuevo.";
        }
      })
      .catch(function(){
        submit.disabled = false; submit.textContent = "Descargar";
        msg.hidden = false;
        msg.className = "dl-msg err";
        msg.textContent = "Error de conexión. Inténtalo de nuevo.";
      });
  });
})();
