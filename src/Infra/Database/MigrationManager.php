<?php

namespace App\Infra\Database;

class MigrationManager
{
    private $connection;
    private $migrationsPath;
    private $migrationsTable = 'migrations';

    public function __construct($connection, string $migrationsPath = 'database/migrations')
    {
        $this->connection = $connection;
        $this->migrationsPath = $migrationsPath;
        $this->createMigrationsTable();
    }

    /**
     * Cria a tabela de controle de migrations
     */
    private function createMigrationsTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS `{$this->migrationsTable}` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `migration` VARCHAR(255) NOT NULL,
            `batch` INT NOT NULL,
            `executed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        mysqli_query($this->connection, $sql);
    }

    /**
     * Executa todas as migrations pendentes
     */
    public function migrate(): array
    {
        $executedMigrations = $this->getExecutedMigrations();
        $pendingMigrations = $this->getPendingMigrations($executedMigrations);
        
        if (empty($pendingMigrations)) {
            return ['message' => 'Nenhuma migration pendente.'];
        }

        $batch = $this->getNextBatch();
        $results = [];

        foreach ($pendingMigrations as $migration) {
            try {
                $this->executeMigration($migration, $batch);
                $results[] = "✓ Migration executada: {$migration}";
            } catch (\Exception $e) {
                $results[] = "✗ Erro na migration {$migration}: " . $e->getMessage();
                break;
            }
        }

        return $results;
    }

    /**
     * Reverte a última batch de migrations
     */
    public function rollback(): array
    {
        $lastBatch = $this->getLastBatch();
        
        if ($lastBatch === 0) {
            return ['message' => 'Nenhuma migration para reverter.'];
        }

        $migrationsToRollback = $this->getMigrationsByBatch($lastBatch);
        $results = [];

        foreach ($migrationsToRollback as $migration) {
            try {
                $this->rollbackMigration($migration);
                $results[] = "✓ Migration revertida: {$migration}";
            } catch (\Exception $e) {
                $results[] = "✗ Erro ao reverter migration {$migration}: " . $e->getMessage();
                break;
            }
        }

        return $results;
    }

    /**
     * Reverte todas as migrations
     */
    public function reset(): array
    {
        $allMigrations = $this->getAllExecutedMigrations();
        $results = [];

        foreach ($allMigrations as $migration) {
            try {
                $this->rollbackMigration($migration);
                $results[] = "✓ Migration revertida: {$migration}";
            } catch (\Exception $e) {
                $results[] = "✗ Erro ao reverter migration {$migration}: " . $e->getMessage();
                break;
            }
        }

        return $results;
    }

    /**
     * Obtém o status das migrations
     */
    public function status(): array
    {
        $executedMigrations = $this->getExecutedMigrations();
        $allMigrations = $this->getAllMigrationFiles();
        
        $status = [];
        
        foreach ($allMigrations as $migration) {
            $status[] = [
                'migration' => $migration,
                'status' => in_array($migration, $executedMigrations) ? 'Executada' : 'Pendente',
                'batch' => $this->getMigrationBatch($migration)
            ];
        }

        return $status;
    }

    /**
     * Executa uma migration específica
     */
    private function executeMigration(string $migrationName, int $batch): void
    {
        $migrationClass = $this->loadMigrationClass($migrationName);
        $migration = new $migrationClass($this->connection);
        
        $migration->up();
        
        $this->recordMigration($migrationName, $batch);
    }

    /**
     * Reverte uma migration específica
     */
    private function rollbackMigration(string $migrationName): void
    {
        $migrationClass = $this->loadMigrationClass($migrationName);
        $migration = new $migrationClass($this->connection);
        
        $migration->down();
        
        $this->removeMigrationRecord($migrationName);
    }

    /**
     * Carrega a classe da migration
     */
    private function loadMigrationClass(string $migrationName): string
    {
        $filePath = $this->migrationsPath . '/' . $migrationName . '.php';
        
        if (!file_exists($filePath)) {
            throw new \Exception("Arquivo de migration não encontrado: {$filePath}");
        }

        require_once $filePath;
        
        // Tenta extrair o nome da classe do arquivo
        $className = $this->extractClassNameFromFile($filePath);
        
        if (!$className || !class_exists($className)) {
            // Fallback para o método antigo
            $className = $this->getClassNameFromFileName($migrationName);
            
            if (!class_exists($className)) {
                throw new \Exception("Classe de migration não encontrada no arquivo: {$filePath}");
            }
        }

        return $className;
    }

    /**
     * Extrai o nome da classe diretamente do arquivo PHP
     */
    private function extractClassNameFromFile(string $filePath): ?string
    {
        $content = file_get_contents($filePath);
        
        // Procura por "class NomeDaClasse" com diferentes padrões
        $patterns = [
            '/class\s+(\w+)\s+extends\s+Migration/',
            '/class\s+(\w+)\s+extends\s+\\\?App\\\Infra\\\Database\\\Migration/',
            '/class\s+(\w+)/'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $content, $matches)) {
                return $matches[1];
            }
        }
        
        return null;
    }

    /**
     * Extrai o nome da classe do nome do arquivo
     */
    private function getClassNameFromFileName(string $fileName): string
    {
        // Remove a extensão .php
        $fileName = str_replace('.php', '', $fileName);
        
        // Remove o timestamp (primeira parte antes do underscore)
        $parts = explode('_', $fileName);
        
        // Remove o timestamp (primeiros 4 números)
        if (count($parts) > 1 && is_numeric($parts[0])) {
            array_shift($parts);
        }
        
        $className = '';
        foreach ($parts as $part) {
            $className .= ucfirst($part);
        }
        
        return $className;
    }

    /**
     * Registra uma migration como executada
     */
    private function recordMigration(string $migration, int $batch): void
    {
        $sql = "INSERT INTO `{$this->migrationsTable}` (migration, batch) VALUES (?, ?)";
        $stmt = mysqli_prepare($this->connection, $sql);
        mysqli_stmt_bind_param($stmt, 'si', $migration, $batch);
        mysqli_stmt_execute($stmt);
    }

    /**
     * Remove o registro de uma migration
     */
    private function removeMigrationRecord(string $migration): void
    {
        $sql = "DELETE FROM `{$this->migrationsTable}` WHERE migration = ?";
        $stmt = mysqli_prepare($this->connection, $sql);
        mysqli_stmt_bind_param($stmt, 's', $migration);
        mysqli_stmt_execute($stmt);
    }

    /**
     * Obtém migrations executadas
     */
    private function getExecutedMigrations(): array
    {
        $sql = "SELECT migration FROM `{$this->migrationsTable}` ORDER BY id";
        $result = mysqli_query($this->connection, $sql);
        
        $migrations = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $migrations[] = $row['migration'];
        }
        
        return $migrations;
    }

    /**
     * Obtém todas as migrations executadas
     */
    private function getAllExecutedMigrations(): array
    {
        $sql = "SELECT migration FROM `{$this->migrationsTable}` ORDER BY id DESC";
        $result = mysqli_query($this->connection, $sql);
        
        $migrations = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $migrations[] = $row['migration'];
        }
        
        return $migrations;
    }

    /**
     * Obtém migrations pendentes
     */
    private function getPendingMigrations(array $executedMigrations): array
    {
        $allMigrations = $this->getAllMigrationFiles();
        return array_diff($allMigrations, $executedMigrations);
    }

    /**
     * Obtém todos os arquivos de migration
     */
    private function getAllMigrationFiles(): array
    {
        if (!is_dir($this->migrationsPath)) {
            return [];
        }

        $files = glob($this->migrationsPath . '/*.php');
        $migrations = [];
        
        foreach ($files as $file) {
            $migrations[] = basename($file, '.php');
        }
        
        sort($migrations);
        return $migrations;
    }

    /**
     * Obtém o próximo número de batch
     */
    private function getNextBatch(): int
    {
        $sql = "SELECT MAX(batch) as max_batch FROM `{$this->migrationsTable}`";
        $result = mysqli_query($this->connection, $sql);
        $row = mysqli_fetch_assoc($result);
        
        return ($row['max_batch'] ?? 0) + 1;
    }

    /**
     * Obtém o último número de batch
     */
    private function getLastBatch(): int
    {
        $sql = "SELECT MAX(batch) as max_batch FROM `{$this->migrationsTable}`";
        $result = mysqli_query($this->connection, $sql);
        $row = mysqli_fetch_assoc($result);
        
        return $row['max_batch'] ?? 0;
    }

    /**
     * Obtém migrations por batch
     */
    private function getMigrationsByBatch(int $batch): array
    {
        $sql = "SELECT migration FROM `{$this->migrationsTable}` WHERE batch = ? ORDER BY id DESC";
        $stmt = mysqli_prepare($this->connection, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $batch);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $migrations = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $migrations[] = $row['migration'];
        }
        
        return $migrations;
    }

    /**
     * Obtém o batch de uma migration
     */
    private function getMigrationBatch(string $migration): ?int
    {
        $sql = "SELECT batch FROM `{$this->migrationsTable}` WHERE migration = ?";
        $stmt = mysqli_prepare($this->connection, $sql);
        mysqli_stmt_bind_param($stmt, 's', $migration);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        
        return $row['batch'] ?? null;
    }
} 