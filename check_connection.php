<?php

echo "🔍 Diagnóstico de Conexão - SpinWin\n";
echo "====================================\n\n";

// Verifica se o arquivo .env existe
if (!file_exists('.env')) {
    echo "❌ Arquivo .env não encontrado!\n";
    echo "Execute: php setup_env.php\n";
    exit;
}

echo "✅ Arquivo .env encontrado\n";

// Carrega variáveis de ambiente
$env = parse_ini_file('.env', false, INI_SCANNER_RAW);
if ($env === false) {
    echo "❌ Erro ao ler arquivo .env\n";
    exit;
}

foreach ($env as $key => $value) {
    $_ENV[$key] = $value;
}

echo "✅ Variáveis de ambiente carregadas\n\n";

// Mostra configurações
echo "📋 Configurações atuais:\n";
echo "   Host: " . ($_ENV['DB_HOST'] ?? 'não definido') . "\n";
echo "   Usuário: " . ($_ENV['DB_USERNAME'] ?? 'não definido') . "\n";
echo "   Senha: " . (empty($_ENV['DB_PASSWORD']) ? '(vazia)' : '(definida)') . "\n";
echo "   Banco: " . ($_ENV['DB_DATABASE'] ?? 'não definido') . "\n";
echo "   Porta: " . ($_ENV['DB_PORT'] ?? 'não definido') . "\n\n";

// Testa conexão
echo "🔌 Testando conexão...\n";

$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$username = $_ENV['DB_USERNAME'] ?? 'root';
$password = $_ENV['DB_PASSWORD'] ?? '';
$port = $_ENV['DB_PORT'] ?? 3306;

// Testa conexão sem banco específico
$connection = mysqli_connect($host, $username, $password, '', $port);

if (!$connection) {
    echo "❌ Erro ao conectar com o servidor MySQL:\n";
    echo "   " . mysqli_connect_error() . "\n\n";
    
    echo "🔧 Possíveis soluções:\n";
    echo "   1. Verifique se o MySQL/MariaDB está rodando\n";
    echo "   2. Verifique as credenciais no arquivo .env\n";
    echo "   3. Tente usar 127.0.0.1 em vez de localhost\n";
    echo "   4. Verifique se a porta está correta\n";
    exit;
}

echo "✅ Conexão com servidor MySQL estabelecida\n";

// Testa criação/seleção do banco
$database = $_ENV['DB_DATABASE'] ?? 'spinwin';

$sql = "CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if (!mysqli_query($connection, $sql)) {
    echo "❌ Erro ao criar/selecionar banco '{$database}':\n";
    echo "   " . mysqli_error($connection) . "\n";
    mysqli_close($connection);
    exit;
}

echo "✅ Banco de dados '{$database}' disponível\n";

// Testa seleção do banco
if (!mysqli_select_db($connection, $database)) {
    echo "❌ Erro ao selecionar banco '{$database}':\n";
    echo "   " . mysqli_error($connection) . "\n";
    mysqli_close($connection);
    exit;
}

echo "✅ Banco de dados selecionado com sucesso\n";

// Testa charset
if (!mysqli_set_charset($connection, 'utf8mb4')) {
    echo "⚠️  Aviso: Não foi possível definir charset utf8mb4\n";
} else {
    echo "✅ Charset utf8mb4 configurado\n";
}

mysqli_close($connection);

echo "\n🎉 Diagnóstico concluído com sucesso!\n";
echo "✅ O sistema está pronto para usar\n";
echo "\n💡 Agora você pode executar:\n";
echo "   php artisan migrate\n"; 