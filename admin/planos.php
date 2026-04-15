<?php
require_once '../includes/config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('/login.php');
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$stmt = $pdo->query("SELECT * FROM planos ORDER BY preco");
$planos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Adicionar novo plano
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_plano'])) {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];
    $armazenamento = $_POST['armazenamento'];
    $largura_banda = $_POST['largura_banda'];
    $sites = $_POST['sites'];
    $contas_email = $_POST['contas_email'];
    $banco_dados = $_POST['banco_dados'];
    $dominio_gratis = isset($_POST['dominio_gratis']) ? 1 : 0;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO planos (nome, descricao, preco, armazenamento, largura_banda, sites, contas_email, banco_dados, dominio_gratis) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $descricao, $preco, $armazenamento, $largura_banda, $sites, $contas_email, $banco_dados, $dominio_gratis]);
        
        $_SESSION['success'] = "Plano adicionado com sucesso!";
        redirect('/admin/planos.php');
    } catch (PDOException $e) {
        $error = "Erro ao adicionar plano: " . $e->getMessage();
    }
}


if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    try {
        // Verificar se há assinaturas ativas para este plano
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM assinaturas WHERE plano_id = ? AND status = 'ativo'");
        $stmt->execute([$id]);
        $assinaturas = $stmt->fetch()['total'];
        
       
            $stmt = $pdo->prepare("DELETE FROM planos WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['success'] = "Plano deletado com sucesso!";
       
        
        redirect('/admin/planos.php');
    } catch (PDOException $e) {
        $error = "Erro ao deletar plano: " . $e->getMessage();
    }
}

require_once '../includes/header.php';
?>

<div class="container my-5">
    <h2 class="mb-4"><i class="bi bi-collection me-2"></i> Gerenciar Planos</h2>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php elseif (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i> Adicionar Novo Plano</h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nome" class="form-label">Nome do Plano</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="preco" class="form-label">Preço (R$)</label>
                        <input type="number" step="0.01" class="form-control" id="preco" name="preco" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="descricao" class="form-label">Descrição</label>
                    <textarea class="form-control" id="descricao" name="descricao" rows="2"></textarea>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="armazenamento" class="form-label">Armazenamento</label>
                        <input type="text" class
                        <input type="text" class="form-control" id="armazenamento" name="armazenamento" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="largura_banda" class="form-label">Largura de Banda</label>
                        <input type="text" class="form-control" id="largura_banda" name="largura_banda" required>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="sites" class="form-label">Sites</label>
                        <input type="number" class="form-control" id="sites" name="sites" required>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="contas_email" class="form-label">Contas de Email</label>
                        <input type="number" class="form-control" id="contas_email" name="contas_email" required>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="banco_dados" class="form-label">Bancos de Dados</label>
                        <input type="number" class="form-control" id="banco_dados" name="banco_dados" required>
                    </div>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="dominio_gratis" name="dominio_gratis">
                    <label class="form-check-label" for="dominio_gratis">
                        Domínio grátis incluso
                    </label>
                </div>
                <button type="submit" name="add_plano" class="btn btn-success"><i class="bi bi-save me-2"></i>Salvar Plano</button>
            </form>
        </div>
    </div>

    <!-- Lista de Planos -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-table me-2"></i> Planos Cadastrados</h5>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Preço</th>
                        <th>Armazenamento</th>
                        <th>Banda</th>
                        <th>Sites</th>
                        <th>Emails</th>
                        <th>DBs</th>
                        <th>Domínio Grátis</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($planos as $plano): ?>
                        <tr>
                            <td><?= htmlspecialchars($plano['nome']) ?></td>
                            <td>R$ <?= number_format($plano['preco'], 2, ',', '.') ?></td>
                            <td><?= htmlspecialchars($plano['armazenamento']) ?></td>
                            <td><?= htmlspecialchars($plano['largura_banda']) ?></td>
                            <td><?= (int)$plano['sites'] ?></td>
                            <td><?= (int)$plano['contas_email'] ?></td>
                            <td><?= (int)$plano['banco_dados'] ?></td>
                            <td><?= $plano['dominio_gratis'] ? 'Sim' : 'Não' ?></td>
                            <td>
                                <a href="editar_plano.php?id=<?= $plano['id'] ?>" class="btn btn-sm btn-primary"><i class="bi bi-pencil-square"></i></a>
                                <a href="?delete=<?= $plano['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este plano?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($planos)): ?>
                        <tr>
                            <td colspan="9" class="text-center">Nenhum plano cadastrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</div>

<?php require_once '../includes/footer.php'; ?>
