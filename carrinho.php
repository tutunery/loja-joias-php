<?php
$pageTitle = "Meu Carrinho";
require_once 'config.php';
require_once 'header.php';

// Verifica se tem itens na sessão
$tem_itens = false;
if (isset($_SESSION['carrinho']) && count($_SESSION['carrinho']) > 0) {
    $tem_itens = true;
}

$total = 0;
?>

<style>
    /* CSS PARA O FOOTER FICAR NO LUGAR CERTO */
    html, body { height: 100%; margin: 0; padding: 0; }
    body { display: flex; flex-direction: column; min-height: 100vh; background-color: var(--bs-body-bg); }
    footer { margin-top: auto; }
</style>

<div class="container py-5">
    <h2 class="mb-4" style="font-family: 'Playfair Display', serif;">
        <i class="fa-solid fa-bag-shopping text-warning me-2"></i> Sacola de Compras
    </h2>

    <?php if (!$tem_itens): ?>
        
        <div class="text-center py-5">
            <i class="fa-solid fa-basket-shopping fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">Sua sacola está vazia.</h4>
            <a href="index.php" class="btn btn-dark mt-3 px-4">Voltar para a Loja</a>
        </div>

    <?php else: ?>

        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Produto</th>
                                        <th>Preço</th>
                                        <th>Qtd</th>
                                        <th>Total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    foreach ($_SESSION['carrinho'] as $id_prod => $qtd): 
                                        // Busca os dados do produto no banco
                                        $sql = "SELECT * FROM produtos WHERE id_produto = :id";
                                        $stmt = $pdo->prepare($sql);
                                        $stmt->execute([':id' => $id_prod]);
                                        $prod = $stmt->fetch(PDO::FETCH_ASSOC);

                                        if(!$prod) continue; // Pula se o produto foi deletado

                                        $subtotal = $prod['preco'] * $qtd;
                                        $total += $subtotal;
                                        $imgSrc = !empty($prod['imagem_url']) ? $prod['imagem_url'] : 'https://via.placeholder.com/60';
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?= $imgSrc ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;" class="me-3">
                                                <div>
                                                    <h6 class="mb-0"><?= $prod['nome'] ?></h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>R$ <?= number_format($prod['preco'], 2, ',', '.') ?></td>
                                        <td><span class="badge bg-secondary"><?= $qtd ?></span></td>
                                        <td class="fw-bold">R$ <?= number_format($subtotal, 2, ',', '.') ?></td>
                                        <td>
                                            <a href="carrinho_acoes.php?acao=remover&id=<?= $id_prod ?>" class="text-danger" title="Remover">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <a href="carrinho_acoes.php?acao=limpar" class="btn btn-outline-danger btn-sm">Limpar Carrinho</a>
                    <a href="index.php" class="btn btn-outline-secondary btn-sm">Continuar Comprando</a>
                </div>
            </div>

            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-dark text-white fw-bold">RESUMO</div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span>R$ <?= number_format($total, 2, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 text-success">
                            <span>Frete</span>
                            <span>Grátis</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4 fs-5 fw-bold text-warning">
                            <span>Total</span>
                            <span>R$ <?= number_format($total, 2, ',', '.') ?></span>
                        </div>
                        <button class="btn btn-warning w-100 fw-bold py-2">FINALIZAR COMPRA</button>
                    </div>
                </div>
            </div>
        </div>

    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>