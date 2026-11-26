
<?php

require_once 'conexao.php';

function escapar($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

$busca = trim($_GET['busca'] ?? '');
$veiculos = [];
$totalVeiculos = 0;
$totalFiltrados = 0;
$erroConsulta = false;

try {
    // Total geral da frota
    $resultadoTotal = $conn->query(
        "SELECT COUNT(*) AS total FROM Veiculos"
    );

    $totalVeiculos = (int) $resultadoTotal->fetch_assoc()['total'];

    // Consulta com pesquisa
    $sql = "SELECT
                V.idVeiculos,
                V.Marca,
                V.Modelo,
                V.AnoFabricacao,
                V.Placa,
                V.Clientes_idClientes,
                C.NomeCliente
            FROM Veiculos V
            LEFT JOIN Clientes C
                ON V.Clientes_idClientes = C.idClientes";

    if ($busca !== '') {
        $sql .= " WHERE
                    V.Placa LIKE ?
                    OR V.Marca LIKE ?
                    OR V.Modelo LIKE ?
                    OR C.NomeCliente LIKE ?";
    }

    $sql .= " ORDER BY V.idVeiculos DESC";

    $stmt = $conn->prepare($sql);

    if ($busca !== '') {
        $termo = '%' . $busca . '%';
        $stmt->bind_param('ssss', $termo, $termo, $termo, $termo);
    }

    $stmt->execute();
    $resultado = $stmt->get_result();

    while ($veiculo = $resultado->fetch_assoc()) {
        $veiculos[] = $veiculo;
    }

    $stmt->close();

    $totalFiltrados = count($veiculos);

} catch (mysqli_sql_exception $e) {
    error_log('Erro ao listar veículos: ' . $e->getMessage());
    $erroConsulta = true;
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg?v=2">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frota de Veículos | AutoPro OS</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            font-family: Inter, ui-sans-serif, system-ui, sans-serif;
            background: #f1f5f9;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 9px;
            color: #cbd5e1;
            transition: .2s;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background: #1e40af;
            color: #fff;
        }

        .table-row:hover {
            background: #f8fafc;
        }

        .search-input {
            outline: none;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            padding: 11px 14px 11px 40px;
            width: 100%;
        }

        .search-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .10);
        }
    </style>
</head>

<body class="min-h-screen">

<div class="min-h-screen flex">

    <!-- Menu lateral -->
    <aside id="sidebar"
           class="fixed inset-y-0 left-0 z-40 w-64 bg-slate-950 text-white
                  transform -translate-x-full transition-transform duration-300
                  lg:translate-x-0 lg:static lg:flex-shrink-0">

        <div class="p-6 border-b border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-blue-600 flex items-center justify-center">
                    <i class="fas fa-car text-xl"></i>
                </div>

                <div>
                    <h1 class="text-xl font-bold tracking-wide">
                        AutoPro <span class="text-blue-400">OS</span>
                    </h1>
                    <p class="text-xs text-slate-400">Gestão de Oficina</p>
                </div>
            </div>
        </div>

        <nav class="p-4 space-y-2">
            <p class="text-xs uppercase tracking-wider text-slate-500 px-3 mb-3">
                Principal
            </p>

            <a href="index.php" class="sidebar-link">
                <i class="fas fa-chart-line w-5"></i>
                Painel
            </a>

            <a href="listar_clientes.php" class="sidebar-link">
                <i class="fas fa-users w-5"></i>
                Clientes
            </a>

            <a href="listar_veiculos.php" class="sidebar-link active">
                <i class="fas fa-car w-5"></i>
                Veículos
            </a>

            <a href="listar_os.php" class="sidebar-link">
                <i class="fas fa-clipboard-list w-5"></i>
                Ordens de Serviço
            </a>
        </nav>

        <div class="absolute bottom-0 left-0 right-0 p-5 border-t border-slate-800">
            <p class="text-xs text-slate-500">AutoPro OS</p>
            <p class="text-sm text-slate-400 mt-1">Sistema de Gestão Automotiva</p>
        </div>
    </aside>

    <!-- Overlay mobile -->
    <div id="overlay"
         class="fixed inset-0 bg-black/50 z-30 hidden lg:hidden"
         onclick="alternarMenu()"></div>

    <!-- Conteúdo principal -->
    <main class="flex-1 min-w-0">

        <header class="bg-white border-b border-slate-200 px-5 md:px-8 py-5">
            <div class="flex items-center justify-between gap-4">

                <div class="flex items-center gap-3">
                    <button onclick="alternarMenu()"
                            class="lg:hidden text-slate-600 text-xl"
                            aria-label="Abrir menu">
                        <i class="fas fa-bars"></i>
                    </button>

                    <div>
                        <h2 class="text-xl md:text-2xl font-bold text-slate-800">
                            Frota de Veículos
                        </h2>
                        <p class="text-sm text-slate-500 mt-1">
                            Gerencie os veículos cadastrados na oficina.
                        </p>
                    </div>
                </div>

                <a href="cadastrar_veiculo.php"
                   class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700
                          text-white px-4 py-3 rounded-lg text-sm font-semibold transition">
                    <i class="fas fa-plus"></i>
                    <span class="hidden sm:inline">Novo Veículo</span>
                    <span class="sm:hidden">Novo</span>
                </a>

            </div>
        </header>

        <section class="p-5 md:p-8 max-w-7xl mx-auto">

            <?php if (isset($_GET['sucesso'])): ?>
                <div class="mb-6 flex items-center gap-3 rounded-xl border border-green-200
                            bg-green-50 p-4 text-green-700">
                    <i class="fas fa-circle-check text-lg"></i>
                    <p class="text-sm font-medium">
                        Veículo cadastrado com sucesso!
                    </p>
                </div>
            <?php endif; ?>

            <?php if ($erroConsulta): ?>
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">
                    Não foi possível carregar a frota. Tente novamente.
                </div>
            <?php endif; ?>

            <!-- Indicadores -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-7">

                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-500 font-medium">
                                Total de veículos
                            </p>
                            <p class="text-3xl font-bold text-slate-800 mt-2">
                                <?= $totalVeiculos ?>
                            </p>
                        </div>

                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600
                                    flex items-center justify-center text-xl">
                            <i class="fas fa-car-side"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-500 font-medium">
                                Veículos encontrados
                            </p>
                            <p class="text-3xl font-bold text-slate-800 mt-2">
                                <?= $totalFiltrados ?>
                            </p>
                        </div>

                        <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600
                                    flex items-center justify-center text-xl">
                            <i class="fas fa-magnifying-glass"></i>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Listagem -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

                <div class="p-5 md:p-6 border-b border-slate-100">

                    <div class="flex flex-col md:flex-row md:items-center
                                justify-between gap-4">

                        <div>
                            <h3 class="text-lg font-bold text-slate-800">
                                Veículos cadastrados
                            </h3>
                            <p class="text-sm text-slate-500 mt-1">
                                Consulte e pesquise os veículos da oficina.
                            </p>
                        </div>

                        <form method="GET" action="listar_veiculos.php"
                              class="w-full md:w-96">

                            <div class="relative">
                                <i class="fas fa-search absolute left-4 top-1/2
                                          -translate-y-1/2 text-slate-400"></i>

                                <input type="text"
                                       name="busca"
                                       value="<?= escapar($busca) ?>"
                                       placeholder="Placa, marca, modelo ou cliente..."
                                       class="search-input">

                            </div>

                            <div class="flex justify-end gap-3 mt-2">
                                <?php if ($busca !== ''): ?>
                                    <a href="listar_veiculos.php"
                                       class="text-xs text-slate-500 hover:text-blue-600">
                                        Limpar busca
                                    </a>
                                <?php endif; ?>

                                <button type="submit"
                                        class="text-xs font-semibold text-blue-600 hover:text-blue-800">
                                    Pesquisar
                                </button>
                            </div>

                        </form>

                    </div>
                </div>

                <?php if (!$erroConsulta && empty($veiculos)): ?>

                    <div class="py-16 px-6 text-center">
                        <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400
                                    flex items-center justify-center text-2xl mx-auto mb-4">
                            <i class="fas fa-car"></i>
                        </div>

                        <h4 class="text-lg font-bold text-slate-700">
                            <?= $busca !== '' ? 'Nenhum veículo encontrado' : 'Nenhum veículo cadastrado' ?>
                        </h4>

                        <p class="text-sm text-slate-500 mt-2">
                            <?= $busca !== ''
                                ? 'Tente pesquisar usando outros termos.'
                                : 'Comece cadastrando o primeiro veículo da oficina.' ?>
                        </p>

                        <?php if ($busca === ''): ?>
                            <a href="cadastrar_veiculo.php"
                               class="inline-flex items-center gap-2 mt-5 bg-blue-600
                                      hover:bg-blue-700 text-white px-5 py-3 rounded-lg
                                      text-sm font-semibold">
                                <i class="fas fa-plus"></i>
                                Cadastrar veículo
                            </a>
                        <?php endif; ?>
                    </div>

                <?php elseif (!$erroConsulta): ?>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">

                            <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
                                <tr>
                                    <th class="px-5 py-4 font-semibold">Veículo</th>
                                    <th class="px-5 py-4 font-semibold">Placa</th>
                                    <th class="px-5 py-4 font-semibold">Proprietário</th>
                                    <th class="px-5 py-4 font-semibold">Ano</th>
                                    <th class="px-5 py-4 font-semibold text-right">Ações</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100">

                                <?php foreach ($veiculos as $veiculo): ?>

                                    <tr class="table-row transition">

                                        <td class="px-5 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-lg bg-blue-50
                                                            text-blue-600 flex items-center
                                                            justify-center">
                                                    <i class="fas fa-car-side"></i>
                                                </div>

                                                <div>
                                                    <p class="font-semibold text-slate-800">
                                                        <?= escapar($veiculo['Marca']) ?>
                                                    </p>
                                                    <p class="text-xs text-slate-500 mt-1">
                                                        <?= escapar($veiculo['Modelo']) ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-5 py-4">
                                            <span class="inline-block bg-slate-100 border
                                                         border-slate-200 rounded-md px-3 py-2
                                                         text-sm font-bold tracking-wider
                                                         text-slate-700">
                                                <?= escapar($veiculo['Placa']) ?>
                                            </span>
                                        </td>

                                        <td class="px-5 py-4">
                                            <p class="text-sm text-slate-700">
                                                <?= escapar($veiculo['NomeCliente'] ?? 'Não vinculado') ?>
                                            </p>
                                        </td>

                                        <td class="px-5 py-4 text-sm text-slate-600">
                                            <?= $veiculo['AnoFabricacao']
                                                ? (int) $veiculo['AnoFabricacao']
                                                : '—' ?>
                                        </td>

                                        <td class="px-5 py-4 text-right">
                                            <span class="text-xs text-slate-400">
                                                ID #<?= (int) $veiculo['idVeiculos'] ?>
                                            </span>
                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>
                        </table>
                    </div>

                    <div class="px-5 py-4 border-t border-slate-100
                                text-xs text-slate-500">
                        Exibindo <?= $totalFiltrados ?>
                        <?= $totalFiltrados === 1 ? 'veículo' : 'veículos' ?>.
                    </div>

                <?php endif; ?>

            </div>

        </section>
    </main>
</div>

<script>
function alternarMenu() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');

    sidebar.classList.toggle('-translate-x-full');
    overlay.classList.toggle('hidden');
}
</script>

</body>
</html>
