<?php
require_once 'conexao.php';

function escapar($valor): string
{
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

function consultarTotal(mysqli $conn, string $sql): int
{
    $resultado = $conn->query($sql);
    return (int) ($resultado->fetch_assoc()['total'] ?? 0);
}

$totalOS = consultarTotal(
    $conn,
    "SELECT COUNT(*) AS total FROM OrdemDeServico"
);

$pendentes = consultarTotal(
    $conn,
    "SELECT COUNT(*) AS total FROM OrdemDeServico
     WHERE Status IS NULL OR Status <> 'Concluída'"
);

$totalAtrasadas = consultarTotal(
    $conn,
    "SELECT COUNT(*) AS total FROM OrdemDeServico
     WHERE Status <> 'Concluída'
     AND Status IS NOT NULL
     AND DataEntrega IS NOT NULL
     AND DataEntrega < CURDATE()"
);

$totalClientes = consultarTotal(
    $conn,
    "SELECT COUNT(*) AS total FROM Clientes"
);

$resultadoFaturamento = $conn->query(
    "SELECT COALESCE(SUM(Valor), 0) AS total
     FROM OrdemDeServico
     WHERE Status = 'Concluída'"
);

$faturamento = (float) ($resultadoFaturamento->fetch_assoc()['total'] ?? 0);

$sql = "
    SELECT
        OS.idOrdemServico,
        OS.NumeroOS,
        OS.DataEntrega,
        OS.Valor,
        OS.Status,
        C.NomeCliente,
        V.Modelo,
        V.Marca
    FROM OrdemDeServico OS
    LEFT JOIN Veiculos V ON OS.veiculo_id = V.idVeiculos
    LEFT JOIN Clientes C ON V.Clientes_idClientes = C.idClientes
    ORDER BY OS.idOrdemServico DESC
    LIMIT 8
";

$res = $conn->query($sql);
$ordens = $res->fetch_all(MYSQLI_ASSOC);

$conn->close();

function estaAtrasada(?string $dataEntrega, ?string $status): bool
{
    return !empty($dataEntrega)
        && $dataEntrega < date('Y-m-d')
        && $status !== 'Concluída';
}

function formatarData(?string $data): string
{
    if (empty($data)) {
        return 'Não definida';
    }

    $timestamp = strtotime($data);

    return $timestamp ? date('d/m/Y', $timestamp) : 'Não definida';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg?v=2">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f172a">
    <meta name="description" content="AutoPro OS - Sistema de gerenciamento de ordens de serviço para oficinas.">
    <title>Dashboard | AutoPro OS</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: #f8fafc;
        }

        .sidebar {
            width: 260px;
            min-width: 260px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 12px;
            color: #94a3b8;
            font-size: 13px;
            font-weight: 600;
            transition: .2s ease;
        }

        .nav-link:hover {
            background: #1e293b;
            color: #fff;
        }

        .nav-link.active {
            background: #2563eb;
            color: #fff;
            box-shadow: 0 5px 15px rgba(37, 99, 235, .22);
        }

        .nav-link i {
            width: 20px;
            text-align: center;
        }

        .stat-card {
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(15, 23, 42, .07);
        }

        .mobile-overlay {
            display: none;
        }

        @media (max-width: 767px) {
            .sidebar {
                position: fixed;
                inset: 0 auto 0 0;
                z-index: 50;
                transform: translateX(-100%);
                transition: transform .25s ease;
                height: 100dvh;
                overflow-y: auto;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .mobile-overlay.open {
                display: block;
                position: fixed;
                inset: 0;
                z-index: 40;
                background: rgba(15, 23, 42, .55);
            }
        }
    </style>
</head>

<body class="min-h-screen">

<div id="mobileOverlay" class="mobile-overlay" onclick="fecharMenu()"></div>

<div class="min-h-screen flex">

    <aside id="sidebar" class="sidebar bg-slate-900 text-white flex flex-col p-5">

        <div class="flex items-center justify-between border-b border-slate-700 pb-6 mb-8">
            <a href="index.php" class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-950/40">
                    <i class="fas fa-screwdriver-wrench text-xl"></i>
                </div>

                <div>
                    <div class="text-xl font-extrabold tracking-tight">
                        AUTOPRO <span class="text-blue-400">OS</span>
                    </div>
                    <div class="text-[10px] text-slate-400 uppercase tracking-[.2em] font-bold">
                        Gestão de oficinas
                    </div>
                </div>
            </a>

            <button type="button" onclick="fecharMenu()" class="md:hidden text-slate-400 hover:text-white p-2" aria-label="Fechar menu">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <div class="text-[10px] uppercase tracking-[.2em] font-bold text-slate-500 px-3 mb-3">
            Menu principal
        </div>

        <nav class="space-y-2">
            <a href="index.php" class="nav-link active" aria-current="page">
                <i class="fas fa-chart-pie"></i>
                Dashboard
            </a>

            <a href="listar_clientes.php" class="nav-link">
                <i class="fas fa-users"></i>
                Clientes
            </a>

            <a href="listar_veiculos.php" class="nav-link">
                <i class="fas fa-car-side"></i>
                Veículos
            </a>

            <a href="listar_os.php" class="nav-link">
                <i class="fas fa-clipboard-list"></i>
                Ordens de serviço
            </a>
        </nav>

        <div class="mt-auto pt-8">
            <div class="bg-slate-800 rounded-xl p-4 border border-slate-700">
                <div class="flex items-center gap-2 text-blue-400 mb-2">
                    <i class="fas fa-shield-halved"></i>
                    <span class="text-xs font-bold">AutoPro OS</span>
                </div>
                <p class="text-[11px] leading-relaxed text-slate-400">
                    Painel de gerenciamento de serviços e operações da oficina.
                </p>
            </div>

            <div class="text-center text-[10px] text-slate-600 mt-5">
                Sistema demonstrativo
            </div>
        </div>

    </aside>

    <main class="flex-1 min-w-0">

        <header class="bg-white border-b border-slate-200 sticky top-0 z-30">
            <div class="px-5 md:px-8 py-4 flex items-center justify-between gap-4">

                <div class="flex items-center gap-3">
                    <button type="button" onclick="abrirMenu()" class="md:hidden w-10 h-10 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200" aria-label="Abrir menu">
                        <i class="fas fa-bars"></i>
                    </button>

                    <div>
                        <h1 class="text-lg md:text-xl font-extrabold text-slate-800">
                            Dashboard
                        </h1>
                        <p class="text-xs text-slate-500 hidden sm:block">
                            Visão geral da sua oficina
                        </p>
                    </div>
                </div>

                <a href="nova_os.php" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 md:px-5 py-3 rounded-xl font-bold text-xs shadow-lg shadow-blue-100 transition">
                    <i class="fas fa-plus"></i>
                    <span class="hidden sm:inline">Nova ordem de serviço</span>
                    <span class="sm:hidden">Nova OS</span>
                </a>

            </div>
        </header>

        <div class="p-5 md:p-8 max-w-[1600px] mx-auto">

            <section class="mb-8">
                <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-3">
                    <div>
                        <div class="text-xs font-bold text-blue-600 uppercase tracking-[.18em] mb-2">
                            Visão geral
                        </div>
                        <h2 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">
                            Bem-vindo ao AutoPro OS
                        </h2>
                        <p class="text-sm text-slate-500 mt-2">
                            Acompanhe os principais indicadores e serviços da oficina.
                        </p>
                    </div>

                    <div class="text-xs text-slate-400 font-medium">
                        <i class="far fa-calendar-alt mr-1"></i>
                        <?= date('d/m/Y') ?>
                    </div>
                </div>
            </section>

            <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

                <article class="stat-card bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                                Total de OS
                            </p>
                            <h3 class="text-3xl font-extrabold text-slate-800 mt-3">
                                <?= $totalOS ?>
                            </h3>
                            <p class="text-xs text-slate-400 mt-2">Ordens cadastradas</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                    </div>
                </article>

                <article class="stat-card bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                                OS pendentes
                            </p>
                            <h3 class="text-3xl font-extrabold text-slate-800 mt-3">
                                <?= $pendentes ?>
                            </h3>
                            <p class="text-xs text-slate-400 mt-2">Aguardando conclusão</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </article>

                <article class="stat-card bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                                Faturamento
                            </p>
                            <h3 class="text-2xl font-extrabold text-slate-800 mt-3">
                                R$ <?= number_format($faturamento, 2, ',', '.') ?>
                            </h3>
                            <p class="text-xs text-slate-400 mt-2">Serviços concluídos</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </article>

                <article class="stat-card bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                                Clientes
                            </p>
                            <h3 class="text-3xl font-extrabold text-slate-800 mt-3">
                                <?= $totalClientes ?>
                            </h3>
                            <p class="text-xs text-slate-400 mt-2">Clientes cadastrados</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center text-lg">
                            <i class="fas fa-user-group"></i>
                        </div>
                    </div>
                </article>

            </section>

            <section class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden mb-8">

                <div class="p-5 md:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-6 bg-red-500 rounded-full"></span>
                            <h2 class="text-base font-extrabold text-slate-800">
                                Acompanhamento de prazos
                            </h2>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 ml-3">
                            Ordens de serviço fora do prazo previsto
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="bg-red-50 border border-red-100 rounded-xl px-4 py-2 flex items-center gap-3">
                            <div class="w-9 h-9 bg-red-100 text-red-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-triangle-exclamation"></i>
                            </div>
                            <div>
                                <div class="text-xl font-extrabold text-red-600 leading-none">
                                    <?= $totalAtrasadas ?>
                                </div>
                                <div class="text-[10px] text-red-500 font-bold uppercase tracking-wider mt-1">
                                    Atrasadas
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </section>

            <section class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

                <div class="p-5 md:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-6 bg-blue-600 rounded-full"></span>
                            <h2 class="text-base font-extrabold text-slate-800">
                                Atividades recentes
                            </h2>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 ml-3">
                            Últimas ordens de serviço cadastradas
                        </p>
                    </div>

                    <a href="listar_os.php" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 text-xs font-bold text-slate-600 transition">
                        Ver todas as OS
                        <i class="fas fa-arrow-right text-[10px]"></i>
                    </a>
                </div>

                <?php if (empty($ordens)): ?>

                    <div class="px-6 py-16 text-center">
                        <div class="w-16 h-16 mx-auto rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mb-4">
                            <i class="fas fa-clipboard-list"></i>
                        </div>

                        <h3 class="font-bold text-slate-700">
                            Nenhuma ordem de serviço encontrada
                        </h3>

                        <p class="text-sm text-slate-500 mt-2 max-w-sm mx-auto">
                            Quando uma nova ordem de serviço for cadastrada, ela aparecerá nesta área.
                        </p>

                        <a href="nova_os.php" class="inline-flex items-center gap-2 mt-5 px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition">
                            <i class="fas fa-plus"></i>
                            Cadastrar primeira OS
                        </a>
                    </div>

                <?php else: ?>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 text-[10px] uppercase tracking-wider text-slate-500">
                                <tr>
                                    <th class="px-5 md:px-6 py-4 font-bold">Cliente / OS</th>
                                    <th class="px-5 md:px-6 py-4 font-bold">Veículo</th>
                                    <th class="px-5 md:px-6 py-4 font-bold">Status</th>
                                    <th class="px-5 md:px-6 py-4 font-bold">Entrega</th>
                                    <th class="px-5 md:px-6 py-4 font-bold">Valor</th>
                                    <th class="px-5 md:px-6 py-4 font-bold text-right">Ações</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100">

                                <?php foreach ($ordens as $row): ?>
                                    <?php
                                    $atrasada = estaAtrasada(
                                        $row['DataEntrega'],
                                        $row['Status']
                                    );

                                    $status = $row['Status'] ?: 'Não informado';

                                    $inicial = mb_strtoupper(
                                        mb_substr($row['NomeCliente'] ?? 'C', 0, 1, 'UTF-8'),
                                        'UTF-8'
                                    );
                                    ?>

                                    <tr class="hover:bg-slate-50 transition">

                                        <td class="px-5 md:px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 shrink-0 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-extrabold text-sm">
                                                    <?= escapar($inicial) ?>
                                                </div>

                                                <div>
                                                    <p class="text-sm font-bold text-slate-800">
                                                        <?= escapar($row['NomeCliente'] ?? 'Consumidor') ?>
                                                    </p>
                                                    <p class="text-[11px] font-semibold text-blue-600 mt-1">
                                                        OS #<?= escapar($row['NumeroOS']) ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-5 md:px-6 py-4">
                                            <div class="text-sm font-semibold text-slate-700">
                                                <?= escapar(trim(($row['Marca'] ?? '') . ' ' . ($row['Modelo'] ?? ''))) ?: 'Sem veículo' ?>
                                            </div>
                                        </td>

                                        <td class="px-5 md:px-6 py-4">
                                            <?php if ($atrasada): ?>
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-red-50 text-red-600 border border-red-100 text-[10px] font-extrabold">
                                                    <i class="fas fa-exclamation-circle"></i>
                                                    ATRASADA
                                                </span>
                                            <?php elseif ($status === 'Concluída'): ?>
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100 text-[10px] font-bold">
                                                    <i class="fas fa-check-circle"></i>
                                                    CONCLUÍDA
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-100 text-[10px] font-bold">
                                                    <i class="fas fa-clock"></i>
                                                    <?= escapar(mb_strtoupper($status, 'UTF-8')) ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="px-5 md:px-6 py-4 text-xs font-semibold text-slate-600">
                                            <?= escapar(formatarData($row['DataEntrega'])) ?>
                                        </td>

                                        <td class="px-5 md:px-6 py-4">
                                            <span class="text-sm font-extrabold text-slate-800">
                                                R$ <?= number_format((float) $row['Valor'], 2, ',', '.') ?>
                                            </span>
                                        </td>

                                        <td class="px-5 md:px-6 py-4">
                                            <div class="flex justify-end gap-2">
                                                <a
                                                    href="imprimir_os.php?id=<?= (int) $row['idOrdemServico'] ?>"
                                                    class="w-9 h-9 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:text-blue-600 hover:border-blue-300 transition"
                                                    title="Imprimir ordem de serviço"
                                                    aria-label="Imprimir OS"
                                                >
                                                    <i class="fas fa-print text-xs"></i>
                                                </a>

                                                <a
                                                    href="editar_os.php?id=<?= (int) $row['idOrdemServico'] ?>"
                                                    class="w-9 h-9 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:text-blue-600 hover:border-blue-300 transition"
                                                    title="Editar ordem de serviço"
                                                    aria-label="Editar OS"
                                                >
                                                    <i class="fas fa-pen text-xs"></i>
                                                </a>
                                            </div>
                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>
                        </table>
                    </div>

                <?php endif; ?>

            </section>

            <footer class="text-center text-xs text-slate-400 py-7">
                AutoPro OS &copy; <?= date('Y') ?> · Sistema demonstrativo de gestão de oficinas
            </footer>

        </div>

    </main>

</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('mobileOverlay');

    function abrirMenu() {
        sidebar.classList.add('open');
        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function fecharMenu() {
        sidebar.classList.remove('open');
        overlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            fecharMenu();
        }
    });

    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768) {
            fecharMenu();
        }
    });
</script>

</body>
</html>