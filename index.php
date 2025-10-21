<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music - Senai</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/header.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <main>
        <div class="welcome-section">
            <h2 class="welcome-title">Olá {nome_usuario}! Seja bem vindo! 😁</h2>
            <h3 class="welcome-subtitle">Continuar ouvindo?</h3>
        </div>
        <div class="main-container">
            <div class="music-player">
                <!-- Album Art -->
                <div class="album-art-container">
                    <img src="imagens/imagem.png" class="album-art" alt="Album Art">
                </div>
                
                <!-- Song Info -->
                <div class="song-info">
                    <h3 class="song-title">Nome da Música</h3>
                    <p class="song-artist">Artista</p>
                </div>
                
                <!-- Progress Bar -->
                <div class="progress-container">
                    <div class="progress-bar">
                        <div class="progress-fill">
                            <div class="progress-handle"></div>
                        </div>
                    </div>
                    <div class="time-display">
                        <span>1:23</span>
                        <span>3:45</span>
                    </div>
                </div>
                
                <!-- Controls -->
                <div class="controls">
                    <!-- Shuffle -->
                    <button class="control-btn shuffle-btn">🔀</button>
                    
                    <!-- Previous -->
                    <button class="control-btn prev-btn">⏮</button>
                    
                    <!-- Play/Pause -->
                    <button class="control-btn play-btn">▶</button>
                    
                    <!-- Next -->
                    <button class="control-btn next-btn">⏭</button>
                    
                    <!-- Repeat -->
                    <button class="control-btn repeat-btn">🔁</button>
                </div>
                
                <!-- Volume -->
                <div class="volume-container">
                    <span class="volume-icon">🔊</span>
                    <div class="volume-bar">
                        <div class="volume-fill"></div>
                    </div>
                    <span class="volume-percentage">70%</span>
                </div>
            </div>
            <div class="background-image-container">
                <img src="imagens/background.jpg" class="background-image" alt="Background">
            </div>
        </div>
    </main>

</body>
</html>