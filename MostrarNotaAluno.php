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

$id_aluno = isset($_POST['idAluno']) ? $_POST['idAluno'] : '';
$notas = [];

if ($id_aluno) {
    try {
        $stmt = $pdo->prepare("SELECT n.valor, t.nome AS turma FROM notas n JOIN turmas t ON n.id_turma = t.id WHERE n.id_aluno = :id_aluno");
        $stmt->execute([':id_aluno' => $id_aluno]);
        $notas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erro ao buscar notas: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mostrar Notas</title>
</head>
<body>
    <h1>Mostrar Notas do Aluno</h1>
    <form method="POST">
        <label for="idAluno">Selecione o Aluno:</label>
        <select id="idAluno" name="idAluno" required onchange="this.form.submit()">
            <option value="">Selecione um aluno</option>
            <?php
                try {
                    $alunos = $pdo->query("SELECT id, nome FROM alunos")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($alunos as $aluno) {
                        $selected = ($aluno['id'] == $id_aluno) ? 'selected' : '';
                        echo "<option value=\"{$aluno['id']}\" $selected>{$aluno['nome']}</option>";
                    }
                } catch (PDOException $e) {
                    echo "Erro ao buscar alunos: " . $e->getMessage();
                }
            ?>
        </select>
    </form>

    <?php if ($notas): ?>
        <h2>Notas de <?php echo $alunos[array_search($id_aluno, array_column($alunos, 'id'))]['nome']; ?></h2>
        <table border="1">
            <tr>
                <th>Nota</th>
                <th>Turma</th>
            </tr>
            <?php foreach ($notas as $nota): ?>
                <tr>
                    <td><?php echo $nota['valor']; ?></td>
                    <td><?php echo $nota['turma']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>