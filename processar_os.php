
<?php

require_once 'conexao.php';

function redirecionarErro($mensagem) {
    header('Location: nova_os.php?erro=' . urlencode($mensagem));
    exit();
}

// Aceita somente requisições POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: nova_os.php');
    exit();
}

// Recebe os dados
$veiculoId = filter_input(INPUT_POST, 'veiculo_id', FILTER_VALIDATE_INT);
$numeroOSInformado = trim($_POST['numero_os'] ?? '');
$valorInformado = trim($_POST['valor'] ?? '');
$dataEntregaInformada = trim($_POST['data_entrega'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');

// Validação do veículo
if (!$veiculoId || $veiculoId <= 0) {
    redirecionarErro('Selecione um veículo válido.');
}

// Validação do número da OS
if ($numeroOSInformado === '' || !ctype_digit($numeroOSInformado)) {
    redirecionarErro('Informe um número de OS válido.');
}

$numeroOS = (int) $numeroOSInformado;

if ($numeroOS <= 0) {
    redirecionarErro('O número da OS deve ser maior que zero.');
}

// Validação do valor
$valorInformado = str_replace(',', '.', $valorInformado);

if ($valorInformado === '' || !is_numeric($valorInformado)) {
    redirecionarErro('Informe um valor válido para a Ordem de Serviço.');
}

$valor = (float) $valorInformado;

if (!is_finite($valor) || $valor < 0 || $valor > 99999999.99) {
    redirecionarErro('O valor informado está fora do limite permitido.');
}

// Validação da data de entrega
$dataEntrega = null;

if ($dataEntregaInformada !== '') {
    $data = DateTime::createFromFormat('!Y-m-d', $dataEntregaInformada);
    $errosData = DateTime::getLastErrors();

    if (
        !$data ||
        ($errosData !== false &&
        ($errosData['warning_count'] > 0 || $errosData['error_count'] > 0)) ||
        $data->format('Y-m-d') !== $dataEntregaInformada
    ) {
        redirecionarErro('Informe uma data de entrega válida.');
    }

    $dataEntrega = $data->format('Y-m-d');
}

// Validação da descrição
if (mb_strlen($descricao, 'UTF-8') > 10000) {
    redirecionarErro('A descrição ultrapassou o limite permitido.');
}

$dataEmissao = date('Y-m-d');
$status = 'Em Aberto';

try {

    // Confirma se o veículo existe
    $consultaVeiculo = $conn->prepare(
        "SELECT idVeiculos FROM Veiculos WHERE idVeiculos = ?"
    );

    $consultaVeiculo->bind_param('i', $veiculoId);
    $consultaVeiculo->execute();

    $resultadoVeiculo = $consultaVeiculo->get_result();
    $veiculoExiste = $resultadoVeiculo->num_rows > 0;

    $consultaVeiculo->close();

    if (!$veiculoExiste) {
        redirecionarErro('O veículo selecionado não foi encontrado.');
    }

    // Verifica se o número da OS já existe
    $consultaOS = $conn->prepare(
        "SELECT idOrdemServico
         FROM OrdemDeServico
         WHERE NumeroOS = ?"
    );

    $consultaOS->bind_param('i', $numeroOS);
    $consultaOS->execute();

    $resultadoOS = $consultaOS->get_result();
    $osExiste = $resultadoOS->num_rows > 0;

    $consultaOS->close();

    if ($osExiste) {
        redirecionarErro('Este número de Ordem de Serviço já está cadastrado.');
    }

    // Insere a nova Ordem de Serviço
    $sql = "INSERT INTO OrdemDeServico
            (veiculo_id, NumeroOS, DataEmissao, Valor, Status, DataEntrega, Descricao)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        'iisdsss',
        $veiculoId,
        $numeroOS,
        $dataEmissao,
        $valor,
        $status,
        $dataEntrega,
        $descricao
    );

    $stmt->execute();
    $stmt->close();

    header('Location: listar_os.php?sucesso=1');
    exit();

} catch (mysqli_sql_exception $e) {

    error_log('Erro ao cadastrar Ordem de Serviço: ' . $e->getMessage());

    if ($e->getCode() === 1062) {
        redirecionarErro('Este número de Ordem de Serviço já está cadastrado.');
    }

    redirecionarErro('Não foi possível cadastrar a Ordem de Serviço. Tente novamente.');
}
