<?php
require_once 'config.php'; // Usa a conexão do seu config.php
header('Content-Type: application/json'); // Diz ao navegador que a resposta é JSON
// Busca os produtos
$sql = "SELECT p.nome, p.descricao, p.preco, p.imagem_url, c.nome_categoria 
        FROM produtos p
        JOIN categorias c ON p.id_categoria = c.id_categoria
        ORDER BY p.id_produto DESC";

try {
    $stmt = $pdo->query($sql);
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC); // Retorna como array associativo

    echo json_encode(['success' => true, 'data' => $produtos]);

} catch (Exception $e) {
    // Em caso de erro
    echo json_encode(['success' => false, 'message' => 'Erro ao buscar produtos: ' . $e->getMessage()]);
}
?>