<?php

namespace App\Infra\Database;

abstract class Seeder
{
    protected $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    /**
     * Executa o seeder
     */
    abstract public function run(): void;

    /**
     * Executa uma query SQL
     */
    protected function execute(string $sql): bool
    {
        return mysqli_query($this->connection, $sql);
    }

    /**
     * Insere dados em uma tabela
     */
    protected function insert(string $table, array $data): bool
    {
        $columns = array_keys($data);
        $values = array_values($data);
        
        $columnsStr = '`' . implode('`, `', $columns) . '`';
        $placeholders = str_repeat('?,', count($values) - 1) . '?';
        
        $sql = "INSERT INTO `{$table}` ({$columnsStr}) VALUES ({$placeholders})";
        
        $stmt = mysqli_prepare($this->connection, $sql);
        
        if (!$stmt) {
            throw new \Exception("Erro ao preparar statement: " . mysqli_error($this->connection));
        }
        
        $types = str_repeat('s', count($values));
        mysqli_stmt_bind_param($stmt, $types, ...$values);
        
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Insere múltiplos registros
     */
    protected function insertMultiple(string $table, array $data): bool
    {
        if (empty($data)) {
            return true;
        }

        $columns = array_keys($data[0]);
        $columnsStr = '`' . implode('`, `', $columns) . '`';
        
        $values = [];
        $placeholders = [];
        
        foreach ($data as $row) {
            $rowPlaceholders = str_repeat('?,', count($row) - 1) . '?';
            $placeholders[] = "({$rowPlaceholders})";
            $values = array_merge($values, array_values($row));
        }
        
        $placeholdersStr = implode(', ', $placeholders);
        $sql = "INSERT INTO `{$table}` ({$columnsStr}) VALUES {$placeholdersStr}";
        
        $stmt = mysqli_prepare($this->connection, $sql);
        
        if (!$stmt) {
            throw new \Exception("Erro ao preparar statement: " . mysqli_error($this->connection));
        }
        
        $types = str_repeat('s', count($values));
        mysqli_stmt_bind_param($stmt, $types, ...$values);
        
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Limpa uma tabela
     */
    protected function truncate(string $table): bool
    {
        $sql = "TRUNCATE TABLE `{$table}`";
        return $this->execute($sql);
    }

    /**
     * Deleta todos os registros de uma tabela
     */
    protected function delete(string $table, string $where = ''): bool
    {
        $sql = "DELETE FROM `{$table}`";
        if ($where) {
            $sql .= " WHERE {$where}";
        }
        return $this->execute($sql);
    }
} 