<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Infra\Database\DatabaseConnection;
use App\Infra\Database\MigrationManager;

// Carrega variáveis de ambiente
if (file_exists(__DIR__ . '/.env')) {
    $env = parse_ini_file(__DIR__ . '/.env', false, INI_SCANNER_RAW);
    if ($env !== false) {
        foreach ($env as $key => $value) {
            $_ENV[$key] = $value;
        }
    }
}

echo "🔄 Resetando Migrations\n";
echo "======================\n\n";

try {
    $connection = DatabaseConnection::getInstance()->getConnection();
    $migrationManager = new MigrationManager($connection);
    
    // Verifica se a tabela migrations existe
    $result = mysqli_query($connection, "SHOW TABLES LIKE 'migrations'");
    if (mysqli_num_rows($result) > 0) {
        echo "🗑️  Limpando tabela migrations...\n";
        mysqli_query($connection, "DROP TABLE migrations");
        echo "✅ Tabela migrations removida\n";
    }
    
    // Verifica se a tabela users existe
    $result = mysqli_query($connection, "SHOW TABLES LIKE 'users'");
    if (mysqli_num_rows($result) > 0) {
        echo "🗑️  Removendo tabela users...\n";
        mysqli_query($connection, "DROP TABLE users");
        echo "✅ Tabela users removida\n";
    }
    
    echo "\n🔄 Executando migrations novamente...\n";
    $results = $migrationManager->migrate();
    
    foreach ($results as $result) {
        echo "  {$result}\n";
    }
    
    echo "\n✅ Reset concluído com sucesso!\n";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
} 