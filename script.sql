-- Tabela de Categorias
CREATE TABLE categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nome_categoria VARCHAR(100) NOT NULL
);

-- Tabela de Produtos
CREATE TABLE produtos (
    id_produto INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10, 2) NOT NULL,
    id_categoria INT,
    imagem_url VARCHAR(255),
    FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria)
);

-- Inserir algumas categorias de exemplo
INSERT INTO categorias (nome_categoria) VALUES ('Anéis'), ('Colares'), ('Brincos'), ('Pulseiras');