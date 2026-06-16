<?php
require_once __DIR__ . '/app/Controllers/UsuariosController.php';
require_once __DIR__ . '/app/Controllers/PessoasController.php';
require_once __DIR__ . '/app/Controllers/TiposAtendimentosController.php';
require_once __DIR__ . '/app/Controllers/AtendimentosController.php';
require_once __DIR__ . '/app/Controllers/AuthController.php';

$controller = $_GET['controller'] ?? 'home';
$action = $_GET['action'] ?? 'index';

switch ($controller) {
    case 'auth':
        $authController = new AuthController();

        switch ($action) {
            case 'login':
                $authController->exibirLogin();
                break;
            
            case 'entrar':
                $authController->entrar();
                break;

            case 'dashboard':
                $authController->dashboard();
                break;
            
            case 'logout':
                $authController->logout();
                break;

            default:
                http_response_code(404);
                echo '<h1>Ação de autenticação não encontrada.</h1>';
        }
        break;

    case 'usuarios':
        // exigirAutenticacao();
        $usuariosController = new UsuariosController();

        switch ($action) {
            case 'listar':
                $usuariosController->listar();
                break;
            
            case 'buscar':
                $usuariosController->buscarPorId();
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
                echo json_encode(['erro' => 'Ação de usuários não encontrada.']);
        }
        break;

    case 'pessoas': 
        $pessoasController = new PessoasController();

        switch ($action) {
            case 'listar':
                $pessoasController->listar();
                break;
            
            case 'buscar':
                $pessoasController->buscarPorId();
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
                echo json_encode(['erro' =>'Ação de pessoas não encontrada.']);
            }
        break;

    case 'tipos_atendimentos':
        $tipos_atendimentosController = new TiposAtendimentosController();

        switch ($action) {
            case 'listar':
                $tipos_atendimentosController->listar();
                break;
            
            case 'buscar':
                $tipos_atendimentosController->buscarPorId();
                break;

            case 'criar':
                $tipos_atendimentosController->criar();
                break;
            
            case 'atualizar':
                $tipos_atendimentosController->atualizar();
                break;
            
            case 'inativar':
                $tipos_atendimentosController->inativar();
                break;

            case 'excluir':
                $tipos_atendimentosController->excluir();
                break;

            default:
                http_response_code(404);
                echo json_encode(['erro' =>'Ação de tipos de atendimento não encontrada.']);
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
            
            case 'atualizarStatus':
                $atendimentosController->atualizarStatus();
                break;

            case 'atualizar':
                $atendimentosController->atualizar();
                break;

            case 'visualizar':
                $atendimentosController->visualizar();
                break;

            default:
                http_response_code(404);
                echo json_encode(['erro' =>'Ação de atendimento não encontrada.']);
            }
        break;
    
    default:
        http_response_code(404);
        echo 'Controller nao encontrado.';
}

