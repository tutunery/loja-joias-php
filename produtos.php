<?php
// Certifica-se de que a sessão está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Define o título da página
$titulo_pagina = "Vitrine de Joias";

require_once 'config.php';
require_once 'header.php'; 

// =======================================================
// PHP: BUSCA DE PRODUTOS PARA A VITRINE
// =======================================================

$produtos = [];
try {
    // 1. MUDANÇA AQUI: Adicionei p.id_produto na consulta
    $sql = "SELECT p.id_produto, p.nome, p.preco, p.imagem_url, c.nome_categoria 
            FROM produtos p
            JOIN categorias c ON p.id_categoria = c.id_categoria
            ORDER BY p.id_produto DESC";

    $stmt = $pdo->query($sql);
    $produtos = $stmt->fetchAll(PDO::FETCH_OBJ); // Garante que vem como Objeto (->)

} catch (PDOException $e) {
    echo "<div class='alert alert-danger m-3'>Erro ao carregar produtos: " . $e->getMessage() . "</div>";
}
?>

<style>
    /* CSS para garantir que o link pareça um botão */
    .btn-comprar {
        background-color: #d4af37;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 5px;
        cursor: pointer;
        width: 100%;
        display: inline-block; /* Importante para o link preencher o espaço */
        text-decoration: none; /* Tira o sublinhado */
        font-weight: bold;
        text-align: center;
        transition: background 0.3s;
    }
    .btn-comprar:hover {
        background-color: #bfa34b;
        color: white;
    }
    
    .lista-produtos {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
    }
    .card-produto {
        border: 1px solid #eee;
        padding: 15px;
        border-radius: 8px;
        width: 250px;
        background: var(--bs-body-bg); /* Respeita Dark Mode */
    }
</style>

<div class="card" style="padding: 30px; border: none; background: transparent;">
    <h2 class="text-center mb-4" style="font-family: 'Playfair Display', serif;">Coleção Exclusiva 💍</h2>
    <p class="text-center text-muted mb-5">Escolha suas peças favoritas.</p>

    <div class="lista-produtos">
        <?php if (count($produtos) > 0): ?>
            <?php foreach ($produtos as $produto): ?>
                <div class="card-produto shadow-sm">
                    
                    <img src="<?= htmlspecialchars($produto->imagem_url ?? 'img_joias/placeholder.jpg') ?>" 
                         alt="<?= htmlspecialchars($produto->nome) ?>" 
                         onerror="this.onerror=null;this.src='https://via.placeholder.com/250x200?text=Sem+Foto';"
                         style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px; margin-bottom: 15px;">
                    
                    <h3 style="font-size: 1.1em; margin-bottom: 5px;"><?= htmlspecialchars($produto->nome) ?></h3>
                    <p style="color: #888; font-size: 0.9em; margin-bottom: 5px;"><?= htmlspecialchars($produto->nome_categoria) ?></p>
                    
                    <p class="fs-5 fw-bold" style="color: #d4af37;">
                        R$ <?= number_format($produto->preco, 2, ',', '.') ?>
                    </p>
                    
                    <a href="carrinho_acoes.php?acao=add&id=<?= $produto->id_produto ?>" class="btn-comprar">
                        <i class="fa-solid fa-bag-shopping me-2"></i> Adicionar
                    </a>

                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center w-100">
                <p>Nenhuma joia encontrada.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'footer.php'; ?>