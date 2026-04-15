<?php
require_once '../includes/config.php';

if (!isLoggedIn()) {
    redirect('/login.php');
}

// Buscar assinatura ativa do usuário
$stmt = $pdo->prepare("SELECT p.*, a.id as assinatura_id, a.plano_id, a.data_inicio, a.data_vencimento, a.status 
                       FROM assinaturas a 
                       JOIN planos p ON a.plano_id = p.id 
                       WHERE a.usuario_id = ? 
                       ORDER BY a.data_inicio DESC LIMIT 1");
$stmt->execute([$_SESSION['user_id']]);
$planoAtivo = $stmt->fetch();

// Processar cancelamento de plano
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancelar_plano'])) {
    if ($planoAtivo) {
        try {
            $stmt = $pdo->prepare("UPDATE assinaturas SET status = 'cancelado' WHERE id = ?");
            $stmt->execute([$planoAtivo['assinatura_id']]);

            $_SESSION['success'] = "Plano cancelado com sucesso!";
            redirect('/user/dashboard.php');
        } catch (PDOException $e) {
            $error = "Erro ao cancelar plano: " . $e->getMessage();
        }
    }
}

// Buscar todos os planos disponíveis (exceto o atual se houver)
$sqlPlanos = "SELECT * FROM planos";
if ($planoAtivo && $planoAtivo['status'] === 'ativo') {
    $sqlPlanos .= " WHERE id != ?";
    $stmt = $pdo->prepare($sqlPlanos);
    $stmt->execute([$planoAtivo['plano_id']]);
} else {

    $stmt = $pdo->query($sqlPlanos);
}
$planos = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/header.php';
?>

<div class="container my-5">
    <?php if (isset($_GET['payment']) && $_GET['payment'] === 'success'): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill me-2"></i> Pagamento realizado com sucesso! Sua assinatura está ativa.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <h2 class="mb-4"><i class="bi bi-speedometer2 me-2"></i> Painel do Usuário</h2>

    <?php if ($planoAtivo && $planoAtivo['status'] === 'ativo'): ?>
        <div class="card mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Seu Plano Ativo</h4>
                <form method="POST" onsubmit="return confirm('Tem certeza que deseja cancelar este plano?');">
                    <button type="submit" name="cancelar_plano" class="btn btn-sm btn-danger">
                        <i class="bi bi-x-circle"></i> Cancelar Plano
                    </button>
                </form>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h3><?= htmlspecialchars($planoAtivo['nome']) ?></h3>
                        <p class="text-muted">Assinado em: <?= date('d/m/Y', strtotime($planoAtivo['data_inicio'])) ?></p>
                        <p class="text-muted">Vencimento: <?= date('d/m/Y', strtotime($planoAtivo['data_vencimento'])) ?></p>
                        <h5>R$ <?= number_format($planoAtivo['preco'], 2, ',', '.') ?> <small class="text-muted fw-light">/mês</small></h5>
                    </div>
                    <div class="col-md-6">
                        <h5>Uso do Plano</h5>
                        <div class="progress mb-3" style="height: 30px;">
                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: 3%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">1%</div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted">Armazenamento: 100MB de <?= $planoAtivo['armazenamento'] ?></small>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Banda: 100MB de <?= $planoAtivo['largura_banda'] ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php elseif ($planoAtivo && $planoAtivo['status'] === 'cancelado'): ?>
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> Seu plano foi cancelado e permanecerá ativo até <?= date('d/m/Y', strtotime($planoAtivo['data_vencimento'])) ?>.
        </div>
    <?php else: ?>
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> Você não possui um plano ativo no momento.
        </div>
    <?php endif; ?>

    <?php if (!$planoAtivo || $planoAtivo['status'] !== 'ativo'): ?>
        <h4 class="mb-3">Planos Disponíveis</h4>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($planos as $plano): ?>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="my-1 text-center"><?= htmlspecialchars($plano['nome']) ?></h5>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title pricing-card-title text-center">R$ <?= number_format($plano['preco'], 2, ',', '.') ?><small class="text-muted fw-light">/mês</small></h3>
                            <ul class="list-unstyled mt-3 mb-4">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> <?= $plano['armazenamento'] ?> SSD</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> <?= $plano['largura_banda'] ?> de banda</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> <?= $plano['sites'] ?> site(s)</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> <?= $plano['contas_email'] ?> contas de e-mail</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-transparent">
                            <a href="/G-tech/planos.php" class="btn btn-outline-primary w-100">Assinar Plano</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</div>

<?php require_once '../includes/footer.php'; ?>