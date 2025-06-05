Mini-ERP
========

**Mini-ERP** é um sistema simples de ERP (Enterprise Resource Planning) desenvolvido em PHP com o framework **CodeIgniter**, que permite o gerenciamento de pedidos e a integração com e-mails via SMTP.

Recursos
--------

- **Gerenciamento de Pedidos**: Cadastro, atualização e exclusão de pedidos no banco de dados.
- **Envio de E-mails**: Integração com SMTP utilizando Mailtrap para testes.
- **Webhook para Atualização de Status**: Permite receber atualizações externas para alterar ou remover pedidos.
- **Docker**: Ambiente de desenvolvimento configurado com contêineres para facilitar a instalação e execução.
- **Banco de Dados MySQL**: Estrutura pronta para armazenar dados de pedidos e clientes.

Tecnologias Utilizadas
----------------------

**Back-end**

- PHP 7.4+
- CodeIgniter Framework

**Banco de Dados**

- MySQL

**Infraestrutura**

- **Docker** com:
  - PHP-Apache
  - MySQL

**Testes de Integração**

- Mailtrap para envio de e-mails.
- Insomnia para testes de APIs e webhooks.

Instalação e Configuração
-------------------------

**1. Pré-requisitos**

Antes de começar, certifique-se de ter instalado:

- Docker e Docker Compose.
- Um editor de texto, como Visual Studio Code (VSCode).

**2. Clone o Repositório**

::

    git clone https://github.com/gersonvinicius/mini_erp.git
    cd mini_erp

**3. Construir e Subir os Contêineres**

Use o comando abaixo para criar e subir os contêineres:

::

    docker-compose up -d

A aplicação estará disponível em: http://localhost:8080.

**4. Criação do Banco de Dados**

Importe o arquivo `database.sql` para o banco de dados MySQL:

::

    docker exec -i mini-erp-mysql mysql -u root -psua_senha erp < database.sql

Configuração de Envio de E-mails
--------------------------------

As configurações de envio de e-mails estão diretamente implementadas no método `enviarEmailConfirmacao`, localizado no controlador responsável pelo envio de notificações. Veja abaixo a configuração utilizada:

.. code-block:: php

    $config = array(
        'protocol'    => 'smtp',
        'smtp_host'   => 'sandbox.smtp.mailtrap.io', // Servidor SMTP do Mailtrap
        'smtp_port'   => 2525, // Porta do Mailtrap
        'smtp_user'   => '04249aae5d6022', // Usuário SMTP
        'smtp_pass'   => '39a92a96096452', // Senha SMTP
        'smtp_crypto' => 'tls', // Protocolo de criptografia
        'mailtype'    => 'html', // Tipo do e-mail: HTML
        'charset'     => 'utf-8', // Codificação do conteúdo
        'wordwrap'    => TRUE, // Quebra automática de linhas
        'crlf'        => "\\r\\n", // Quebra de linha (compatível com SMTP)
        'newline'     => "\\r\\n", // Nova linha (compatível com SMTP)
    );

**Nota:** Essas credenciais foram configuradas manualmente no código e não utilizam variáveis de ambiente.

Uso
---

**1. Envio de E-mails**

- Os e-mails são enviados automaticamente ao registrar um pedido.
- O Mailtrap é utilizado para interceptar os e-mails enviados, garantindo que não sejam entregues a destinatários reais durante os testes.

**2. Webhook**

**Endpoint**: `/pedidos/webhook`  
**Método**: `POST`  
**Descrição**: Recebe atualizações para alterar ou remover pedidos no banco de dados.

**Exemplo de Requisição**:

.. code-block:: json

    {
      "pedido_id": 1,
      "status": "cancelado"
    }

**Status Aceitos**:

- `pendente`
- `finalizado`
- `cancelado`

Estrutura do Banco de Dados
---------------------------

**Tabela: pedidos**

+-------------------+---------------+-------------------------------------+
| Coluna            | Tipo          | Descrição                           |
+===================+===============+=====================================+
| `id`              | INT (PK)      | ID único do pedido.                 |
+-------------------+---------------+-------------------------------------+
| `cliente_nome`    | VARCHAR(255)  | Nome do cliente.                    |
+-------------------+---------------+-------------------------------------+
| `cliente_email`   | VARCHAR(255)  | E-mail do cliente.                  |
+-------------------+---------------+-------------------------------------+
| `cliente_endereco`| TEXT          | Endereço do cliente.                |
+-------------------+---------------+-------------------------------------+
| `status`          | ENUM          | Status do pedido                    |
|                   |               | (`pendente`, `finalizado`,          |
|                   |               | `cancelado`).                       |
+-------------------+---------------+-------------------------------------+
| `subtotal`        | DECIMAL(10,2) | Subtotal do pedido.                 |
+-------------------+---------------+-------------------------------------+
| `frete`           | DECIMAL(10,2) | Valor do frete.                     |
+-------------------+---------------+-------------------------------------+
| `total`           | DECIMAL(10,2) | Valor total do pedido.              |
+-------------------+---------------+-------------------------------------+
| `cupom_id`        | INT (FK)      | ID do cupom de desconto utilizado.  |
+-------------------+---------------+-------------------------------------+
| `created_at`      | TIMESTAMP     | Data de criação do pedido.          |
+-------------------+---------------+-------------------------------------+
| `updated_at`      | TIMESTAMP     | Data de última atualização.         |
+-------------------+---------------+-------------------------------------+

Testando o Webhook
-------------------

**Usando Insomnia**

1. Configure uma requisição **POST** para: `http://localhost:8080/pedidos/webhook`.
2. No corpo da requisição, insira um JSON como este:

.. code-block:: json

    {
      "pedido_id": 1,
      "status": "finalizado"
    }

3. Verifique a resposta:

**Sucesso**:

.. code-block:: json

    {
      "success": "Status do pedido atualizado com sucesso."
    }

**Erro**:

.. code-block:: json

    {
      "error": "Pedido não encontrado ou status inválido."
    }

Desenvolvimento
---------------

**Estrutura de Diretórios**

- **`application/`**: Contém os arquivos principais do CodeIgniter, como controladores, modelos e views.
- **`assets/`**: Contém os arquivos estáticos, como CSS, JavaScript e imagens.
- **`system/`**: Diretório do núcleo do CodeIgniter.
- **`database.sql`**: Dump do banco de dados para inicialização do sistema.

Contribuição
------------

1. Faça um fork do repositório.
2. Crie uma branch para suas modificações:

::

    git checkout -b minha-feature

3. Faça commit das suas mudanças:

::

    git commit -m "Minha nova feature"

4. Faça push para a branch:

::

    git push origin minha-feature

5. Abra um Pull Request para revisão.

Licença
-------

Este projeto está licenciado sob a licença [MIT](LICENSE).
