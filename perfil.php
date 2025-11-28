<?php
// 1. INICIA SESSÃO E VERIFICA SEGURANÇA
session_start();

// Se não tiver login, chuta para a página de entrar
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// 2. CONEXÃO E BUSCA DE DADOS
require_once 'config.php';

try {
    $id = $_SESSION['usuario_id'];
    // Busca todos os dados do usuário logado
    $sql = "SELECT * FROM usuarios WHERE id_usuario = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // Se o usuário foi deletado do banco mas a sessão continua ativa
        session_destroy();
        header("Location: login.php");
        exit();
    }

} catch (PDOException $e) {
    die("Erro ao carregar perfil: " . $e->getMessage());
}

// 3. CARREGA O CABEÇALHO (Aqui vem o Menu e o Dark Mode)
require_once 'header.php';
?>

<div class="container py-5">
    
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="display-6" style="font-family: 'Playfair Display', serif;">
                <i class="fa-regular fa-id-card text-warning me-2"></i> Minha Conta
            </h2>
            <p class="text-muted">Gerencie suas informações pessoais e de acesso.</p>
        </div>
    </div>

    <div class="row g-4">
        
        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header fw-bold text-uppercase" style="background-color: rgba(212, 175, 55, 0.1); color: #d4af37;">
                    Dados Pessoais
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Nome:</span>
                            <span class="fw-medium"><?php echo $user['nome']; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">CPF:</span>
                            <span><?php echo $user['cpf']; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Telefone:</span>
                            <span><?php echo $user['telefone']; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">E-mail:</span>
                            <span><?php echo $user['email']; ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header fw-bold text-uppercase" style="background-color: rgba(212, 175, 55, 0.1); color: #d4af37;">
                    Endereço de Entrega
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $user['logradouro']; ?>, <?php echo $user['numero']; ?></h5>
                    <p class="card-text mb-1 text-muted"><?php echo $user['bairro']; ?></p>
                    <p class="card-text mb-1"><?php echo $user['cidade']; ?> - <?php echo $user['uf']; ?></p>
                    <hr>
                    <p class="card-text"><small class="text-muted fw-bold">CEP:</small> <?php echo $user['cep']; ?></p>
                </div>
            </div>
        </div>

    </div>

    <div class="d-flex justify-content-between align-items-center mt-5 p-4 bg-body-tertiary rounded border">
        <div>
            <span class="text-muted d-block small">Deseja sair do sistema?</span>
            <span class="fw-bold text-success">Sessão ativa</span>
        </div>
        <div>
            <a href="index.php" class="btn btn-outline-secondary me-2">Voltar para Loja</a>
            
            <a href="logout.php" class="btn btn-danger">
                <i class="fa-solid fa-right-from-bracket"></i> Sair da Conta
            </a>
        </div>
    </div>

</div>

<?php 
// 4. CARREGA O RODAPÉ (Scripts do Bootstrap)
require_once 'footer.php'; 
?>