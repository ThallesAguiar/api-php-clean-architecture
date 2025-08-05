<?php

namespace App\Infra\Database;

abstract class Migration
{
    protected $connection;
    protected $tableName;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    /**
     * Executa a migration (cria/modifica tabelas)
     */
    abstract public function up(): void;

    /**
     * Reverte a migration (remove tabelas/modificações)
     */
    abstract public function down(): void;

    /**
     * Executa uma query SQL
     */
    protected function execute(string $sql): bool
    {
        return mysqli_query($this->connection, $sql);
    }

    /**
     * Cria uma tabela
     */
    protected function createTable(string $tableName, callable $callback): void
    {
        $this->tableName = $tableName;
        $blueprint = new Blueprint($tableName);
        $callback($blueprint);
        
        $sql = $blueprint->toSql();
        $this->execute($sql);
    }

    /**
     * Remove uma tabela
     */
    protected function dropTable(string $tableName): void
    {
        $sql = "DROP TABLE IF EXISTS `{$tableName}`";
        $this->execute($sql);
    }

    /**
     * Adiciona uma coluna
     */
    protected function addColumn(string $tableName, string $columnName, string $definition): void
    {
        $sql = "ALTER TABLE `{$tableName}` ADD COLUMN `{$columnName}` {$definition}";
        $this->execute($sql);
    }

    /**
     * Remove uma coluna
     */
    protected function dropColumn(string $tableName, string $columnName): void
    {
        $sql = "ALTER TABLE `{$tableName}` DROP COLUMN `{$columnName}`";
        $this->execute($sql);
    }
} 