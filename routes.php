<?php
require_once __DIR__ . '/app/Controllers/UsuariosController.php';
require_once __DIR__ . '/app/Controllers/PessoasController.php';
require_once __DIR__ . '/app/Controllers/TiposAtendimentosController.php';
require_once __DIR__ . '/app/Controllers/AtendimentosController.php';

$controller = $_GET['controller'] ?? 'home';
$action = $_GET['action'] ?? 'index';

if ($controller === 'usuarios') {
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
            echo json_encode(['erro' => 'Ação de usuários não encontrada.']);
            break;
    }
} elseif ($controller === 'pessoas') {
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
            echo json_encode(['erro' =>'Ação de pessoas não encontrada.']);
            break;
        }
    } elseif ($controller === 'tipos_atendimentos') {
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
            echo json_encode(['erro' =>'Ação de tipos de atendimento não encontrada.']);
            break;
        }
    } elseif ($controller === 'atendimentos') {
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
            echo json_encode(['erro' =>'Ação de atendimento não encontrada.']);
            break;
        }
    } else {
    echo '<h1>AtendeLab</h1>';
    echo '<p>Projeto em execução. Use ?controller=usuario&action=listar para testar. </p>';
    }

