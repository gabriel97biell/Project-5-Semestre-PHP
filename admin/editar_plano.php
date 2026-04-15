<?php
require_once '../includes/config.php';
require_once '../includes/header.php';

// Verificar se o usuário está logado e é admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('/G-tech/login.php');
}

// Exibição de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar se o ID do plano foi passado na URL
if (isset($_GET['id'])) {
    $plano_id = $_GET['id'];

    // Buscar o plano no banco de dados
    $stmt = $pdo->prepare("SELECT * FROM planos WHERE id = ?");
    $stmt->execute([$plano_id]);
    $plano = $stmt->fetch(PDO::FETCH_ASSOC);

    // Caso o plano não seja encontrado
    if (!$plano) {
        $_SESSION['error'] = "Plano não encontrado.";
        redirect('/admin/planos.php');
    }
} else {
    // Caso o ID não seja passado na URL
    $_SESSION['error'] = "Plano não especificado.";
    redirect('/admin/planos.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = [
        'nome' => $_POST['nome'],
        'descricao' => $_POST['descricao'],
        'preco' => (float)$_POST['preco'],
        'armazenamento' => $_POST['armazenamento'],
        'largura_banda' => $_POST['largura_banda'],
        'sites' => (int)$_POST['sites'],
        'contas_email' => (int)$_POST['contas_email'],
        'banco_dados' => (int)$_POST['banco_dados'],
        'dominio_gratis' => isset($_POST['dominio_gratis']) ? 1 : 0,
        'id' => $plano_id
    ];

    try {
        // Atualizar plano no banco de dados
        $stmt = $pdo->prepare("UPDATE planos SET 
                              nome = :nome,
                              descricao = :descricao,
                              preco = :preco,
                              armazenamento = :armazenamento,
                              largura_banda = :largura_banda,
                              sites = :sites,
                              contas_email = :contas_email,
                              banco_dados = :banco_dados,
                              dominio_gratis = :dominio_gratis
                              WHERE id = :id");

        $stmt->execute($dados);

        // Mensagem de sucesso e redirecionamento
        $_SESSION['success'] = "Plano atualizado com sucesso!";
        redirect('/admin/planos.php');
    } catch (PDOException $e) {
        $error = "Erro ao atualizar plano: " . $e->getMessage();
    }
}
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h2><i class="bi bi-pencil-square"></i> Editar Plano</h2>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <!-- Formulário de edição do plano -->
            <form method="POST">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        Informações Básicas
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nome" class="form-label">Nome do Plano</label>
                                    <input type="text" class="form-control" id="nome" name="nome"
                                        value="<?= htmlspecialchars($plano['nome']) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="preco" class="form-label">Preço (R$)</label>
                                    <input type="number" step="0.01" class="form-control" id="preco" name="preco"
                                        value="<?= $plano['preco'] ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="3"><?= htmlspecialchars($plano['descricao']) ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header bg-primary text-white">
                        Recursos do Plano
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="armazenamento" class="form-label">Armazenamento</label>
                                    <input type="text" class="form-control" id="armazenamento" name="armazenamento"
                                        value="<?= $plano['armazenamento'] ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="largura_banda" class="form-label">Largura de Banda</label>
                                    <input type="text" class="form-control" id="largura_banda" name="largura_banda"
                                        value="<?= $plano['largura_banda'] ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="sites" class="form-label">Sites Permitidos</label>
                                    <input type="number" class="form-control" id="sites" name="sites"
                                        value="<?= $plano['sites'] ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contas_email" class="form-label">Contas de E-mail</label>
                                    <input type="number" class="form-control" id="contas_email" name="contas_email"
                                        value="<?= $plano['contas_email'] ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="banco_dados" class="form-label">Bancos de Dados</label>
                                    <input type="number" class="form-control" id="banco_dados" name="banco_dados"
                                        value="<?= $plano['banco_dados'] ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="dominio_gratis" name="dominio_gratis"
                                <?= $plano['dominio_gratis'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="dominio_gratis">
                                Domínio Grátis Incluso
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Salvar Alterações
                    </button>
                    <a href="/G-tech/admin/planos.php" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</div>

<?php require_once '../includes/footer.php'; ?>