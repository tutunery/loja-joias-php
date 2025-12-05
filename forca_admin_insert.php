<?php
// Script de uso único para FORÇAR a criação do usuário ADMINISTRADOR
// e garantir que o HASH seja gerado corretamente pelo seu ambiente PHP.

require_once 'config.php'; // Inclui a conexão configurada

$email_admin = 'masterloja@gmail.com';
$senha_pura = '123456';
$cpf_admin = '00000000001';

// 1. GERAÇÃO DO HASH NO SEU AMBIENTE
$senha_hash = password_hash($senha_pura, PASSWORD_DEFAULT);

// Limpa registros de teste existentes para evitar conflitos
$pdo->exec("DELETE FROM usuarios WHERE email = '$email_admin' OR cpf = '$cpf_admin'");

try {
    // 2. INSERÇÃO LIMPA NO BANCO DE DADOS
    $sql = "INSERT INTO usuarios 
            (id_usuario, nome, email, senha, cpf, telefone, cep, logradouro, numero, bairro, cidade, uf, nivel_acesso)
    VALUES 
        (1, 'Master Loja', ?, ?, ?, '00000000000', '00000000', 'Rua Master', '1', 'Centro', 'Cidade Teste', 'UF', 'admin')";
    
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        $email_admin, 
        $senha_hash,  // O hash gerado PELO SEU PHP
        $cpf_admin
    ]);

    echo "SUCESSO: Usuário Admin ($email_admin) criado/reinserido!<br>";
    echo "Hash gerado (correto): " . $senha_hash . "<br>";
    echo "Tente logar com a senha: " . $senha_pura;

} catch (PDOException $e) {
    echo "ERRO FATAL DURANTE A INSERÇÃO: " . $e->getMessage();
}
?>