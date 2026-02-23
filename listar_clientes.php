<?php include 'conexao.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes - AutoPro</title>
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
            <a href="listar_clientes.php" class="flex items-center gap-3 p-3 bg-blue-600 rounded-xl shadow-lg font-semibold text-white">
                <i class="fas fa-users w-5 text-center"></i> Clientes
            </a>
            <a href="cadastrar_veiculo.php" class="flex items-center gap-3 p-3 hover:bg-slate-800 rounded-xl transition text-slate-300">
                <i class="fas fa-car w-5 text-center"></i> Veículos
            </a>
        </nav>
    </aside>

    <main class="flex-1 p-8">
        <header class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Gestão de Clientes</h1>
                <p class="text-slate-500 font-medium">Visualize e gerencie sua base de contatos.</p>
            </div>
            <a href="cadastrar_cliente.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-bold transition shadow-lg flex items-center gap-2">
                <i class="fas fa-user-plus"></i> Novo Cliente
            </a>
        </header>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-slate-400 text-[11px] font-bold uppercase tracking-widest border-b">
                    <tr>
                        <th class="p-5">ID</th>
                        <th class="p-5">Nome do Cliente</th>
                        <th class="p-5">Telefone</th>
                        <th class="p-5">Endereço</th>
                        <th class="p-5 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php
                    $sql = "SELECT * FROM Clientes ORDER BY idClientes DESC";
                    $resultado = $conn->query($sql);

                    if ($resultado->num_rows > 0) {
                        while($row = $resultado->fetch_assoc()):
                    ?>
                    <tr class="hover:bg-slate-50 transition group">
                        <td class="p-5 text-slate-400 font-mono text-sm">#<?php echo $row['idClientes']; ?></td>
                        <td class="p-5 font-bold text-slate-700"><?php echo $row['NomeCliente']; ?></td>
                        <td class="p-5 text-slate-600">
                            <i class="fas fa-phone mr-2 text-slate-300 text-xs"></i><?php echo $row['Telefone']; ?>
                        </td>
                        <td class="p-5 text-slate-500 text-sm italic"><?php echo $row['Endereco']; ?></td>
                        <td class="p-5 text-right space-x-2">
                            <button class="text-slate-400 hover:text-blue-600 transition"><i class="fas fa-edit"></i></button>
                            <button class="text-slate-400 hover:text-red-500 transition"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    <?php endwhile; 
                    } else {
                        echo "<tr><td colspan='5' class='p-20 text-center text-slate-400 italic'>Nenhum cliente cadastrado ainda.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>