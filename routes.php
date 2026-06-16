<?php
require_once __DIR__ . '/app/Controllers/UsuariosController.php';

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

        case 'excluir':
            $usuariosController->excluir();
            break;

        default:
            echo 'Ação de usuários não encontrada.';
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

        case 'excluir':
            $pessoasController->excluir();
            break;

        default:
            echo 'Ação de pessoas não encontrada.';
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

        case 'excluir':
            $tipos_atendimentosController->excluir();
            break;

        default:
            echo 'Ação de tipos de atendimento não encontrada.';
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
        
        case 'atualizar':
            $atendimentosController->atualizar();
            break;

        case 'status':
            $atendimentosController->status();
            break;

        case 'visualizar':
            $atendimentosController->visualizar();
            break;

        default:
            echo 'Ação de atendimento não encontrada.';
            break;
        }
    } else {
    echo '<h1>AtendeLab</h1>';
    echo '<p>Projeto em execução. Use ?controller=usuario&action=listar para testar. </p>';
    }

