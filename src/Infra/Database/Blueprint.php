<?php

namespace App\Infra\Database;

class Blueprint
{
    private string $tableName;
    private array $columns = [];
    private array $indexes = [];
    private array $foreignKeys = [];

    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * Adiciona uma coluna ID auto increment
     */
    public function id(string $name = 'id'): self
    {
        $this->columns[] = "`{$name}` INT AUTO_INCREMENT PRIMARY KEY";
        return $this;
    }

    /**
     * Adiciona uma coluna string
     */
    public function string(string $name, int $length = 255): self
    {
        $this->columns[] = "`{$name}` VARCHAR({$length})";
        return $this;
    }

    /**
     * Adiciona uma coluna text
     */
    public function text(string $name): self
    {
        $this->columns[] = "`{$name}` TEXT";
        return $this;
    }

    /**
     * Adiciona uma coluna integer
     */
    public function integer(string $name): self
    {
        $this->columns[] = "`{$name}` INT";
        return $this;
    }

    /**
     * Adiciona uma coluna bigint
     */
    public function bigInteger(string $name): self
    {
        $this->columns[] = "`{$name}` BIGINT";
        return $this;
    }

    /**
     * Adiciona uma coluna decimal
     */
    public function decimal(string $name, int $precision = 8, int $scale = 2): self
    {
        $this->columns[] = "`{$name}` DECIMAL({$precision}, {$scale})";
        return $this;
    }

    /**
     * Adiciona uma coluna boolean
     */
    public function boolean(string $name): self
    {
        $this->columns[] = "`{$name}` BOOLEAN";
        return $this;
    }

    /**
     * Adiciona uma coluna timestamp
     */
    public function timestamp(string $name): self
    {
        $this->columns[] = "`{$name}` TIMESTAMP";
        return $this;
    }

    /**
     * Adiciona timestamps (created_at e updated_at)
     */
    public function timestamps(): self
    {
        // Usa sintaxe compatível com todas as versões do MySQL/MariaDB
        $this->columns[] = "`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        $this->columns[] = "`updated_at` TIMESTAMP DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP";
        return $this;
    }

    /**
     * Adiciona uma coluna nullable
     */
    public function nullable(): self
    {
        if (!empty($this->columns)) {
            $lastColumn = array_pop($this->columns);
            $this->columns[] = $lastColumn . " NULL";
        }
        return $this;
    }

    /**
     * Adiciona uma coluna com valor padrão
     */
    public function default($value): self
    {
        if (!empty($this->columns)) {
            $lastColumn = array_pop($this->columns);
            $defaultValue = is_string($value) ? "'{$value}'" : $value;
            $this->columns[] = $lastColumn . " DEFAULT {$defaultValue}";
        }
        return $this;
    }

    /**
     * Adiciona uma coluna unique
     */
    public function unique(): self
    {
        if (!empty($this->columns)) {
            $lastColumn = array_pop($this->columns);
            $this->columns[] = $lastColumn . " UNIQUE";
        }
        return $this;
    }

    /**
     * Adiciona um índice
     */
    public function index(string $column): self
    {
        $this->indexes[] = "INDEX `idx_{$this->tableName}_{$column}` (`{$column}`)";
        return $this;
    }

    /**
     * Adiciona uma chave estrangeira
     */
    public function foreignId(string $column): self
    {
        $this->columns[] = "`{$column}` INT";
        return $this;
    }

    /**
     * Referencia uma tabela para chave estrangeira
     */
    public function references(string $column): self
    {
        $this->foreignKeys[] = [
            'column' => $column,
            'references' => null,
            'on' => null
        ];
        return $this;
    }

    /**
     * Define a tabela referenciada
     */
    public function on(string $table): self
    {
        if (!empty($this->foreignKeys)) {
            $lastForeignKey = array_pop($this->foreignKeys);
            $lastForeignKey['on'] = $table;
            $this->foreignKeys[] = $lastForeignKey;
        }
        return $this;
    }

    /**
     * Gera o SQL para criar a tabela
     */
    public function toSql(): string
    {
        $sql = "CREATE TABLE IF NOT EXISTS `{$this->tableName}` (\n";
        $sql .= implode(",\n", $this->columns);
        
        if (!empty($this->indexes)) {
            $sql .= ",\n" . implode(",\n", $this->indexes);
        }

        if (!empty($this->foreignKeys)) {
            foreach ($this->foreignKeys as $foreignKey) {
                $sql .= ",\nFOREIGN KEY (`{$foreignKey['column']}`) REFERENCES `{$foreignKey['on']}`(`id`)";
            }
        }

        $sql .= "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        return $sql;
    }
} 