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
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $username = $_ENV['DB_USERNAME'] ?? 'root';
        $password = $_ENV['DB_PASSWORD'] ?? '';
        $database = $_ENV['DB_DATABASE'] ?? 'spinwin';
        $port = $_ENV['DB_PORT'] ?? 3306;

        $this->connection = mysqli_connect($host, $username, $password, $database, $port);

        if (!$this->connection) {
            throw new \Exception("Erro ao conectar com o banco de dados: " . mysqli_connect_error());
        }

        mysqli_set_charset($this->connection, 'utf8mb4');
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