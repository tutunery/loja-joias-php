<?php
// GARANTIR QUE ESTA É A PRIMEIRA LINHA DO ARQUIVO.
require_once 'config.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Coleta dos dados
    $nome  = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $cpf   = trim($_POST['cpf'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $cep = trim($_POST['cep'] ?? '');
    $logradouro = trim($_POST['logradouro'] ?? '');
    $numero = trim($_POST['numero'] ?? '');
    $bairro = trim($_POST['bairro'] ?? '');
    $cidade = trim($_POST['cidade'] ?? '');
    $uf = trim($_POST['uf'] ?? '');
    
    // Mensagem de log para debug - REMOVA APÓS CORREÇÃO
    error_log("Formulário de Registro Recebido. Email: " . $email); 

    // 1. Limpeza e Validação de Formato (RegEx no PHP)
    $cpf_limpo = preg_replace('/\D/', '', $cpf); 
    $telefone_limpo = preg_replace('/\D/', '', $telefone);
    $cep_limpo = preg_replace('/\D/', '', $cep);

    // Expressões Regulares de Verificação
    $regex_cpf = '/^\d{11}$/'; 
    $regex_tel = '/^\d{10,11}$/'; 

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: registro.php?status=erro&msg=" . urlencode("Formato de e-mail inválido."));
        exit();
    }
    if (!preg_match($regex_cpf, $cpf_limpo)) {
        header("Location: registro.php?status=erro&msg=" . urlencode("CPF inválido (deve ter 11 dígitos)."));
        exit();
    }
    if (!preg_match($regex_tel, $telefone_limpo)) {
        header("Location: registro.php?status=erro&msg=" . urlencode("Telefone inválido (10 ou 11 dígitos c/ DDD)."));
        exit();
    }
    
    // Validação de Endereço Básico (para evitar inserção de endereço vazio)
    if (empty($logradouro) || empty($numero) || empty($cidade)) {
        header("Location: registro.php?status=erro&msg=" . urlencode("Por favor, preencha o endereço (e certifique-se que o CEP foi preenchido)."));
        exit();
    }

    // 2. Criptografia da Senha (Segurança!)
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    
    // Adicionamos 'nivel_acesso' na inserção, definindo como 'cliente' por padrão.
    $nivel_acesso = 'cliente'; 
    
    // 3. Inserção no Banco de Dados
    try {
        $sql = "INSERT INTO usuarios (nome, email, senha, cpf, telefone, cep, logradouro, numero, bairro, cidade, uf, nivel_acesso) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute([
            $nome, $email, $senha_hash, $cpf_limpo, $telefone_limpo, $cep_limpo, 
            $logradouro, $numero, $bairro, $cidade, $uf, $nivel_acesso
        ]);
        
        // SUCESSO: Redireciona para o login
        header("Location: login.php?status=sucesso_registro");
        exit();

    } catch (PDOException $e) {
        // Verifica erro de duplicidade (Email ou CPF já cadastrados)
        if ($e->getCode() == 23000) { 
            $msg = "Email ou CPF já cadastrado. Tente outro.";
        } else {
            $msg = "Erro ao registrar usuário. Por favor, tente novamente.";
            // Mensagem detalhada para DEBUG: $msg = "Erro no DB: " . $e->getMessage();
        }
        header("Location: registro.php?status=erro&msg=" . urlencode($msg));
        exit();
    }
} else {
    // Acesso direto à página sem POST
    header("Location: registro.php");
    exit();
}
// 🛑 IMPORTANTE: NÃO DEIXE NENHUM ESPAÇO OU LINHA EM BRANCO APÓS ESTA TAG DE FECHAMENTO.
?>