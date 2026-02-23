<?php
include 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cliente_id = $_POST['cliente_id'];
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $placa = $_POST['placa'];
    $ano = $_POST['ano'];

    $sql = "INSERT INTO Veiculos (Marca, Modelo, AnoFabricacao, Placa, Clientes_idClientes) 
            VALUES ('$marca', '$modelo', '$ano', '$placa', '$cliente_id')";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
    } else {
        echo "Erro ao cadastrar veículo: " . $conn->error;
    }
}
?>