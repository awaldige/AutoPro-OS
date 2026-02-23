<?php
include 'conexao.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Deleta a Ordem de Serviço pelo ID
    $sql = "DELETE FROM OrdemDeServico WHERE idOrdemServico = $id";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: listar_os.php"); // Redireciona de volta
    } else {
        echo "Erro ao excluir: " . $conn->error;
    }
}
?>