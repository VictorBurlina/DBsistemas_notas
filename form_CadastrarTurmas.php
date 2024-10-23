
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

        echo "Recebido: <br>";
        echo "Nome do livro: " . htmlspecialchars($nome) . "<br>";

        $codigoSQL = "INSERT INTO `turmas` (`id`, `nome`) VALUES (NULL, :nome)";

        $comando = $conexao->prepare($codigoSQL);
        $resultado = $comando->execute(array(
            ':nome' => $nome,
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