<?php
require_once 'config.php'; 

header('Content-Type: application/json');

$id_produto = $_GET['id'] ?? null;

if (empty($id_produto) || !is_numeric($id_produto)) {
    echo json_encode(['success' => false, 'message' => 'ID de produto inválido.']);
    exit();
}

try {
    $sql = "SELECT id_produto, nome, descricao, preco, id_categoria, imagem_url FROM produtos WHERE id_produto = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_produto]);
    $joia = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($joia) {
        echo json_encode(['success' => true, 'data' => $joia]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Joia não encontrada.']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro no banco de dados: ' . $e->getMessage()]);
}
?>