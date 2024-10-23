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

$id_turma = isset($_POST['idTurma']) ? $_POST['idTurma'] : (isset($_GET['idTurma']) ? $_GET['idTurma'] : '');
$alunos = [];
$mensagem = '';

if ($id_turma) {
    try {
        $stmt = $pdo->prepare("SELECT id, nome FROM alunos WHERE id_turma = :id_turma");
        $stmt->execute([':id_turma' => $id_turma]);
        $alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erro ao buscar alunos: " . $e->getMessage();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['valor'])) {
    $id_aluno = $_POST['idAluno']; 
    $valor = $_POST['valor'];

    if ($valor < 0 || $valor > 10) {
        $mensagem = "A nota deve estar entre 0 e 10.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM alunos WHERE id = :id_aluno");
            $stmt->execute([':id_aluno' => $id_aluno]);
            if ($stmt->fetchColumn() == 0) {
                $mensagem = "Aluno não encontrado.";
            } else {
                $codigoSQL = "INSERT INTO notas (id_aluno, id_turma, valor) VALUES (:id_aluno, :id_turma, :valor)";
                $comando = $pdo->prepare($codigoSQL);
                $resultado = $comando->execute([
                    ':id_aluno' => $id_aluno,
                    ':id_turma' => $id_turma,
                    ':valor' => $valor,
                ]);

                if ($resultado) {
                    header("Location: " . $_SERVER['PHP_SELF'] . "?success=1&idTurma=$id_turma");
                    exit;
                } else {
                    $mensagem = "Erro ao cadastrar a nota.";
                }
            }
        } catch (PDOException $e) {
            $mensagem = "Erro: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Notas</title>
</head>
<body>
    <?php if (!empty($mensagem)): ?>
        <p style="color: red;"><?php echo $mensagem; ?></p>
    <?php endif; ?>
    
    <?php if (isset($_GET['success'])): ?>
        <p style="color: green;">Nota cadastrada com sucesso!</p>
    <?php endif; ?>
    
    <form action="receberNotas.php" method="POST">
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
        </select><br>

        <label for="idAluno">Selecione o Aluno:</label>
        <select id="idAluno" name="idAluno" required>
            <?php
                if (empty($alunos)) {
                    echo "<option value=\"\">Nenhum aluno encontrado</option>";
                } else {
                    foreach ($alunos as $aluno) {
                        echo "<option value=\"{$aluno['id']}\">{$aluno['nome']}</option>";
                    }
                }
            ?>
        </select><br>

        <label for="valor">Digite a Nota (0 a 10):</label>
        <input type="number" name="valor" step="0.1" min="0" max="10" required><br>

        <input type="submit" value="Salvar">
    </form>
</body>
</html>