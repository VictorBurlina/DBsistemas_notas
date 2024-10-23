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

$id_turma = isset($_POST['idTurma']) ? $_POST['idTurma'] : '';
$alunos = [];

if ($id_turma) {
    try {
        $stmt = $pdo->prepare("SELECT id, nome FROM alunos WHERE id_turma = :id_turma");
        $stmt->execute([':id_turma' => $id_turma]);
        $alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erro ao buscar alunos: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Alunos</title>
</head>
<body>
    <h1>Listar Alunos da Turma</h1>
    <form method="POST">
        <label for="idTurma">Selecione a Turma:</label>
        <select id="idTurma" name="idTurma" required onchange="this.form.submit()">
            <option value="">Selecione uma turma</option>
            <?php
                try {
                    $turmas = $pdo->query("SELECT id, nome FROM turmas")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($turmas as $turma) {
                        $selected = ($turma['id'] == $id_turma) ? 'selected' : '';
                        echo "<option value=\"{$turma['id']}\" $selected>{$turma['nome']}</option>";
                    }
                } catch (PDOException $e) {
                    echo "Erro ao buscar turmas: " . $e->getMessage();
                }
            ?>
        </select>
    </form>

    <?php if ($alunos): ?>
        <h2>Alunos na Turma: <?php echo $turmas[array_search($id_turma, array_column($turmas, 'id'))]['nome']; ?></h2>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Nome</th>
            </tr>
            <?php foreach ($alunos as $aluno): ?>
                <tr>
                    <td><?php echo $aluno['id']; ?></td>
                    <td><?php echo $aluno['nome']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>