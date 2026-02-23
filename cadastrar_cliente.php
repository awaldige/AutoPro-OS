<?php 
include 'conexao.php'; 

// Lógica para Salvar o Cliente se o formulário for enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $endereco = $_POST['endereco'];
    $telefone = $_POST['telefone'];

    $sql = "INSERT INTO Clientes (NomeCliente, Endereco, Telefone) 
            VALUES ('$nome', '$endereco', '$telefone')";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php"); // Volta para o painel após salvar
        exit();
    } else {
        $erro = "Erro ao cadastrar: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Cliente - AutoPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 flex min-h-screen">

    <aside class="w-64 bg-slate-900 text-white p-6 hidden md:block">
        <div class="flex items-center gap-3 mb-10 border-b border-slate-700 pb-6">
            <i class="fas fa-tools text-3xl text-blue-400"></i>
            <span class="text-xl font-black tracking-tighter">AUTOPRO</span>
        </div>
        <nav class="space-y-2">
            <a href="index.php" class="flex items-center gap-3 p-3 hover:bg-slate-800 rounded-xl transition text-slate-300">
                <i class="fas fa-chart-line w-5 text-center"></i> Dashboard
            </a>
            <a href="cadastrar_cliente.php" class="flex items-center gap-3 p-3 bg-blue-600 rounded-xl shadow-lg font-semibold text-white">
                <i class="fas fa-users w-5 text-center"></i> Clientes
            </a>
            <a href="cadastrar_veiculo.php" class="flex items-center gap-3 p-3 hover:bg-slate-800 rounded-xl transition text-slate-300">
                <i class="fas fa-car w-5 text-center"></i> Veículos
            </a>
        </nav>
    </aside>

    <main class="flex-1 p-8">
        <div class="max-w-2xl mx-auto">
            <header class="mb-8">
                <h1 class="text-3xl font-extrabold text-slate-800">Novo Cliente</h1>
                <p class="text-slate-500">Preencha os dados para cadastrar o proprietário.</p>
            </header>

            <?php if(isset($erro)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r shadow-sm">
                    <p class="font-bold">Atenção!</p>
                    <p><?php echo $erro; ?></p>
                </div>
            <?php endif; ?>

            <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
                <form action="cadastrar_cliente.php" method="POST" class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nome Completo</label>
                        <input type="text" name="nome" placeholder="Ex: João da Silva" required
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Endereço</label>
                        <input type="text" name="endereco" placeholder="Rua, Número, Bairro"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Telefone com DDD</label>
                        <input type="text" name="telefone" placeholder="(11) 99999-9999"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition">
                    </div>

                    <div class="pt-4 flex gap-4">
                        <button type="submit" class="flex-1 bg-blue-600 text-white font-bold py-3 rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-100">
                            Salvar Cliente
                        </button>
                        <a href="index.php" class="px-6 py-3 bg-slate-100 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition">
                            Voltar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>