<?php

$servidor = 'localhost';
$banco = 'sistema_notas'; 
$usuario = 'root';
$senha = '';

try {
    $conexao = new PDO("mysql:host=$servidor;dbname=$banco", $usuario, $senha);
    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nome = $_POST['nome'];
        $id_turma = $_POST['idTurma'];

        if (empty($nome) || empty($id_turma)) {
            echo "Por favor, preencha todos os campos.";
            exit;
        }

        echo "Recebido: <br>";
        echo "Nome do Aluno: " . htmlspecialchars($nome) . "<br>";
        echo "ID da Turma: " . htmlspecialchars($id_turma) . "<br>";

        $codigoSQL = "INSERT INTO alunos (nome, id_turma) VALUES (:nome, :id_turma)";
        $comando = $conexao->prepare($codigoSQL);
        $resultado = $comando->execute(array(
            ':nome' => $nome,
            ':id_turma' => $id_turma,
        ));

        if ($resultado) {
            echo "Dados salvos com sucesso!";
        } else {
            echo "Erro ao salvar os dados!";
        }
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}

$conexao = null;

?>
