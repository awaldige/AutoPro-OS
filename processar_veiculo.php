
<?php

require_once 'conexao.php';

function redirecionarErro($mensagem) {
    header('Location: cadastrar_veiculo.php?erro=' . urlencode($mensagem));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cadastrar_veiculo.php');
    exit();
}

$clienteId = filter_input(INPUT_POST, 'cliente_id', FILTER_VALIDATE_INT);
$marca = trim($_POST['marca'] ?? '');
$modelo = trim($_POST['modelo'] ?? '');
$placa = strtoupper(trim($_POST['placa'] ?? ''));
$anoInformado = trim($_POST['ano'] ?? '');

// Validação do cliente
if (!$clienteId || $clienteId <= 0) {
    redirecionarErro('Selecione um cliente válido.');
}

// Validação da marca
if ($marca === '' || mb_strlen($marca, 'UTF-8') > 45) {
    redirecionarErro('Informe uma marca válida, com até 45 caracteres.');
}

// Validação do modelo
if ($modelo === '' || mb_strlen($modelo, 'UTF-8') > 45) {
    redirecionarErro('Informe um modelo válido, com até 45 caracteres.');
}

// Padronização e validação da placa
$placa = preg_replace('/[^A-Z0-9]/', '', $placa);

$placaValida = preg_match('/^[A-Z]{3}[0-9]{4}$/', $placa)
    || preg_match('/^[A-Z]{3}[0-9][A-Z][0-9]{2}$/', $placa);

if (!$placaValida) {
    redirecionarErro('Placa inválida. Informe uma placa antiga ou padrão Mercosul.');
}

// Validação do ano
$ano = null;

if ($anoInformado !== '') {
    if (!ctype_digit($anoInformado)) {
        redirecionarErro('Informe um ano de fabricação válido.');
    }

    $ano = (int) $anoInformado;
    $anoAtual = (int) date('Y');

    if ($ano < 1900 || $ano > $anoAtual + 1) {
        redirecionarErro('O ano de fabricação informado não é válido.');
    }
}

try {
    // Confirma se o cliente existe
    $consultaCliente = $conn->prepare(
        "SELECT idClientes FROM Clientes WHERE idClientes = ?"
    );

    $consultaCliente->bind_param('i', $clienteId);
    $consultaCliente->execute();

    $resultadoCliente = $consultaCliente->get_result();
    $clienteExiste = $resultadoCliente->num_rows > 0;

    $consultaCliente->close();

    if (!$clienteExiste) {
        redirecionarErro('O cliente selecionado não foi encontrado.');
    }

    // Verifica se a placa já está cadastrada
    $consultaPlaca = $conn->prepare(
        "SELECT idVeiculos FROM Veiculos WHERE Placa = ?"
    );

    $consultaPlaca->bind_param('s', $placa);
    $consultaPlaca->execute();

    $resultadoPlaca = $consultaPlaca->get_result();
    $placaExiste = $resultadoPlaca->num_rows > 0;

    $consultaPlaca->close();

    if ($placaExiste) {
        redirecionarErro('Já existe um veículo cadastrado com essa placa.');
    }

    // Insere o veículo com parâmetros preparados
    $sql = "INSERT INTO Veiculos
            (Marca, Modelo, AnoFabricacao, Placa, Clientes_idClientes)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        'ssisi',
        $marca,
        $modelo,
        $ano,
        $placa,
        $clienteId
    );

    $stmt->execute();
    $stmt->close();

    header('Location: listar_veiculos.php?sucesso=1');
    exit();

} catch (mysqli_sql_exception $e) {

    error_log('Erro ao cadastrar veículo: ' . $e->getMessage());

    if ($e->getCode() === 1062) {
        redirecionarErro('Esta placa já está cadastrada no sistema.');
    }

    redirecionarErro('Não foi possível cadastrar o veículo. Tente novamente.');
}
