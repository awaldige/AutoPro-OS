
<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once 'conexao.php';

function redirecionar($mensagem) {
    header('Location: listar_clientes.php?erro=' . urlencode($mensagem));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: listar_clientes.php');
    exit();
}

// Validação CSRF
$tokenSessao = $_SESSION['csrf_token'] ?? '';
$tokenRecebido = $_POST['csrf_token'] ?? '';

if (
    !is_string($tokenSessao) ||
    !is_string($tokenRecebido) ||
    $tokenSessao === '' ||
    !hash_equals($tokenSessao, $tokenRecebido)
) {
    http_response_code(403);
    die('Requisição inválida. Atualize a página e tente novamente.');
}

// Validação do ID
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    redirecionar('id_invalido');
}

// Recebimento dos campos
$nome = trim($_POST['nome'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$endereco = trim($_POST['endereco'] ?? '');

// Validações
if ($nome === '') {
    redirecionar('nome_obrigatorio');
}

if (
    mb_strlen($nome, 'UTF-8') > 45 ||
    mb_strlen($telefone, 'UTF-8') > 15 ||
    mb_strlen($endereco, 'UTF-8') > 45
) {
    redirecionar('limite_caracteres');
}

try {
    // Confirma que o cliente existe
    $consulta = $conn->prepare(
        "SELECT idClientes FROM Clientes WHERE idClientes = ?"
    );

    $consulta->bind_param('i', $id);
    $consulta->execute();

    $resultado = $consulta->get_result();
    $clienteExiste = $resultado->num_rows > 0;

    $consulta->close();

    if (!$clienteExiste) {
        redirecionar('cliente_nao_encontrado');
    }

    // Atualiza somente os dados do cliente
    $sql = "UPDATE Clientes
            SET NomeCliente = ?, Telefone = ?, Endereco = ?
            WHERE idClientes = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssi', $nome, $telefone, $endereco, $id);
    $stmt->execute();
    $stmt->close();

    header('Location: listar_clientes.php?sucesso_edicao=1');
    exit();

} catch (mysqli_sql_exception $e) {
    error_log('Erro ao atualizar cliente: ' . $e->getMessage());
    redirecionar('falha_atualizacao');
}
