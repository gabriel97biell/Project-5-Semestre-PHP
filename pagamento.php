<?php
require_once 'includes/config.php';

if (!isLoggedIn()) {
    $_SESSION['error'] = "Você precisa estar logado para acessar esta página.";
    redirect('/gtech/login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['plano_id'])) {
    $plano_id = $_POST['plano_id'];


    $stmt = $pdo->prepare("SELECT * FROM planos WHERE id = ?");
    $stmt->execute([$plano_id]);
    $plano = $stmt->fetch();

    if (!$plano) {
        $_SESSION['error'] = "Plano não encontrado.";
        redirect('/planos.php');
    }


    $stmt = $pdo->prepare("SELECT * FROM assinaturas WHERE usuario_id = ? AND plano_id = ? AND status = 'ativo'");
    $stmt->execute([$_SESSION['user_id'], $plano_id]);
    $assinatura = $stmt->fetch();

    if ($assinatura) {
        $_SESSION['info'] = "Você já possui este plano ativo.";
        redirect('/user/dashboard.php');
    }


    $data_vencimento = date('Y-m-d H:i:s', strtotime('+1 month'));
    $stmt = $pdo->prepare("INSERT INTO assinaturas (usuario_id, plano_id, data_vencimento) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $plano_id, $data_vencimento]);
    $assinatura_id = $pdo->lastInsertId();

    $_SESSION['assinatura_id'] = $assinatura_id;
    $_SESSION['plano_id'] = $plano_id;
    $_SESSION['valor'] = $plano['preco'];
} else {
    $_SESSION['error'] = "Requisição inválida.";
    redirect('/planos.php');
}

require_once 'includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-credit-card me-2"></i> Finalizar Pagamento</h4>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5>Resumo do Pedido</h5>
                        <p>Plano: <strong><?= htmlspecialchars($plano['nome']) ?></strong></p>
                        <p>Valor: <strong>R$ <?= number_format($plano['preco'], 2, ',', '.') ?> mensais</strong></p>
                    </div>

                    <form id="paymentForm" action="process_payment.php" method="post">
                        <input type="hidden" name="assinatura_id" value="<?= $assinatura_id ?>">
                        <input type="hidden" name="valor" value="<?= $plano['preco'] ?>">

                        <h5 class="mb-3">Método de Pagamento</h5>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="metodo" id="cartao" value="cartao" checked>
                                <label class="form-check-label" for="cartao">
                                    <i class="bi bi-credit-card me-2"></i> Cartão de Crédito
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="metodo" id="pix" value="pix">
                                <label class="form-check-label" for="pix">
                                    <i class="bi bi-upc-scan me-2"></i> Pix
                                </label>
                            </div>
                        </div>

                        <div id="cartaoFields">
                            <div class="mb-3">
                                <label for="numeroCartao" class="form-label">Número do Cartão</label>
                                <input type="text" class="form-control" id="numeroCartao" placeholder="1234 5678 9012 3456">
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="validade" class="form-label">Validade</label>
                                    <input type="text" class="form-control" id="validade" placeholder="MM/AA">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="cvv" class="form-label">CVV</label>
                                    <input type="text" class="form-control" id="cvv" placeholder="123">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="nomeTitular" class="form-label">Nome do Titular</label>
                                <input type="text" class="form-control" id="nomeTitular" placeholder="Como no cartão">
                            </div>
                        </div>

                        <div id="pixFields" style="display: none;">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle-fill me-2"></i> Após confirmar, você será redirecionado para gerar o QR Code Pix.
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <span id="buttonText">Confirmar Pagamento</span>
                                <div id="spinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cartaoRadio = document.getElementById('cartao');
        const pixRadio = document.getElementById('pix');
        const cartaoFields = document.getElementById('cartaoFields');
        const pixFields = document.getElementById('pixFields');

        cartaoRadio.addEventListener('change', function() {
            if (this.checked) {
                cartaoFields.style.display = 'block';
                pixFields.style.display = 'none';
            }
        });

        pixRadio.addEventListener('change', function() {
            if (this.checked) {
                cartaoFields.style.display = 'none';
                pixFields.style.display = 'block';
            }
        });

        const paymentForm = document.getElementById('paymentForm');
        const submitBtn = document.getElementById('submitBtn');
        const buttonText = document.getElementById('buttonText');
        const spinner = document.getElementById('spinner');

        paymentForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Mostrar spinner e desativar botão
            buttonText.textContent = 'Processando pagamento...';
            spinner.classList.remove('d-none');
            submitBtn.disabled = true;

            // Simular processamento do pagamento (7 segundos)
            setTimeout(function() {

                window.location.href = '/G-tech/user/dashboard.php?payment=success';
            }, 7000);
        });
    });
</script>

<?php require_once 'includes/footer.php'; ?>