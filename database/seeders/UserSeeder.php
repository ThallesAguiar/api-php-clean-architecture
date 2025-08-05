<?php

use App\Infra\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Limpa a tabela antes de inserir
        $this->truncate('users');
        
        // Insere usuários de exemplo
        $this->insertMultiple('users', [
            [
                'name' => 'João Silva',
                'email' => 'joao@example.com',
                'password' => password_hash('123456', PASSWORD_DEFAULT),
                'active' => true
            ],
            [
                'name' => 'Maria Santos',
                'email' => 'maria@example.com',
                'password' => password_hash('123456', PASSWORD_DEFAULT),
                'active' => true
            ],
            [
                'name' => 'Pedro Oliveira',
                'email' => 'pedro@example.com',
                'password' => password_hash('123456', PASSWORD_DEFAULT),
                'active' => false
            ]
        ]);
        
        echo "Usuários de exemplo inseridos com sucesso!\n";
    }
} 