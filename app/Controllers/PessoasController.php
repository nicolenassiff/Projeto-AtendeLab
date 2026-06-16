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
                ORDER BY id_pessoas DESC';
        
        $stmt = $this->pdo->query($sql);
        $pessoas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($pessoas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    }

    public function buscarPorID(): void{
        header('Content-Type: application/json; charset=utf-8');
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id){
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido.']);
            return;
        }

        $sql = 'SELECT id_pessoas, nome_pessoas, documento, telefone, curso, periodo, status_pessoas, criado_em
                FROM pessoas
                WHERE id_pessoas = :id';
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario){
            http_response_code(404);
            echo json_encode(['erro' => 'Pessoa não encontrado.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        echo json_encode($usuario, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function criar(): void
    {
        header('Content-Type: application/json; charset=utf-8');
 
        $nome      = trim($_POST['nome_pessoas'] ?? '');
        $documento = trim($_POST['documento']    ?? '');
        $telefone  = trim($_POST['telefone']     ?? '');
        $curso     = trim($_POST['curso']        ?? '');
        $periodo   = trim($_POST['periodo']      ?? '');
        $status    = $_POST['status_pessoas']    ?? 'ativo';
 
        if ($nome === '' || $documento === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'Nome e documento são obrigatórios.']);
            return;
        }
 
        
        if (!in_array($status, ['ativo', 'inativo'], true)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Status inválido. Use: ativo ou inativo.']);
            return;
        }
 
        try {
            $sql = 'INSERT INTO pessoas
                        (nome_pessoas, documento, telefone, curso, periodo, status_pessoas)
                    VALUES
                        (:nome, :documento, :telefone, :curso, :periodo, :status)';
 
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nome',      $nome);
            $stmt->bindValue(':documento', $documento);
            $stmt->bindValue(':telefone',  $telefone  ?: null);
            $stmt->bindValue(':curso',     $curso     ?: null);
            $stmt->bindValue(':periodo',   $periodo   ?: null);
            $stmt->bindValue(':status',    $status);
            $stmt->execute();
 
            http_response_code(201);
            echo json_encode([
                'mensagem' => 'Pessoa cadastrada com sucesso.',
                'id'       => $this->pdo->lastInsertId()
            ], JSON_UNESCAPED_UNICODE);
 
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao cadastrar pessoa.']);
        }
    }

     public function atualizar(): void
    {
        header('Content-Type: application/json; charset=utf-8');
 
        $id        = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nome      = trim($_POST['nome_pessoas'] ?? '');
        $documento = trim($_POST['documento']    ?? '');
        $telefone  = trim($_POST['telefone']     ?? '');
        $curso     = trim($_POST['curso']        ?? '');
        $periodo   = trim($_POST['periodo']      ?? '');
        $status    = $_POST['status_pessoas']    ?? 'ativo';
 
        if (!$id || $nome === '' || $documento === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'ID, nome e documento são obrigatórios.']);
            return;
        }
 
        if (!in_array($status, ['ativo', 'inativo'], true)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Status inválido. Use: ativo ou inativo.']);
            return;
        }
 
        try {
            $sql = 'UPDATE pessoas
                    SET nome_pessoas    = :nome,
                        documento       = :documento,
                        telefone        = :telefone,
                        curso           = :curso,
                        periodo         = :periodo,
                        status_pessoas  = :status
                    WHERE id_pessoas = :id';
 
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nome',      $nome);
            $stmt->bindValue(':documento', $documento);
            $stmt->bindValue(':telefone',  $telefone  ?: null);
            $stmt->bindValue(':curso',     $curso     ?: null);
            $stmt->bindValue(':periodo',   $periodo   ?: null);
            $stmt->bindValue(':status',    $status);
            $stmt->bindValue(':id',        $id, PDO::PARAM_INT);
            $stmt->execute();
 
            echo json_encode(
                ['mensagem' => 'Pessoa atualizada com sucesso.'],
                JSON_UNESCAPED_UNICODE
            );
 
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao atualizar pessoa.']);
        }
    }

    public function inativar(): void{
        header('Content-Type: application/json; charset=utf-8');
 
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
 
        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido.']);
            return;
        }
 
        try {
            $sql  = 'UPDATE pessoas SET status_pessoas = :status WHERE id_pessoas = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':status', 'inativo');
            $stmt->bindValue(':id',     $id, PDO::PARAM_INT);
            $stmt->execute();
 
            echo json_encode(
                ['mensagem' => 'Pessoa inativada com sucesso.'],
                JSON_UNESCAPED_UNICODE
            );
 
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao inativar pessoa.']);
        }
    }

    public function excluir(): void
    {
        header('Content-Type: application/json; charset=utf-8');
 
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
 
        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido.']);
            return;
        }
 
        try {
            $sql  = 'DELETE FROM pessoas WHERE id_pessoas = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
 
            echo json_encode(
                ['mensagem' => 'Pessoa excluída com sucesso.'],
                JSON_UNESCAPED_UNICODE
            );
 
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao excluir pessoa. Verifique se há atendimentos vinculados.']);
        }
    }
}
