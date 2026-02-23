<?php include 'conexao.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frota de Veículos - AutoPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 flex">

    <aside class="w-64 bg-slate-900 min-h-screen text-white p-6 hidden md:block sticky top-0">
        <div class="flex items-center gap-3 mb-10 border-b border-slate-700 pb-6">
            <i class="fas fa-tools text-3xl text-blue-400"></i>
            <span class="text-xl font-black tracking-tighter">AUTOPRO</span>
        </div>
        <nav class="space-y-2">
            <a href="index.php" class="flex items-center gap-3 p-3 hover:bg-slate-800 rounded-xl transition text-slate-300">
                <i class="fas fa-chart-line w-5 text-center"></i> Dashboard
            </a>
            <a href="listar_clientes.php" class="flex items-center gap-3 p-3 hover:bg-slate-800 rounded-xl transition text-slate-300">
                <i class="fas fa-users w-5 text-center"></i> Clientes
            </a>
            <a href="listar_veiculos.php" class="flex items-center gap-3 p-3 bg-blue-600 rounded-xl shadow-lg font-semibold text-white">
                <i class="fas fa-car w-5 text-center"></i> Veículos
            </a>
        </nav>
    </aside>

    <main class="flex-1 p-8">
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Frota de Veículos</h1>
                <p class="text-slate-500 font-medium">Consulte os veículos registrados no sistema.</p>
            </div>
            <a href="cadastrar_veiculo.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-bold transition shadow-lg flex items-center gap-2">
                <i class="fas fa-plus-circle"></i> Novo Veículo
            </a>
        </header>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-slate-400 text-[11px] font-bold uppercase tracking-widest border-b">
                    <tr>
                        <th class="p-5">Placa</th>
                        <th class="p-5">Marca / Modelo</th>
                        <th class="p-5">Proprietário</th>
                        <th class="p-5">Ano</th>
                        <th class="p-5 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php
                    // SQL com JOIN para trazer o nome do cliente
                    $sql = "SELECT V.*, C.NomeCliente 
                            FROM Veiculos V 
                            JOIN Clientes C ON V.Clientes_idClientes = C.idClientes 
                            ORDER BY V.idVeiculos DESC";
                    
                    $resultado = $conn->query($sql);

                    if ($resultado && $resultado->num_rows > 0) {
                        while($row = $resultado->fetch_assoc()):
                    ?>
                    <tr class="hover:bg-slate-50 transition group">
                        <td class="p-5">
                            <span class="bg-slate-800 text-white px-3 py-1 rounded font-mono font-bold text-sm tracking-widest uppercase">
                                <?php echo $row['Placa']; ?>
                            </span>
                        </td>
                        <td class="p-5 font-bold text-slate-700">
                            <?php echo $row['Marca'] . " " . $row['Modelo']; ?>
                        </td>
                        <td class="p-5 text-slate-600">
                            <i class="fas fa-user-circle mr-2 text-slate-300"></i><?php echo $row['NomeCliente']; ?>
                        </td>
                        <td class="p-5 text-slate-500 font-medium">
                            <?php echo $row['AnoFabricacao']; ?>
                        </td>
                        <td class="p-5 text-right space-x-3">
                            <button class="text-slate-300 hover:text-blue-600 transition"><i class="fas fa-edit"></i></button>
                            <button class="text-slate-300 hover:text-red-500 transition"><i class="fas fa-trash-alt"></i></button>
                        </td>
                    </tr>
                    <?php endwhile; 
                    } else {
                        echo "<tr><td colspan='5' class='p-20 text-center text-slate-400 italic font-medium'>Nenhum veículo encontrado. Comece cadastrando um!</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>