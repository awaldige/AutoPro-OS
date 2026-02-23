<?php
include 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $veiculo_id = intval($_POST['veiculo_id']); 
    $numero_os = $conn->real_escape_string($_POST['numero_os']);
    $valor = isset($_POST['valor']) ? $_POST['valor'] : 0;
    $status = "Em Aberto"; 
    $data_emissao = date('Y-m-d');
    $data_entrega = $_POST['data_entrega'];
    $descricao = $conn->real_escape_string($_POST['descricao']);

    // AGORA USAMOS veiculo_id para o veículo e deixamos o idOrdemServico vazio (null) 
    // para o banco gerar o próximo número sozinho 1, 2, 3...
    $sql = "INSERT INTO OrdemDeServico (veiculo_id, NumeroOS, DataEmissao, Valor, Status, DataEntrega, Descricao) 
            VALUES ('$veiculo_id', '$numero_os', '$data_emissao', '$valor', '$status', '$data_entrega', '$descricao')";

    if ($conn->query($sql) === TRUE) {
        header("Location: listar_os.php?sucesso=1");
        exit();
    } else {
        echo "Erro crítico: " . $conn->error;
    }
}
?>