<?php
require_once 'config.php';

// Busca TODOS os produtos
try {
    $sql = "SELECT p.nome, p.preco, c.nome_categoria 
            FROM produtos p 
            JOIN categorias c ON p.id_categoria = c.id_categoria 
            ORDER BY p.nome ASC";
    $stmt = $pdo->query($sql);
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        body { background: #fff; color: #000; padding: 20px; }
        .cabecalho-pdf { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 10px; }
    </style>
</head>
<body>

<div class="container">
    <div class="d-flex justify-content-between mb-4" id="botoes">
        <a href="estatistica.php" class="btn btn-secondary">Voltar</a>
        <button onclick="gerarPDF()" class="btn btn-danger">Baixar PDF</button>
    </div>

    <div id="conteudoRelatorio">
        <div class="cabecalho-pdf">
            <h1>Lumière Joias</h1>
            <h3>Relatório de Estoque Completo</h3>
            <p>Data de emissão: <?php echo date('d/m/Y H:i'); ?></p>
        </div>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Produto</th>
                    <th>Categoria</th>
                    <th>Preço</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($produtos as $prod): ?>
                <tr>
                    <td><?php echo $prod['nome']; ?></td>
                    <td><?php echo $prod['nome_categoria']; ?></td>
                    <td>R$ <?php echo number_format($prod['preco'], 2, ',', '.'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div style="margin-top: 20px; text-align: right;">
            <strong>Total de Itens: <?php echo count($produtos); ?></strong>
        </div>
    </div>

</div>

<script>
    function gerarPDF() {
        const elemento = document.getElementById('conteudoRelatorio');
        
        const config = {
            margin: 10,
            filename: 'relatorio_joias.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        html2pdf().set(config).from(elemento).save();
    }
</script>

</body>
</html>