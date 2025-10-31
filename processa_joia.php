<?php
// Inclui o arquivo de conexão com o banco de dados
require_once 'config.php'; 

// Diretório de upload de imagens
$upload_dir = 'img_joias/';

/**
 * Função auxiliar para lidar com o upload de imagem
 * @return string|false Retorna o caminho da imagem salva ou false em caso de falha.
 */
function handle_image_upload($file_key, $upload_dir) {
    if (!isset($_FILES[$file_key]) || $_FILES[$file_key]['error'] !== UPLOAD_ERR_OK) {
        return false; // Nenhuma imagem enviada ou erro de upload
    }

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file_tmp  = $_FILES[$file_key]['tmp_name'];
    $file_name = uniqid('joia_') . '_' . basename($_FILES[$file_key]['name']); 
    $destino   = $upload_dir . $file_name;

    if (move_uploaded_file($file_tmp, $destino)) {
        return $destino;
    } else {
        return false;
    }
}

// Verifica se a requisição é POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $acao = $_POST['acao'] ?? '';
    $id_produto = $_POST['id_produto'] ?? null;

    // =========================================================================
    // 1. Lógica para CRIAR/CADASTRAR (CREATE)
    // =========================================================================
    if ($acao === 'criar') {
        
        $nome         = trim($_POST['nome'] ?? '');
        $preco        = $_POST['preco'] ?? 0;
        $id_categoria = $_POST['id_categoria'] ?? null;
        $descricao    = trim($_POST['descricao'] ?? '');
        
        // Validação básica
        if (empty($nome) || $preco <= 0 || empty($id_categoria) || !is_numeric($id_categoria)) {
            $msg = "Por favor, preencha todos os campos obrigatórios corretamente.";
            header("Location: admin.php?status=erro&msg=" . urlencode($msg));
            exit();
        }

        // Processa o upload da imagem (obrigatório para criação)
        $imagem_url = handle_image_upload('imagem', $upload_dir);
        if (!$imagem_url) {
            $msg = "Erro: Nenhuma imagem válida foi enviada para cadastro.";
            header("Location: admin.php?status=erro&msg=" . urlencode($msg));
            exit();
        }

        // Inserção no Banco de Dados
        try {
            $sql = "INSERT INTO produtos (nome, descricao, preco, id_categoria, imagem_url) 
                    VALUES (?, ?, ?, ?, ?)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nome, $descricao, $preco, $id_categoria, $imagem_url]);
            
            header("Location: admin.php?status=sucesso_cadastro");
            exit();

        } catch (PDOException $e) {
            // Se falhar no DB, deleta a imagem salva para evitar lixo
            if (file_exists($imagem_url)) {
                unlink($imagem_url);
            }
            $msg = "Erro no DB ao cadastrar: " . $e->getMessage();
            header("Location: admin.php?status=erro&msg=" . urlencode($msg));
            exit();
        }
    } 
    
    // =========================================================================
    // 2. Lógica para EDITAR/ATUALIZAR (UPDATE)
    // =========================================================================
    elseif ($acao === 'editar') {
        
        $nome         = trim($_POST['nome'] ?? '');
        $preco        = $_POST['preco'] ?? 0;
        $id_categoria = $_POST['id_categoria'] ?? null;
        $descricao    = trim($_POST['descricao'] ?? '');
        
        if (empty($id_produto) || !is_numeric($id_produto) || empty($nome) || $preco <= 0 || empty($id_categoria)) {
            $msg = "Dados de edição inválidos ou incompletos.";
            header("Location: admin.php?status=erro&msg=" . urlencode($msg));
            exit();
        }

        $imagem_url_update = handle_image_upload('imagem', $upload_dir);
        
        // --- Montagem da Query de UPDATE ---
        $sql = "UPDATE produtos SET nome = ?, preco = ?, id_categoria = ?, descricao = ?";
        $params = [$nome, $preco, $id_categoria, $descricao];
        
        try {
            // Se houve upload de uma nova imagem
            if ($imagem_url_update) {
                // 1. Busca o caminho da imagem antiga para deletar
                $stmt_old_img = $pdo->prepare("SELECT imagem_url FROM produtos WHERE id_produto = ?");
                $stmt_old_img->execute([$id_produto]);
                $old_joia = $stmt_old_img->fetch(PDO::FETCH_OBJ);
                
                // 2. Adiciona a imagem_url na query e nos parâmetros
                $sql .= ", imagem_url = ?";
                $params[] = $imagem_url_update;
            }

            $sql .= " WHERE id_produto = ?";
            $params[] = $id_produto;

            // Executa o UPDATE
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            // 3. Se deu certo, deleta a imagem antiga do servidor
            if ($imagem_url_update && $old_joia && !empty($old_joia->imagem_url) && file_exists($old_joia->imagem_url)) {
                unlink($old_joia->imagem_url);
            }
            
            header("Location: admin.php?status=sucesso_edicao");
            exit();

        } catch (PDOException $e) {
            // Se falhar no DB, deleta a nova imagem salva
            if ($imagem_url_update && file_exists($imagem_url_update)) {
                unlink($imagem_url_update);
            }
            $msg = "Erro ao editar no DB: " . $e->getMessage();
            header("Location: admin.php?status=erro&msg=" . urlencode($msg));
            exit();
        }
    } 
    
    // =========================================================================
    // 3. Lógica para DELETAR (DELETE)
    // =========================================================================
    elseif ($acao === 'deletar') {
        
        if (empty($id_produto) || !is_numeric($id_produto)) {
            $msg = "ID de deleção inválido.";
            header("Location: admin.php?status=erro&msg=" . urlencode($msg));
            exit();
        }
        
        try {
            // Busca o caminho da imagem para deletá-la do servidor
            $stmt_img = $pdo->prepare("SELECT imagem_url FROM produtos WHERE id_produto = ?");
            $stmt_img->execute([$id_produto]);
            $joia = $stmt_img->fetch(PDO::FETCH_OBJ);

            // 1. Deleta a Joia do Banco de Dados
            $sql = "DELETE FROM produtos WHERE id_produto = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_produto]);

            // 2. Deleta o arquivo de imagem do servidor, se existir
            if ($joia && !empty($joia->imagem_url) && file_exists($joia->imagem_url)) {
                unlink($joia->imagem_url);
            }
            
            header("Location: admin.php?status=sucesso_delecao");
            exit();

        } catch (PDOException $e) {
            $msg = "Erro ao deletar no DB: " . $e->getMessage();
            header("Location: admin.php?status=erro&msg=" . urlencode($msg));
            exit();
        }
    } else {
        $msg = "Ação desconhecida.";
        header("Location: admin.php?status=erro&msg=" . urlencode($msg));
        exit();
    }

} else {
    // Se não for POST (alguém tentou acessar diretamente), redireciona
    header("Location: admin.php");
    exit();
}
?>