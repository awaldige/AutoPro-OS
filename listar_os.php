<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Gera um token CSRF seguro para a sessão.
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once 'conexao.php';

function escapar($valor): string
{
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

function formatarMoeda($valor): string
{
    return 'R$ ' . number_format((float) $valor, 2, ',', '.');
}

$mensagem = '';
$tipoMensagem = '';

if (isset($_GET['sucesso'])) {
    $mensagem = 'Ordem de Serviço cadastrada com sucesso!';
    $tipoMensagem = 'sucesso';
} elseif (isset($_GET['msg']) && $_GET['msg'] === 'sucesso') {
    $mensagem = 'Ordem de Serviço atualizada com sucesso!';
    $tipoMensagem = 'sucesso';
} elseif (isset($_GET['excluido'])) {
    $mensagem = 'Ordem de Serviço excluída com sucesso!';
    $tipoMensagem = 'sucesso';
} elseif (isset($_GET['erro'])) {
    $erros = [
        'id_invalido' => 'Identificador da OS inválido.',
        'os_nao_encontrada' => 'Ordem de Serviço não encontrada.',
        'exclusao' => 'Não foi possível excluir a Ordem de Serviço.'
    ];

    $codigoErro = $_GET['erro'];

    $mensagem = $erros[$codigoErro] ?? 'Ocorreu um erro ao processar a solicitação.';
    $tipoMensagem = 'erro';
}


$busca = trim($_GET['busca'] ?? '');
$filtroStatus = trim($_GET['status'] ?? '');

$statusPermitidos = [
    'Em Aberto',
    'Em Andamento',
    'Concluída',
    'Cancelada'
];

if (!in_array($filtroStatus, $statusPermitidos, true)) {
    $filtroStatus = '';
}

$totalOS = 0;
$totalAbertas = 0;
$totalAndamento = 0;
$totalConcluidas = 0;
$faturamento = 0.0;

$erroConsulta = false;
$ordens = [];

try {
    // Indicadores gerais
    $sqlIndicadores = "
        SELECT
            COUNT(*) AS total,
            COALESCE(SUM(CASE WHEN Status = 'Em Aberto' THEN 1 ELSE 0 END), 0) AS abertas,
            COALESCE(SUM(CASE WHEN Status = 'Em Andamento' THEN 1 ELSE 0 END), 0) AS andamento,
            COALESCE(SUM(CASE WHEN Status = 'Concluída' THEN 1 ELSE 0 END), 0) AS concluidas,
            COALESCE(SUM(CASE WHEN Status != 'Cancelada' OR Status IS NULL THEN Valor ELSE 0 END), 0) AS faturamento
        FROM OrdemDeServico
    ";

    $resultadoIndicadores = $conn->query($sqlIndicadores);
    $indicadores = $resultadoIndicadores->fetch_assoc();

    $totalOS = (int) $indicadores['total'];
    $totalAbertas = (int) $indicadores['abertas'];
    $totalAndamento = (int) $indicadores['andamento'];
    $totalConcluidas = (int) $indicadores['concluidas'];
    $faturamento = (float) $indicadores['faturamento'];

    // Consulta principal
    $sql = "
        SELECT
            OS.idOrdemServico,
            OS.NumeroOS,
            OS.DataEmissao,
            OS.Valor,
            OS.Status,
            OS.DataEntrega,
            OS.Descricao,
            V.Marca,
            V.Modelo,
            V.Placa,
            C.NomeCliente
        FROM OrdemDeServico OS
        LEFT JOIN Veiculos V
            ON OS.veiculo_id = V.idVeiculos
        LEFT JOIN Clientes C
            ON V.Clientes_idClientes = C.idClientes
        WHERE 1 = 1
    ";

    $parametros = [];
    $tipos = '';

    if ($busca !== '') {
        $sql .= "
            AND (
                CAST(OS.NumeroOS AS CHAR) LIKE ?
                OR C.NomeCliente LIKE ?
                OR V.Placa LIKE ?
                OR V.Modelo LIKE ?
                OR V.Marca LIKE ?
            )
        ";

        $termo = '%' . $busca . '%';

        $parametros = [
            $termo,
            $termo,
            $termo,
            $termo,
            $termo
        ];

        $tipos = 'sssss';
    }

    if ($filtroStatus !== '') {
        $sql .= " AND OS.Status = ?";
        $parametros[] = $filtroStatus;
        $tipos .= 's';
    }

    $sql .= " ORDER BY OS.idOrdemServico DESC";

    $stmt = $conn->prepare($sql);

    if (!empty($parametros)) {
        $stmt->bind_param($tipos, ...$parametros);
    }

    $stmt->execute();

    $resultado = $stmt->get_result();

    while ($row = $resultado->fetch_assoc()) {
        $ordens[] = $row;
    }

    $stmt->close();

} catch (mysqli_sql_exception $e) {
    error_log('Erro ao consultar Ordens de Serviço: ' . $e->getMessage());
    $erroConsulta = true;
}

function classeStatus($status): string
{
    return match ($status) {
        'Em Aberto' => 'bg-amber-100 text-amber-700',
        'Em Andamento' => 'bg-blue-100 text-blue-700',
        'Concluída' => 'bg-emerald-100 text-emerald-700',
        'Cancelada' => 'bg-rose-100 text-rose-700',
        default => 'bg-slate-100 text-slate-600'
    };
}

function iconeStatus($status): string
{
    return match ($status) {
        'Em Aberto' => 'fa-clock',
        'Em Andamento' => 'fa-gears',
        'Concluída' => 'fa-circle-check',
        'Cancelada' => 'fa-circle-xmark',
        default => 'fa-circle-info'
    };
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg?v=2">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Ordens de Serviço | AutoPro OS</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .card-indicador {
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .card-indicador:hover {
            transform: translateY(-3px);
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900">

<div class="min-h-screen flex">

    <!-- Menu lateral -->
    <aside id="sidebar"
           class="fixed inset-y-0 left-0 z-40 w-72 bg-slate-900 text-white p-7
                  flex flex-col gap-8 shadow-2xl transform -translate-x-full
                  transition-transform duration-300 lg:translate-x-0 lg:static">

        <div class="flex items-center gap-3">
            <div class="bg-blue-600 p-3 rounded-xl shadow-lg shadow-blue-500/20">
                <i class="fas fa-car-side text-xl"></i>
            </div>

            <div>
                <h2 class="text-2xl font-black tracking-tight">AUTOPRO</h2>
                <p class="text-[9px] text-slate-400 font-bold tracking-[0.3em]">
                    SISTEMA DE GESTÃO
                </p>
            </div>
        </div>

        <nav class="flex flex-col gap-2">

            <a href="index.php"
               class="flex items-center gap-4 p-4 rounded-xl text-slate-400 hover:bg-slate-800 transition">
                <i class="fas fa-chart-pie w-5 text-center"></i>
                Painel
            </a>

            <a href="listar_os.php"
               class="flex items-center gap-4 p-4 rounded-xl bg-blue-600 text-white font-bold shadow-lg shadow-blue-600/20">
                <i class="fas fa-file-invoice w-5 text-center"></i>
                Ordens de Serviço
            </a>

            <a href="listar_clientes.php"
               class="flex items-center gap-4 p-4 rounded-xl text-slate-400 hover:bg-slate-800 transition">
                <i class="fas fa-users w-5 text-center"></i>
                Clientes
            </a>

            <a href="listar_veiculos.php"
               class="flex items-center gap-4 p-4 rounded-xl text-slate-400 hover:bg-slate-800 transition">
                <i class="fas fa-car w-5 text-center"></i>
                Veículos
            </a>

        </nav>

        <div class="mt-auto p-5 bg-slate-800/60 rounded-2xl border border-slate-700">
            <p class="text-[10px] uppercase font-black tracking-widest text-slate-500 mb-2">
                AW TECHNOLOGY
            </p>

            <p class="text-xs text-slate-300 font-semibold">
                AutoPro OS
            </p>

            <p class="text-[10px] text-slate-500 mt-1">
                Sistema demonstrativo
            </p>
        </div>

    </aside>

    <!-- Fundo do menu mobile -->
    <div id="overlay"
         class="fixed inset-0 bg-slate-950/50 z-30 hidden lg:hidden">
    </div>

    <!-- Conteúdo -->
    <main class="flex-1 min-w-0 p-5 md:p-8 xl:p-12">

        <!-- Cabeçalho -->
        <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-5 mb-9">

            <div class="flex items-center gap-4">

                <button id="menuButton"
                        type="button"
                        class="lg:hidden bg-white border border-slate-200 rounded-xl p-3 text-slate-700">
                    <i class="fas fa-bars"></i>
                </button>

                <div>
                    <p class="text-blue-600 text-xs font-black uppercase tracking-[0.2em] mb-2">
                        Gestão operacional
                    </p>

                    <h1 class="text-3xl md:text-4xl font-black tracking-tight">
                        Ordens de Serviço
                    </h1>

                    <p class="text-slate-500 text-sm mt-2">
                        Acompanhe os atendimentos e serviços da oficina.
                    </p>
                </div>

            </div>

            <a href="nova_os.php"
               class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white
                      px-6 py-4 rounded-2xl font-bold shadow-lg shadow-blue-200
                      transition flex items-center justify-center gap-3">

                <i class="fas fa-plus-circle"></i>
                Abrir Nova OS

            </a>

        </header>

        <!-- Mensagem -->
        <?php if ($mensagem !== ''): ?>

            <div class="mb-7 p-4 rounded-2xl border flex items-center gap-3
                <?= $tipoMensagem === 'sucesso'
                    ? 'bg-emerald-50 border-emerald-200 text-emerald-700'
                    : 'bg-rose-50 border-rose-200 text-rose-700' ?>">

                <i class="fas fa-circle-check"></i>

                <span class="font-semibold text-sm">
                    <?= escapar($mensagem) ?>
                </span>

            </div>

        <?php endif; ?>

        <!-- Indicadores -->
        <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

            <div class="card-indicador bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
                <div class="flex justify-between items-center mb-5">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Total de OS
                    </span>

                    <div class="w-11 h-11 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-invoice text-lg"></i>
                    </div>
                </div>

                <p class="text-3xl font-black text-slate-900">
                    <?= $totalOS ?>
                </p>

                <p class="text-xs text-slate-400 mt-2">Ordens cadastradas</p>
            </div>

            <div class="card-indicador bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
                <div class="flex justify-between items-center mb-5">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Em aberto
                    </span>

                    <div class="w-11 h-11 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clock text-lg"></i>
                    </div>
                </div>

                <p class="text-3xl font-black text-amber-600">
                    <?= $totalAbertas ?>
                </p>

                <p class="text-xs text-slate-400 mt-2">Aguardando atendimento</p>
            </div>

            <div class="card-indicador bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
                <div class="flex justify-between items-center mb-5">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Em andamento
                    </span>

                    <div class="w-11 h-11 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-gears text-lg"></i>
                    </div>
                </div>

                <p class="text-3xl font-black text-blue-600">
                    <?= $totalAndamento ?>
                </p>

                <p class="text-xs text-slate-400 mt-2">Serviços em execução</p>
            </div>

            <div class="card-indicador bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
                <div class="flex justify-between items-center mb-5">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Valor das OS
                    </span>

                    <div class="w-11 h-11 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-wallet text-lg"></i>
                    </div>
                </div>

                <p class="text-2xl font-black text-emerald-600">
                    <?= escapar(formatarMoeda($faturamento)) ?>
                </p>

                <p class="text-xs text-slate-400 mt-2">
                    Exceto ordens canceladas
                </p>
            </div>

        </section>

        <!-- Busca e filtros -->
        <section class="bg-white border border-slate-100 rounded-3xl p-5 md:p-6 shadow-sm mb-7">

            <form method="GET" action="listar_os.php"
                  class="flex flex-col lg:flex-row gap-4">

                <div class="relative flex-1">

                    <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-400"></i>

                    <input type="search"
                           name="busca"
                           value="<?= escapar($busca) ?>"
                           placeholder="Buscar por OS, cliente, placa, marca ou modelo..."
                           class="w-full pl-12 pr-5 py-4 bg-slate-50 border border-slate-200
                                  rounded-2xl outline-none focus:border-blue-500
                                  focus:ring-4 focus:ring-blue-500/10 transition text-sm">

                </div>

                <div class="lg:w-64">

                    <select name="status"
                            class="w-full py-4 px-4 bg-slate-50 border border-slate-200
                                   rounded-2xl outline-none focus:border-blue-500 text-sm font-semibold">

                        <option value="">Todos os status</option>

                        <?php foreach ($statusPermitidos as $status): ?>

                            <option value="<?= escapar($status) ?>"
                                <?= $filtroStatus === $status ? 'selected' : '' ?>>

                                <?= escapar($status) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <button type="submit"
                        class="bg-slate-900 hover:bg-blue-700 text-white px-7 py-4
                               rounded-2xl font-bold transition">

                    <i class="fas fa-filter mr-2"></i>
                    Filtrar

                </button>

                <?php if ($busca !== '' || $filtroStatus !== ''): ?>

                    <a href="listar_os.php"
                       class="px-5 py-4 bg-slate-100 hover:bg-slate-200 text-slate-600
                              rounded-2xl font-bold text-center transition">

                        Limpar

                    </a>

                <?php endif; ?>

            </form>

        </section>

        <!-- Tabela -->
        <section class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">

            <div class="p-6 md:p-7 border-b border-slate-100 flex flex-col sm:flex-row justify-between gap-3">

                <div>
                    <h2 class="text-lg font-black text-slate-900">
                        Histórico de atendimentos
                    </h2>

                    <p class="text-xs text-slate-400 mt-1">
                        <?= count($ordens) ?> resultado(s) encontrado(s)
                    </p>
                </div>

                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                    <i class="fas fa-database mr-1"></i>
                    Banco de dados
                </div>

            </div>

            <?php if ($erroConsulta): ?>

                <div class="p-12 text-center">

                    <i class="fas fa-triangle-exclamation text-4xl text-rose-400 mb-4"></i>

                    <h3 class="text-lg font-black text-slate-800">
                        Não foi possível carregar as Ordens de Serviço
                    </h3>

                    <p class="text-sm text-slate-500 mt-2">
                        Verifique a conexão com o banco de dados e tente novamente.
                    </p>

                </div>

            <?php elseif (empty($ordens)): ?>

                <div class="p-12 md:p-20 text-center">

                    <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-inbox text-3xl text-slate-300"></i>
                    </div>

                    <h3 class="text-xl font-black text-slate-800">
                        Nenhuma ordem encontrada
                    </h3>

                    <p class="text-sm text-slate-400 mt-2">
                        <?php if ($busca !== '' || $filtroStatus !== ''): ?>
                            Tente alterar os filtros ou limpar a pesquisa.
                        <?php else: ?>
                            Cadastre a primeira Ordem de Serviço para começar.
                        <?php endif; ?>
                    </p>

                    <?php if ($busca === '' && $filtroStatus === ''): ?>

                        <a href="nova_os.php"
                           class="inline-flex items-center gap-2 mt-6 bg-blue-600
                                  hover:bg-blue-700 text-white px-6 py-3 rounded-xl
                                  font-bold transition">

                            <i class="fas fa-plus"></i>
                            Nova Ordem de Serviço

                        </a>

                    <?php endif; ?>

                </div>

            <?php else: ?>

                <div class="overflow-x-auto">

                    <table class="w-full text-left">

                        <thead class="bg-slate-50">

                            <tr>
                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">
                                    Ordem / Emissão
                                </th>

                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">
                                    Cliente / Veículo
                                </th>

                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">
                                    Status
                                </th>

                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">
                                    Valor
                                </th>

                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">
                                    Ações
                                </th>
                            </tr>

                        </thead>

                        <tbody class="divide-y divide-slate-100">

                            <?php foreach ($ordens as $row): ?>

                                <?php
                                    $id = (int) $row['idOrdemServico'];
                                    $status = $row['Status'] ?? '';
                                    $cliente = $row['NomeCliente'] ?? 'Cliente não vinculado';

                                    $modelo = trim(
                                        ($row['Marca'] ?? '') . ' ' . ($row['Modelo'] ?? '')
                                    );

                                    $veiculo = $modelo !== ''
                                        ? $modelo . ' • ' . ($row['Placa'] ?? 'Sem placa')
                                        : 'Veículo não vinculado';
                                ?>

                                <tr class="hover:bg-blue-50/40 transition">

                                    <td class="px-6 py-6 whitespace-nowrap">

                                        <span class="block text-xl font-black text-blue-600">
                                            #<?= escapar($row['NumeroOS']) ?>
                                        </span>

                                        <span class="text-[11px] text-slate-400 font-semibold">
                                            <i class="far fa-calendar-alt mr-1"></i>

                                            <?= !empty($row['DataEmissao'])
                                                ? escapar(date('d/m/Y', strtotime($row['DataEmissao'])))
                                                : 'Data não informada' ?>
                                        </span>

                                    </td>

                                    <td class="px-6 py-6 min-w-56">

                                        <span class="block font-bold text-slate-800">
                                            <?= escapar($cliente) ?>
                                        </span>

                                        <span class="inline-flex items-center gap-2 mt-2
                                                     bg-slate-100 px-3 py-1.5 rounded-lg
                                                     text-[10px] font-bold text-slate-500">

                                            <i class="fas fa-car-side text-blue-500"></i>

                                            <?= escapar($veiculo) ?>

                                        </span>

                                    </td>

                                    <td class="px-6 py-6 whitespace-nowrap">

                                        <span class="inline-flex items-center gap-2 px-3 py-2
                                                     rounded-xl text-[10px] font-black uppercase
                                                     <?= classeStatus($status) ?>">

                                            <i class="fas <?= escapar(iconeStatus($status)) ?>"></i>

                                            <?= escapar($status !== '' ? $status : 'Não informado') ?>

                                        </span>

                                    </td>

                                    <td class="px-6 py-6 whitespace-nowrap">

                                        <span class="font-black text-slate-900">
                                            <?= escapar(formatarMoeda($row['Valor'])) ?>
                                        </span>

                                    </td>

                                    <td class="px-6 py-6">

                                        <div class="flex justify-end items-center gap-2">

                                            <a href="imprimir_os.php?id=<?= $id ?>"
                                               title="Imprimir OS"
                                               class="w-10 h-10 rounded-xl bg-slate-100
                                                      text-slate-600 hover:bg-slate-900
                                                      hover:text-white flex items-center
                                                      justify-center transition">

                                                <i class="fas fa-print"></i>

                                            </a>

                                            <a href="editar_os.php?id=<?= $id ?>"
                                               title="Editar OS"
                                               class="w-10 h-10 rounded-xl bg-blue-50
                                                      text-blue-600 hover:bg-blue-600
                                                      hover:text-white flex items-center
                                                      justify-center transition">

                                                <i class="fas fa-pen"></i>

                                            </a>

                                            <form action="excluir_os.php"
                                                  method="POST"
                                                  onsubmit="return confirm('Deseja realmente excluir esta Ordem de Serviço? Os itens vinculados também serão removidos.');">

                                                <input type="hidden" name="id" value="<?= $id ?>">
                                                <input type="hidden" name="csrf_token"
                                                value="<?= escapar($_SESSION['csrf_token']) ?>">

                                                <button type="submit"
                                                        title="Excluir OS"
                                                        class="w-10 h-10 rounded-xl bg-rose-50
                                                               text-rose-500 hover:bg-rose-500
                                                               hover:text-white flex items-center
                                                               justify-center transition">

                                                    <i class="fas fa-trash-alt"></i>

                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

        </section>

        <!-- Rodapé -->
        <footer class="mt-10 flex flex-col sm:flex-row justify-between gap-3 text-[10px]
                       font-bold uppercase tracking-widest text-slate-400">

            <span>© <?= date('Y') ?> AutoPro OS</span>

            <span>Desenvolvido por AW TECHNOLOGY</span>

        </footer>

    </main>

</div>

<script>
    const menuButton = document.getElementById('menuButton');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');

    function abrirMenu() {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    }

    function fecharMenu() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }

    if (menuButton) {
        menuButton.addEventListener('click', abrirMenu);
    }

    if (overlay) {
        overlay.addEventListener('click', fecharMenu);
    }
</script>

</body>
</html>
