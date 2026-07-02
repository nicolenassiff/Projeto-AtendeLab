<?php
//Controller da entidade de pessoas.
class PessoasController{
    private PDO $pdo;

    public function __construct(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    private function json(array $dados, int $status = 200): void{
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($status);
        echo json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    private function barrarAluno(): bool {
        if (isset($_SESSION['usuario']['perfil']) && $_SESSION['usuario']['perfil'] === 'aluno') {
            $this->json(['erro' => 'Acesso negado: Alunos não têm permissão para gerenciar pessoas.'], 403);
            return true;
        }
        return false;
    }

    public function listar(): void{
        if ($this->barrarAluno()) return;

        // Alterado: Adicionado o JOIN com a tabela de usuários para trazer o nome do usuário relacionado
        $sql = 'SELECT p.id_pessoas AS id, p.nome_pessoas AS nome, p.documento, p.telefone, p.email,
                       p.curso, p.periodo, p.status_pessoas AS status, p.criado_em, p.usuario_id,
                       u.nome AS usuario_relacionado
                FROM pessoas p
                LEFT JOIN usuarios u ON p.usuario_id = u.id
                ORDER BY p.id_pessoas DESC';

        $this->json($this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC));
    }

    public function buscarPorID(): void{
        if ($this->barrarAluno()) return;

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id){
            $this->json(['erro' => 'ID inválido.'], 400);
            return;
        }

        $sql = 'SELECT id_pessoas AS id, nome_pessoas AS nome, documento, telefone, email,
                       curso, periodo, status_pessoas AS status, criado_em, usuario_id
                FROM pessoas
                WHERE id_pessoas = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $pessoa = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pessoa){
            $this->json(['erro' => 'Pessoa não encontrada.'], 404);
            return;
        }

        $this->json($pessoa);
    }

    public function criar(): void{
        if ($this->barrarAluno()) return;

        $nome        = trim($_POST['nome'] ?? '');
        $documento   = trim($_POST['documento']    ?? '');
        $telefone    = trim($_POST['telefone']     ?? '');
        $email       = trim($_POST['email']        ?? '');
        $curso       = trim($_POST['curso']        ?? '');
        $periodo     = trim($_POST['periodo']      ?? '');
        $status      = $_POST['status']    ?? 'ativo';
        $observacoes = trim($_POST['observacoes'] ?? '');
        // Novo campo recebido do formulário
        $usuario_id  = filter_var($_POST['usuario_id'] ?? null, FILTER_VALIDATE_INT) ?: null;

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
                        (nome_pessoas, documento, telefone, email, curso, periodo, status_pessoas, observacoes, usuario_id)
                    VALUES
                        (:nome, :documento, :telefone, :email, :curso, :periodo, :status, :observacoes, :usuario_id)'
            );
            $stmt->execute(compact('nome', 'documento', 'telefone', 'email', 'curso', 'periodo', 'status', 'observacoes', 'usuario_id'));

            $this->json(['mensagem' => 'Pessoa cadastrada com sucesso.', 'id' => (int) $this->pdo->lastInsertId()], 201);

        } catch (PDOException $e) {
            $this->json(['erro' => 'Erro ao cadastrar pessoa: ' . $e->getMessage()], 400);
        }
    }

    public function atualizar(): void{
        if ($this->barrarAluno()) return;

        $id          = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        $nome        = trim($_POST['nome'] ?? '');
        $documento   = trim($_POST['documento']    ?? '');
        $telefone    = trim($_POST['telefone']     ?? '');
        $email       = trim($_POST['email']        ?? '');
        $curso       = trim($_POST['curso']        ?? '');
        $periodo     = trim($_POST['periodo']      ?? '');
        $status      = $_POST['status']    ?? 'ativo';
        $observacoes = trim($_POST['observacoes'] ?? '');
        $usuario_id  = filter_var($_POST['usuario_id'] ?? null, FILTER_VALIDATE_INT) ?: null;

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
                        observacoes     = :observacoes,
                        usuario_id      = :usuario_id
                    WHERE id_pessoas = :id');

            $stmt->execute(compact('nome', 'documento', 'telefone', 'email', 'curso', 'periodo', 'status', 'observacoes', 'usuario_id', 'id'));

            $this->json(['mensagem' => 'Pessoa atualizada com sucesso.']);

        } catch (PDOException $e) {
            $this->json(['erro' => 'Erro ao atualizar pessoa.'], 500);
        }
    }

    public function inativar(): void {
        if ($this->barrarAluno()) return;

        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

        if (!$id) {
            $this->json(['erro' => 'ID inválido.'], 422);
            return;
        }

        $stmt = $this->pdo->prepare(
            'UPDATE pessoas SET status_pessoas = :status WHERE id_pessoas = :id'
        );

        $stmt->execute(['status' => 'inativo', 'id' => $id]);
        $this->json(['mensagem' => 'Pessoa inativada com sucesso.']);
    }

    public function reativar(): void {
        if ($this->barrarAluno()) return;

        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

        if (!$id) {
            $this->json(['erro' => 'ID inválido.'], 422);
            return;
        }

        $stmt = $this->pdo->prepare(
            'UPDATE pessoas SET status_pessoas = :status WHERE id_pessoas = :id'
        );

        $stmt->execute(['status' => 'ativo', 'id' => $id]);
        $this->json(['mensagem' => 'Pessoa reativada com sucesso.']);
    }
}