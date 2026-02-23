<?php 
include 'conexao.php'; 

// Verifica se o ID foi passado
if (!isset($_GET['id'])) {
    die("ID da OS não informado.");
}

$id = intval($_GET['id']);

// 1. LÓGICA DE SALVAMENTO (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $numero = $conn->real_escape_string($_POST['numero']);
    $status = $_POST['status'];
    $dataEntrega = $_POST['data_entrega'];
    $descricao = $conn->real_escape_string($_POST['descricao']);
    
    // Atualiza os dados básicos da OS
    $conn->query("UPDATE OrdemDeServico SET 
                  NumeroOS='$numero', 
                  Status='$status', 
                  DataEntrega='$dataEntrega', 
                  Descricao='$descricao' 
                  WHERE idOrdemServico=$id");

    // Lógica de Itens: Remove os antigos para reinserir os novos atualizados
    $conn->query("DELETE FROM Itens_OS WHERE idOrdemServico=$id");
    
    $totalGeral = 0;
    if(isset($_POST['item_nome'])) {
        foreach($_POST['item_nome'] as $key => $nome) {
            $nome = $conn->real_escape_string($nome);
            $qtd = floatval($_POST['item_qtd'][$key]);
            $valor = floatval($_POST['item_valor'][$key]);
            $tipo = $_POST['item_tipo'][$key];
            
            $subtotal = $qtd * $valor;
            $totalGeral += $subtotal;

            $conn->query("INSERT INTO Itens_OS (idOrdemServico, Descricao, Quantidade, ValorUnitario, Tipo) 
                          VALUES ($id, '$nome', '$qtd', '$valor', '$tipo')");
        }
    }

    // Atualiza o Valor Total final na tabela principal
    $conn->query("UPDATE OrdemDeServico SET Valor='$totalGeral' WHERE idOrdemServico=$id");
    
    header("Location: listar_os.php?msg=sucesso");
    exit();
}

// 2. BUSCA DE DADOS PARA EXIBIÇÃO
// Buscamos dados da OS e fazemos um LEFT JOIN para ter informações do veículo/cliente se necessário
$resOS = $conn->query("SELECT OS.*, V.Modelo, V.Placa FROM OrdemDeServico OS 
                       LEFT JOIN Veiculos V ON OS.veiculo_id = V.idVeiculos 
                       WHERE idOrdemServico=$id");
$dados = $resOS->fetch_assoc();

if (!$dados) {
    die("Ordem de Serviço não encontrada.");
}

$resItens = $conn->query("SELECT * FROM Itens_OS WHERE idOrdemServico=$id");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar OS #<?php echo $dados['NumeroOS']; ?> - AutoPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-100 p-4 md:p-8">

<div class="max-w-5xl mx-auto">
    <form method="POST" id="formOS">
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-200">
            
            <div class="bg-slate-900 p-8 text-white flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-black italic tracking-tighter">AUTOPRO <span class="text-blue-500 underline">EDITOR</span></h1>
                    <p class="text-slate-400 text-sm font-bold uppercase tracking-widest mt-1">
                        Veículo: <?php echo $dados['Modelo'] ?? 'Não vinculado'; ?> | Placa: <?php echo $dados['Placa'] ?? '---'; ?>
                    </p>
                </div>
                <a href="listar_os.php" class="bg-slate-800 hover:bg-red-500 px-6 py-2 rounded-xl transition text-xs font-bold flex items-center gap-2">
                    <i class="fas fa-times"></i> DESCARTAR ALTERAÇÕES
                </a>
            </div>

            <div class="p-8 space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest">Número do Documento</label>
                        <input type="text" name="numero" value="<?php echo $dados['NumeroOS']; ?>" class="w-full p-3 bg-slate-50 border-2 border-slate-100 rounded-xl font-bold focus:border-blue-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest">Status do Serviço</label>
                        <select name="status" class="w-full p-3 bg-slate-50 border-2 border-slate-100 rounded-xl font-black text-blue-600 focus:border-blue-500 outline-none transition cursor-pointer">
                            <option value="Em Aberto" <?php if($dados['Status']=='Em Aberto') echo 'selected'; ?>>🟡 EM ABERTO</option>
                            <option value="Em Andamento" <?php if($dados['Status']=='Em Andamento') echo 'selected'; ?>>🔵 EM ANDAMENTO</option>
                            <option value="Concluída" <?php if($dados['Status']=='Concluída') echo 'selected'; ?>>🟢 CONCLUÍDA</option>
                            <option value="Cancelada" <?php if($dados['Status']=='Cancelada') echo 'selected'; ?>>🔴 CANCELADA</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest">Previsão de Entrega</label>
                        <input type="date" name="data_entrega" value="<?php echo $dados['DataEntrega']; ?>" class="w-full p-3 bg-slate-50 border-2 border-slate-100 rounded-xl font-bold focus:border-blue-500 outline-none transition">
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-8">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-black text-slate-800 tracking-tighter italic"><i class="fas fa-cubes text-blue-600 mr-2"></i>Peças e Mão de Obra</h2>
                        <button type="button" onclick="addLinha()" class="bg-blue-600 text-white px-5 py-2.5 rounded-xl font-black text-xs hover:bg-blue-700 transition shadow-lg shadow-blue-200 flex items-center gap-2">
                            <i class="fas fa-plus"></i> NOVO ITEM
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
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
                                <?php while($item = $resItens->fetch_assoc()): ?>
                                <tr class="item-linha group">
                                    <td class="py-2 pr-2">
                                        <input type="text" name="item_nome[]" value="<?php echo $item['Descricao']; ?>" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:border-blue-400 outline-none font-semibold" required>
                                    </td>
                                    <td class="py-2 pr-2">
                                        <input type="number" name="item_qtd[]" value="<?php echo $item['Quantidade']; ?>" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-center font-bold outline-none" required>
                                    </td>
                                    <td class="py-2 pr-2">
                                        <input type="number" step="0.01" name="item_valor[]" value="<?php echo $item['ValorUnitario']; ?>" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-right font-bold outline-none" required>
                                    </td>
                                    <td class="py-2 pr-2">
                                        <select name="item_tipo[]" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold outline-none">
                                            <option value="Peça" <?php if($item['Tipo']=='Peça') echo 'selected'; ?>>📦 PEÇA</option>
                                            <option value="Serviço" <?php if($item['Tipo']=='Serviço') echo 'selected'; ?>>🛠️ SERVIÇO</option>
                                        </select>
                                    </td>
                                    <td class="py-2 text-right">
                                        <button type="button" onclick="this.parentElement.parentElement.remove(); calcularTotal();" class="w-10 h-10 flex items-center justify-center text-slate-300 hover:text-red-500 transition">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-slate-100 pt-8">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-3 tracking-widest">Relatório Técnico / Defeito Relatado</label>
                        <textarea name="descricao" rows="4" class="w-full p-5 bg-slate-50 border-2 border-slate-100 rounded-3xl text-slate-600 focus:border-blue-500 outline-none transition text-sm italic"><?php echo $dados['Descricao']; ?></textarea>
                    </div>
                    <div class="flex flex-col justify-center">
                        <div class="bg-blue-600 p-8 rounded-[2.5rem] text-white shadow-2xl shadow-blue-200 relative overflow-hidden">
                            <i class="fas fa-wallet absolute -right-4 -bottom-4 text-8xl opacity-10"></i>
                            <p class="text-[10px] font-black uppercase tracking-[0.3em] opacity-70 mb-2">Total Estimado</p>
                            <div class="flex items-baseline gap-2">
                                <span class="text-xl font-bold opacity-60">R$</span>
                                <span id="total_exibicao" class="text-5xl font-black tracking-tighter">0,00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-slate-900 hover:bg-blue-600 text-white font-black py-5 rounded-3xl shadow-xl transition-all active:scale-[0.98] flex justify-center items-center gap-3 text-lg">
                        <i class="fas fa-check-circle text-2xl text-blue-400"></i> ATUALIZAR ORDEM DE SERVIÇO
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// 1. Função para adicionar nova linha dinamicamente
function addLinha() {
    const html = `
    <tr class="item-linha group animate-pulse">
        <td class="py-2 pr-2"><input type="text" name="item_nome[]" placeholder="Descreva o serviço ou peça..." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:border-blue-400 outline-none font-semibold" required></td>
        <td class="py-2 pr-2"><input type="number" name="item_qtd[]" value="1" min="1" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-center font-bold outline-none" required></td>
        <td class="py-2 pr-2"><input type="number" step="0.01" name="item_valor[]" placeholder="0.00" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-right font-bold outline-none" required></td>
        <td class="py-2 pr-2">
            <select name="item_tipo[]" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold outline-none">
                <option value="Peça">📦 PEÇA</option>
                <option value="Serviço">🛠️ SERVIÇO</option>
            </select>
        </td>
        <td class="py-2 text-right"><button type="button" onclick="this.parentElement.parentElement.remove(); calcularTotal();" class="w-10 h-10 flex items-center justify-center text-slate-300 hover:text-red-500 transition"><i class="fas fa-trash-alt"></i></button></td>
    </tr>`;
    document.getElementById('listaItens').insertAdjacentHTML('beforeend', html);
    
    // Remove o efeito de pulso após 1 segundo
    setTimeout(() => {
        const ultimas = document.querySelectorAll('.item-linha');
        ultimas[ultimas.length - 1].classList.remove('animate-pulse');
    }, 500);
}

// 2. Função principal de cálculo
function calcularTotal() {
    let total = 0;
    const linhas = document.querySelectorAll('.item-linha');
    
    linhas.forEach(linha => {
        const qtdInput = linha.querySelector('input[name="item_qtd[]"]');
        const valorInput = linha.querySelector('input[name="item_valor[]"]');
        
        const qtd = parseFloat(qtdInput.value) || 0;
        const valor = parseFloat(valorInput.value) || 0;
        
        total += (qtd * valor);
    });

    // Atualiza o display formatado para moeda brasileira
    document.getElementById('total_exibicao').innerText = total.toLocaleString('pt-BR', { 
        minimumFractionDigits: 2, 
        maximumFractionDigits: 2 
    });
}

// 3. Monitoramento de eventos (Delegação)
document.getElementById('formOS').addEventListener('input', function(e) {
    if (e.target.name === 'item_qtd[]' || e.target.name === 'item_valor[]') {
        calcularTotal();
    }
});

// Inicialização automática do cálculo ao abrir a página
window.onload = calcularTotal;
</script>

</body>
</html>