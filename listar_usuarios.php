<?php
require_once 'conexao.php';

// Verifica se foi feita uma requisição POST e se o parâmetro 'id' está definido
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];

    $sql = "SELECT * FROM usuarios WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Retorna os dados do usuário no formato JSON
        echo json_encode($row);
    } else {
        // Retorna uma mensagem de erro no formato JSON
        echo json_encode(array("error" => "Usuário não encontrado."));
    }
} else {
    // Caso contrário, lista todos os usuários
    $sql = "SELECT * FROM usuarios";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $users = array();
        while($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        // Retorna a lista de usuários no formato JSON
        echo json_encode($users);
    } else {
        // Retorna uma mensagem de erro no formato JSON
        echo json_encode(array("error" => "Nenhum usuário encontrado."));
    }
}

$conn->close();
?>




