<?php

namespace App\Infra\Database;

class SeederManager
{
    private $connection;
    private $seedersPath;

    public function __construct($connection, string $seedersPath = 'database/seeders')
    {
        $this->connection = $connection;
        $this->seedersPath = $seedersPath;
    }

    /**
     * Executa todos os seeders
     */
    public function run(): array
    {
        $seeders = $this->getAllSeeders();
        $results = [];

        foreach ($seeders as $seeder) {
            try {
                $this->executeSeeder($seeder);
                $results[] = "✓ Seeder executado: {$seeder}";
            } catch (\Exception $e) {
                $results[] = "✗ Erro no seeder {$seeder}: " . $e->getMessage();
                break;
            }
        }

        return $results;
    }

    /**
     * Executa um seeder específico
     */
    public function runSeeder(string $seederName): array
    {
        try {
            $this->executeSeeder($seederName);
            return ["✓ Seeder executado: {$seederName}"];
        } catch (\Exception $e) {
            return ["✗ Erro no seeder {$seederName}: " . $e->getMessage()];
        }
    }

    /**
     * Lista todos os seeders disponíveis
     */
    public function list(): array
    {
        return $this->getAllSeeders();
    }

    /**
     * Executa um seeder específico
     */
    private function executeSeeder(string $seederName): void
    {
        $seederClass = $this->loadSeederClass($seederName);
        $seeder = new $seederClass($this->connection);
        
        $seeder->run();
    }

    /**
     * Carrega a classe do seeder
     */
    private function loadSeederClass(string $seederName): string
    {
        $filePath = $this->seedersPath . '/' . $seederName . '.php';
        
        if (!file_exists($filePath)) {
            throw new \Exception("Arquivo de seeder não encontrado: {$filePath}");
        }

        require_once $filePath;
        
        $className = $this->getClassNameFromFileName($seederName);
        
        if (!class_exists($className)) {
            throw new \Exception("Classe de seeder não encontrada: {$className}");
        }

        return $className;
    }

    /**
     * Extrai o nome da classe do nome do arquivo
     */
    private function getClassNameFromFileName(string $fileName): string
    {
        $parts = explode('_', $fileName);
        
        $className = '';
        foreach ($parts as $part) {
            $className .= ucfirst($part);
        }
        
        return $className;
    }

    /**
     * Obtém todos os arquivos de seeder
     */
    private function getAllSeeders(): array
    {
        if (!is_dir($this->seedersPath)) {
            return [];
        }

        $files = glob($this->seedersPath . '/*.php');
        $seeders = [];
        
        foreach ($files as $file) {
            $seeders[] = basename($file, '.php');
        }
        
        sort($seeders);
        return $seeders;
    }
} 