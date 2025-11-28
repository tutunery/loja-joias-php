<?php
session_start();

// 1. Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php"); // Leva para a tela de login
    exit();
}

// 2. Verifica se o usuário tem permissão de administrador
if ($_SESSION['nivel_acesso'] !== 'admin') {
    // Se não for admin, você pode:
    
    // Opção A: Redirecionar para a página inicial
    header("Location: ../index.php"); 
    
    // Opção B: Mostrar uma mensagem de erro
    // die("Você não tem permissão para acessar esta área."); 
    
    exit();
}

// Se chegou até aqui, o usuário é um ADMIN e pode ver o conteúdo abaixo!
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel de Administração - Loja de Joias</title>
</head>
<body>
    <h1>Bem-vindo ao Painel de Administração!</h1>
    <p>Olá, <?= htmlspecialchars($_SESSION['usuario_nome']) ?>. Aqui você gerencia produtos, pedidos e clientes.</p>
    
    <a href="../logout.php">Sair</a>
</body>
</html>