<?php

class DashboardController
{
    private PDO $pdo;

    public function __construct()
    {
        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    private function json(array $dados, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    }

    public function resumo(): void
    {
        try {
            $totalPessoas = (int) $this->pdo
                ->query("SELECT COUNT(*) FROM pessoas WHERE status = 'ativo'")
                ->fetchColumn();

            $totalTipos = (int) $this->pdo
                ->query("SELECT COUNT(*) FROM tipos_atendimentos WHERE status = 'ativo'")
                ->fetchColumn();

            $totalAtendimentos = (int) $this->pdo
                ->query('SELECT COUNT(*) FROM atendimentos')
                ->fetchColumn();

            $sql = 'SELECT a.id, p.nome AS pessoa_nome,
                           t.nome AS tipo_nome,
                           u.nome AS responsavel_nome,
                           a.descricao, a.status,
                           a.data_atendimento, a.horario_atendimento,
                           a.observacao_final
                    FROM atendimentos a
                    INNER JOIN pessoas p ON p.id = a.pessoa_id
                    INNER JOIN tipos_atendimentos t ON t.id = a.tipo_atendimento_id
                    INNER JOIN usuarios u ON u.id = a.usuario_id
                    ORDER BY a.id DESC
                    LIMIT 5';

            $recentes = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

            $this->json([
                'indicadores' => [
                    'total_pessoas' => $totalPessoas,
                    'total_tipos' => $totalTipos,
                    'total_atendimentos' => $totalAtendimentos,
                ],
                'atendimentos_recentes' => $recentes,
            ]);
        } catch (PDOException $e) {
            $this->json(['erro' => 'Não foi possível carregar o resumo do dashboard.'], 400);
        }
    }
}
