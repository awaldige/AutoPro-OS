<?php
include 'conexao.php'; // Conecta ao banco na porta 3308

// Verifica se o ID foi passado via URL (método GET)
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // intval garante que o ID seja um número por segurança
    $dataHoje = date('Y-m-d');

    // SQL para mudar o status para 'Concluída' e registrar a data de entrega
    $sql = "UPDATE OrdemDeServico 
            SET Status = 'Concluída', DataEntrega = '$dataHoje' 
            WHERE idOrdemServico = $id";

    if ($conn->query($sql) === TRUE) {
        // Se funcionar, redireciona de volta para a lista atualizada
        header("Location: index.php");
        exit();
    } else {
        echo "Erro ao atualizar status: " . $conn->error;
    }
} else {
    echo "ID da Ordem de Serviço não fornecido.";
}
?>