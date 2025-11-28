<?php
// login.php
session_start();

// Se já estiver logado, redireciona para a home ou admin dependendo do nível
if (isset($_SESSION['nivel_acesso'])) {
    if ($_SESSION['nivel_acesso'] === 'admin') {
        header("Location: admin.php");
    } else {
        header("Location: index.php");
    }
    exit();
}

// Tenta incluir a conexão
if (file_exists('config.php')) {
    require_once 'config.php';
} elseif (file_exists('conexao.php')) {
    require_once 'conexao.php';
} else {
    die("Erro: Arquivo de conexão não encontrado.");
}

$erro = '';
$email = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    
    if (empty($email) || empty($senha)) {
        $erro = "Por favor, preencha todos os campos.";
    } else {
        try {
            // Busca o usuário
            $sql = "SELECT id_usuario, nome, senha, nivel_acesso FROM usuarios WHERE email = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$email]);
            $usuario = $stmt->fetch(PDO::FETCH_OBJ);

            // Verifica Senha
            if ($usuario && password_verify($senha, $usuario->senha)) {
                
                // --- SUCESSO! ---
                session_regenerate_id(true);

                // Salva dados na sessão
                $_SESSION['usuario_id'] = $usuario->id_usuario;
                $_SESSION['usuario_nome'] = $usuario->nome;
                $_SESSION['nivel_acesso'] = $usuario->nivel_acesso;

                // --- REDIRECIONAMENTO INTELIGENTE ---
                if ($usuario->nivel_acesso === 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: index.php");
                }
                exit();
                
            } else {
                $erro = "Email ou senha incorretos.";
            }
        } catch (PDOException $e) {
            $erro = "Erro no sistema: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Lumière Joias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; height: 100vh; font-family: 'Lato', sans-serif; }
        .login-card { width: 100%; max-width: 400px; padding: 30px; border-radius: 10px; background: white; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        
        /* Botão Principal Dourado */
        .btn-gold { background-color: #d4af37; color: white; border: none; font-weight: bold; }
        .btn-gold:hover { background-color: #bfa34b; color: white; }
        
        /* Link Dourado para o Registro */
        .text-gold { color: #d4af37; }
        .text-gold:hover { color: #bfa34b; text-decoration: underline !important; }

        h3 { font-family: 'Playfair Display', serif; color: #333; }
    </style>
</head>
<body>

<div class="login-card">
    <div class="text-center mb-4">
        <h3>Lumière<span style="color:#d4af37">.</span></h3>
        <p class="text-muted">Acesse sua conta</p>
    </div>

    <?php if (!empty($erro)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Senha</label>
            <input type="password" name="senha" class="form-control" required>
        </div>
        
        <button type="submit" class="btn btn-gold w-100 py-2 mb-3">Entrar</button>
    </form>

    <div class="text-center border-top pt-3">
        <p class="small text-muted mb-1">Ainda não tem uma conta?</p>
        <a href="registro.php" class="text-gold text-decoration-none fw-bold">
            Criar minha conta
        </a>
    </div>

    <div class="text-center mt-3">
        <a href="header.php" class="text-decoration-none text-muted small">Voltar para a Loja</a>
    </div>
</div>

</body>
</html>