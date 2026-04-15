<?php
require_once '../includes/config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('/login.php');
}


$stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
$totalUsuarios = $stmt->fetch()['total'];


$stmt = $pdo->query("SELECT COUNT(*) as total FROM assinaturas WHERE status = 'ativo'");
$totalAssinaturas = $stmt->fetch()['total'];


$stmt = $pdo->query("SELECT COUNT(*) as total FROM planos");
$totalPlanos = $stmt->fetch()['total'];


$stmt = $pdo->query("
    SELECT 
        p.id,
        p.valor,
        p.metodo,
        p.data_pagamento,
        p.status,
        u.nome AS usuario_nome,
        pl.nome AS plano_nome
    FROM pagamentos p
    JOIN assinaturas a ON p.assinatura_id = a.id
    JOIN usuarios u ON a.usuario_id = u.id
    JOIN planos pl ON a.plano_id = pl.id
    ORDER BY p.data_pagamento DESC 
    LIMIT 5
");

$ultimosPagamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/header.php';
?>

<div class="container my-5">
    <h2 class="mb-4"><i class="bi bi-speedometer2 me-2"></i> Painel Administrativo</h2>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Usuários</h5>
                            <h2 class="mb-0"><?= $totalUsuarios ?></h2>
                        </div>
                        <i class="bi bi-people-fill fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Assinaturas Ativas</h5>
                            <h2 class="mb-0"><?= $totalAssinaturas ?></h2>
                        </div>
                        <i class="bi bi-check-circle-fill fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Planos</h5>
                            <h2 class="mb-0"><?= $totalPlanos ?></h2>
                        </div>
                        <i class="bi bi-collection-fill fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i> Últimos Pagamentos</h5>
                </div>
                <div class="card-body">
                    <?php if (count($ultimosPagamentos) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Usuário</th>
                                        <th>Valor</th>
                                        <th>Método</th>
                                        <th>Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($ultimosPagamentos as $pagamento): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($pagamento['usuario_nome']) ?></td>
                                            <td>R$ <?= number_format($pagamento['valor'], 2, ',', '.') ?></td>
                                            <td><?= ucfirst($pagamento['metodo']) ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($pagamento['data_pagamento'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle-fill me-2"></i> Nenhum pagamento registrado recentemente.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-lightning-charge-fill me-2"></i> Ações Rápidas</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?= BASE_URL ?>/admin/planos.php" class="btn btn-primary">
                            <i class="bi bi-plus-circle-fill me-2"></i> Gerenciar Planos
                        </a>

                        <a href="<?= BASE_URL ?>/admin/usuarios.php" class="btn btn-secondary">
                            <i class="bi bi-people-fill me-2"></i> Gerenciar Usuários
                        </a>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</div>

<?php require_once '../includes/footer.php'; ?>