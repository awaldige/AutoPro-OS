<?php include 'conexao.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Veículo - Oficina</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10">
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-gray-800 italic underline">Cadastrar Veículo</h2>
        
        <form action="processar_veiculo.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-gray-700">Dono (Cliente):</label>
                <select name="cliente_id" class="w-full border p-2 rounded" required>
                    <option value="">Selecione um cliente...</option>
                    <?php
                    $res = $conn->query("SELECT idClientes, NomeCliente FROM Clientes ORDER BY NomeCliente");
                    while($cli = $res->fetch_assoc()) {
                        echo "<option value='".$cli['idClientes']."'>".$cli['NomeCliente']."</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <input type="text" name="marca" placeholder="Marca (ex: Ford)" class="border p-2 rounded" required>
                <input type="text" name="modelo" placeholder="Modelo (ex: Ka)" class="border p-2 rounded" required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <input type="text" name="placa" placeholder="Placa" class="border p-2 rounded" required>
                <input type="number" name="ano" placeholder="Ano" class="border p-2 rounded">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded hover:bg-blue-700">
                Salvar Veículo
            </button>
            <a href="index.php" class="block text-center text-gray-500 mt-4">Voltar para o Painel</a>
        </form>
    </div>
</body>
</html>