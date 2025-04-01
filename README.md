Repositório para envio de teste em seletivo da seplag

Candidato: Renato Tavares de Melo

---------------------Instruções para executar o projeto:---------------------------

1 - Máquina com Docker e Docker Compose instalado

2 - No diretório do projeto executar o comandos para subir os containers.
$_> docker compose up -d

3 - Entrar no terminal da imagem do PHP
$_> docker exec -it php-seplag bash

4 - Já no terminal do container PHP: cria o .env copiando do .env.example
$_> cp .env.example .env

5 - Instala as dependências do PHP
$_> composer install

6 - Executa as migrations e cria as tabelas no banco
$_> php artisan migrate

7 - Criar um usuario para os teste, nesse passo criará um usuario com nome 'Seplag', email 'seplag@teste.com' e senha '123456'
$_> php artisan db:seed

---------------Endpoints da API-------------

-> Rota de Login

POST http://localhost/api/login
campos obrigatórios: { email, password }

OBS.: usar o email seplag@teste.com e password 123456
após o login a API retornará o token de acesso do usuário válido por 5 minutos
{
    "token": "1|LdiEkRzqNzpZJJQx9MbOHSyFgxTTkerJCWXeek0q3c087afc"
}

---------------------- ROTAS AUTENTICADAS --------------------------

para acessar essas rotas o usuário precisa logar e enviar o token (Bearer) no cabeçalho da requisição

------------
-> Rota de renovação do token do usuário

POST http://localhost/api/renova-token
adicionará 5 minutos ao tempo de expiração do token
{
    "nova_expiracao": "2025-03-31 20:07:18"
}

OBS: todas as Rotas GET que retorna uma listagem de itens retornará 20 itens por página por default
{
    "last_page": 1, 
    "per_page": 20, #pode passar um valor máximo de 50, se exceder esse valor ele limita em 50
    "total": 2,
    "itens": [] #array com os itens da listagem
}

------------
-> Rotas de Unidade
o termo id nas rotas é um inteiro e representa o id da unidade. EX: 1

Listagem das Unidades: GET http://localhost/api/unidade

Adicionar Unidade: POST http://localhost/api/unidade
campos obrigatórios: { nome, sigla, tipo_logradouro, logradouro, numero, bairro, cidade, uf }

Exibir uma Unidade: GET http://localhost/api/unidade/id

Atualizar Unidade: PUT http://localhost/api/unidade/id
atualizará somente os campos que forem passados

Excluir uma Unidade: DELETE http://localhost/api/unidade/id
somente poderá sem excluídas unidades que não possuem servidores lotados nela

Listar servidores efetivos de uma Unidade: GET http://localhost/api/unidade/id/servidoresEfetivos

Listar servidores temporários de uma Unidade: GET http://localhost/api/unidade/id/servidoresTemporarios


------------
-> Rotas de Servidores Efetivos
matricula nas rotas é a matricula do servidor: ex: 203040

Listagem dos Servidores Efetivos: GET http://localhost/api/servidor-efetivo

Adicionar um Servidore Efetivo: POST http://localhost/api/servidor-efetivo
campos obrigatórios: { matricula, nome, data_nascimento, sexo, mae, pai, tipo_logradouro, logradouro, numero, bairro, cidade, uf, foto }

Exibir um Servidore Efetivo: GET http://localhost/api/servidor-efetivo/matricula

Atualizar um Servidore Efetivo: PUT http://localhost/api/servidor-efetivo/matricula
atualizará somente os campos que forem passados

Excluir um Servidore Efetivo: DELETE http://localhost/api/servidor-efetivo/matricula

Busca servidores pelo nome e retorna dados de lotacao: GET http://localhost/api/servidor-efetivo/busca
campos obrigatórios: { nome }

------------
-> Rotas de Servidores Temporários
matricula nas rotas é a matricula do servidor: ex: 203040

Listagem dos Servidores Temporários: GET http://localhost/api/servidor-temporario

Adicionar um Servidor Temporário: POST http://localhost/api/servidor-temporario
campos obrigatórios: { data_admissao, nome, data_nascimento, sexo, mae, pai, tipo_logradouro, logradouro, numero, bairro, cidade, uf, foto }
campos opcionais: { data_demissao }

Exibir um Servidor Temporário: GET http://localhost/api/servidor-temporario/matricula

Atualizar um Servidor Temporário: PUT http://localhost/api/servidor-temporario/matricula
atualizará somente os campos que forem passados

Excluir um Servidor Temporário: DELETE http://localhost/api/servidor-temporario/matricula

Busca servidores pelo nome e retorna dados de lotacao: GET http://localhost/api/servidor-temporario/busca
campos obrigatórios: { nome }

------------
-> Rotas de Lotação

Listagem das Lotações: GET http://localhost/api/lotacao

Adicionar uma Lotação: POST http://localhost/api/lotacao
campos obrigatórios: { pessoa_id, unidade_id, data_lotacao, portaria }
campos opcionais: { data_remocao }

Exibir uma Lotação: GET http://localhost/api/lotacao/id

Atualizar uma Lotação: PUT http://localhost/api/lotacao/id
atualizará somente os campos que forem passados

Excluir uma Lotação: DELETE http://localhost/api/lotacao/id

