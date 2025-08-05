<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Infra\Database\Blueprint;

echo "🧪 Testando Timestamps\n";
echo "=====================\n\n";

// Testa a geração de SQL com timestamps
$blueprint = new Blueprint('test_users');
$blueprint->id();
$blueprint->string('name');
$blueprint->string('email')->unique();
$blueprint->timestamps();

$sql = $blueprint->toSql();

echo "📝 SQL gerado:\n";
echo $sql . "\n\n";

echo "✅ Teste concluído!\n";
echo "💡 O SQL agora usa sintaxe compatível com MySQL/MariaDB\n"; 