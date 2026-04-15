function openModal(modalId) {
  const modal = document.getElementById(modalId);
  modal.style.display = "flex";
  document.body.style.overflow = "hidden";
}

function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  modal.style.display = "none";
  document.body.style.overflow = "auto";
}

function openMenu() {
  const nav = document.querySelector('nav');
  nav.classList.toggle('open');
}

window.addEventListener("click", (e) => {
  const modals = document.querySelectorAll(".modal");
  modals.forEach((modal) => {
    if (e.target === modal) {
      modal.style.display = "none";
      document.body.style.overflow = "auto";
    }
  });
});

function setupPaymentToggles() {
  const paymentMethods = document.querySelectorAll('input[name="metodo"]');
  const cardFields = document.getElementById("cartaoFields");
  const pixFields = document.getElementById("pixFields");

  if (!paymentMethods.length || !cardFields || !pixFields) return;

  paymentMethods.forEach((method) => {
    method.addEventListener("change", function () {
      if (this.value === "cartao") {
        cardFields.style.display = "block";
        pixFields.style.display = "none";
      } else {
        cardFields.style.display = "none";
        pixFields.style.display = "block";
      }
    });
  });
}

function setupFormSubmissions() {
  const forms = document.querySelectorAll("form[data-submit-spinner]");

  forms.forEach((form) => {
    form.addEventListener("submit", function (e) {
      const submitBtn = this.querySelector('button[type="submit"]');
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
          <span class="spinner"></span> Processando...
        `;
      }
    });
  });
}

function setupInputMasks() {
  const cardNumber = document.getElementById("numeroCartao");
  if (cardNumber) {
    cardNumber.addEventListener("input", function (e) {
      let value = e.target.value.replace(/\D/g, "");
      value = value.replace(/(\d{4})(?=\d)/g, "$1 ");
      e.target.value = value;
    });
  }

  const cardExpiry = document.getElementById("validade");
  if (cardExpiry) {
    cardExpiry.addEventListener("input", function (e) {
      let value = e.target.value.replace(/\D/g, "");
      if (value.length > 2) {
        value = value.substring(0, 2) + "/" + value.substring(2, 4);
      }
      e.target.value = value;
    });
  }

  const cardCvv = document.getElementById("cvv");
  if (cardCvv) {
    cardCvv.addEventListener("input", function (e) {
      e.target.value = e.target.value.replace(/\D/g, "").substring(0, 3);
    });
  }
}

// NOVA FUNÇÃO: Alternar exibição dos detalhes dos cards
function mostrarDetalhes(botao) {
  // Fecha todos os detalhes primeiro
  const todosOsDetalhes = document.querySelectorAll('.detalhes');
  todosOsDetalhes.forEach((detalhe) => {
    if (detalhe !== botao.nextElementSibling) {
      detalhe.style.display = "none";
    }
  });

  // Alterna o card clicado
  const detalheAtual = botao.nextElementSibling;
  if (detalheAtual.style.display === "block") {
    detalheAtual.style.display = "none";
  } else {
    detalheAtual.style.display = "block";
  }
}

document.addEventListener("DOMContentLoaded", function () {
  setupPaymentToggles();
  setupFormSubmissions();
  setupInputMasks();

  setTimeout(() => {
    const alerts = document.querySelectorAll(".alert");
    alerts.forEach((alert) => {
      alert.style.opacity = "0";
      setTimeout(() => alert.remove(), 300);
    });
  }, 5000);
});



