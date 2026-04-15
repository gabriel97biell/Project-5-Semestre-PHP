<footer>
  <div class="top">
    <div class="logo">
      <img src="/gtech/assets/img/logo G-TECH.jpg"/>
    </div>

  </div>
  <div class="separator"></div>
  <div class="bottom">
    <p>❤️ By G-TECH</p>
  </div>
</footer>
<!-- Modal de confirmação do plano -->
<div id="modalPlano" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.7); justify-content:center; align-items:center; z-index:999;">
  <div style="background-color:#fff; padding:30px; border-radius:10px; text-align:center; max-width:400px;">
    <p id="mensagemPlano" style="font-size:18px;"></p>
    <br>
    <button onclick="confirmarPlano()" style="margin-right: 10px;">Confirmar</button>
    <button onclick="fecharModal()">Cancelar</button>
  </div>
  <script>
    function openMenu() {
      const nav = document.querySelector("nav");
      nav.classList.toggle("open");
    }

    function abrirModal(plano) {
      const modal = document.getElementById("modalPlano");
      const mensagem = document.getElementById("mensagemPlano");
      mensagem.textContent = `Você escolheu o plano: ${plano}. Deseja confirmar?`;
      modal.style.display = "flex";
    }

    function fecharModal() {
      document.getElementById("modalPlano").style.display = "none";
    }

    function confirmarPlano() {
      alert("Plano confirmado com sucesso!");
      fecharModal();
    }
  </script>


  <!-- <script src="/assets/js/script.js"></script> -->
  <!-- Bootstrap Bundle com Popper (necessário para dropdowns e modals) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


  </body>

  </html>