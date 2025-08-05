<?php

echo "🔧 Configuração do Arquivo .env\n";
echo "===============================\n\n";

// Verifica se o arquivo .env já existe
if (file_exists('.env')) {
    echo "⚠️  O arquivo .env já existe!\n";
    echo "Deseja sobrescrever? (s/N): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);
    
    if (trim(strtolower($line)) !== 's') {
        echo "Operação cancelada.\n";
        exit;
    }
}

echo "📝 Criando arquivo .env...\n";

$envContent = "# Configurações do Banco de Dados\n";
$envContent .= "DB_HOST=127.0.0.1\n";
$envContent .= "DB_USERNAME=root\n";
$envContent .= "DB_PASSWORD=\n";
$envContent .= "DB_DATABASE=spinwin\n";
$envContent .= "DB_PORT=3306\n";

if (file_put_contents('.env', $envContent)) {
    echo "✅ Arquivo .env criado com sucesso!\n\n";
    echo "📋 Configurações padrão:\n";
    echo "   Host: 127.0.0.1\n";
    echo "   Usuário: root\n";
    echo "   Senha: (vazia)\n";
    echo "   Banco: spinwin\n";
    echo "   Porta: 3306\n\n";
    
    echo "🔧 Para personalizar, edite o arquivo .env\n";
    echo "💡 Dica: Use 127.0.0.1 em vez de localhost para MariaDB\n";
} else {
    echo "❌ Erro ao criar arquivo .env\n";
} 