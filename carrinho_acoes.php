<?php
session_start();

// Verifica se existe o array do carrinho, se não, cria
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

$acao = $_GET['acao'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 1. ADICIONAR PRODUTO
if ($acao == 'add' && $id > 0) {
    if (isset($_SESSION['carrinho'][$id])) {
        $_SESSION['carrinho'][$id]++; // Se já existe, aumenta a quantidade
    } else {
        $_SESSION['carrinho'][$id] = 1; // Se não, adiciona 1
    }
    header("Location: carrinho.php"); // Vai para a tela do carrinho
    exit;
}

// 2. REMOVER PRODUTO
if ($acao == 'remover' && $id > 0) {
    if (isset($_SESSION['carrinho'][$id])) {
        unset($_SESSION['carrinho'][$id]);
    }
    header("Location: carrinho.php");
    exit;
}

// 3. LIMPAR CARRINHO
if ($acao == 'limpar') {
    unset($_SESSION['carrinho']);
    header("Location: carrinho.php");
    exit;
}

// Se nada der certo, volta pra home
header("Location: index.php");
exit;
?>