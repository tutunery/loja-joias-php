<?php
// 1. INICIA A SESSÃO
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. CONEXÃO COM O BANCO (Necessária para o menu do usuário funcionar)
if (file_exists('config.php')) {
    require_once 'config.php';
} elseif (file_exists('conexao.php')) {
    require_once 'conexao.php';
}
?>

<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lumière Joias</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">

    <style>
        /* --- ESTILOS GERAIS --- */
        body { font-family: 'Lato', sans-serif; transition: background-color 0.3s, color 0.3s; }
        .navbar-brand { font-family: 'Playfair Display', serif; font-weight: 700; letter-spacing: 1px; font-size: 1.8rem; color: #d4af37 !important; }
        
        /* Links do Menu */
        .nav-link { font-weight: 500; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px; margin: 0 10px; }
        .nav-link:hover { color: #d4af37 !important; }

        /* Ícones do Topo */
        .header-actions a { color: inherit; margin-left: 15px; transition: 0.3s; cursor: pointer; text-decoration: none; font-size: 1.1rem; }
        .header-actions a:hover { color: #d4af37; }

        /* --- MENU DO USUÁRIO (DROPDOWN) --- */
        .user-toggle { font-size: 0.9rem; font-weight: bold; display: flex; align-items: center; gap: 8px; }
        .dropdown-menu { border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-radius: 8px; margin-top: 10px; padding: 0; overflow: hidden; }
        .dropdown-header-user { background-color: var(--bs-tertiary-bg); padding: 15px; border-bottom: 1px solid var(--bs-border-color); }
        .dropdown-item { padding: 10px 20px; font-size: 0.9rem; }
        .dropdown-item:hover { background-color: var(--bs-secondary-bg); color: #d4af37; }
        .dropdown-item i { width: 20px; text-align: center; margin-right: 10px; }

        /* Botão Dark Mode */
        .btn-theme { border: none; background: transparent; color: inherit; cursor: pointer; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg border-bottom sticky-top bg-body-tertiary shadow-sm">
  <div class="container">
    
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>

    <a class="navbar-brand mx-auto mx-lg-0" href="index.php">Lumière.</a>

    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav mx-auto text-center mt-3 mt-lg-0">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Anéis</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Colares</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Sobre</a></li>
      </ul>
    </div>

    <div class="header-actions d-flex align-items-center position-absolute end-0 me-3 position-lg-static">
        
        <button class="btn-theme me-2" id="btnTheme" onclick="toggleTheme()" title="Mudar Tema">
            <i class="fa-solid fa-moon"></i>
        </button>

        <a href="#" class="d-none d-md-block" title="Buscar"><i class="fa-solid fa-magnifying-glass"></i></a>
        <a href="carrinho.php" class="position-relative me-3" title="Sacola"><i class="fa-solid fa-bag-shopping"></i></a>

        <?php if(isset($_SESSION['usuario_nome'])): ?>
            
            <div class="dropdown">
                <a href="#" class="user-toggle dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-regular fa-user"></i>
                    <span class="d-none d-md-inline">
                        <?php echo strtok($_SESSION['usuario_nome'], " "); ?>
                    </span>
                </a>

                <ul class="dropdown-menu dropdown-menu-end">
                    <li class="dropdown-header-user">
                        <small class="text-muted d-block">Usuário</small>
                        <strong><?php echo $_SESSION['usuario_nome']; ?></strong>
                    </li>

                    <?php if(isset($_SESSION['nivel_acesso']) && $_SESSION['nivel_acesso'] == 'admin'): ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><h6 class="dropdown-header text-uppercase text-warning" style="font-size: 0.7rem;">Gerenciamento</h6></li>
                        <li><a class="dropdown-item" href="admin.php"><i class="fa-solid fa-pen-to-square"></i> Painel Produtos</a></li>
                        <li><a class="dropdown-item" href="estatistica.php"><i class="fa-solid fa-chart-line"></i> Relatórios & Gráficos</a></li>
                        <li><hr class="dropdown-divider"></li>
                    <?php endif; ?>

                    <li><a class="dropdown-item" href="perfil.php"><i class="fa-regular fa-id-card"></i> Meu Perfil</a></li>
                    <li><a class="dropdown-item" href="pedidos.php"><i class="fa-solid fa-box-open"></i> Meus Pedidos</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Sair</a></li>
                </ul>
            </div>

        <?php else: ?>
            <a href="login.php" class="user-toggle"><i class="fa-regular fa-user"></i> <span class="d-none d-md-inline ms-2">Entrar</span></a>
        <?php endif; ?>

    </div>
  </div>
</nav>

<script>
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-bs-theme', savedTheme);
    updateIcon(savedTheme);

    function toggleTheme() {
        const current = document.documentElement.getAttribute('data-bs-theme');
        const newTheme = current === 'light' ? 'dark' : 'light';
        document.documentElement.setAttribute('data-bs-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateIcon(newTheme);
    }

    function updateIcon(theme) {
        const btn = document.getElementById('btnTheme');
        if(btn) btn.innerHTML = theme === 'dark' ? '<i class="fa-solid fa-sun"></i>' : '<i class="fa-solid fa-moon"></i>';
    }
</script>