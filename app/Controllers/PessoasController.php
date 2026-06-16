<?php
//Controller da entidade de pessoas.
class PessoasController{
    private PDO $pdo;

    public function __construct()
    {
        require_once __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    public function listar(): void{
        header('Content-Type: application/json; charset=utf-8');

        $sql = 'SELECT id_pessoas, nome_pessoas, documento, telefone, curso, periodo, status_pessoas, criado_em
                FROM pessoas
                ORDER BY id DESC';
        
        $stmt = $this->pdo->query($sql);
        $pessoas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($pessoas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    }

    public function buscarPorId(): void{
        // a fazer
    }

    public function criar(): void{
        // a fazer
    }

    public function atualizar(): void{
        // a fazer
    }

    public function excluir(): void{
        // a fazer
    }
}
