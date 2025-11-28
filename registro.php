<?php
// registro.php
session_start();
require_once 'config.php';

$erro = '';
$sucesso = '';

// Variáveis para manter o formulário preenchido (Sticky Form)
$nome = ''; $email = ''; $cpf = ''; $telefone = '';
$cep = ''; $logradouro = ''; $numero = ''; $bairro = ''; $cidade = ''; $uf = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Recebe os dados
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $confirma_senha = $_POST['confirma_senha']; // Novo campo
    $cpf = trim($_POST['cpf']);
    $telefone = $_POST['telefone'];
    
    // Endereço
    $cep = $_POST['cep'];
    $logradouro = $_POST['logradouro'];
    $numero = $_POST['numero'];
    $bairro = $_POST['bairro'];
    $cidade = $_POST['cidade'];
    $uf = $_POST['uf'];

    // 2. Validações Básicas
    if (empty($nome) || empty($email) || empty($senha) || empty($cpf) || empty($confirma_senha)) {
        $erro = "Por favor, preencha todos os campos obrigatórios.";
    } 
    // 3. VALIDAÇÃO DA SENHA (NOVA)
    elseif ($senha !== $confirma_senha) {
        $erro = "As senhas digitadas não coincidem!";
    }
    else {
        try {
            // 4. Verifica duplicidade (Email ou CPF)
            $sql_verifica = "SELECT id_usuario FROM usuarios WHERE email = :email OR cpf = :cpf";
            $stmt_verifica = $pdo->prepare($sql_verifica);
            $stmt_verifica->execute([':email' => $email, ':cpf' => $cpf]);
            
            if ($stmt_verifica->rowCount() > 0) {
                $erro = "Já existe uma conta cadastrada com este E-mail ou CPF.";
            } else {
                // 5. Sucesso: Cadastra no Banco
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

                $sql = "INSERT INTO usuarios (nome, email, senha, cpf, telefone, cep, logradouro, numero, bairro, cidade, uf, nivel_acesso) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'cliente')";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nome, $email, $senha_hash, $cpf, $telefone, $cep, $logradouro, $numero, $bairro, $cidade, $uf]);

                // Redireciona com flag de sucesso
                header("Location: login.php?status=sucesso_registro");
                exit();
            }

        } catch (PDOException $e) {
            $erro = "Erro no sistema: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Criar Conta - Lumière Joias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body { background-color: #f8f9fa; font-family: 'Lato', sans-serif; padding: 40px 0; }
        .register-card { max-width: 700px; margin: auto; padding: 30px; background: white; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .btn-gold { background-color: #d4af37; color: white; border: none; font-weight: bold; }
        .btn-gold:hover { background-color: #bfa34b; color: white; }
        h3 { font-family: 'Playfair Display', serif; color: #333; text-align: center; margin-bottom: 20px; }
        .form-label { font-weight: 600; font-size: 0.9rem; color: #555; }
    </style>
</head>
<body>

<div class="container">
    <div class="register-card">
        <h3>Crie sua Conta</h3>

        <form method="POST">
            <h5 class="mb-3 text-muted border-bottom pb-2">Dados Pessoais</h5>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Nome Completo</label>
                    <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($nome) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">CPF</label>
                    <input type="text" name="cpf" class="form-control" placeholder="000.000.000-00" value="<?= htmlspecialchars($cpf) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Telefone</label>
                    <input type="text" name="telefone" class="form-control" placeholder="(11) 99999-9999" value="<?= htmlspecialchars($telefone) ?>" required>
                </div>
            </div>

            <h5 class="mb-3 mt-2 text-muted border-bottom pb-2">Acesso</h5>
            <div class="mb-3">
                <label class="form-label">E-mail</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Senha</label>
                    <input type="password" name="senha" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Confirmar Senha</label>
                    <input type="password" name="confirma_senha" class="form-control" required>
                </div>
            </div>

            <h5 class="mb-3 mt-2 text-muted border-bottom pb-2">Endereço</h5>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">CEP</label>
                    <input type="text" name="cep" id="cep" class="form-control" maxlength="9" value="<?= htmlspecialchars($cep) ?>" required onblur="pesquisacep(this.value);">
                </div>
                <div class="col-md-8 mb-3">
                    <label class="form-label">Rua</label>
                    <input type="text" name="logradouro" id="rua" class="form-control" value="<?= htmlspecialchars($logradouro) ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Número</label>
                    <input type="text" name="numero" id="numero" class="form-control" value="<?= htmlspecialchars($numero) ?>" required>
                </div>
                <div class="col-md-8 mb-3">
                    <label class="form-label">Bairro</label>
                    <input type="text" name="bairro" id="bairro" class="form-control" value="<?= htmlspecialchars($bairro) ?>" required>
                </div>
                <div class="col-md-8 mb-3">
                    <label class="form-label">Cidade</label>
                    <input type="text" name="cidade" id="cidade" class="form-control" value="<?= htmlspecialchars($cidade) ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Estado</label>
                    <input type="text" name="uf" id="uf" class="form-control" maxlength="2" value="<?= htmlspecialchars($uf) ?>" required>
                </div>
            </div>

            <button type="submit" class="btn btn-gold w-100 py-2 mt-3">Cadastrar</button>
        </form>

        <div class="text-center mt-3">
            <a href="login.php" class="text-decoration-none text-muted small">Já tenho conta. Fazer Login.</a>
        </div>
    </div>
</div>

<?php if (!empty($erro)): ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            icon: 'error',
            title: 'Atenção!',
            text: '<?= $erro ?>', // Insere o texto do erro PHP aqui
            confirmButtonColor: '#d4af37'
        });
    });
</script>
<?php endif; ?>

<script>
    function limpa_formulário_cep() {
            document.getElementById('rua').value=("");
            document.getElementById('bairro').value=("");
            document.getElementById('cidade').value=("");
            document.getElementById('uf').value=("");
    }

    function meu_callback(conteudo) {
        if (!("erro" in conteudo)) {
            document.getElementById('rua').value=(conteudo.logradouro);
            document.getElementById('bairro').value=(conteudo.bairro);
            document.getElementById('cidade').value=(conteudo.localidade);
            document.getElementById('uf').value=(conteudo.uf);
            document.getElementById('numero').focus();
        } 
        else {
            limpa_formulário_cep();
            // Alerta bonito de erro no CEP
            Swal.fire({
                icon: 'error',
                title: 'Ops...',
                text: 'CEP não encontrado!',
                confirmButtonColor: '#d4af37'
            });
        }
    }
        
    function pesquisacep(valor) {
        var cep = valor.replace(/\D/g, '');
        if (cep != "") {
            var validacep = /^[0-9]{8}$/;
            if(validacep.test(cep)) {
                document.getElementById('rua').value="...";
                document.getElementById('bairro').value="...";
                document.getElementById('cidade').value="...";
                document.getElementById('uf').value="...";
                
                var script = document.createElement('script');
                script.src = 'https://viacep.com.br/ws/'+ cep + '/json/?callback=meu_callback';
                document.body.appendChild(script);
            } else {
                limpa_formulário_cep();
                // Alerta bonito de formato inválido
                Swal.fire({
                    icon: 'warning',
                    title: 'Formato Inválido',
                    text: 'O CEP deve conter 8 dígitos.',
                    confirmButtonColor: '#d4af37'
                });
            }
        } else {
            limpa_formulário_cep();
        }
    };
</script>

</body>
</html>