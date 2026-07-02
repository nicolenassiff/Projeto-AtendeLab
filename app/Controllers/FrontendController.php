<?php

class FrontendController
{
    private function verificarPermissao(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['usuario']['perfil']) && $_SESSION['usuario']['perfil'] === 'aluno') {
            header('Location: ?controller=frontend&action=atendimentos');
            exit;
        }
    }

    public function pessoas(): void
    {
        $this->verificarPermissao();
        require __DIR__ . '/../Views/pessoas/index.php';
    }

    public function tiposAtendimentos(): void
    {
        $this->verificarPermissao();
        require __DIR__ . '/../Views/tipos-atendimentos/index.php';
    }

    public function atendimentos(): void
    {
        require __DIR__ . '/../Views/atendimentos/index.php';
    }
}