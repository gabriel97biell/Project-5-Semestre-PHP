<?php
session_start();
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Requisição inválida.";
    header('Location: /G-tech/planos.php');
    exit;
}

$assinatura_id = $_POST['assinatura_id'] ?? null;
$valor = $_POST['valor'] ?? null;
$metodo = $_POST['metodo'] ?? null;

if (!$assinatura_id || !$valor || !$metodo) {
    $_SESSION['error'] = "Dados incompletos para processar o pagamento.";
    header('Location: /G-tech/planos.php');
    exit;
}

$pdo->beginTransaction();

try {
    // Registrar o pagamento
    $stmt = $pdo->prepare("INSERT INTO pagamentos 
                          (assinatura_id, valor, metodo, status, data_pagamento) 
                          VALUES (?, ?, ?, 'completo', NOW())");
    $stmt->execute([
        $assinatura_id,
        $valor,
        $metodo
    ]);

    // Atualizar assinatura
    $vencimento = date('Y-m-d H:i:s', strtotime('+1 month'));
    $stmt = $pdo->prepare("UPDATE assinaturas 
                          SET status = 'ativo', data_vencimento = ? 
                          WHERE id = ?");
    $stmt->execute([$vencimento, $assinatura_id]);

    $pdo->commit();

    $_SESSION['success'] = "Pagamento realizado com sucesso!";
    header('Location: /G-tech/user/dashboard.php?payment=success');
    exit;

} catch (PDOException $e) {
    $pdo->rollBack();
    $_SESSION['error'] = "Erro ao registrar pagamento: " . $e->getMessage();
    header('Location: /G-tech/user/dashboard.php?payment=error');
    exit;
}
