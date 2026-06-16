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

         $sql = 'SELECT
                    a.id_atendimentos,
                    p.nome_pessoas          AS pessoa,
                    t.nome                  AS tipo_atendimento,
                    u.nome                  AS usuario,
                    a.data_atendimento,
                    a.hora_atendimento,
                    a.descricao,
                    a.observacao,
                    a.status,
                    a.criado_em
                FROM atendimentos a
                INNER JOIN pessoas            p ON p.id_pessoas            = a.pessoa_id
                INNER JOIN tipos_atendimentos t ON t.id_tiposatendimentos  = a.tipo_atendimento_id
                INNER JOIN usuarios           u ON u.id                    = a.usuario_id
                ORDER BY a.id_atendimentos DESC';
        
        $stmt = $this->pdo->query($sql);
        $atendimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($atendimentos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    }

    public function criar(): void{
        header('Content-Type: application/json; charset=utf-8');
 
        $pessoaId      = filter_input(INPUT_POST, 'pessoa_id',           FILTER_VALIDATE_INT);
        $tipoId        = filter_input(INPUT_POST, 'tipo_atendimento_id', FILTER_VALIDATE_INT);
        $usuarioId     = filter_input(INPUT_POST, 'usuario_id',          FILTER_VALIDATE_INT);
        $data          = trim($_POST['data_atendimento']  ?? '');
        $hora          = trim($_POST['hora_atendimento']  ?? '');
        $descricao     = trim($_POST['descricao']         ?? '');
        $observacao    = trim($_POST['observacao']        ?? '');
        $status        = $_POST['status']                 ?? 'aberto';
 
        if (!$pessoaId || !$tipoId || !$usuarioId || $data === '' || $hora === '') {
            http_response_code(400);
            echo json_encode([
                'erro' => 'pessoa_id, tipo_atendimento_id, usuario_id, data_atendimento e hora_atendimento são obrigatórios.'
            ]);
            return;
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Formato de data inválido. Use YYYY-MM-DD.']);
            return;
        }

        if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $hora)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Formato de hora inválido. Use HH:MM ou HH:MM:SS.']);
            return;
        }
 
        if (!in_array($status, ['aberto', 'em_andamento', 'concluido'], true)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Status inválido. Use: aberto, em_andamento ou concluido.']);
            return;
        }
 
        try {
            $sql = 'INSERT INTO atendimentos
                        (pessoa_id, tipo_atendimento_id, usuario_id,
                         data_atendimento, hora_atendimento,
                         descricao, observacao, status)
                    VALUES
                        (:pessoa_id, :tipo_id, :usuario_id,
                         :data, :hora,
                         :descricao, :observacao, :status)';
 
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':pessoa_id',  $pessoaId,  PDO::PARAM_INT);
            $stmt->bindValue(':tipo_id',    $tipoId,    PDO::PARAM_INT);
            $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
            $stmt->bindValue(':data',       $data);
            $stmt->bindValue(':hora',       $hora);
            $stmt->bindValue(':descricao',  $descricao  ?: null);
            $stmt->bindValue(':observacao', $observacao ?: null);
            $stmt->bindValue(':status',     $status);
            $stmt->execute();
 
            http_response_code(201);
            echo json_encode([
                'mensagem' => 'Atendimento cadastrado com sucesso.',
                'id'       => $this->pdo->lastInsertId()
            ], JSON_UNESCAPED_UNICODE);
 
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao cadastrar atendimento.']);
        }
    }

    public function atualizarStatus(): void
    {
        header('Content-Type: application/json; charset=utf-8');
 
        $id     = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $status = $_POST['status'] ?? '';
 
        if (!$id || $status === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'ID e status são obrigatórios.']);
            return;
        }
 
        if (!in_array($status, ['aberto', 'em_andamento', 'concluido'], true)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Status inválido. Use: aberto, em_andamento ou concluido.']);
            return;
        }
 
        try {
            $sql  = 'UPDATE atendimentos SET status = :status WHERE id_atendimentos = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':id',     $id, PDO::PARAM_INT);
            $stmt->execute();
 
            echo json_encode(
                ['mensagem' => 'Status do atendimento atualizado com sucesso.'],
                JSON_UNESCAPED_UNICODE
            );
 
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao atualizar status do atendimento.']);
        }
    }

    public function atualizar(): void
    {
        header('Content-Type: application/json; charset=utf-8');
 
        $id         = filter_input(INPUT_POST, 'id',                   FILTER_VALIDATE_INT);
        $pessoaId   = filter_input(INPUT_POST, 'pessoa_id',            FILTER_VALIDATE_INT);
        $tipoId     = filter_input(INPUT_POST, 'tipo_atendimento_id',  FILTER_VALIDATE_INT);
        $usuarioId  = filter_input(INPUT_POST, 'usuario_id',           FILTER_VALIDATE_INT);
        $data       = trim($_POST['data_atendimento']  ?? '');
        $hora       = trim($_POST['hora_atendimento']  ?? '');
        $descricao  = trim($_POST['descricao']         ?? '');
        $observacao = trim($_POST['observacao']        ?? '');
        $status     = $_POST['status']                 ?? 'aberto';
 
        if (!$id || !$pessoaId || !$tipoId || !$usuarioId || $data === '' || $hora === '') {
            http_response_code(400);
            echo json_encode([
                'erro' => 'id, pessoa_id, tipo_atendimento_id, usuario_id, data_atendimento e hora_atendimento são obrigatórios.'
            ]);
            return;
        }
 
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Formato de data inválido. Use YYYY-MM-DD.']);
            return;
        }
 
        if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $hora)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Formato de hora inválido. Use HH:MM ou HH:MM:SS.']);
            return;
        }
 
        if (!in_array($status, ['aberto', 'em_andamento', 'concluido'], true)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Status inválido. Use: aberto, em_andamento ou concluido.']);
            return;
        }
 
        try {
            $sql = 'UPDATE atendimentos
                    SET pessoa_id           = :pessoa_id,
                        tipo_atendimento_id = :tipo_id,
                        usuario_id          = :usuario_id,
                        data_atendimento    = :data,
                        hora_atendimento    = :hora,
                        descricao           = :descricao,
                        observacao          = :observacao,
                        status              = :status
                    WHERE id_atendimentos = :id';
 
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':pessoa_id',  $pessoaId,  PDO::PARAM_INT);
            $stmt->bindValue(':tipo_id',    $tipoId,    PDO::PARAM_INT);
            $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
            $stmt->bindValue(':data',       $data);
            $stmt->bindValue(':hora',       $hora);
            $stmt->bindValue(':descricao',  $descricao  ?: null);
            $stmt->bindValue(':observacao', $observacao ?: null);
            $stmt->bindValue(':status',     $status);
            $stmt->bindValue(':id',         $id, PDO::PARAM_INT);
            $stmt->execute();
 
            echo json_encode(
                ['mensagem' => 'Atendimento atualizado com sucesso.'],
                JSON_UNESCAPED_UNICODE
            );
 
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao atualizar atendimento.']);
        }
    }

    public function visualizar(): void
    {
        header('Content-Type: application/json; charset=utf-8');
 
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
 
        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido.']);
            return;
        }
 
        $sql = 'SELECT
                    -- Dados do atendimento
                    a.id_atendimentos,
                    a.data_atendimento,
                    a.hora_atendimento,
                    a.descricao,
                    a.observacao,
                    a.status,
                    a.criado_em,
 
                    -- Dados completos da pessoa atendida
                    a.pessoa_id,
                    p.nome_pessoas   AS pessoa_nome,
                    p.documento      AS pessoa_documento,
                    p.telefone       AS pessoa_telefone,
                    p.curso          AS pessoa_curso,
                    p.periodo        AS pessoa_periodo,
                    p.status_pessoas AS pessoa_status,
 
                    -- Dados completos do tipo de atendimento
                    a.tipo_atendimento_id,
                    t.nome           AS tipo_nome,
                    t.descricao      AS tipo_descricao,
                    t.status         AS tipo_status,
 
                    -- Dados do usuário responsável
                    a.usuario_id,
                    u.nome           AS usuario_nome,
                    u.email          AS usuario_email,
                    u.perfil         AS usuario_perfil
 
                FROM atendimentos a
                INNER JOIN pessoas            p ON p.id_pessoas           = a.pessoa_id
                INNER JOIN tipos_atendimentos t ON t.id_tiposatendimentos = a.tipo_atendimento_id
                INNER JOIN usuarios           u ON u.id                   = a.usuario_id
                WHERE a.id_atendimentos = :id';
 
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
 
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
 
        if (!$row) {
            http_response_code(404);
            echo json_encode(['erro' => 'Atendimento não encontrado.']);
            return;
        }
 
        $statusLabels = [
            'aberto'       => 'Aberto',
            'em_andamento' => 'Em andamento',
            'concluido'    => 'Concluído',
        ];
 
        $resposta = [
            'atendimento' => [
                'id'           => (int) $row['id_atendimentos'],
                'data'         => $row['data_atendimento'],
                'hora'         => $row['hora_atendimento'],
                'descricao'    => $row['descricao'],
                'observacao'   => $row['observacao'],
                'status'       => $row['status'],
                'status_label' => $statusLabels[$row['status']] ?? $row['status'],
                'criado_em'    => $row['criado_em'],
            ],
            'pessoa' => [
                'id'        => (int) $row['pessoa_id'],
                'nome'      => $row['pessoa_nome'],
                'documento' => $row['pessoa_documento'],
                'telefone'  => $row['pessoa_telefone'],
                'curso'     => $row['pessoa_curso'],
                'periodo'   => $row['pessoa_periodo'],
                'status'    => $row['pessoa_status'],
            ],
            'tipo_atendimento' => [
                'id'        => (int) $row['tipo_atendimento_id'],
                'nome'      => $row['tipo_nome'],
                'descricao' => $row['tipo_descricao'],
                'status'    => $row['tipo_status'],
            ],
            'usuario' => [
                'id'     => (int) $row['usuario_id'],
                'nome'   => $row['usuario_nome'],
                'email'  => $row['usuario_email'],
                'perfil' => $row['usuario_perfil'],
            ],
        ];
 
        echo json_encode($resposta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
