<?php include 'conexao.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Abrir Nova OS - AutoPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap'); body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 p-6 md:p-10">
    <div class="max-w-3xl mx-auto bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-100">
        
        <div class="bg-slate-900 p-8 text-white flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-black uppercase tracking-tight italic">
                    <i class="fas fa-file-signature mr-2 text-blue-500"></i> Abrir Nova OS
                </h2>
                <p class="text-slate-400 text-sm">Inicie um novo atendimento para seu cliente.</p>
            </div>
            <i class="fas fa-tools text-4xl text-slate-800"></i>
        </div>
        
        <form action="processar_os.php" method="POST" class="p-8 space-y-6">
            
            <div>
                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">1. Selecione o Veículo:</label>
                <select name="veiculo_id" class="w-full bg-slate-50 border-2 border-slate-100 p-4 rounded-2xl focus:border-blue-500 focus:bg-white outline-none transition appearance-none font-semibold text-slate-700" required>
                    <option value="">Clique para buscar por Placa, Modelo ou Cliente...</option>
                    <?php
                    $res = $conn->query("SELECT V.idVeiculos, V.Modelo, V.Placa, C.NomeCliente 
                                       FROM Veiculos V 
                                       JOIN Clientes C ON V.Clientes_idClientes = C.idClientes 
                                       ORDER BY V.Placa ASC");
                    while($row = $res->fetch_assoc()) {
                        echo "<option value='".$row['idVeiculos']."'>".$row['Placa']." - ".$row['Modelo']." (".$row['NomeCliente'].")</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Nº da Ordem:</label>
                    <input type="number" name="numero_os" placeholder="Ex: 1001" 
                           class="w-full bg-slate-50 border-2 border-slate-100 p-4 rounded-2xl focus:border-blue-500 focus:bg-white outline-none font-bold text-blue-600" required>
                </div>
                
                <div>
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Previsão de Entrega:</label>
                    <input type="date" name="data_entrega" value="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
                           class="w-full bg-slate-50 border-2 border-slate-100 p-4 rounded-2xl focus:border-blue-500 focus:bg-white outline-none font-semibold text-slate-600">
                </div>
            </div>

            <div>
                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Descrição / Reclamação do Cliente:</label>
                <textarea name="descricao" rows="4" 
                          class="w-full bg-slate-50 border-2 border-slate-100 p-4 rounded-2xl focus:border-blue-500 focus:bg-white outline-none transition text-slate-600 font-medium" 
                          placeholder="Descreva aqui o que o cliente relatou ou o serviço inicial..."></textarea>
                <p class="text-[10px] text-slate-400 mt-2 italic">* Você poderá adicionar peças e valores detalhados na próxima tela.</p>
            </div>

            <div class="flex flex-col md:flex-row gap-4 pt-4">
                <button type="submit" class="flex-1 bg-blue-600 text-white font-black py-5 rounded-2xl hover:bg-blue-700 transition shadow-xl shadow-blue-100 uppercase tracking-widest">
                    <i class="fas fa-check-circle mr-2"></i> Criar e Adicionar Peças
                </button>
                <a href="listar_os.php" class="bg-slate-100 text-slate-500 font-bold py-5 px-8 rounded-2xl hover:bg-slate-200 transition uppercase tracking-widest text-center text-sm">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</body>
</html>