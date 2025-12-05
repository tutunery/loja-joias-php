<?php
// login.php
session_start();

// Redirecionamento se já estiver logado
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
    // Bloco Try-Catch para isolar falhas no config.php (conexão DB)
    try {
        require_once 'config.php';
    } catch (PDOException $e) {
        $erro = "FALHA CRÍTICA: Erro na Conexão com o Banco de Dados. Verifique o XAMPP.";
        $pdo = null;
    }
} elseif (file_exists('conexao.php')) {
    // Opção de fallback (mantida do seu código)
    require_once 'conexao.php';
} else {
    $erro = "FALHA CRÍTICA: Arquivo de conexão não encontrado.";
    $pdo = null;
}

$email = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && $pdo !== null) {
    
    // CORREÇÃO: Garante que o email seja limpo de espaços (essencial para consulta)
    $email = trim($_POST['email'] ?? ''); 
    $senha = $_POST['senha'] ?? '';
    
    // 🛑 DEBUG: Exibe dados recebidos (para confirmar que estamos usando 123456)
    echo "<div class='debug-msg'>DEBUG: Email digitado: [" . $email . "]<br>";
    echo "DEBUG: Senha digitada: [" . $senha . "]</div>";
    
    if (empty($email) || empty($senha)) {
        $erro = "Por favor, preencha todos os campos.";
    } else {
        try {
            // Busca o usuário
            $sql = "SELECT id_usuario, nome, senha, nivel_acesso FROM usuarios WHERE email = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$email]);
            $usuario = $stmt->fetch(PDO::FETCH_OBJ);

            
            // Verifica Senha APENAS se o usuário foi encontrado
            if ($usuario && password_verify($senha, $usuario->senha)) {
                
                // --- SUCESSO! ---
                echo "<div class='debug-msg'>DEBUG: Senha VERIFICADA com SUCESSO! Redirecionando...</div>";

                session_regenerate_id(true);

                // Salva dados na sessão
                $_SESSION['usuario_id'] = $usuario->id_usuario;
                $_SESSION['usuario_nome'] = $usuario->nome;
                $_SESSION['nivel_acesso'] = $usuario->nivel_acesso;

                // Redirecionamento inteligente
                if ($usuario->nivel_acesso === 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: index.php");
                }
                exit();
                
            } else {
                 // Esta condição é atingida se o usuário não foi encontrado OU se a senha falhou
                $erro = "Email ou senha incorretos.";
                
                if (!$usuario) {
                     echo "<div class='debug-msg'>DEBUG: Falha na consulta SQL. Usuário NÃO encontrado no BD.</div>";
                } else {
                     echo "<div class='debug-msg'>DEBUG: Usuário encontrado, mas senha falhou na verificação (password_verify).</div>";
                }
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
        /* CSS Básico para o Formulário de Login */
        body { background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; height: 100vh; font-family: 'Lato', sans-serif; }
        .login-card { width: 100%; max-width: 400px; padding: 30px; border-radius: 10px; background: white; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .btn-gold { background-color: #d4af37; color: white; border: none; font-weight: bold; }
        .btn-gold:hover { background-color: #bfa34b; color: white; }
        .text-gold { color: #d4af37; }
        .text-gold:hover { color: #bfa34b; text-decoration: underline !important; }
        h3 { font-family: 'Playfair Display', serif; color: #333; }
        
        /* Estilo para as mensagens de DEBUG */
        .debug-msg { 
            background-color: #fff3cd; 
            border: 1px solid #ffeeba; 
            color: #856404; 
            padding: 10px; 
            margin-bottom: 15px; 
            border-radius: 5px;
            text-align: left;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="text-center mb-4">
        <h3>Lumière<span style="color:#d4af37">.</span></h3>
        <p class="text-muted">Acesse sua conta</p>
    </div>

    <!-- Exibe as mensagens de DEBUG na área de alertas -->
    
    <?php if (!empty($erro)): ?>
        <!-- O erro de conexão crítica ou de login falho aparecerá aqui -->
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