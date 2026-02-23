<?php 
include 'conexao.php'; 

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // MUDANÇA: LEFT JOIN para evitar que a página quebre se faltar algum dado de vínculo
    $sql = "SELECT OS.*, V.Marca, V.Modelo, V.Placa, C.NomeCliente, C.Telefone 
            FROM OrdemDeServico OS
            LEFT JOIN Veiculos V ON OS.veiculo_id = V.idVeiculos
            LEFT JOIN Clientes C ON V.Clientes_idClientes = C.idClientes
            WHERE OS.idOrdemServico = $id";
    
    $res = $conn->query($sql);
    
    if ($res && $res->num_rows > 0) {
        $dados = $res->fetch_assoc();
    } else {
        die("Erro: Ordem de Serviço não encontrada no sistema.");
    }

    // Busca os itens detalhados
    $sqlItens = "SELECT * FROM Itens_OS WHERE idOrdemServico = $id";
    $resItens = $conn->query($sqlItens);

} else {
    die("ID da OS não informado.");
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Imprimir OS #<?php echo $dados['NumeroOS']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none; }
            body { background: white; padding: 0; }
            .print-border { border: none !important; box-shadow: none !important; }
            .page-break { page-break-before: always; }
        }
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap');
        body { font-family: 'Inter', sans-serif; -webkit-print-color-adjust: exact; }
    </style>
</head>
<body class="bg-slate-100 p-4 md:p-10">

    <div class="max-w-4xl mx-auto no-print mb-6 flex justify-between items-center">
        <a href="listar_os.php" class="text-slate-500 hover:text-slate-800 font-bold transition flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
        <div class="flex gap-3">
            <button onclick="window.print()" class="bg-blue-600 text-white px-8 py-3 rounded-2xl font-black shadow-lg hover:bg-blue-700 transition flex items-center gap-2">
                <i class="fas fa-print"></i> IMPRIMIR
            </button>
        </div>
    </div>

    <div class="max-w-4xl mx-auto bg-white shadow-2xl p-12 print-border rounded-sm min-h-[1100px] flex flex-col relative overflow-hidden">
        
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 opacity-[0.03] pointer-events-none">
            <i class="fas fa-tools text-[400px]"></i>
        </div>

        <div class="flex justify-between items-start border-b-4 border-slate-900 pb-8 mb-10 relative z-10">
            <div>
                <h1 class="text-5xl font-black tracking-tighter text-slate-900 uppercase">AutoPro <span class="text-blue-600 italic">OS</span></h1>
                <p class="text-slate-500 font-bold uppercase text-[10px] tracking-[0.3em] mt-2 italic">Performance & Manutenção Automotiva</p>
            </div>
            <div class="text-right">
                <div class="bg-slate-900 text-white px-4 py-1 text-[10px] font-black uppercase tracking-widest mb-2 rounded-sm">Via do Cliente</div>
                <p class="text-4xl font-black text-slate-900">#<?php echo $dados['NumeroOS']; ?></p>
                <p class="text-slate-400 font-bold text-sm mt-1"><?php echo date('d/m/Y H:i', strtotime($dados['DataEmissao'])); ?></p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-12 mb-12 relative z-10">
            <div class="border-l-4 border-blue-600 pl-6">
                <h2 class="text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest italic">Identificação do Cliente</h2>
                <p class="text-xl font-bold text-slate-800"><?php echo $dados['NomeCliente'] ?? 'CLIENTE NÃO IDENTIFICADO'; ?></p>
                <p class="text-slate-500 font-medium italic text-sm mt-1">Contato: <?php echo $dados['Telefone'] ?? '---'; ?></p>
            </div>
            <div class="border-l-4 border-slate-900 pl-6">
                <h2 class="text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest italic">Dados do Patrimônio</h2>
                <p class="text-xl font-bold text-slate-800"><?php echo ($dados['Marca'] ?? '') . " " . ($dados['Modelo'] ?? 'VEÍCULO NÃO VINCULADO'); ?></p>
                <div class="inline-block bg-slate-100 px-3 py-1 mt-2 rounded border border-slate-200">
                   <p class="text-slate-900 font-mono font-black uppercase tracking-tighter italic text-sm">PLACA: <?php echo $dados['Placa'] ?? '---'; ?></p>
                </div>
            </div>
        </div>

        <div class="flex-grow relative z-10">
            <table class="w-full mb-10">
                <thead>
                    <tr class="text-left border-b-2 border-slate-100 text-slate-400">
                        <th class="py-4 text-[10px] font-black uppercase tracking-widest italic">Especificação Técnica (Serviços/Peças)</th>
                        <th class="py-4 text-[10px] font-black uppercase tracking-widest w-20 text-center italic">Qtd</th>
                        <th class="py-4 text-[10px] font-black uppercase tracking-widest w-32 text-right italic">Unitário</th>
                        <th class="py-4 text-[10px] font-black uppercase tracking-widest w-32 text-right italic">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-sm">
                    <?php 
                    if ($resItens && $resItens->num_rows > 0):
                        while($item = $resItens->fetch_assoc()): 
                            $subtotal = $item['Quantidade'] * $item['ValorUnitario'];
                    ?>
                    <tr>
                        <td class="py-4">
                            <p class="font-bold text-slate-800"><?php echo $item['Descricao']; ?></p>
                            <span class="text-[9px] font-bold uppercase text-blue-500 italic"><?php echo $item['Tipo']; ?></span>
                        </td>
                        <td class="py-4 text-center font-medium text-slate-600"><?php echo number_format($item['Quantidade'], 0); ?></td>
                        <td class="py-4 text-right font-medium text-slate-600">R$ <?php echo number_format($item['ValorUnitario'], 2, ',', '.'); ?></td>
                        <td class="py-4 text-right font-bold text-slate-900">R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr>
                        <td colspan="4" class="py-12 text-center">
                            <i class="fas fa-info-circle text-slate-200 text-4xl mb-3 block"></i>
                            <p class="text-slate-400 italic font-medium">Consulte o valor global abaixo para serviços realizados.</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <?php if(!empty($dados['Descricao'])): ?>
            <div class="bg-slate-50 p-8 rounded-3xl border border-slate-100 mb-10 border-dashed">
                <h3 class="text-[10px] font-black uppercase text-slate-400 mb-3 tracking-widest italic">Diagnóstico e Observações</h3>
                <p class="text-slate-700 text-sm leading-relaxed italic">
                    <?php echo nl2br(htmlspecialchars($dados['Descricao'])); ?>
                </p>
            </div>
            <?php endif; ?>
        </div>

        <div class="mt-auto relative z-10">
            <div class="flex justify-between items-center bg-slate-900 text-white p-10 rounded-[2.5rem] shadow-xl">
                <div>
                    <p class="text-[10px] font-black uppercase text-slate-400 tracking-[0.3em] mb-2 italic">Status Operacional</p>
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full <?php echo ($dados['Status'] == 'Concluída') ? 'bg-green-400' : 'bg-amber-400'; ?> animate-pulse"></div>
                        <p class="text-2xl font-black italic tracking-tighter">
                            <?php echo strtoupper($dados['Status']); ?>
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-black uppercase text-slate-400 tracking-[0.3em] mb-1 italic">Total Líquido</p>
                    <p class="text-6xl font-black tracking-tighter italic">R$ <?php echo number_format($dados['Valor'], 2, ',', '.'); ?></p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-16 mt-16 px-6">
                <div class="text-center">
                    <div class="w-full border-b border-slate-300 mb-3 pt-4"></div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Técnico Responsável</p>
                </div>
                <div class="text-center">
                    <div class="w-full border-b border-slate-300 mb-3 pt-4"></div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Aceite do Proprietário</p>
                </div>
            </div>
            
            <div class="mt-12 pt-6 border-t border-slate-100 flex justify-between items-center text-[9px] text-slate-300 font-bold uppercase tracking-[0.3em]">
                <p>AUTOPRO v2.5 - Sistema de Gestão Inteligente</p>
                <p>Impressão: <?php echo date('d/m/Y H:i'); ?></p>
            </div>
        </div>
    </div>

</body>
</html>