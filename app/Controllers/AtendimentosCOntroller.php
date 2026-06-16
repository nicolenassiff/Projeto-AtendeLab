<?php
//Controller da entidade de atendimentos.
class AtendimentosController{
    private PDO $pdo;

    public function __construct()
    {
        require_once __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    public function listar(): void{
        header('Content-Type: application/json; charset=utf-8');

        $sql = 'SELECT id_atendimentos, pessoa_id, tipo_atendimento_id, usuario_id, data_atendimento, hora_atendimento, descricao, observacao, status, criado_em
                FROM atendimentos
                ORDER BY id DESC';
        
        $stmt = $this->pdo->query($sql);
        $atendimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($atendimentos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    }

    public function criar(): void{
        // a fazer
    }

    public function atualizar(): void{
        // a fazer
    }

    public function status(): void{
        // a fazer
    }

    public function visualizar(): void{
        // a fazer
    }
}
