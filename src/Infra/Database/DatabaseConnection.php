<?php

namespace App\Infra\Database;

class DatabaseConnection
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        $this->connect();
    }

    /**
     * Obtém a instância singleton
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Estabelece a conexão com o banco
     */
    private function connect(): void
    {
        $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $username = $_ENV['DB_USERNAME'] ?? 'root';
        $password = $_ENV['DB_PASSWORD'] ?? '';
        $database = $_ENV['DB_DATABASE'] ?? 'spinwin';
        $port = $_ENV['DB_PORT'] ?? 3306;

        // Primeiro tenta conectar sem especificar o banco
        $this->connection = mysqli_connect($host, $username, $password, '', $port);

        if (!$this->connection) {
            throw new \Exception("Erro ao conectar com o servidor MySQL: " . mysqli_connect_error());
        }

        // Tenta criar o banco se não existir
        $this->createDatabaseIfNotExists($database);

        // Conecta ao banco específico
        if (!mysqli_select_db($this->connection, $database)) {
            throw new \Exception("Erro ao selecionar o banco de dados '{$database}': " . mysqli_error($this->connection));
        }

        mysqli_set_charset($this->connection, 'utf8mb4');
    }

    /**
     * Cria o banco de dados se não existir
     */
    private function createDatabaseIfNotExists(string $database): void
    {
        $sql = "CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        
        if (!mysqli_query($this->connection, $sql)) {
            throw new \Exception("Erro ao criar banco de dados '{$database}': " . mysqli_error($this->connection));
        }
    }

    /**
     * Obtém a conexão mysqli
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Fecha a conexão
     */
    public function close(): void
    {
        if ($this->connection) {
            mysqli_close($this->connection);
        }
    }

    /**
     * Executa uma query
     */
    public function query(string $sql)
    {
        return mysqli_query($this->connection, $sql);
    }

    /**
     * Obtém o último erro
     */
    public function getLastError(): string
    {
        return mysqli_error($this->connection);
    }

    /**
     * Obtém o último ID inserido
     */
    public function getLastInsertId(): int
    {
        return mysqli_insert_id($this->connection);
    }

    /**
     * Escapa uma string para evitar SQL injection
     */
    public function escape(string $string): string
    {
        return mysqli_real_escape_string($this->connection, $string);
    }
} 