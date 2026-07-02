<?php
$tituloPagina = 'Gerenciar Usuários';
require __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Usuários do Sistema</h1>
    <button class="btn btn-primary" onclick="abrirModalUsuario()">Novo Usuário</button>
</div>

<div id="alerta"></div>

<div class="card shadow-sm">
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Perfil</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody id="tabelaUsuarios"></tbody>
    </table>
</div>

<div class="modal fade" id="modalUsuario" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" id="formUsuario">
            <div class="modal-header">
                <h5 class="modal-title">Novo Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="userId">
                <div class="mb-3"><label>Nome</label><input class="form-control" name="nome" required></div>
                <div class="mb-3"><label>E-mail</label><input class="form-control" type="email" name="email" required></div>
                <div class="mb-3"><label>Senha</label><input class="form-control" type="password" name="senha" id="inputSenha"></div>
                <div class="mb-3">
                    <label>Perfil</label>
                    <select class="form-select" name="perfil">
                        <option value="atendente">Atendente</option>
                        <option value="admin">Administrador</option>
                        <option value="aluno">Aluno</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script>
    const formUsuario = document.getElementById('formUsuario');
    const modal = new bootstrap.Modal(document.getElementById('modalUsuario'));

    function abrirModalUsuario() {
        formUsuario.reset();
        document.getElementById('userId').value = '';
        modal.show();
    }

    formUsuario.addEventListener('submit', async (e) => {
        e.preventDefault();
        try {
            await AtendeLabApi.post('usuarios', 'criar', new FormData(formUsuario));
            modal.hide();
            AtendeLabApi.showAlert('alerta', 'Usuário criado com sucesso!');
        } catch (err) {
            AtendeLabApi.showAlert('alerta', err.message, 'danger');
        }
    });
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
