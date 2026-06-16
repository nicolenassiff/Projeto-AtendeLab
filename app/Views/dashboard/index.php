<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">
    
    <title>Dashboard - AtendeLab</title>

    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
        rel="stylesheet">
</head>

<body class="bd-light">
    
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <span class="navbar-brand">AtendeLab</span>

            <a class="btn btn-outline-light btn-sm"
                href="?controller=auth&action=logout">
                 Sair
            </a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-body">

                <?php if ($usuario['perfil'] === 'admin'): ?>
                    <h1 class="h4">Painel Administrativo</h1>
                    <div class="card-body">
                        <h5 class="card-title">Olá, Administrador!</h5>
                        <p class="card-text">
                            Você tem acesso total ao gerenciamento de usuários e atendimentos.
                        </p>
                    </div>
                </div>
                <?php else: ?>
                    <h1 class="h4">Painel do Usuário</h1>
                    <div class="card-body">
                        <h5 class="card-title">Olá!</h5>
                        <p class="card-text">
                            Você está autenticado e pode utilizar os recursos disponíveis para seu perfil.
                        </p>
                    </div>
                </div>
                <?php endif; ?>

                <a class="btn btn-primary"
                    href="?controller=usuarios&action=listar">
                    Testar rota protegida de usuarios
                </a>
            </div>
        </div>
    </div>

</body>
</html>
