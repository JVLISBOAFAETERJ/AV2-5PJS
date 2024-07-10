<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>CRUD de Usuários</title>
    <link rel="stylesheet" href="css/styles.css"> <!-- Se houver arquivo de estilo personalizado -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
        #mensagem {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>CRUD de Usuários</h2>
        <form id="formInserirUsuario">
            <input type="text" name="nome" id="nome" placeholder="Nome" required>
            <select name="cargo" id="cargo" required>
                <option value="aluno">Aluno</option>
                <option value="professor">Professor</option>
            </select>
            <input type="email" name="email" id="email" placeholder="Email" required>
            <button type="submit" class="btn btn-primary">Inserir</button>
        </form>
        
        <hr>
        
        <h3>Listagem de Usuários</h3>
        <div id="listaUsuarios">
            <!-- Aqui será carregada a lista de usuários via AJAX -->
        </div>

        <div id="mensagem"></div>
    </div>

    <!-- Modal de Edição -->
    <div class="modal fade" id="editarUsuarioModal" tabindex="-1" role="dialog" aria-labelledby="editarUsuarioModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarUsuarioModalLabel">Editar Usuário</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formEditarUsuario">
                        <input type="hidden" name="idEditar" id="idEditar">
                        <input type="text" name="nomeEditar" id="nomeEditar" placeholder="Nome" required>
                        <select name="cargoEditar" id="cargoEditar" required>
                            <option value="aluno">Aluno</option>
                            <option value="professor">Professor</option>
                        </select>
                        <input type="email" name="emailEditar" id="emailEditar" placeholder="Email" required>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnSalvarEdicao">Salvar Alterações</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // Função para carregar a lista de usuários ao carregar a página
        carregarListaUsuarios();

        // Função para carregar a lista de usuários via AJAX
        function carregarListaUsuarios() {
            $.ajax({
                type: 'POST',
                url: 'listar_usuarios.php',
                dataType: 'json',
                success: function(response) {
                    if (response.error) {
                        exibirMensagem('danger', response.error);
                    } else {
                        var html = '<ul class="list-group">';
                        response.forEach(function(user) {
                            html += '<li class="list-group-item">';
                            html += '<strong>ID:</strong> ' + user.id + ' - <strong>Nome:</strong> ' + user.nome + ' - <strong>Cargo:</strong> ' + user.cargo + ' - <strong>Email:</strong> ' + user.email;
                            html += '<button class="btn btn-sm btn-primary ml-2" onclick="carregarDadosUsuario(' + user.id + ')">Editar</button>';
                            html += '<button class="btn btn-sm btn-danger ml-2" onclick="excluirUsuario(' + user.id + ')">Excluir</button>';
                            html += '</li>';
                        });
                        html += '</ul>';
                        $('#listaUsuarios').html(html);
                    }
                },
                error: function(xhr, status, error) {
                    exibirMensagem('danger', 'Erro ao carregar lista de usuários: ' + error);
                }
            });
        }

        // Função para exibir mensagem na interface
        function exibirMensagem(type, message) {
            var mensagemHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">';
            mensagemHtml += message;
            mensagemHtml += '<button type="button" class="close" data-dismiss="alert" aria-label="Fechar">';
            mensagemHtml += '<span aria-hidden="true">&times;</span>';
            mensagemHtml += '</button>';
            mensagemHtml += '</div>';

            $('#mensagem').html(mensagemHtml).fadeIn().delay(3000).fadeOut('slow');
        }

        // Função para carregar dados do usuário no modal de edição
        window.carregarDadosUsuario = function(id) {
            $.ajax({
                type: 'POST',
                url: 'listar_usuarios.php',
                dataType: 'json',
                data: { id: id },
                success: function(response) {
                    if (response.error) {
                        exibirMensagem('danger', response.error);
                    } else {
                        $('#idEditar').val(response.id);
                        $('#nomeEditar').val(response.nome);
                        $('#cargoEditar').val(response.cargo);
                        $('#emailEditar').val(response.email);
                        $('#editarUsuarioModal').modal('show');
                    }
                },
                error: function(xhr, status, error) {
                    exibirMensagem('danger', 'Erro ao carregar dados do usuário: ' + error);
                }
            });
        };

        // Função para salvar as alterações de edição
        $('#btnSalvarEdicao').click(function() {
            var id = $('#idEditar').val();
            var nome = $('#nomeEditar').val();
            var cargo = $('#cargoEditar').val();
            var email = $('#emailEditar').val();

            $.ajax({
                type: 'POST',
                url: 'atualizar_usuario.php',
                data: {
                    id: id,
                    nome: nome,
                    cargo: cargo,
                    email: email
                },
                success: function(response) {
                    exibirMensagem('success', response);
                    $('#editarUsuarioModal').modal('hide');
                    // Atualiza a lista de usuários após edição
                    carregarListaUsuarios();
                },
                error: function(xhr, status, error) {
                    exibirMensagem('danger', 'Erro ao atualizar usuário: ' + error);
                }
            });
        });

        // Função para excluir usuário via AJAX
        window.excluirUsuario = function(id) {
            if (confirm('Tem certeza que deseja excluir este usuário?')) {
                $.ajax({
                    type: 'POST',
                    url: 'deletar_usuario.php',
                    data: {
                        id: id
                    },
                    success: function(response) {
                        exibirMensagem('success', response);
                        // Atualiza a lista de usuários após exclusão
                        carregarListaUsuarios();
                    },
                    error: function(xhr, status, error) {
                        exibirMensagem('danger', 'Erro ao excluir usuário: ' + error);
                    }
                });
            }
        };

        // Função para submeter o formulário de inserção de usuário via AJAX
        $('#formInserirUsuario').submit(function(event) {
            event.preventDefault();
            var nome = $('#nome').val();
            var cargo = $('#cargo').val();
            var email = $('#email').val();

            $.ajax({
                type: 'POST',
                url: 'inserir_usuario.php',
                data: {
                    nome: nome,
                    cargo: cargo,
                    email: email
                },
                success: function(response) {
                    exibirMensagem('success', response);
                    $('#nome').val('');
                    $('#cargo').val('aluno');
                    $('#email').val('');
                    // Atualiza a lista de usuários após inserção
                    carregarListaUsuarios();
                },
                error: function(xhr, status, error) {
                    exibirMensagem('danger', 'Erro ao inserir usuário: ' + error);
                }
            });
        });
    });
    </script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>






