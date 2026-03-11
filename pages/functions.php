<?php
// Logic preserved
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
    <title>Music - Quiz Musical | Premium Experience</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    
    <!-- Bootstrap 4.6 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Custom Design System -->
    <link rel="stylesheet" href="../css/style.css">

    <style>
        .quiz-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .quiz-header {
            text-align: center;
            padding: 50px 0;
        }
        
        .question-card {
            margin-bottom: 40px;
            padding: 30px;
        }
        
        .lyrics-box {
            font-style: italic;
            font-size: 1.4rem;
            color: var(--secondary);
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            border-left: 4px solid var(--primary);
            background: rgba(255,255,255,0.02);
        }
        
        .options-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        @media (max-width: 576px) {
            .options-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .option-label {
            display: block;
            margin: 0;
            cursor: pointer;
        }
        
        .option-card-inner {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 15px;
            padding: 15px;
            text-align: center;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }
        
        .option-image-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 auto 15px auto;
            background-size: cover;
            background-position: center;
            border: 3px solid transparent;
            transition: var(--transition);
        }
        
        .option-label input[type="radio"] {
            display: none;
        }
        
        .option-label input[type="radio"]:checked + .option-card-inner {
            background: rgba(138, 43, 226, 0.15);
            border-color: var(--primary);
            transform: scale(1.03);
            box-shadow: 0 0 20px var(--primary-glow);
        }
        
        .option-label input[type="radio"]:checked + .option-card-inner .option-image-circle {
            border-color: var(--primary);
        }
        
        .option-label:hover .option-card-inner {
            background: rgba(255,255,255,0.08);
            border-color: var(--secondary);
        }
        
        .artist-name-label {
            font-weight: 600;
            display: block;
            margin-top: 10px;
        }
        
        .result-card {
            text-align: center;
            padding: 60px 40px;
        }
        
        .score-circle {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 8px solid var(--primary);
            margin: 0 auto 30px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: 800;
            box-shadow: 0 0 30px var(--primary-glow);
        }
    </style>
</head>
<body class="animate-fade-in">

    <?php include '../includes/navbar.php'; ?>

    <main class="container py-5">
        
        <?php if ($mostrar_resultados) : ?>
            <div class="quiz-container">
                <div class="glass-panel result-card mt-5">
                    <div class="score-circle">
                        <?= $acertos ?>/<?= $total ?>
                    </div>
                    <h2 class="h1 font-weight-bold mb-3">Quiz Finalizado!</h2>
                    <p class="lead text-muted mb-5">
                        <?php 
                        if($acertos == $total) echo "UAU! Você é um mestre da música brasileira! 🏆";
                        elseif($acertos > $total/2) echo "Muito bem! Você conhece bem o nosso som. 👏";
                        else echo "Continue explorando! A música brasileira é um universo. 🎵";
                        ?>
                    </p>
                    <a href="functions.php" class="btn btn-primary btn-lg">Tentar Novamente</a>
                    <div class="mt-4">
                        <a href="../index.php" class="text-secondary"><i class="fas fa-arrow-left mr-2"></i>Voltar para o Início</a>
                    </div>
                </div>
            </div>
        <?php else : ?>
            
            <div class="quiz-header">
                <h1 class="font-weight-bold display-4">Musical Quiz</h1>
                <p class="text-muted">De quem são as seguintes letras famosas? Mostre que você conhece!</p>
            </div>

            <form method="post" class="quiz-container">
                <?php foreach ($perguntas as $i => $pergunta) : ?>
                    <div class="glass-panel question-card animate-fade-in" style="animation-delay: <?= $i * 0.1 ?>s">
                        <div class="lyrics-box">
                            "<?= htmlspecialchars($pergunta[0]); ?>"
                        </div>
                        
                        <div class="options-grid">
                            <?php 
                            foreach ($pergunta[1] as $j => $alternativa) : 
                                $artist_name = $alternativa[0];
                                $image_file = $alternativa[1];
                            ?>
                                <label class="option-label">
                                    <input type="radio" name="p<?php echo $i; ?>" value="<?php echo $j; ?>" required>
                                    <div class="option-card-inner">
                                        <div class="option-image-circle" style="background-image: url(../imagens/<?php echo htmlspecialchars($image_file); ?>)"></div>
                                        <span class="artist-name-label"><?php echo htmlspecialchars($artist_name); ?></span>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="text-center py-5">
                    <button type="submit" class="btn btn-primary btn-lg px-5" style="border-radius: 50px;">
                        <i class="fas fa-paper-plane mr-2"></i> Finalizar Quiz
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>