-- Tabela de Categorias
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
    -- Relacionamento: Garante que a categoria exista
    FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria)
);

-- Tabela de Usuários (Clientes e Administradores)
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    cpf VARCHAR(14) NOT NULL UNIQUE,
    telefone VARCHAR(20) NOT NULL,
    
    -- Campos de Endereço (Preenchidos pelo ViaCEP)
    cep VARCHAR(10) NOT NULL,
    logradouro VARCHAR(255) NOT NULL,
    numero VARCHAR(20) NOT NULL,
    bairro VARCHAR(100) NOT NULL,
    cidade VARCHAR(100) NOT NULL,
    uf CHAR(2) NOT NULL,
    
    -- Controle de Acesso (Admin vs Cliente)
    nivel_acesso VARCHAR(20) DEFAULT 'cliente',
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Inserção de Dados Iniciais (Categorias)
INSERT INTO categorias (nome_categoria) VALUES 
    ('Anéis'), 
    ('Colares'), 
    ('Brincos'), 
    ('Pulseiras');

-- Nota Importante:
-- Lembre-se de usar o script forca_admin_insert.php para criar o usuário 'masterloja@gmail.com'
-- com a senha '123456' e o nível de acesso 'admin'.