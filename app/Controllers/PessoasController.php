<?php
//Controller da entidade de pessoas.
class PessoasController{
    private PDO $pdo;

    public function __construct(){
        require_once __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    public function json(array $dados, int $status = 200): void{
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($status);
        echo json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function listar(): void{

        $sql = 'SELECT id_pessoas, nome_pessoas, documento, telefone, curso, periodo, status_pessoas, criado_em
                FROM pessoas
                ORDER BY id_pessoas DESC';
        
        $this->json($this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC));
    }

    public function buscarPorID(): void{
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id){
            $this->json(['erro' => 'ID inválido.'], 400);
            return;
        }

        $sql = 'SELECT id_pessoas, nome_pessoas, documento, telefone, curso, periodo, status_pessoas, criado_em
                FROM pessoas
                WHERE id_pessoas = :id';
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $pessoa = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pessoa){
            $this->json(['erro' => 'Pessoa não encontrado.'], 404);
            return;
        }

        $this->json($pessoa);
    }

    public function criar(): void{

        $nome      = trim($_POST['nome_pessoas'] ?? '');
        $documento = trim($_POST['documento']    ?? '');
        $telefone  = trim($_POST['telefone']     ?? '');
        $email     = trim($_POST['email']        ?? '');
        $curso     = trim($_POST['curso']        ?? '');
        $periodo   = trim($_POST['periodo']      ?? '');
        $status    = $_POST['status_pessoas']    ?? 'ativo';
        $observacoes = trim($_POST['observacoes'] ?? '');
 
        if ($nome === '' || $documento === '' || $email === '') {
            $this->json(['erro' => 'Nome, documento e email são obrigatórios.'], 400);
            return;
        }
 
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json(['erro' => 'Email inválido.'], 422);
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            $this->json(['erro' => 'Status inválido. Use: ativo ou inativo.'], 400);
            return;
        }
 
        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO pessoas
                        (nome_pessoas, documento, telefone, email, curso, periodo, status_pessoas, observacoes)
                    VALUES
                        (:nome, :documento, :telefone, :email, :curso, :periodo, :status, :observacoes)'
            );
            $stmt->execute(compact('nome', 'documento', 'telefone', 'email', 'curso', 'periodo', 'status', 'observacoes'));
 
            $this->json (['mensagem' => 'Pessoa cadastrada com sucesso.', 201]);
 
        } catch (PDOException $e) {
            $this->json(['erro' => 'Erro ao cadastrar pessoa.'], 400);
        }
    }

     public function atualizar(): void{

        $id        = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        $nome      = trim($_POST['nome_pessoas'] ?? '');
        $documento = trim($_POST['documento']    ?? '');
        $telefone  = trim($_POST['telefone']     ?? '');
        $email     = trim($_POST['email']        ?? '');
        $curso     = trim($_POST['curso']        ?? '');
        $periodo   = trim($_POST['periodo']      ?? '');
        $status    = $_POST['status_pessoas']    ?? 'ativo';
        $observacoes = trim($_POST['observacoes'] ?? '');

 
        if (!$id || $nome === '' || $documento === '' || $email === '') {
            $this->json(['erro' => 'Dados obrigatórios ausentes.'], 400);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json(['erro' => 'Email inválido.'], 422);
            return;
        }
 
        if (!in_array($status, ['ativo', 'inativo'], true)) {
            $this->json(['erro' => 'Status inválido. Use: ativo ou inativo.'], 400);
            return;
        }
 
        try {
            $stmt = $this->pdo->prepare(
                'UPDATE pessoas
                    SET nome_pessoas    = :nome,
                        documento       = :documento,
                        telefone        = :telefone,
                        email           = :email,
                        curso           = :curso,
                        periodo         = :periodo,
                        status_pessoas  = :status,
                        observacoes     = :observacoes
                    WHERE id_pessoas = :id');
 
            $stmt->execute(compact('nome', 'documento', 'telefone', 'email', 'curso', 'periodo', 'status', 'observacoes', 'id'));
 
            $this->json(['mensagem' => 'Pessoa atualizada com sucesso.']);
 
        } catch (PDOException $e) {
            $this->json(['erro' => 'Erro ao atualizar pessoa.'], 500);
        }
    }

    public function inativar(): void {
       
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
 
        if (!$id) {
            $this->json(['erro' => 'ID inválido.'], 422);
            return;
        }
 
        $stmt = $this->pdo->prepare(
            "UPDATE pessoas SET status_pessoas = :status WHERE id_pessoas = :id"
            );

        $stmt->execute(['id_pessoas' => $id]);
        $this->json(['mensagem' => 'Pessoa inativada com sucesso.']);
    }
}
