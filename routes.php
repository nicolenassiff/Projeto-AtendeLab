<?php
require_once __DIR__ . '/app/Controllers/UsuariosController.php';
require_once __DIR__ . '/app/Controllers/PessoasController.php';
require_once __DIR__ . '/app/Controllers/TiposAtendimentosController.php';
require_once __DIR__ . '/app/Controllers/AtendimentosController.php';
require_once __DIR__ . '/app/Controllers/AuthController.php';

$controller = $_GET['controller'] ?? 'auth';
$action = $_GET['action'] ?? 'login';

if ($controller === 'auth') {
    $auth = new AuthController();

    switch($action) {
        case 'login':
            $auth->exibirLogin();
            break;
        case 'entrar':
            $auth->entrar();
            break;
        case 'dashboard':
            exigirAutenticacao();
            $auth->dashboard();
            break;
        case 'logout':
            $auth->logout();
            break;
        default:
            http_response_code(404);
            echo "Ação de autenticação não encontrada.";
    }
    exit;
}

exigirAutenticacao();

switch($controller) {
    case 'usuarios':
        $usuarios = new UsuariosController();
        switch ($action) {
            case 'listar':
                $usuarios->listar();
                break;
            case 'buscar':
                $usuarios->buscarPorID();
                break;
            case 'criar':
                $usuarios->criar();
                break;
            case 'atualizar':
                $usuarios->atualizar();
                break;
            case 'inativar':
                $usuarios->inativar();
                break;
            case 'excluir':
                $usuarios->excluir();
                break;
            default:
                http_response_code(404);
                echo "Ação de usuários não encontrada.";
        }
        break;

    case 'pessoas':
        $pessoas = new PessoasController();
        switch ($action) {
            case 'listar':
                $pessoas->listar();
                break;
            case 'buscar':
                $pessoas->buscarPorID();
                break;
            case 'criar':
                $pessoas->criar();
                break;
            case 'atualizar':
                $pessoas->atualizar();
                break;
            case 'inativar':
                $pessoas->inativar();
                break;
            case 'reativar':
                $pessoas->reativar();
                break;
            default:
                http_response_code(404);
                echo "Ação de pessoas não encontrada.";
        }
        break;

    case 'tipos':
        exigirAutenticacao();
        $tiposAtendimentos = new TiposAtendimentosController();
        switch ($action) {
            case 'listar':
                $tiposAtendimentos->listar();
                break;
            case 'buscar':
                $tiposAtendimentos->buscarPorID();
                break;
            case 'criar':
                $tiposAtendimentos->criar();
                break;
            case 'atualizar':
                $tiposAtendimentos->atualizar();
                break;
            case 'inativar':
                $tiposAtendimentos->inativar();
                break;
            case 'excluir':
                $tiposAtendimentos->excluir();
                break;
            case 'reativar':
                $tiposAtendimentos->reativar();
                break;
            default:
                http_response_code(404);
                echo 'Ação de tipos de atendimento não encontrada.';
        }
        break;

    case 'atendimentos':
        exigirAutenticacao();
        $atendimentos = new AtendimentosController();
        switch ($action) {
            case 'listar':
                $atendimentos->listar();
                break;
            case 'criar':
                $atendimentos->criar();
                break;
            case 'atualizar':
                $atendimentos->atualizar();
                break;
            case 'atualizarStatus':
                $atendimentos->atualizarStatus();
                break;
            case 'visualizar':
                $atendimentos->visualizar();
                break;
            default:
                http_response_code(404);
                echo 'Ação de tipos de atendimento não encontrada.';
        }
        break;
    case 'dashboard':
        require_once __DIR__ . '/app/Controllers/DashboardController.php';
        $dashboard = new DashboardController();
        switch ($action) {
         case 'resumo':
                $dashboard->resumo();
                break;
            default:
                http_response_code(404);
                echo "Ação de dashboard não encontrada.";
        }
        break;
    case 'frontend':
        require_once __DIR__ . '/app/Controllers/FrontendController.php';
        $frontend = new FrontendController();
        switch ($action) {
            case 'pessoas':
                $frontend->pessoas();
                break;
            case 'tipos':
                $frontend->tiposAtendimentos();
                break;
            case 'atendimentos':
                $frontend->atendimentos();
                break;
            default:
                http_response_code(404);
                echo "Ação de frontend não encontrada.";
        }
        break;
    default:
        http_response_code(404);
        echo 'Ação de tipos de atendimento não encontrada.';
}
