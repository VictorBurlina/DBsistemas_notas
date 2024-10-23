<?php

$servidor = 'localhost';
$banco = 'sistema_notas'; 
$usuario = 'root';
$senha = '';

try {
    $pdo = new PDO("mysql:host=$servidor;dbname=$banco", $usuario, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receber dados dos Alunos</title>
</head>
<body>
    <form action="form_CadastrarAlunos.php" method="POST">
        <label for="nome">Digite o Nome do Aluno:</label>
        <input type="text" name="nome" required><br>
        
        <label for="idTurma">Selecione o Nome da Turma:</label>
        <select name="idTurma" required>
            <?php
                try {
                    $turmas = $pdo->query("SELECT id, nome FROM turmas")->fetchAll(PDO::FETCH_ASSOC);

                    if (empty($turmas)) {
                        echo "<option value=\"\">Nenhuma turma cadastrada</option>";
                    } else {
                        foreach ($turmas as $turma) {
                            echo "<option value=\"{$turma['id']}\">{$turma['nome']}</option>";
                        }
                    }
                } catch (PDOException $e) {
                    echo "Erro ao buscar turmas: " . $e->getMessage();
                }
            ?>
        </select><br>

        <input type="submit" value="Salvar">
    </form>
</body>
</html>
