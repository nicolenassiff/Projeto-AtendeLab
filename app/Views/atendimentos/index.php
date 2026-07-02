<?php
$tituloPagina = 'Atendimentos';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="h3 mb-1">Atendimentos</h1>
        <p class="text-secondary mb-0">
            Registro e acompanhamento dos atendimentos acadêmicos.
        </p>
    </div>

    <?php if (isset($_SESSION['usuario']['perfil']) && $_SESSION['usuario']['perfil'] !== 'aluno'): ?>
    <button class="btn btn-success" type="button" onclick="novoAtendimento()">
        <i class="bi bi-plus-lg"></i> Novo Atendimento
    </button>
    <?php endif; ?>
</div>

<div id="alerta"></div>

<div class="card border-0 shadow-sm mb-4 d-none" id="cardFormulario">
    <div class="card-body">
        <h2 class="h5 mb-3" id="tituloFormulario">Novo atendimento</h2>

        <form id="formAtendimento">
            <input type="hidden" name="usuario_id" value="1">
            <input type="hidden" name="id" id="atendimentoId">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Pessoa *</label>
                    <select class="form-select" name="pessoa_id" id="pessoaSelect" required>
                        <option value="">Carregando...</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Tipo *</label>
                    <select class="form-select" name="tipo_atendimento_id" id="tipoSelect" required>
                        <option value="">Carregando...</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Data *</label>
                    <input class="form-control" type="date" name="data_atendimento" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Horário *</label>
                    <input class="form-control" type="time" name="hora_atendimento" required>
                </div>

                <div class="col-12">
                    <label class="form-label">Descrição *</label>
                    <textarea class="form-control" name="descricao" rows="3" required placeholder="Escreva os detalhes iniciais do atendimento..."></textarea>
                </div>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button class="btn btn-success" type="submit">Registrar</button>
                <button class="btn btn-outline-secondary" type="button" onclick="fecharFormulario()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Pessoa</th>
                    <th>Tipo</th>
                    <th>Responsável</th>
                    <th>Data</th>
                    <th>Status</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>

            <tbody id="tabelaAtendimentos">
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        Carregando listagem de atendimentos...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalStatus" tabindex="-1" aria-labelledby="modalStatusLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalStatusLabel">Atualizar Status do Atendimento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <form id="formStatus">
        <div class="modal-body">
          <input type="hidden" name="id" id="statusId">

          <div class="mb-3">
            <label class="form-label">Novo Status *</label>
            <select class="form-select" name="status" id="statusSelect" required>
              <option value="aberto">Aberto</option>
              <option value="em_andamento">Em andamento</option>
              <option value="concluido">Concluído</option>
            </select>
          </div>

        <div class="mb-3 d-none" id="containerObservacao">
            <label class="form-label">Observação Final (Obrigatório para Concluir)</label>
            <textarea class="form-control" name="observacao_final" id="statusObservacao" rows="3" placeholder="Insira o parecer do encerramento do caso..."></textarea>
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
          <button type="submit" form="formStatus" class="btn btn-primary">Salvar Alterações</button>
        </div>
      </form>
      
    </div>
  </div>
</div>

<script>
const formAtendimento = document.getElementById('formAtendimento');
const formStatus = document.getElementById('formStatus');
const cardFormulario = document.getElementById('cardFormulario');

function novoAtendimento() {
  cardFormulario.classList.remove('d-none');

  const hoje = new Date();
  const hojeFormatado = hoje.toISOString().split('T')[0];
  const campoData = document.querySelector('input[name="data_atendimento"]');
  if (campoData) {
    campoData.min = hojeFormatado;
  }

  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  });
}

function fecharFormulario() {
  cardFormulario.classList.add('d-none');
  formAtendimento.reset();
}

function labelRegistro(obj, ...keys) {
  for (const key of keys) {
    if (obj[key] !== undefined && obj[key] !== null) {
      return obj[key];
    }
  }
  return '';
}

async function carregarCombos() {
  try {
    const [pessoasResp, tiposResp] = await Promise.all([
      AtendeLabApi.get('pessoas', 'listar'),
      AtendeLabApi.get('tipos', 'listar')
    ]);

    const pessoas = AtendeLabApi.toList(pessoasResp).filter(p => p.status !== 'inativo');
    const tipos = AtendeLabApi.toList(tiposResp).filter(t => t.status !== 'inativo');

    document.getElementById('pessoaSelect').innerHTML =
      '<option value="">Selecione</option>' +
      pessoas.map(p => `<option value="${Number(p.id || p.id_pessoas)}">${AtendeLabApi.escape(p.nome || p.nome_pessoas)}</option>`).join('');

    document.getElementById('tipoSelect').innerHTML =
      '<option value="">Selecione</option>' +
      tipos.map(t => `<option value="${Number(t.id || t.id_tiposatendimentos)}">${AtendeLabApi.escape(t.nome)}</option>`).join('');
  } catch (erro) {
    console.error("Erro ao alimentar menus suspensos (combos):", erro);
  }
}

async function carregarAtendimentos() {
  try {
    const resposta = await AtendeLabApi.get('atendimentos', 'listar');
    const atendimentos = AtendeLabApi.toList(resposta);
    const tbody = document.getElementById('tabelaAtendimentos');

    if (!atendimentos || !atendimentos.length) {
      tbody.innerHTML = `
        <tr>
          <td colspan="7" class="text-center py-4 text-muted">Nenhum atendimento registrado no sistema.</td>
        </tr>
      `;
      return;
    }

    tbody.innerHTML = atendimentos.map(atendimento => {
      const pessoa = labelRegistro(atendimento, 'pessoa');
      const tipo = labelRegistro(atendimento, 'tipo_atendimento');
      const responsavel = labelRegistro(atendimento, 'usuario');
      const data = labelRegistro(atendimento, 'data_atendimento');
      const id = atendimento.id_atendimentos;

      let classeStatus = 'bg-secondary';
      let labelStatus = atendimento.status;

      if (atendimento.status === 'concluido') {
        classeStatus = 'bg-success';
        labelStatus = 'Concluído';
      } else if (atendimento.status === 'em_andamento') {
        classeStatus = 'bg-warning text-dark';
        labelStatus = 'Em andamento';
      } else if (atendimento.status === 'aberto') {
        classeStatus = 'bg-info text-dark';
        labelStatus = 'Aberto';
      }

      const dataFormatada = data ? data.split('-').reverse().join('/') : '';

      return `
        <tr>
            <td><strong>#${id}</strong></td>
            <td>${AtendeLabApi.escape(pessoa)}</td>
            <td>${AtendeLabApi.escape(tipo)}</td>
            <td>${AtendeLabApi.escape(responsavel)}</td>
            <td>${dataFormatada}</td>
            <td><span class="badge ${classeStatus}">${labelStatus}</span></td>
            <td class="text-end">
            <button class="btn btn-sm btn-outline-secondary me-1" onclick="editarAtendimento(${id})">
                Editar
            </button>
            <button class="btn btn-sm btn-outline-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#modalStatus"
                    onclick="alterarStatus(${id})">
                Status
            </button>
            </td>
        </tr>
        `;
    }).join('');

  } catch (erro) {
    console.error("Erro ao carregar atendimentos:", erro);
    document.getElementById('tabelaAtendimentos').innerHTML = `
      <tr>
        <td colspan="7" class="text-center py-4 text-danger">Erro ao carregar dados do servidor. Verifique o console.</td>
      </tr>
    `;
  }
}

async function editarAtendimento(id) {
    if (typeof perfilUsuario !== 'undefined' && perfilUsuario === 'aluno') {
        return;
    }
  try {
    novoAtendimento();
    document.getElementById('tituloFormulario').innerText = 'Editar atendimento';

    const resposta = await AtendeLabApi.get('atendimentos', 'visualizar', { id: id });
    
    const dados = resposta.atendimento;
    
    const pessoaId = resposta.pessoa ? resposta.pessoa.id : '';
    const tipoId = resposta.tipo_atendimento ? resposta.tipo_atendimento.id : '';

    document.getElementById('atendimentoId').value = id;
    document.getElementById('pessoaSelect').value = pessoaId;
    document.getElementById('tipoSelect').value = tipoId;
    
    document.querySelector('input[name="data_atendimento"]').value = dados.data || '';
    document.querySelector('input[name="hora_atendimento"]').value = dados.hora ? dados.hora.substring(0, 5) : '';
    
    document.querySelector('textarea[name="descricao"]').value = dados.descricao || '';

  } catch (erro) {
    console.error("Erro ao carregar dados do atendimento:", erro);
    alert("Não foi possível carregar os dados para edição.");
  }
  console.log("Editando atendimento:", id);
}

function fecharFormulario() {
  cardFormulario.classList.add('d-none');
  formAtendimento.reset();
  document.getElementById('atendimentoId').value = '';
  document.getElementById('tituloFormulario').innerText = 'Novo atendimento';
}

function alterarStatus(id) {
    if (typeof perfilUsuario !== 'undefined' && perfilUsuario === 'aluno') {
        return;}

  document.getElementById('statusId').value = id;
  
  const campoObs = document.getElementById('statusObservacao');
  if (campoObs) campoObs.value = '';

  document.getElementById('containerObservacao').classList.add('d-none');
  document.getElementById('statusObservacao').required = false;
  document.getElementById('statusSelect').value = 'aberto';

  console.log("Alterando status do atendimento:", id);
}

document.getElementById('statusSelect').addEventListener('change', function() {
  const containerObs = document.getElementById('containerObservacao');
  const campoObs = document.getElementById('statusObservacao');

  if (this.value === 'concluido') {
    containerObs.classList.remove('d-none');
    campoObs.required = true;
  } else {
    containerObs.classList.add('d-none');
    campoObs.required = false;
  }
});

formAtendimento.addEventListener('submit', async (e) => {
  e.preventDefault();
  const formData = new FormData(formAtendimento);
  const idEdicao = document.getElementById('atendimentoId').value;

  try {
    if (idEdicao) {
      await AtendeLabApi.post('atendimentos', 'atualizar', formData); 
    } else {
      await AtendeLabApi.post('atendimentos', 'criar', formData);
    }

    fecharFormulario();
    await carregarAtendimentos();

    document.getElementById('alerta').innerHTML = `
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        Atendimento ${idEdicao ? 'atualizado' : 'registrado'} com sucesso!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    `;
  } catch (erro) {
    console.error("Erro ao salvar atendimento:", erro);
    alert("Erro ao salvar o atendimento. Verifique os dados inseridos.");
  }
});

formStatus.addEventListener('submit', async (e) => {
  e.preventDefault();
  const formData = new FormData(formStatus);

  try {
    await AtendeLabApi.post('atendimentos', 'atualizarStatus', formData);

    const botaoFechar = document.querySelector('#modalStatus .btn-close');
    if (botaoFechar) {
      botaoFechar.click();
    }
    
    formStatus.reset();
    await carregarAtendimentos(); 

    document.getElementById('alerta').innerHTML = `
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        Status do atendimento modificado com sucesso!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    `;

  } catch (erro) {
    console.error("Erro ao salvar alteração de status:", erro);
    alert("Não foi possível salvar o status. Lembre-se que o preenchimento da 'Observação Final' é obrigatório caso queira encerrar o caso como 'Concluído'.");
  }
});

document.addEventListener('DOMContentLoaded', () => {
  carregarCombos();
  carregarAtendimentos();
});

document.addEventListener('DOMContentLoaded', function() {
    const modaisBloqueados = ['modalStatus', 'modalAtendimento']; 

    modaisBloqueados.forEach(idModal => {
        const modalElement = document.getElementById(idModal);
        
        if (modalElement) {
            modalElement.addEventListener('show.bs.modal', function(evento) {
                // Se for aluno, cancela a abertura do modal imediatamente
                if (typeof perfilUsuario !== 'undefined' && perfilUsuario === 'aluno') {
                    evento.preventDefault(); // Para o Bootstrap na hora!
                    alert('Acesso negado: Alunos não possuem permissão para alterar dados.');
                }
            });
        }
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
