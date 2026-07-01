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
        $usuariosController = new UsuariosController();
        switch ($action) {
            case 'listar':
                $usuariosController->listar();
                break;
            case 'buscarPorID':
                $usuariosController->buscarPorID();
                break;
            case 'criar':
                $usuariosController->criar();
                break;
            case 'atualizar':
                $usuariosController->atualizar();
                break;
            case 'inativar':
                $usuariosController->inativar();
                break;
            case 'excluir':
                $usuariosController->excluir();
                break;
            default:
                http_response_code(404);
                echo "Ação de usuários não encontrada.";
        }
        break;

    case 'pessoas':
        $pessoasController = new PessoasController();
        switch ($action) {
            case 'listar':
                $pessoasController->listar();
                break;
            case 'buscarPorID':
                $pessoasController->buscarPorID();
                break;
            case 'criar':
                $pessoasController->criar();
                break;
            case 'atualizar':
                $pessoasController->atualizar();
                break;
            case 'inativar':
                $pessoasController->inativar();
                break;
            case 'excluir':
                $pessoasController->excluir();
                break;
            default:
                http_response_code(404);
                echo "Ação de pessoas não encontrada.";
        }
        break;

    case 'tipos_atendimentos':
        $tiposAtendimentosController = new TiposAtendimentosController();
        switch ($action) {
            case 'listar':
                $tiposAtendimentosController->listar();
                break;
            case 'buscarPorID':
                $tiposAtendimentosController->buscarPorID();
                break;
            case 'criar':
                $tiposAtendimentosController->criar();
                break;
            case 'atualizar':
                $tiposAtendimentosController->atualizar();
                break;
            case 'inativar':
                $tiposAtendimentosController->inativar();
                break;
            case 'excluir':
                $tiposAtendimentosController->excluir();
                break;
            default:
                http_response_code(404);
                echo "Ação de tipos de atendimentos não encontrada.";
        }
        break;

    case 'atendimentos':
        $atendimentosController = new AtendimentosController();
        switch ($action) {
            case 'listar':
                $atendimentosController->listar();
                break;
            case 'criar':
                $atendimentosController->criar();
                break;
            case 'atualizar':
                $atendimentosController->atualizar();
                break;
            case 'atualizarStatus':
                $atendimentosController->atualizarStatus();
                break;
            case 'visualizar':
                $atendimentosController->visualizar();
                break;
            default:
                http_response_code(404);
                echo "Ação de atendimentos não encontrada.";
        }
    default:
        http_response_code(404);
        exit "Controlador não encontrado.";
}

if (!method_exists($obj, $action)) {
    http_response_code(404);
    exit "Ação não encontrada.";
}

$obj->$action();
