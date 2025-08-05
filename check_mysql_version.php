<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Infra\Database\DatabaseConnection;

// Carrega variáveis de ambiente
if (file_exists(__DIR__ . '/.env')) {
    $env = parse_ini_file(__DIR__ . '/.env', false, INI_SCANNER_RAW);
    if ($env !== false) {
        foreach ($env as $key => $value) {
            $_ENV[$key] = $value;
        }
    }
}

echo "🔍 Verificando Versão do MySQL/MariaDB\n";
echo "=====================================\n\n";

try {
    $connection = DatabaseConnection::getInstance()->getConnection();
    
    // Verifica versão
    $result = mysqli_query($connection, "SELECT VERSION() as version");
    $row = mysqli_fetch_assoc($result);
    $version = $row['version'];
    
    echo "📋 Versão: {$version}\n";
    
    // Verifica se é MariaDB
    $result = mysqli_query($connection, "SELECT @@version_comment as comment");
    $row = mysqli_fetch_assoc($result);
    $comment = $row['comment'];
    
    echo "📋 Tipo: {$comment}\n";
    
    // Testa criação de tabela com timestamps
    echo "\n🧪 Testando criação de tabela com timestamps...\n";
    
    $sql = "CREATE TABLE IF NOT EXISTS `test_timestamps` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(255),
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if (mysqli_query($connection, $sql)) {
        echo "✅ Tabela criada com sucesso!\n";
        
        // Remove a tabela de teste
        mysqli_query($connection, "DROP TABLE test_timestamps");
        echo "✅ Tabela de teste removida\n";
    } else {
        echo "❌ Erro ao criar tabela: " . mysqli_error($connection) . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
} 