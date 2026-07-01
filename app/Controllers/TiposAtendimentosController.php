<?php
//Controller da entidade de tipo de atendimentos.
class TiposAtendimentosController{
    private PDO $pdo;

    public function __construct()
    {
        require_once __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    private function json(array $dados, int $status = 200): void
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($status);
        echo json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function listar(): void{
        
        $sql = 'SELECT id_tiposatendimentos, nome, descricao, status, criado_em
                FROM tipos_atendimentos
                ORDER BY id_tiposatendimentos DESC';
        
        $this->json($this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC));
    
    }

     public function buscarPorId(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $ this->json(['erro' => 'ID inválido.'], 400);
            return;
        }
 
        $stmt = $this->pdo->prepare('SELECT id_tiposatendimentos, nome, descricao, status, criado_em
                FROM tipos_atendimentos
                WHERE id_tiposatendimentos = :id';)
 
        $stmt->execute(['id' => $id]);
        $tipo = $stmt->fetch(PDO::FETCH_ASSOC);
 
        if (!$tipo) {
            $this->json(['erro' => 'Tipo de atendimento não encontrado.'], 404);
            return;
        }
 
        $this->json($tipo);
    }

      public function criar(): void{
    
        $nome      = trim($_POST['nome']      ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $status    = $_POST['status']         ?? 'ativo';
 
        if ($nome === '') {
            $this->json(['erro' => 'Nome é obrigatório.'], 422);
            return;
        }
 
        if (!in_array($status, ['ativo', 'inativo'], true)) {
            $this->json(['erro' => 'Status inválido. Use: ativo ou inativo.'], 422);
            return;
        }
 
        $stmt = $this->pdo->prepare(
            'INSERT INTO tipos_atendimentos (nome, descricao, status)
            VALUES (:nome, :descricao, :status)'
        );

        $stmt->execute(compact('nome', 'descricao', 'status'));
        $this->json(['mensagem' => 'Tipo de atendimento cadastrado com sucesso.'], 201);
    }

    public function atualizar(): void{
        $id        = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
        $nome      = trim($_POST['nome']      ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $status    = $_POST['status']         ?? 'ativo';
 
        if (!$id || $nome === '') {
            $this->json(['erro' => 'ID e nome são obrigatórios.'], 400);
            return;
        }
 
        if (!in_array($status, ['ativo', 'inativo'], true)) {
            $this->json(['erro' => 'Status inválido. Use: ativo ou inativo.'], 400);
            return;
        }
 
        $stmt = $this->pdo->prepare(
            'UPDATE tipos_atendimentos
            SET nome      = :nome,
                descricao = :descricao,
                status    = :status
            WHERE id_tiposatendimentos = :id'
        );

        $stmt->execute(compact('nome', 'descricao', 'status', 'id'));
        $this->json(['mensagem' => 'Tipo de atendimento atualizado com sucesso.']);
 
    }

     public function inativar(): void{

        $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
 
        if (!$id) {
            $this->json(['erro' => 'ID inválido.'], 400);
            return;
        }
 
        $stmt = $this->pdo->prepare(
            'UPDATE tipos_atendimentos SET status = 'inativo' WHERE id_tiposatendimentos = :id'
            );
        $stmt->execute(['id_tiposatendimentos' => $id]);
 
            $this->json(['mensagem' => 'Tipo de atendimento inativado com sucesso.'], 200);
 
    }

     public function excluir(): void{
        header('Content-Type: application/json; charset=utf-8');
 
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
 
        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido.']);
            return;
        }
 
        try {
            $sql  = 'DELETE FROM tipos_atendimentos WHERE id_tiposatendimentos = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
 
            echo json_encode(
                ['mensagem' => 'Tipo de atendimento excluído com sucesso.'],
                JSON_UNESCAPED_UNICODE
            );
 
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao excluir tipo de atendimento. Verifique se há atendimentos vinculados.']);
        }
    }
}
