
<?php

require_once 'conexao.php';

function escapar($valor): string
{
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

function decimalValido($valor, bool $aceitaZero = true): bool
{
    if (!is_string($valor) && !is_numeric($valor)) {
        return false;
    }

    $valor = (string) $valor;

    if (!preg_match('/^\d+(\.\d{1,2})?$/', $valor)) {
        return false;
    }

    $numero = (float) $valor;

    return $numero <= 99999999.99
        && ($aceitaZero ? $numero >= 0 : $numero > 0);
}

function dataValida($data): bool
{
    if ($data === '') {
        return true;
    }

    $dataObjeto = DateTime::createFromFormat('!Y-m-d', $data);

    return $dataObjeto !== false
        && $dataObjeto->format('Y-m-d') === $data;
}

// Validação do identificador
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header('Location: listar_os.php?erro=id_invalido');
    exit;
}

$mensagemErro = '';
$dados = [];
$itensFormulario = [];

// Processamento do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $numero = trim($_POST['numero'] ?? '');
    $status = trim($_POST['status'] ?? '');
    $dataEntrega = trim($_POST['data_entrega'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');

    $nomes = $_POST['item_nome'] ?? [];
    $quantidades = $_POST['item_qtd'] ?? [];
    $valores = $_POST['item_valor'] ?? [];
    $tipos = $_POST['item_tipo'] ?? [];

    // Preserva os valores preenchidos caso ocorra algum erro
    if (!is_array($nomes)) {
        $nomes = [];
    }

    if (!is_array($quantidades)) {
        $quantidades = [];
    }

    if (!is_array($valores)) {
        $valores = [];
    }

    if (!is_array($tipos)) {
        $tipos = [];
    }

    $quantidadeItens = count($nomes);

    for ($i = 0; $i < $quantidadeItens; $i++) {
        $itensFormulario[] = [
            'Descricao' => $nomes[$i] ?? '',
            'Quantidade' => $quantidades[$i] ?? '1',
            'ValorUnitario' => $valores[$i] ?? '0.00',
            'Tipo' => $tipos[$i] ?? 'Peça'
        ];
    }

    $dados = [
        'NumeroOS' => $numero,
        'Status' => $status,
        'DataEntrega' => $dataEntrega,
        'Descricao' => $descricao
    ];

    // Validações básicas
    if (!ctype_digit($numero) || (int) $numero <= 0) {
        $mensagemErro = 'Informe um número de OS válido.';
    } elseif ((int) $numero > 2147483647) {
        $mensagemErro = 'O número da OS ultrapassa o limite permitido.';
    } elseif (!in_array($status, [
        'Em Aberto',
        'Em Andamento',
        'Concluída',
        'Cancelada'
    ], true)) {
        $mensagemErro = 'Selecione um status válido.';
    } elseif (!dataValida($dataEntrega)) {
        $mensagemErro = 'Informe uma data de entrega válida.';
    } elseif (strlen($descricao) > 10000) {
        $mensagemErro = 'A descrição ultrapassa o limite de caracteres.';
    } elseif (
        !is_array($_POST['item_nome'] ?? null) ||
        !is_array($_POST['item_qtd'] ?? null) ||
        !is_array($_POST['item_valor'] ?? null) ||
        !is_array($_POST['item_tipo'] ?? null) ||
        count($nomes) !== count($quantidades) ||
        count($nomes) !== count($valores) ||
        count($nomes) !== count($tipos)
    ) {
        $mensagemErro = 'Os dados dos itens estão inconsistentes.';
    }

    $totalGeral = 0.0;
    $itensValidados = [];

    // Validação individual dos itens
    if ($mensagemErro === '') {

        foreach ($nomes as $indice => $nomeOriginal) {

            $nome = trim((string) $nomeOriginal);
            $qtdOriginal = (string) $quantidades[$indice];
            $valorOriginal = (string) $valores[$indice];
            $tipo = $tipos[$indice];

            if ($nome === '' || strlen($nome) > 255) {
                $mensagemErro = 'A descrição de um dos itens está inválida.';
                break;
            }

            if (!decimalValido($qtdOriginal, false)) {
                $mensagemErro = 'Informe uma quantidade válida e maior que zero.';
                break;
            }

            if (!decimalValido($valorOriginal, true)) {
                $mensagemErro = 'Informe um valor unitário válido.';
                break;
            }

            if (!in_array($tipo, ['Peça', 'Serviço'], true)) {
                $mensagemErro = 'A categoria de um dos itens é inválida.';
                break;
            }

            $qtd = (float) $qtdOriginal;
            $valor = (float) $valorOriginal;

            $subtotal = round($qtd * $valor, 2);
            $totalGeral = round($totalGeral + $subtotal, 2);

            if ($totalGeral > 99999999.99) {
                $mensagemErro = 'O valor total da OS ultrapassa o limite permitido.';
                break;
            }

            $itensValidados[] = [
                'nome' => $nome,
                'quantidade' => $qtd,
                'valor' => $valor,
                'tipo' => $tipo
            ];
        }
    }

    // Atualização segura com transação
    if ($mensagemErro === '') {

        try {

            $conn->begin_transaction();

            // Confirma que a OS existe e bloqueia sua linha durante a atualização
            $stmtVerifica = $conn->prepare(
                "SELECT idOrdemServico
                 FROM OrdemDeServico
                 WHERE idOrdemServico = ?
                 FOR UPDATE"
            );

            $stmtVerifica->bind_param('i', $id);
            $stmtVerifica->execute();

            $resultadoVerifica = $stmtVerifica->get_result();
            $osExiste = $resultadoVerifica->num_rows > 0;

            $stmtVerifica->close();

            if (!$osExiste) {
                throw new RuntimeException('OS não encontrada.');
            }

            // Verifica duplicidade do número da OS
            $stmtDuplicidade = $conn->prepare(
                "SELECT idOrdemServico
                 FROM OrdemDeServico
                 WHERE NumeroOS = ?
                   AND idOrdemServico <> ?
                 LIMIT 1"
            );

            $numeroInt = (int) $numero;

            $stmtDuplicidade->bind_param('ii', $numeroInt, $id);
            $stmtDuplicidade->execute();

            $resultadoDuplicidade = $stmtDuplicidade->get_result();
            $numeroDuplicado = $resultadoDuplicidade->num_rows > 0;

            $stmtDuplicidade->close();

            if ($numeroDuplicado) {
                throw new RuntimeException('O número informado já está sendo utilizado por outra OS.');
            }

            // Atualiza os dados principais
            $dataEntregaBanco = $dataEntrega !== ''
                ? $dataEntrega
                : null;

            $stmtAtualiza = $conn->prepare(
                "UPDATE OrdemDeServico
                 SET NumeroOS = ?,
                     Status = ?,
                     DataEntrega = ?,
                     Descricao = ?
                 WHERE idOrdemServico = ?"
            );

            $stmtAtualiza->bind_param(
                'isssi',
                $numeroInt,
                $status,
                $dataEntregaBanco,
                $descricao,
                $id
            );

            $stmtAtualiza->execute();
            $stmtAtualiza->close();

            // Remove os itens antigos
            $stmtDelete = $conn->prepare(
                "DELETE FROM Itens_OS
                 WHERE idOrdemServico = ?"
            );

            $stmtDelete->bind_param('i', $id);
            $stmtDelete->execute();
            $stmtDelete->close();

            // Prepara a inclusão dos novos itens
            $stmtItem = $conn->prepare(
                "INSERT INTO Itens_OS
                 (idOrdemServico, Descricao, Quantidade, ValorUnitario, Tipo)
                 VALUES (?, ?, ?, ?, ?)"
            );

            foreach ($itensValidados as $item) {

                $nomeItem = $item['nome'];
                $qtdItem = $item['quantidade'];
                $valorItem = $item['valor'];
                $tipoItem = $item['tipo'];

                $stmtItem->bind_param(
                    'isdds',
                    $id,
                    $nomeItem,
                    $qtdItem,
                    $valorItem,
                    $tipoItem
                );

                $stmtItem->execute();
            }

            $stmtItem->close();

            // Atualiza o valor total da OS
            $stmtTotal = $conn->prepare(
                "UPDATE OrdemDeServico
                 SET Valor = ?
                 WHERE idOrdemServico = ?"
            );

            $stmtTotal->bind_param('di', $totalGeral, $id);
            $stmtTotal->execute();
            $stmtTotal->close();

            // Confirma todas as alterações
            $conn->commit();

            header('Location: listar_os.php?msg=sucesso');
            exit;

        } catch (Throwable $e) {

            $conn->rollback();

            if ($e instanceof RuntimeException) {
                $mensagemErro = $e->getMessage();
            } else {
                error_log('Erro ao atualizar OS: ' . $e->getMessage());
                $mensagemErro = 'Não foi possível atualizar a Ordem de Serviço. Verifique os dados e tente novamente.';
            }
        }
    }
}

// Busca os dados atuais da OS
if (empty($dados)) {

    try {

        $stmtOS = $conn->prepare(
            "SELECT
                OS.*,
                V.Modelo,
                V.Placa,
                V.Marca
             FROM OrdemDeServico OS
             LEFT JOIN Veiculos V
                ON OS.veiculo_id = V.idVeiculos
             WHERE OS.idOrdemServico = ?"
        );

        $stmtOS->bind_param('i', $id);
        $stmtOS->execute();

        $resultadoOS = $stmtOS->get_result();
        $dados = $resultadoOS->fetch_assoc();

        $stmtOS->close();

        if (!$dados) {
            header('Location: listar_os.php?erro=os_nao_encontrada');
            exit;
        }

    } catch (mysqli_sql_exception $e) {

        error_log('Erro ao consultar OS: ' . $e->getMessage());

        die('Não foi possível carregar a Ordem de Serviço.');
    }
}

// Busca os itens cadastrados quando não houve tentativa de envio
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    try {

        $stmtItens = $conn->prepare(
            "SELECT *
             FROM Itens_OS
             WHERE idOrdemServico = ?
             ORDER BY idItem ASC"
        );

        $stmtItens->bind_param('i', $id);
        $stmtItens->execute();

        $resultadoItens = $stmtItens->get_result();

        while ($item = $resultadoItens->fetch_assoc()) {
            $itensFormulario[] = $item;
        }

        $stmtItens->close();

    } catch (mysqli_sql_exception $e) {

        error_log('Erro ao consultar itens da OS: ' . $e->getMessage());

        die('Não foi possível carregar os itens da Ordem de Serviço.');
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg?v=2">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar OS #<?= escapar($dados['NumeroOS']) ?> - AutoPro</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-100 p-4 md:p-8">

<div class="max-w-5xl mx-auto">

    <form method="POST"
          action="editar_os.php?id=<?= $id ?>"
          id="formOS">

        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-200">

            <!-- Cabeçalho -->
            <div class="bg-slate-900 p-8 text-white flex flex-col md:flex-row justify-between items-start md:items-center gap-5">

                <div>
                    <h1 class="text-2xl font-black italic tracking-tighter">
                        AUTOPRO <span class="text-blue-500 underline">EDITOR</span>
                    </h1>

                    <p class="text-slate-400 text-sm font-bold uppercase tracking-widest mt-1">
                        Veículo:
                        <?= escapar($dados['Modelo'] ?? 'Não vinculado') ?>
                        | Placa:
                        <?= escapar($dados['Placa'] ?? '---') ?>
                    </p>
                </div>

                <a href="listar_os.php"
                   class="bg-slate-800 hover:bg-red-500 px-6 py-3 rounded-xl transition text-xs font-bold flex items-center gap-2">

                    <i class="fas fa-times"></i>
                    DESCARTAR ALTERAÇÕES

                </a>

            </div>

            <div class="p-5 md:p-8 space-y-8">

                <!-- Mensagem de erro -->
                <?php if ($mensagemErro !== ''): ?>

                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 flex items-center gap-3">

                        <i class="fas fa-circle-exclamation"></i>

                        <span class="text-sm font-semibold">
                            <?= escapar($mensagemErro) ?>
                        </span>

                    </div>

                <?php endif; ?>

                <!-- Dados principais -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest">
                            Número do Documento
                        </label>

                        <input type="number"
                               name="numero"
                               min="1"
                               max="2147483647"
                               value="<?= escapar($dados['NumeroOS']) ?>"
                               required
                               class="w-full p-3 bg-slate-50 border-2 border-slate-100 rounded-xl font-bold focus:border-blue-500 outline-none transition">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest">
                            Status do Serviço
                        </label>

                        <select name="status"
                                required
                                class="w-full p-3 bg-slate-50 border-2 border-slate-100 rounded-xl font-black text-blue-600 focus:border-blue-500 outline-none transition cursor-pointer">

                            <?php
                            $statusAtual = $dados['Status'] ?? 'Em Aberto';

                            $opcoesStatus = [
                                'Em Aberto' => '🟡 EM ABERTO',
                                'Em Andamento' => '🔵 EM ANDAMENTO',
                                'Concluída' => '🟢 CONCLUÍDA',
                                'Cancelada' => '🔴 CANCELADA'
                            ];
                            ?>

                            <?php foreach ($opcoesStatus as $valor => $rotulo): ?>

                                <option value="<?= escapar($valor) ?>"
                                    <?= $statusAtual === $valor ? 'selected' : '' ?>>

                                    <?= escapar($rotulo) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest">
                            Previsão de Entrega
                        </label>

                        <input type="date"
                               name="data_entrega"
                               value="<?= escapar($dados['DataEntrega'] ?? '') ?>"
                               class="w-full p-3 bg-slate-50 border-2 border-slate-100 rounded-xl font-bold focus:border-blue-500 outline-none transition">
                    </div>

                </div>

                <!-- Itens -->
                <div class="border-t border-slate-100 pt-8">

                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">

                        <h2 class="text-xl font-black text-slate-800 tracking-tighter italic">
                            <i class="fas fa-cubes text-blue-600 mr-2"></i>
                            Peças e Mão de Obra
                        </h2>

                        <button type="button"
                                onclick="addLinha()"
                                class="bg-blue-600 text-white px-5 py-3 rounded-xl font-black text-xs hover:bg-blue-700 transition shadow-lg shadow-blue-200 flex items-center gap-2">

                            <i class="fas fa-plus"></i>
                            NOVO ITEM

                        </button>

                    </div>

                    <div class="overflow-x-auto">

                        <table class="w-full text-left min-w-[650px]">

                            <thead>
                                <tr class="text-[10px] text-slate-400 uppercase font-black tracking-[0.2em]">

                                    <th class="pb-4">Descrição Detalhada</th>
                                    <th class="pb-4 w-24 text-center">Qtd</th>
                                    <th class="pb-4 w-32 text-right">Unitário (R$)</th>
                                    <th class="pb-4 w-32">Categoria</th>
                                    <th class="pb-4 w-10"></th>

                                </tr>
                            </thead>

                            <tbody id="listaItens">

                                <?php foreach ($itensFormulario as $item): ?>

                                    <tr class="item-linha group">

                                        <td class="py-2 pr-2">

                                            <input type="text"
                                                   name="item_nome[]"
                                                   value="<?= escapar($item['Descricao'] ?? '') ?>"
                                                   maxlength="255"
                                                   class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:border-blue-400 outline-none font-semibold"
                                                   required>

                                        </td>

                                        <td class="py-2 pr-2">

                                            <input type="number"
                                                   name="item_qtd[]"
                                                   value="<?= escapar($item['Quantidade'] ?? '1') ?>"
                                                   min="0.01"
                                                   max="99999999.99"
                                                   step="0.01"
                                                   class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-center font-bold outline-none"
                                                   required>

                                        </td>

                                        <td class="py-2 pr-2">

                                            <input type="number"
                                                   name="item_valor[]"
                                                   value="<?= escapar($item['ValorUnitario'] ?? '0.00') ?>"
                                                   min="0"
                                                   max="99999999.99"
                                                   step="0.01"
                                                   class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-right font-bold outline-none"
                                                   required>

                                        </td>

                                        <td class="py-2 pr-2">

                                            <select name="item_tipo[]"
                                                    class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold outline-none">

                                                <option value="Peça"
                                                    <?= ($item['Tipo'] ?? '') === 'Peça' ? 'selected' : '' ?>>
                                                    📦 PEÇA
                                                </option>

                                                <option value="Serviço"
                                                    <?= ($item['Tipo'] ?? '') === 'Serviço' ? 'selected' : '' ?>>
                                                    🛠️ SERVIÇO
                                                </option>

                                            </select>

                                        </td>

                                        <td class="py-2 text-right">

                                            <button type="button"
                                                    onclick="this.closest('tr').remove(); calcularTotal();"
                                                    title="Remover item"
                                                    class="w-10 h-10 flex items-center justify-center text-slate-300 hover:text-red-500 transition">

                                                <i class="fas fa-trash-alt"></i>

                                            </button>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                    <p class="text-xs text-slate-400 mt-3">
                        Você pode adicionar, editar ou remover peças e serviços.
                    </p>

                </div>

                <!-- Descrição e total -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-slate-100 pt-8">

                    <div>

                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-3 tracking-widest">
                            Relatório Técnico / Defeito Relatado
                        </label>

                        <textarea name="descricao"
                                  rows="4"
                                  maxlength="10000"
                                  class="w-full p-5 bg-slate-50 border-2 border-slate-100 rounded-3xl text-slate-600 focus:border-blue-500 outline-none transition text-sm italic"><?= escapar($dados['Descricao'] ?? '') ?></textarea>

                    </div>

                    <div class="flex flex-col justify-center">

                        <div class="bg-blue-600 p-8 rounded-[2.5rem] text-white shadow-2xl shadow-blue-200 relative overflow-hidden">

                            <i class="fas fa-wallet absolute -right-4 -bottom-4 text-8xl opacity-10"></i>

                            <p class="text-[10px] font-black uppercase tracking-[0.3em] opacity-70 mb-2">
                                Total Estimado
                            </p>

                            <div class="flex items-baseline gap-2">

                                <span class="text-xl font-bold opacity-60">R$</span>

                                <span id="total_exibicao"
                                      class="text-5xl font-black tracking-tighter">
                                    0,00
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- Botão salvar -->
                <div class="pt-4">

                    <button type="submit"
                            class="w-full bg-slate-900 hover:bg-blue-600 text-white font-black py-5 rounded-3xl shadow-xl transition-all active:scale-[0.98] flex justify-center items-center gap-3 text-lg">

                        <i class="fas fa-check-circle text-2xl text-blue-400"></i>
                        ATUALIZAR ORDEM DE SERVIÇO

                    </button>

                </div>

            </div>

        </div>

    </form>

</div>

<script>

// Adiciona uma nova linha de item
function addLinha() {

    const html = `
        <tr class="item-linha group">

            <td class="py-2 pr-2">
                <input type="text"
                       name="item_nome[]"
                       maxlength="255"
                       placeholder="Descreva o serviço ou peça..."
                       class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:border-blue-400 outline-none font-semibold"
                       required>
            </td>

            <td class="py-2 pr-2">
                <input type="number"
                       name="item_qtd[]"
                       value="1"
                       min="0.01"
                       max="99999999.99"
                       step="0.01"
                       class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-center font-bold outline-none"
                       required>
            </td>

            <td class="py-2 pr-2">
                <input type="number"
                       name="item_valor[]"
                       value="0.00"
                       min="0"
                       max="99999999.99"
                       step="0.01"
                       class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-right font-bold outline-none"
                       required>
            </td>

            <td class="py-2 pr-2">
                <select name="item_tipo[]"
                        class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold outline-none">

                    <option value="Peça">📦 PEÇA</option>
                    <option value="Serviço">🛠️ SERVIÇO</option>

                </select>
            </td>

            <td class="py-2 text-right">
                <button type="button"
                        onclick="this.closest('tr').remove(); calcularTotal();"
                        title="Remover item"
                        class="w-10 h-10 flex items-center justify-center text-slate-300 hover:text-red-500 transition">

                    <i class="fas fa-trash-alt"></i>

                </button>
            </td>

        </tr>
    `;

    document.getElementById('listaItens').insertAdjacentHTML('beforeend', html);

    calcularTotal();
}

// Calcula o total dos itens
function calcularTotal() {

    let total = 0;

    const linhas = document.querySelectorAll('.item-linha');

    linhas.forEach(linha => {

        const qtdInput = linha.querySelector('input[name="item_qtd[]"]');
        const valorInput = linha.querySelector('input[name="item_valor[]"]');

        const qtd = parseFloat(qtdInput.value) || 0;
        const valor = parseFloat(valorInput.value) || 0;

        total += qtd * valor;

    });

    document.getElementById('total_exibicao').innerText =
        total.toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
}

// Atualização automática ao editar quantidade ou valor
document.getElementById('formOS').addEventListener('input', function(event) {

    if (
        event.target.name === 'item_qtd[]' ||
        event.target.name === 'item_valor[]'
    ) {
        calcularTotal();
    }

});

// Cálculo inicial
window.addEventListener('DOMContentLoaded', calcularTotal);

</script>

</body>
</html>
