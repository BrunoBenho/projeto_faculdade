# Controle de Processos Trabalhistas

Trabalho da disciplina de Programação Web (ITE, 2026).

## Intuito do projeto

Desenvolver um sistema web simples para **incluir, consultar, alterar e excluir** (CRUD) processos trabalhistas, usando **PHP, HTML, CSS e MySQL**. Tudo funciona em um único arquivo, `processos.php`, com um só formulário para incluir e alterar, e a lista de processos com as opções Editar e Excluir na mesma página.

## Arquivos

- `processos.php`: conexão com o banco, formulário, tabela de consulta e estilos (PHP, HTML e CSS).
- `banco.sql`: criação do banco `processostrabalhistas`, da tabela `processos` e dos dados iniciais.

## Banco de dados

Tabela `processos`:

| Campo | Tipo |
|---|---|
| registro | INT, chave primária, AUTO_INCREMENT |
| advogado | VARCHAR(100) |
| trabalhador | VARCHAR(100) |
| empresa | VARCHAR(100) |

## Como executar

1. Inicie o Apache e o MySQL no XAMPP.
2. Execute o `banco.sql` no phpMyAdmin.
3. Coloque a pasta em `htdocs` e acesse `http://localhost/projeto_faculdade/processos.php`.

## Tecnologias

PHP (mysqli com comandos preparados), HTML, CSS e MySQL/MariaDB.
