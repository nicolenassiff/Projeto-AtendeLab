<?php
$tituloPagina = 'Tipos de atendimento';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="h3 mb-1">Tipos de atendimento</h1>
        <p class="text-secondary mb-0">Gerencie as categorias de serviços oferecidos.</p>
    </div>
    <button class="btn btn-success" type="button" onclick="novoTipo()">Novo tipo</button>
</div>

<div id="alerta"></div>

<div class="card border-0 shadow-sm mb-4 d-none" id="cardFormulario">
    <div class="card-body">
        <h2 class="h5" id="tituloFormulario">Novo tipo</h2>
        <form id="formTipo">
            <input type="hidden" name="id" id="tipoId">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Nome *</label>
                    <input class="form-control" name="nome" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="ativo">Ativo</option>
                        <option value="inativo">Inativo</option>
                    </select>
                </div>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button class="btn btn-success" type="submit">Salvar</button>
                <button class="btn btn-outline-secondary" type="button" onclick="fecharFormulario()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 80px;">ID</th>
                    <th>Nome</th>
                    <th style="width: 150px;">Status</th>
                    <th style="width: 180px;" class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody id="tabelaTipos">
                <tr>
                    <td colspan="4" class="text-center py-4">Carregando...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
const formTipo = document.getElementById('formTipo');
const cardFormulario = document.getElementById('cardFormulario');

function abrirFormulario() {
    cardFormulario.classList.remove('d-none');
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function fecharFormulario() {
    cardFormulario.classList.add('d-none');
    formTipo.reset();
    document.getElementById('tipoId').value = '';
}

function novoTipo() {
    fecharFormulario();
    document.getElementById('tituloFormulario').textContent = 'Novo tipo';
    abrirFormulario();
}

async function carregarTipos() {
    try {
        const dados = AtendeLabApi.toList(await AtendeLabApi.get('tipos', 'listar'));
        const tbody = document.getElementById('tabelaTipos');
        
        if (!dados.length) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center py-4">Nenhum tipo cadastrado.</td></tr>';
            return;
        }

        tbody.innerHTML = dados.map(t => `
            <tr>
                <td>${AtendeLabApi.escape(t.id)}</td>
                <td>${AtendeLabApi.escape(t.nome)}</td>
                <td>
                    <span class="badge ${t.status === 'ativo' ? 'text-bg-success' : 'text-bg-secondary'}">
                        ${AtendeLabApi.escape(t.status)}
                    </span>
                </td>
                <td class="text-end">
                    <button class="btn btn-sm btn-outline-primary" onclick="editarTipo(${Number(t.id)})">Editar</button>
                    ${t.status === 'ativo'
                        ? `<button class="btn btn-sm btn-outline-danger" onclick="inativarTipo(${Number(t.id)})">Inativar</button>`
                        : `<button class="btn btn-sm btn-outline-success" onclick="reativarTipo(${Number(t.id)})">Reativar</button>`
                    }
                </td>
            </tr>
        `).join('');
    } catch (error) {
        AtendeLabApi.showAlert('alerta', error.message, 'danger');
    }
}

async function editarTipo(id) {
    try {
        const t = AtendeLabApi.toObject(await AtendeLabApi.get('tipos', 'buscar', { id }));
        novoTipo();
        document.getElementById('tituloFormulario').textContent = 'Editar tipo';
        
        // CORREÇÃO: Força a gravação do ID no input oculto de controle
        document.getElementById('tipoId').value = t.id ?? '';
        
        for (const [key, value] of Object.entries(t)) {
            const field = formTipo.elements.namedItem(key);
            if (field) field.value = value ?? '';
        }
    } catch (error) {
        AtendeLabApi.showAlert('alerta', error.message, 'danger');
    }
}

formTipo.addEventListener('submit', async event => {
    event.preventDefault();
    const id = document.getElementById('tipoId').value;
    
    try {
        await AtendeLabApi.post('tipos', id ? 'atualizar' : 'criar', new FormData(formTipo));
        AtendeLabApi.showAlert('alerta', id ? 'Tipo atualizado com sucesso.' : 'Tipo cadastrado com sucesso.');
        fecharFormulario();
        await carregarTipos();
    } catch (error) {
        AtendeLabApi.showAlert('alerta', error.message, 'danger');
    }
});

async function inativarTipo(id) {
    if (!confirm('Deseja inativar este tipo?')) return;
    try {
        await AtendeLabApi.post('tipos', 'inativar', { id });
        AtendeLabApi.showAlert('alerta', 'Tipo inativado com sucesso.');
        await carregarTipos();
    } catch (error) {
        AtendeLabApi.showAlert('alerta', error.message, 'danger');
    }
}

async function reativarTipo(id) {
    if (!confirm('Deseja reativar este tipo?')) return;
    try {
        await AtendeLabApi.post('tipos', 'reativar', { id });
        AtendeLabApi.showAlert('alerta', 'Tipo reativado com sucesso.');
        await carregarTipos();
    } catch (error) {
        AtendeLabApi.showAlert('alerta', error.message, 'danger');
    }
}

document.addEventListener('DOMContentLoaded', carregarTipos);
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>