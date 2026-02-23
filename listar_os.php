<?php 
// 1. Conexão obrigatória com o banco
require_once 'conexao.php'; 
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de OS | AutoPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* Ajuste para scrollbar suave */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Destaque para a busca */
        .search-focus:focus-within {
            ring: 2px;
            ring-color: #2563eb;
            border-color: #2563eb;
        }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-900">

<div class="flex min-h-screen">
    <aside class="w-72 bg-slate-900 text-white p-8 hidden lg:flex flex-col gap-8 shadow-2xl sticky top-0 h-screen">
        <div class="flex items-center gap-3">
            <div class="bg-blue-600 p-2 rounded-lg shadow-lg shadow-blue-500/20">
                <i class="fas fa-car-side text-xl text-white"></i>
            </div>
            <span class="text-2xl font-black tracking-tighter">AUTOPRO</span>
        </div>
        
        <nav class="flex flex-col gap-2">
            <a href="index.php" class="flex items-center gap-4 p-4 rounded-xl text-slate-400 hover:bg-slate-800 transition">
                <i class="fas fa-home w-5 text-center"></i> Painel
            </a>
            <a href="listar_os.php" class="flex items-center gap-4 p-4 rounded-xl bg-blue-600 shadow-lg shadow-blue-600/20 text-white font-bold">
                <i class="fas fa-file-invoice w-5 text-center"></i> Ordens de Serviço
            </a>
            <a href="listar_clientes.php" class="flex items-center gap-4 p-4 rounded-xl text-slate-400 hover:bg-slate-800 transition">
                <i class="fas fa-user-friends w-5 text-center"></i> Clientes
            </a>
            <a href="listar_veiculos.php" class="flex items-center gap-4 p-4 rounded-xl text-slate-400 hover:bg-slate-800 transition">
                <i class="fas fa-car w-5 text-center"></i> Veículos
            </a>
        </nav>

        <div class="mt-auto p-4 bg-slate-800/50 rounded-2xl border border-slate-700/50">
            <p class="text-[10px] uppercase font-black tracking-widest text-slate-500 mb-1">Suporte Técnico</p>
            <p class="text-xs text-slate-300 font-medium">v1.2.0 - 2026</p>
        </div>
    </aside>

    <main class="flex-1 p-6 lg:p-12">
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">
            <div>
                <h1 class="text-4xl font-extrabold tracking-tight text-slate-900">Histórico de Ordens</h1>
                <p class="text-slate-500 font-medium mt-1">Gerencie e visualize todos os serviços da oficina.</p>
            </div>
            <a href="nova_os.php" class="bg-slate-900 hover:bg-blue-700 text-white px-8 py-4 rounded-2xl font-bold shadow-xl transition-all hover:scale-[1.02] active:scale-95 flex items-center gap-3">
                <i class="fas fa-plus-circle text-lg"></i> Abrir Nova OS
            </a>
        </header>

        <div class="mb-8 relative max-w-xl group">
            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                <i class="fas fa-search text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
            </div>
            <input type="text" id="inputBusca" onkeyup="filtrarTabela()" 
                   placeholder="Buscar por cliente, placa, modelo ou nº da OS..." 
                   class="block w-full pl-12 pr-4 py-4 bg-white border border-slate-200 rounded-2xl shadow-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all font-medium text-slate-600 outline-none">
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/60 border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" id="tabelaOS">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100">
                            <th class="px-8 py-6 text-[11px] font-black uppercase tracking-[0.15em] text-slate-400">Nº / Emissão</th>
                            <th class="px-8 py-6 text-[11px] font-black uppercase tracking-[0.15em] text-slate-400">Cliente & Veículo</th>
                            <th class="px-8 py-6 text-[11px] font-black uppercase tracking-[0.15em] text-slate-400">Status</th>
                            <th class="px-8 py-6 text-[11px] font-black uppercase tracking-[0.15em] text-slate-400">Investimento</th>
                            <th class="px-8 py-6 text-[11px] font-black uppercase tracking-[0.15em] text-slate-400 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php
                        // LEFT JOIN garante que as 6 OS apareçam, mesmo se houver erro de vínculo
                        $sql = "SELECT OS.*, C.NomeCliente, V.Modelo, V.Placa 
                                FROM OrdemDeServico OS
                                LEFT JOIN Veiculos V ON OS.veiculo_id = V.idVeiculos
                                LEFT JOIN Clientes C ON V.Clientes_idClientes = C.idClientes
                                ORDER BY OS.idOrdemServico DESC";
                        
                        $resultado = $conn->query($sql);

                        if ($resultado && $resultado->num_rows > 0):
                            while($row = $resultado->fetch_assoc()):
                                // Cores dinâmicas para o Status
                                $statusStyle = [
                                    'Concluída' => 'bg-emerald-100 text-emerald-700',
                                    'Em Aberto' => 'bg-amber-100 text-amber-700',
                                    'Cancelada' => 'bg-rose-100 text-rose-700'
                                ][$row['Status']] ?? 'bg-slate-100 text-slate-600';

                                // Tratamento para dados nulos
                                $clienteNome = $row['NomeCliente'] ?? 'Cliente Não Vinculado';
                                $veiculoInfo = $row['Modelo'] ? ($row['Modelo'] . " • " . $row['Placa']) : 'Veículo Pendente';
                        ?>
                        <tr class="group hover:bg-blue-50/30 transition-all tr-item">
                            <td class="px-8 py-7">
                                <span class="block font-black text-blue-600 text-xl tracking-tighter">#<?php echo $row['NumeroOS']; ?></span>
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                    <i class="far fa-calendar-alt mr-1"></i> 
                                    <?php echo $row['DataEmissao'] ? date('d/m/Y', strtotime($row['DataEmissao'])) : '--/--/----'; ?>
                                </span>
                            </td>
                            <td class="px-8 py-7">
                                <span class="block font-extrabold text-slate-800 text-lg leading-tight mb-1"><?php echo $clienteNome; ?></span>
                                <span class="inline-flex items-center gap-2 px-2.5 py-1 bg-slate-100 rounded-lg text-[10px] font-black text-slate-500 uppercase tracking-tighter">
                                    <i class="fas fa-car-side text-blue-500"></i> <?php echo $veiculoInfo; ?>
                                </span>
                            </td>
                            <td class="px-8 py-7">
                                <span class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-sm <?php echo $statusStyle; ?>">
                                    <?php echo $row['Status']; ?>
                                </span>
                            </td>
                            <td class="px-8 py-7">
                                <span class="text-xl font-black text-slate-900 tracking-tighter">R$ <?php echo number_format($row['Valor'], 2, ',', '.'); ?></span>
                            </td>
                            <td class="px-8 py-7 text-right">
                                <div class="flex justify-end gap-3 opacity-80 group-hover:opacity-100 transition-opacity">
                                    <a href="imprimir_os.php?id=<?php echo $row['idOrdemServico']; ?>" class="h-11 w-11 flex items-center justify-center rounded-2xl bg-slate-100 text-slate-500 hover:bg-slate-900 hover:text-white transition-all shadow-sm" title="Imprimir OS">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <a href="editar_os.php?id=<?php echo $row['idOrdemServico']; ?>" class="h-11 w-11 flex items-center justify-center rounded-2xl bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Editar">
                                        <i class="fas fa-pen-nib"></i>
                                    </a>
                                    <a href="excluir_os.php?id=<?php echo $row['idOrdemServico']; ?>" onclick="return confirm('Deseja realmente remover esta OS?')" class="h-11 w-11 flex items-center justify-center rounded-2xl bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white transition-all shadow-sm" title="Excluir">
                                        <i class="fas fa-trash-alt text-sm"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr>
                            <td colspan="5" class="px-8 py-32 text-center">
                                <div class="bg-slate-100 h-20 w-20 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <i class="fas fa-inbox text-slate-300 text-3xl"></i>
                                </div>
                                <h3 class="text-slate-900 font-extrabold text-xl">Nenhuma ordem encontrada</h3>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <footer class="mt-12 text-center lg:text-left">
            <p class="text-slate-400 text-xs font-bold uppercase tracking-[0.3em]">
                &copy; 2026 AutoPro Dashboard • Gestão Profissional
            </p>
        </footer>
    </main>
</div>

<script>
function filtrarTabela() {
    const input = document.getElementById("inputBusca");
    const filter = input.value.toUpperCase();
    const table = document.getElementById("tabelaOS");
    const tr = table.getElementsByClassName("tr-item");

    for (let i = 0; i < tr.length; i++) {
        const content = tr[i].textContent || tr[i].innerText;
        if (content.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
        } else {
            tr[i].style.display = "none";
        }
    }
}
</script>

</body>
</html>