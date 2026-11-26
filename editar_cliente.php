
<?php
require_once 'conexao.php';

function escapar($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header('Location: listar_clientes.php?erro=id_invalido');
    exit();
}

$cliente = null;
$erro = '';

try {
    $sql = "SELECT idClientes, NomeCliente, Endereco, Telefone
            FROM Clientes
            WHERE idClientes = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $cliente = $resultado->fetch_assoc();

    $stmt->close();

    if (!$cliente) {
        header('Location: listar_clientes.php?erro=cliente_nao_encontrado');
        exit();
    }
} catch (mysqli_sql_exception $e) {
    error_log('Erro ao consultar cliente para edição: ' . $e->getMessage());
    $erro = 'Não foi possível carregar os dados do cliente.';
}

$nome = $cliente['NomeCliente'] ?? '';
$telefone = $cliente['Telefone'] ?? '';
$endereco = $cliente['Endereco'] ?? '';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg?v=2">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f172a">
    <title>Editar Cliente - AutoPro OS</title>

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

        <div class="mx-auto max-w-3xl">

            <div class="mb-8">
                <div class="mb-4">
                    <a href="listar_clientes.php"
                       class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 transition hover:text-blue-600">
                        <i class="fas fa-arrow-left"></i>
                        Voltar para clientes
                    </a>
                </div>

                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-100 text-blue-600">
                        <i class="fas fa-user-pen text-2xl"></i>
                    </div>

                    <div>
                        <h1 class="text-2xl font-extrabold tracking-tight text-slate-800 md:text-3xl">
                            Editar Cliente
                        </h1>
                        <p class="mt-1 text-sm text-slate-500">
                            Atualize as informações do cadastro.
                        </p>
                    </div>
                </div>
            </div>

            <?php if ($erro !== ''): ?>
                <div class="mb-6 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-red-700"
                     role="alert">
                    <i class="fas fa-circle-exclamation mt-0.5"></i>
                    <p class="text-sm font-medium"><?php echo escapar($erro); ?></p>
                </div>
            <?php endif; ?>

            <?php if ($cliente): ?>

                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                    <div class="border-b border-slate-100 px-6 py-5 md:px-8">
                        <h2 class="font-bold text-slate-800">Informações do cliente</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Cliente #<?php echo (int) $cliente['idClientes']; ?>
                        </p>
                    </div>

                    <form action="atualizar_cliente.php" method="POST"
                          class="space-y-6 p-6 md:p-8">

                        <input type="hidden" name="id"
                               value="<?php echo (int) $cliente['idClientes']; ?>">

                        <input type="hidden" name="csrf_token"
                               value="<?php echo escapar($_SESSION['csrf_token']); ?>">

                        <div>
                            <label for="nome" class="mb-2 block text-sm font-bold text-slate-700">
                                Nome completo <span class="text-red-500">*</span>
                            </label>

                            <div class="relative">
                                <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>

                                <input type="text" id="nome" name="nome"
                                       maxlength="45" required
                                       value="<?php echo escapar($nome); ?>"
                                       autocomplete="name"
                                       class="w-full rounded-xl border border-slate-200 py-3 pl-11 pr-4 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            </div>
                        </div>

                        <div>
                            <label for="telefone" class="mb-2 block text-sm font-bold text-slate-700">
                                Telefone
                            </label>

                            <div class="relative">
                                <i class="fas fa-phone absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>

                                <input type="tel" id="telefone" name="telefone"
                                       maxlength="15"
                                       value="<?php echo escapar($telefone); ?>"
                                       autocomplete="tel"
                                       class="w-full rounded-xl border border-slate-200 py-3 pl-11 pr-4 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            </div>
                        </div>

                        <div>
                            <label for="endereco" class="mb-2 block text-sm font-bold text-slate-700">
                                Endereço
                            </label>

                            <div class="relative">
                                <i class="fas fa-location-dot absolute left-4 top-4 text-slate-400"></i>

                                <input type="text" id="endereco" name="endereco"
                                       maxlength="45"
                                       value="<?php echo escapar($endereco); ?>"
                                       autocomplete="street-address"
                                       class="w-full rounded-xl border border-slate-200 py-3 pl-11 pr-4 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            </div>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-4">
                            <p class="text-xs text-slate-500">
                                <i class="fas fa-info-circle mr-1 text-blue-500"></i>
                                As alterações serão aplicadas somente aos dados deste cliente.
                                Os veículos vinculados serão preservados.
                            </p>
                        </div>

                        <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:justify-end">

                            <a href="listar_clientes.php"
                               class="rounded-xl bg-slate-100 px-6 py-3 text-center text-sm font-bold text-slate-600 transition hover:bg-slate-200">
                                Cancelar
                            </a>

                            <button type="submit"
                                    class="rounded-xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-100 transition hover:bg-blue-700">
                                <i class="fas fa-save mr-2"></i>
                                Salvar Alterações
                            </button>

                        </div>

                    </form>
                </section>

            <?php endif; ?>

            <p class="mt-6 text-center text-xs text-slate-400">
                AutoPro OS · AW TECHNOLOGY
            </p>

        </div>
    </main>
</div>

</body>
</html>
