<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/header.php';


if (!isLoggedIn() || !isAdmin()) {
    $_SESSION['error'] = "Acesso não autorizado.";
    redirect('/login.php');
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $user_id = (int)$_POST['user_id'];
    
    try {
        switch ($_POST['action']) {
            case 'toggle_admin':
                $stmt = $pdo->prepare("UPDATE usuarios SET is_admin = NOT is_admin WHERE id = ?");
                $stmt->execute([$user_id]);
                $_SESSION['success'] = "Privilégios de administrador atualizados!";
                break;
                
            case 'delete':
               
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM assinaturas WHERE usuario_id = ? AND status = 'ativo'");
                $stmt->execute([$user_id]);
                $assinaturas_ativas = $stmt->fetchColumn();
                
                if ($assinaturas_ativas > 0) {
                    $_SESSION['error'] = "Não é possível excluir usuário com assinaturas ativas.";
                } else {
                    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
                    $stmt->execute([$user_id]);
                    $_SESSION['success'] = "Usuário excluído com sucesso!";
                }
                break;
        }
        
        
        redirect('/admin/usuarios.php');
        
    } catch (PDOException $e) {
        $error = "Erro ao processar ação: " . $e->getMessage();
    }
}

// Buscar todos os usuários (exceto o atual para evitar auto-remoção de admin)
$current_user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT id, nome, email, is_admin, data_cadastro FROM usuarios WHERE id != ? ORDER BY data_cadastro DESC");
$stmt->execute([$current_user_id]);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-people-fill"></i> Gerenciamento de Usuários</h2>
                <a href="adicionar-usuario.php" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Adicionar Usuário
                </a>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= $_SESSION['success'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= $_SESSION['error'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Tipo</th>
                                    <th>Data Cadastro</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($usuarios) > 0): ?>
                                    <?php foreach ($usuarios as $usuario): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($usuario['nome']) ?></td>
                                            <td><?= htmlspecialchars($usuario['email']) ?></td>
                                            <td>
                                                <?php if ($usuario['is_admin']): ?>
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-shield-check"></i> Administrador
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-primary">
                                                        <i class="bi bi-person"></i> Usuário
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('d/m/Y H:i', strtotime($usuario['data_cadastro'])) ?></td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="user_id" value="<?= $usuario['id'] ?>">
                                                        <input type="hidden" name="action" value="toggle_admin">
                                                        <button type="submit" class="btn btn-sm <?= $usuario['is_admin'] ? 'btn-warning' : 'btn-success' ?>">
                                                            <i class="bi bi-shield-<?= $usuario['is_admin'] ? 'x' : 'check' ?>"></i>
                                                            <?= $usuario['is_admin'] ? 'Remover Admin' : 'Tornar Admin' ?>
                                                        </button>
                                                    </form>

                                                    <!--<a href="editar-usuario.php?id=<?= $usuario['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-pencil"></i> Editar
                                                    </a> rever com o pessoal se faz sentido o adm poder editar o usuario -->

                                                    <form method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');">
                                                        <input type="hidden" name="user_id" value="<?= $usuario['id'] ?>">
                                                        <input type="hidden" name="action" value="delete">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-trash"></i> Excluir
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <div class="alert alert-info mb-0">
                                                <i class="bi bi-info-circle"></i> Nenhum usuário cadastrado.
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</div>

<?php require_once __DIR__ . '/../includes/footer.php';?>