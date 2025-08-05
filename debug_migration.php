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

echo "🔍 Debug da Migration\n";
echo "====================\n\n";

try {
    $connection = DatabaseConnection::getInstance()->getConnection();
    $migrationManager = new MigrationManager($connection);
    
    // Lista arquivos de migration
    $migrationsPath = 'database/migrations';
    $files = glob($migrationsPath . '/*.php');
    
    echo "📁 Arquivos encontrados em {$migrationsPath}:\n";
    foreach ($files as $file) {
        $fileName = basename($file, '.php');
        echo "  - {$fileName}\n";
        
        // Testa extração do nome da classe
        $content = file_get_contents($file);
        if (preg_match('/class\s+(\w+)\s+extends\s+Migration/', $content, $matches)) {
            echo "    ✅ Classe encontrada: {$matches[1]}\n";
        } else {
            echo "    ❌ Classe não encontrada no arquivo\n";
        }
    }
    
    echo "\n📊 Status das migrations:\n";
    $status = $migrationManager->status();
    foreach ($status as $migration) {
        echo "  {$migration['migration']} - {$migration['status']}\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
} 