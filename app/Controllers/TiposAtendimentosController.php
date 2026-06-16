<?php
//Controller da entidade de tipo de atendimentos.
class TiposAtendimentosController{
    private PDO $pdo;

    public function __construct()
    {
        require_once __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    public function listar(): void{
        header('Content-Type: application/json; charset=utf-8');

        $sql = 'SELECT id_tiposatendimentos, nome, descricao, status, criado_em
                FROM tipos_atendimentos
                ORDER BY id DESC';
        
        $stmt = $this->pdo->query($sql);
        $tipos_atendimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($tipos_atendimentos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
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
