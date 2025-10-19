<?php
function criarQuiz() {
    // Cada pergunta é um array com: [enunciado, alternativas, índice da correta]
    return [
        ["Quem canta a música 'Mina do condomínio'? :", ["Ana Castela", "Seu Jorge", "Anitta"], 1],
        ["Quem canta a música 'Balada'? :", ["Gustavo Lima", "Veigh", "Oruam"], 0],
        ["Quem canta a música 'Infiel'? :", ["Lucas Lucco", "Michel Teló", "Marília Mendonça"], 2],
        ["Quem canta a música 'Cheri Cheri Lady'? :", ["MC Poze", "Pablo Vittar", "Modern Talking"], 2],
        ["Quem canta a música 'Hey Brother'? :", ["Marshmello", "Alok", "Avicii"], 2],
    ];
}

//Função que corrige o quiz
function corrigirQuiz($perguntas, $respostas) {
    $acertos = 0;

    for ($i = 0; $i < count($perguntas); $i++) {
        $indiceCorreto = $perguntas[$i][2];   // índice da resposta correta
        $respostaUsuario = $respostas["p$i"]; // resposta marcada 

        if ($respostaUsuario == $indiceCorreto) {
            $acertos++;
        }
    }

    return $acertos;
}

//Execução principal
$perguntas = criarQuiz();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $acertos = corrigirQuiz($perguntas, $_POST);
    $total = count($perguntas);
    echo "<h2>Você acertou $acertos de $total perguntas!</h2>";
    echo '<a href="functions.php">Tentar novamente</a>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Music</title>
</head>
<body>
    <h1>De quem são as seguintes musicas?</h1>

    <form method="post">
        <?php
        for ($i = 0; $i < count($perguntas); $i++) {
            echo "<fieldset>";
            echo "<legend>" . $perguntas[$i][0] . "</legend>";

            //Percorre as alternativas
            for ($j = 0; $j < count($perguntas[$i][1]); $j++) {
                echo "<label>";
                echo "<input type='radio' name='p$i' value='$j' required> ";
                echo $perguntas[$i][1][$j];
                echo "</label><br>";
            }

            echo "</fieldset><br>";
        }
        ?>
        <button type="submit">Enviar Respostas</button>
    </form>
</body>
</html>
