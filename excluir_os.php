
<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once 'conexao.php';

// Aceita somente requisições POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: listar_os.php');
    exit;
}

// Valida o token CSRF antes de qualquer operação.
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

// Valida o ID recebido.
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header('Location: listar_os.php?erro=id_invalido');
    exit;
}

try {
    $sql = "DELETE FROM OrdemDeServico WHERE idOrdemServico = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $stmt->close();

        header('Location: listar_os.php?excluido=1');
        exit;
    }

    $stmt->close();

    header('Location: listar_os.php?erro=os_nao_encontrada');
    exit;

} catch (mysqli_sql_exception $e) {
    error_log("Erro ao excluir OS: " . $e->getMessage());

    header('Location: listar_os.php?erro=exclusao');
    exit;
}
