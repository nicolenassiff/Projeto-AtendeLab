<?php
//Controller da entidade de atendimentos.
class AtendimentosController{
    private PDO $pdo;

    public function __construct()
    {
        require_once __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    private function json(array $dados, int $status = 200): void{
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($status);
        echo json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function listar(): void{
        
         $sql = 'SELECT
                    a.id_atendimentos,
                    p.nome_pessoas          AS pessoa,
                    t.nome                  AS tipo_atendimento,
                    u.nome                  AS usuario,
                    a.data_atendimento,
                    a.hora_atendimento,
                    a.descricao,
                    a.observacao_final,
                    a.status,
                    a.criado_em
                FROM atendimentos a
                INNER JOIN pessoas            p ON p.id_pessoas            = a.pessoa_id
                INNER JOIN tipos_atendimentos t ON t.id_tiposatendimentos  = a.tipo_atendimento_id
                INNER JOIN usuarios           u ON u.id                    = a.usuario_id
                ORDER BY a.id_atendimentos DESC';
        
        $this->json($this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC));    
    }

    public function criar(): void{
        
        $pessoaId      = filter_var($_POST['pessoa_id'] ?? null, FILTER_VALIDATE_INT);
        $tipoId        = filter_var($_POST['tipo_atendimento_id'] ?? null, FILTER_VALIDATE_INT);
        $usuarioId     = filter_var($_POST['usuario_id'] ?? null, FILTER_VALIDATE_INT);
        $data          = $_POST['data_atendimento']  ?? '';
        $hora          = $_POST['hora_atendimento']  ?? '';
        $descricao     = trim($_POST['descricao']         ?? '');
        $status        = $_POST['status']                 ?? 'aberto';
 
        if (!$pessoaId || !$tipoId || !$usuarioId || $descricao === '' || $data === '' || $hora === '') {
            $this->json(['erro' => 'Preencha os campos obrigatórios.'], 422);
            return;
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
            $this->json(['erro' => 'Formato de data inválido. Use YYYY-MM-DD.'], 400);
            return;
        }

        if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $hora)) {
            $this->json(['erro' => 'Formato de hora inválido. Use HH:MM ou HH:MM:SS.'], 400);
            return;
        }
 
        if (!in_array($status, ['aberto', 'em_andamento', 'concluido'], true)) {
            $this->json(['erro' => 'Status inválido. Use: aberto, em_andamento ou concluido.'], 422);
            return;
        }
 
        $stmt = $this->pdo->prepare(
            'INSERT INTO atendimentos
                (pessoa_id, tipo_atendimento_id, usuario_id,
                data_atendimento, hora_atendimento,
                descricao, observacao, status)
            VALUES
                (:pessoa_id, :tipo_id, :usuario_id,
                :data, :hora,
                :descricao, :observacao, :status)';
        )

        $stmt->execute([
            ':pessoa_id'  => $pessoaId,
            ':tipo_id'    => $tipoId,
            ':usuario_id' => $usuarioId,
            ':data'       => $data,
            ':hora'       => $hora,
            ':descricao'  => $descricao,
            ':status'     => $status,
        ]);
 
        $this->json(['mensagem' => 'Atendimento criado com sucesso.'], 201);
    }

    public function atualizarStatus(): void
    {
        $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
        $status = $_POST['status'] ?? '';
        $observacao = trim($_POST['observacao_final'] ?? '');
 
        if (!$id || !in_array(
            $status, ['aberto', 'em_andamento', 'concluido'], true)) {
            $this->json(['erro' => 'ID ou status inválidos.'], 422);
            return;
        }
 
        if ($status === 'concluido' && $observacao === '') {
            $this->json(['erro' => 'Observação final é obrigatória ao concluir o atendimento.'], 422);
            return;
        }
 
        $stmt = $this->pdo->prepare(
            'UPDATE atendimentos SET status = :status, observacao_final = :observacao WHERE id_atendimentos = :id'
        );

        $stmt->execute([
            ':status' => $status,
            ':observacao' => $observacao,
            ':id' => $id,
        ]);

        $this->json(['mensagem' => 'Status do atendimento atualizado com sucesso.']);
    }

    public function atualizar(): void
    {
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
