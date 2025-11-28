<?php
// Define o título para aparecer na aba do navegador
$pageTitle = "Gráficos e Estatísticas";

// 1. Inclui configurações e o Header (que já tem o Dark Mode)
require_once 'config.php';
require_once 'header.php';

// 2. BUSCA DADOS NO BANCO PARA O GRÁFICO
// Vamos contar quantos produtos existem em cada categoria
$labels = []; // Nomes das categorias (eixo X)
$data = [];   // Quantidades (eixo Y)

try {
    // A query usa LEFT JOIN para trazer categorias mesmo que não tenham produtos (com count 0)
    $sql = "SELECT c.nome_categoria, COUNT(p.id_produto) as total 
            FROM categorias c 
            LEFT JOIN produtos p ON c.id_categoria = p.id_categoria 
            GROUP BY c.id_categoria";
            
    $stmt = $pdo->query($sql);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Separa os dados para o JavaScript
    foreach($resultados as $item) {
        $labels[] = $item['nome_categoria'];
        $data[] = $item['total'];
    }

} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Erro ao buscar dados: " . $e->getMessage() . "</div>";
}

// Transforma os arrays do PHP em texto JSON para o JavaScript ler
$jsonLabels = json_encode($labels);
$jsonData = json_encode($data);
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fa-solid fa-chart-column"></i> Estoque por Categoria</h4>
                </div>
                
                <div class="card-body">
                    <canvas id="graficoEstoque" style="max-height: 400px;"></canvas>
                </div>
            </div>

            <div class="text-center mb-5">
                <p class="text-muted">Deseja ver os detalhes em lista?</p>
                <a href="relatorio.php" class="btn btn-primary btn-lg">
                    <i class="fa-solid fa-file-pdf"></i> Gerar Relatório Completo (PDF)
                </a>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Pega o elemento da tela
    const ctx = document.getElementById('graficoEstoque').getContext('2d');

    // Cria o Gráfico
    new Chart(ctx, {
        type: 'bar', // Tipo: Barra (pode mudar para 'pie', 'line', 'doughnut')
        data: {
            labels: <?php echo $jsonLabels; ?>, // Nomes vindos do PHP
            datasets: [{
                label: 'Quantidade de Joias',
                data: <?php echo $jsonData; ?>, // Números vindos do PHP
                backgroundColor: [
                    'rgba(212, 175, 55, 0.7)',  // Dourado
                    'rgba(52, 58, 64, 0.7)',    // Preto
                    'rgba(168, 127, 94, 0.7)',  // Bronze
                    'rgba(192, 192, 192, 0.7)'  // Prata
                ],
                borderColor: [
                    'rgba(212, 175, 55, 1)',
                    'rgba(52, 58, 64, 1)',
                    'rgba(168, 127, 94, 1)',
                    'rgba(192, 192, 192, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true, // Começa do 0
                    ticks: {
                        stepSize: 1 // Garante que mostre números inteiros (1, 2, 3...) e não quebrados (1.5)
                    }
                }
            },
            plugins: {
                legend: {
                    display: false // Esconde a legenda do topo se quiser
                }
            }
        }
    });
</script>

<?php 
// 5. Inclui o Rodapé
require_once 'footer.php'; 
?>