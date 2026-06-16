# AtendeLab
Sistema de Controle de Atendimentos Acadêmicos desenvolvido na disciplina de Fábrica de
Software.

## Tecnologias utilizadas
- PHP 8.x
- MySQL
- phpMyAdmin
- HTML
- CSS
- Bootstrap
- Git e GitHub

## Funcionalidades previstas
- Página pública
- Login
- Dashboard
- Cadastro de pessoas atendidas
- Cadastro de tipos de atendimento
- Registro de atendimentos
- Relatórios

## Funcionalidades Implementadas
- Login de usuários
- Controle de sessão
- Logout seguro
- CRUD de usuários
- Dashboard inicial
- Diferenciação de mensagens por perfil de usuário

## Como executar localmente
1. Clonar o repositório.
2. Colocar a pasta no htdocs do XAMPP.
3. Iniciar Apache e MySQL.
4. Criar o banco atendelab.
5. Importar o script database/atendelab.sql.
6. Acessar http://localhost/atendelab/public/

## Fluxo de Autenticação
1. Acesse a tela de login.
2. Informe e-mail e senha.
3. Após a autenticação, o sistema redirecionará para o dashboard.
4. Apenas usuários autenticados podem acessar páginas protegidas.
5. Para encerrar a sessão, clique em Sair.

## Usuário de Teste
- Email: admin@atendelab.com
- Senha: 123456