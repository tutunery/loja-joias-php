<?php
session_start();
require_once 'config.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    
    if (empty($email) || empty($senha)) {
        // Redireciona para o login com erro
        header("Location: login.php?erro=campos_vazios");
        exit();
    }

    try {
        $sql = "SELECT id_usuario, nome, senha FROM usuarios WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_OBJ);

        if ($usuario && password_verify($senha, $usuario->senha)) {
            // LOGIN BEM-SUCEDIDO: Configura a sessão
            $_SESSION['usuario_id'] = $usuario->id_usuario;
            $_SESSION['usuario_nome'] = $usuario->nome;
            
            header("Location: admin.php"); // Redireciona para a página principal
            exit();
        } else {
            // LOGIN FALHOU
            header("Location: login.php?erro=credenciais_invalidas");
            exit();
        }
    } catch (PDOException $e) {
        header("Location: login.php?erro=db_falha");
        exit();
    }
}
?>