<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Infra\Database\DatabaseConnection;
use App\Infra\Database\MigrationManager;
use App\Infra\Database\SeederManager;

// Carrega variáveis de ambiente
if (file_exists(__DIR__ . '/.env')) {
    $env = parse_ini_file(__DIR__ . '/.env');
    foreach ($env as $key => $value) {
        $_ENV[$key] = $value;
    }
}

echo "🚀 Testando Sistema de Migrations - SpinWin\n";
echo "==========================================\n\n";

try {
    // Conecta ao banco
    $connection = DatabaseConnection::getInstance()->getConnection();
    echo "✅ Conexão com banco estabelecida\n";
    
    // Inicializa managers
    $migrationManager = new MigrationManager($connection);
    $seederManager = new SeederManager($connection);
    
    echo "\n📊 Status das Migrations:\n";
    $status = $migrationManager->status();
    foreach ($status as $migration) {
        $batch = $migration['batch'] ? " (Batch: {$migration['batch']})" : '';
        echo "  {$migration['migration']} - {$migration['status']}{$batch}\n";
    }
    
    echo "\n🔄 Executando Migrations:\n";
    $results = $migrationManager->migrate();
    foreach ($results as $result) {
        echo "  {$result}\n";
    }
    
    echo "\n🌱 Executando Seeders:\n";
    $results = $seederManager->run();
    foreach ($results as $result) {
        echo "  {$result}\n";
    }
    
    echo "\n📊 Status Final das Migrations:\n";
    $status = $migrationManager->status();
    foreach ($status as $migration) {
        $batch = $migration['batch'] ? " (Batch: {$migration['batch']})" : '';
        echo "  {$migration['migration']} - {$migration['status']}{$batch}\n";
    }
    
    echo "\n✅ Teste concluído com sucesso!\n";
    
} catch (Exception $e) {
    echo "\n❌ Erro: " . $e->getMessage() . "\n";
    echo "Verifique se:\n";
    echo "  1. O banco de dados está rodando\n";
    echo "  2. As configurações no arquivo .env estão corretas\n";
    echo "  3. O banco 'spinwin' existe\n";
} 