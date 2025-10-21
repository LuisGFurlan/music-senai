<?php
function carregarPerguntas() {
    require_once 'quiz_data.php';
    return $perguntas;
}

$perguntas = carregarPerguntas();

function corrigirQuiz($perguntas, $respostas) {
    $acertos = 0;
    for ($i = 0; $i < count($perguntas); $i++) {
        if (isset($respostas["p$i"])) {
            $indiceCorreto = $perguntas[$i][2];
            $respostaUsuario = $respostas["p$i"];
            if ($respostaUsuario == $indiceCorreto) {
                $acertos++;
            }
        }
    }
    return $acertos;
}

$mostrar_resultados = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $acertos = corrigirQuiz($perguntas, $_POST);
    $total = count($perguntas);
    $mostrar_resultados = true;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Music</title>
    <link rel="stylesheet" href="../css/functions.css">
    <link rel="stylesheet" href="../css/header.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <main class="main-content">
        <?php if ($mostrar_resultados) : ?>
            <div class="results">
                <h2>Você acertou <?php echo $acertos; ?> de <?php echo $total; ?> perguntas!</h2>
                <a href="functions.php">Tentar novamente</a>
            </div>
        <?php else : ?>
            <h1 class="quiz-title">De quem são as seguintes músicas?</h1>
            <form method="post">
                <?php
                foreach ($perguntas as $i => $pergunta) :
                ?>
                    <div class="question-block">
                        <h2 class="question-list-title"><?php echo htmlspecialchars($pergunta[0]); ?></h2>
                        <div class="options-section-list">
                            <?php 
                            foreach ($pergunta[1] as $j => $alternativa) : 
                                $artist_name = $alternativa[0];
                                $image_file = $alternativa[1];
                            ?>
                                <div class="option-container">
                                    <label class="option-card">
                                        <input type="radio" name="p<?php echo $i; ?>" value="<?php echo $j; ?>" required>
                                        <div class="option-image" style="background-image: url(../imagens/<?php echo htmlspecialchars($image_file); ?>)"></div>
                                    </label>
                                    <div class="artist-name"><?php echo htmlspecialchars($artist_name); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <button type="submit" class="submit-btn">Enviar Respostas</button>
            </form>
        <?php endif; ?>
    </main>
</body>
</html>