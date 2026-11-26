
<?php
require_once 'conexao.php';

function escapar($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

$clientes = [];
$totalClientes = 0;
$erro = '';

$busca = trim($_GET['busca'] ?? '');

try {
    $consultaTotal = $conn->query("SELECT COUNT(*) AS total FROM Clientes");
    $totalClientes = (int) $consultaTotal->fetch_assoc()['total'];

    if ($busca !== '') {
        $sql = "SELECT idClientes, NomeCliente, Telefone, Endereco
                FROM Clientes
                WHERE NomeCliente LIKE ?
                   OR Telefone LIKE ?
                   OR Endereco LIKE ?
                ORDER BY idClientes DESC";

        $stmt = $conn->prepare($sql);
        $termo = '%' . $busca . '%';
        $stmt->bind_param('sss', $termo, $termo, $termo);
        $stmt->execute();

        $resultado = $stmt->get_result();

        while ($row = $resultado->fetch_assoc()) {
            $clientes[] = $row;
        }

        $stmt->close();
    } else {
        $sql = "SELECT idClientes, NomeCliente, Telefone, Endereco
                FROM Clientes
                ORDER BY idClientes DESC";

        $resultado = $conn->query($sql);

        while ($row = $resultado->fetch_assoc()) {
            $clientes[] = $row;
        }
    }
} catch (mysqli_sql_exception $e) {
    error_log('Erro ao consultar clientes: ' . $e->getMessage());
    $erro = 'Não foi possível carregar a lista de clientes.';
}

$quantidadeExibida = count($clientes);

$cadastroSucesso = isset($_GET['sucesso']) && $_GET['sucesso'] === '1';
$edicaoSucesso = isset($_GET['sucesso_edicao']) && $_GET['sucesso_edicao'] === '1';
$mensagemErro = $_GET['erro'] ?? '';

$mensagensErro = [
    'id_invalido' => 'Identificador do cliente inválido.',
    'cliente_nao_encontrado' => 'O cliente informado não foi encontrado.',
    'nome_obrigatorio' => 'Informe o nome completo do cliente.',
    'limite_caracteres' => 'Verifique os limites de caracteres dos campos.',
    'falha_atualizacao' => 'Não foi possível atualizar o cliente. Tente novamente.'
];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg?v=2">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f172a">

    <title>Clientes - AutoPro OS</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="min-h-screen bg-slate-50 text-slate-800">

<div class="min-h-screen md:flex">

    <aside class="w-full bg-slate-900 text-white md:fixed md:inset-y-0 md:left-0 md:w-64">

        <div class="flex items-center justify-between border-b border-slate-700 px-6 py-5 md:mb-8">
            <a href="index.php" class="flex items-center gap-3">
                <i class="fas fa-tools text-3xl text-blue-400"></i>
                <span class="text-xl font-black tracking-tight">
                    AUTOPRO <span class="text-blue-400">OS</span>
                </span>
            </a>

            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 md:hidden">
                Clientes
            </span>
        </div>

        <nav class="flex gap-2 overflow-x-auto px-4 pb-4 md:flex-col md:space-y-2 md:px-5">

            <a href="index.php"
               class="flex shrink-0 items-center gap-3 rounded-xl px-4 py-3 text-slate-300 transition hover:bg-slate-800 hover:text-white">
                <i class="fas fa-chart-line w-5 text-center"></i>
                <span>Dashboard</span>
            </a>

            <a href="listar_clientes.php"
               class="flex shrink-0 items-center gap-3 rounded-xl bg-blue-600 px-4 py-3 font-semibold text-white shadow-lg shadow-blue-950/20">
                <i class="fas fa-users w-5 text-center"></i>
                <span>Clientes</span>
            </a>

            <a href="cadastrar_veiculo.php"
               class="flex shrink-0 items-center gap-3 rounded-xl px-4 py-3 text-slate-300 transition hover:bg-slate-800 hover:text-white">
                <i class="fas fa-car w-5 text-center"></i>
                <span>Veículos</span>
            </a>

        </nav>
    </aside>

    <main class="min-w-0 flex-1 p-5 md:ml-64 md:p-8 lg:p-10">

        <div class="mx-auto max-w-7xl">

            <header class="mb-8 flex flex-col justify-between gap-5 sm:flex-row sm:items-center">

                <div>
                    <p class="mb-2 text-sm font-semibold uppercase tracking-wider text-blue-600">
                        Administração
                    </p>

                    <h1 class="text-2xl font-extrabold tracking-tight text-slate-800 md:text-3xl">
                        Gestão de Clientes
                    </h1>

                    <p class="mt-2 text-sm text-slate-500">
                        Visualize e consulte os proprietários cadastrados no sistema.
                    </p>
                </div>

                <a href="cadastrar_cliente.php"
                   class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 py-3 font-bold text-white shadow-lg shadow-blue-100 transition hover:bg-blue-700">
                    <i class="fas fa-user-plus"></i>
                    Novo Cliente
                </a>

            </header>

            <?php if ($cadastroSucesso): ?>
                <div class="mb-6 flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 p-4 text-green-700"
                     role="status">
                    <i class="fas fa-circle-check text-lg"></i>
                    <p class="text-sm font-semibold">
                        Cliente cadastrado com sucesso!
                    </p>
                </div>
            <?php endif; ?>

            <?php if ($edicaoSucesso): ?>
                <div class="mb-6 flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 p-4 text-green-700"
                     role="status">
                    <i class="fas fa-circle-check text-lg"></i>
                    <p class="text-sm font-semibold">
                        Cliente atualizado com sucesso!
                    </p>
                </div>
            <?php endif; ?>

            <?php if (isset($mensagensErro[$mensagemErro])): ?>
                <div class="mb-6 flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-red-700"
                     role="alert">
                    <i class="fas fa-circle-exclamation text-lg"></i>
                    <p class="text-sm font-semibold">
                        <?php echo escapar($mensagensErro[$mensagemErro]); ?>
                    </p>
                </div>
            <?php endif; ?>

            <?php if ($erro !== ''): ?>
                <div class="mb-6 flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-red-700"
                     role="alert">
                    <i class="fas fa-circle-exclamation text-lg"></i>
                    <p class="text-sm font-semibold">
                        <?php echo escapar($erro); ?>
                    </p>
                </div>
            <?php endif; ?>

            <section class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-sm font-medium text-slate-500">
                                Total de clientes
                            </p>

                            <h2 class="mt-2 text-3xl font-extrabold text-slate-800">
                                <?php echo $totalClientes; ?>
                            </h2>

                            <p class="mt-1 text-xs text-slate-400">
                                Registros na base de dados
                            </p>
                        </div>

                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                            <i class="fas fa-users text-xl"></i>
                        </div>

                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-sm font-medium text-slate-500">
                                Clientes encontrados
                            </p>

                            <h2 class="mt-2 text-3xl font-extrabold text-slate-800">
                                <?php echo $quantidadeExibida; ?>
                            </h2>

                            <p class="mt-1 text-xs text-slate-400">
                                Resultado da consulta atual
                            </p>
                        </div>

                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600">
                            <i class="fas fa-search text-xl"></i>
                        </div>

                    </div>
                </div>

            </section>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="flex flex-col justify-between gap-4 border-b border-slate-100 p-5 md:flex-row md:items-center md:px-6">

                    <div>
                        <h2 class="font-bold text-slate-800">
                            Lista de clientes
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Consulte os dados cadastrados.
                        </p>
                    </div>

                    <form action="listar_clientes.php" method="GET"
                          class="flex w-full gap-2 md:max-w-md">

                        <div class="relative min-w-0 flex-1">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>

                            <input
                                type="search"
                                name="busca"
                                value="<?php echo escapar($busca); ?>"
                                placeholder="Buscar cliente..."
                                class="w-full rounded-xl border border-slate-200 py-3 pl-11 pr-4 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            >
                        </div>

                        <button type="submit"
                                class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:bg-slate-700">
                            Buscar
                        </button>

                    </form>

                </div>

                <?php if ($busca !== ''): ?>
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 bg-slate-50 px-5 py-3 text-sm">

                        <p class="text-slate-500">
                            Resultados para:
                            <strong class="text-slate-700"><?php echo escapar($busca); ?></strong>
                        </p>

                        <a href="listar_clientes.php"
                           class="font-semibold text-blue-600 hover:text-blue-700">
                            Limpar busca
                        </a>

                    </div>
                <?php endif; ?>

                <?php if ($erro === ''): ?>

                    <?php if ($quantidadeExibida > 0): ?>

                        <div class="overflow-x-auto">

                            <table class="w-full min-w-[900px] text-left">

                                <thead class="border-b border-slate-100 bg-slate-50">
                                    <tr class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                        <th class="px-6 py-4">ID</th>
                                        <th class="px-6 py-4">Nome do cliente</th>
                                        <th class="px-6 py-4">Telefone</th>
                                        <th class="px-6 py-4">Endereço</th>
                                        <th class="px-6 py-4 text-right">Cadastro</th>
                                        <th class="px-6 py-4 text-center">Ações</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-slate-100">

                                    <?php foreach ($clientes as $cliente): ?>

                                        <tr class="transition hover:bg-slate-50">

                                            <td class="px-6 py-5 font-mono text-sm text-slate-400">
                                                #<?php echo (int) $cliente['idClientes']; ?>
                                            </td>

                                            <td class="px-6 py-5">
                                                <div class="flex items-center gap-3">

                                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 font-bold text-blue-600">
                                                        <i class="fas fa-user"></i>
                                                    </div>

                                                    <span class="font-bold text-slate-700">
                                                        <?php echo escapar($cliente['NomeCliente']); ?>
                                                    </span>

                                                </div>
                                            </td>

                                            <td class="px-6 py-5 text-sm text-slate-600">
                                                <?php if (!empty($cliente['Telefone'])): ?>
                                                    <i class="fas fa-phone mr-2 text-xs text-slate-400"></i>
                                                    <?php echo escapar($cliente['Telefone']); ?>
                                                <?php else: ?>
                                                    <span class="text-slate-400">Não informado</span>
                                                <?php endif; ?>
                                            </td>

                                            <td class="px-6 py-5 text-sm text-slate-600">
                                                <?php if (!empty($cliente['Endereco'])): ?>
                                                    <i class="fas fa-location-dot mr-2 text-xs text-slate-400"></i>
                                                    <?php echo escapar($cliente['Endereco']); ?>
                                                <?php else: ?>
                                                    <span class="text-slate-400">Não informado</span>
                                                <?php endif; ?>
                                            </td>

                                            <td class="px-6 py-5 text-right">
                                                <span class="inline-flex items-center gap-2 rounded-lg bg-green-50 px-3 py-2 text-xs font-semibold text-green-700">
                                                    <i class="fas fa-check-circle"></i>
                                                    Cadastrado
                                                </span>
                                            </td>

                                            <td class="px-6 py-5 text-center">
                                                <a href="editar_cliente.php?id=<?php echo (int) $cliente['idClientes']; ?>"
                                                   class="inline-flex items-center gap-2 rounded-lg bg-blue-50 px-4 py-2 text-xs font-bold text-blue-700 transition hover:bg-blue-100"
                                                   title="Editar cliente">
                                                    <i class="fas fa-pen"></i>
                                                    Editar
                                                </a>
                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                </tbody>

                            </table>

                        </div>

                        <div class="border-t border-slate-100 px-6 py-4">
                            <p class="text-xs text-slate-400">
                                Exibindo <?php echo $quantidadeExibida; ?>
                                <?php echo $quantidadeExibida === 1 ? 'cliente' : 'clientes'; ?>.
                            </p>
                        </div>

                    <?php else: ?>

                        <div class="px-6 py-16 text-center">

                            <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                <i class="fas fa-users text-2xl"></i>
                            </div>

                            <h3 class="text-lg font-bold text-slate-700">
                                <?php echo $busca !== '' ? 'Nenhum resultado encontrado' : 'Nenhum cliente cadastrado'; ?>
                            </h3>

                            <p class="mx-auto mt-2 max-w-md text-sm text-slate-500">
                                <?php if ($busca !== ''): ?>
                                    Não encontramos clientes com os dados informados. Tente outra pesquisa.
                                <?php else: ?>
                                    Cadastre o primeiro cliente para começar a organizar os proprietários dos veículos.
                                <?php endif; ?>
                            </p>

                            <?php if ($busca !== ''): ?>

                                <a href="listar_clientes.php"
                                   class="mt-6 inline-flex items-center gap-2 rounded-xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-200">
                                    <i class="fas fa-arrow-left"></i>
                                    Voltar para a lista
                                </a>

                            <?php else: ?>

                                <a href="cadastrar_cliente.php"
                                   class="mt-6 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-blue-700">
                                    <i class="fas fa-user-plus"></i>
                                    Cadastrar primeiro cliente
                                </a>

                            <?php endif; ?>

                        </div>

                    <?php endif; ?>

                <?php endif; ?>

            </section>

            <p class="mt-6 text-center text-xs text-slate-400">
                AutoPro OS · AW TECHNOLOGY
            </p>

        </div>

    </main>

</div>

</body>
</html>
