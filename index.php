<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music - Senai | Premium Audio Experience</title>
    
    <!-- Bootstrap 4.6 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Custom Design System -->
    <link rel="stylesheet" href="css/style.css">
    
    <style>
        .player-container {
            max-width: 900px;
            margin: 40px auto;
            position: relative;
            z-index: 2;
        }
        
        .music-card {
            display: flex;
            flex-direction: column;
            gap: 20px;
            padding: 30px;
            overflow: hidden;
        }
        
        .album-art-wrapper {
            width: 100%;
            aspect-ratio: 1/1;
            max-width: 350px;
            margin: 0 auto;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            transition: var(--transition);
        }
        
        .album-art-wrapper:hover {
            transform: scale(1.02);
            box-shadow: 0 15px 50px rgba(138, 43, 226, 0.3);
        }
        
        .album-art-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .player-controls-section {
            flex-grow: 1;
        }
        
        .song-details h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .song-details p {
            color: var(--secondary);
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .custom-progress {
            height: 6px;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            margin: 25px 0 10px 0;
            position: relative;
            cursor: pointer;
        }
        
        .custom-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            width: 35%;
            border-radius: 10px;
            position: relative;
            box-shadow: 0 0 10px var(--primary-glow);
        }
        
        .custom-progress-fill::after {
            content: '';
            position: absolute;
            right: -6px;
            top: 50%;
            transform: translateY(-50%);
            width: 12px;
            height: 12px;
            background: white;
            border-radius: 50%;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
        }
        
        .time-labels {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            color: var(--text-muted);
        }
        
        .main-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 30px;
            margin: 20px 0;
        }
        
        .control-icon {
            font-size: 1.2rem;
            color: var(--text-muted);
            cursor: pointer;
            transition: var(--transition);
        }
        
        .control-icon:hover {
            color: var(--text-white);
            transform: scale(1.2);
        }
        
        .play-pause-btn {
            width: 60px;
            height: 60px;
            background: var(--text-white);
            color: var(--bg-dark);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.5rem;
            box-shadow: 0 6px 20px rgba(255,255,255,0.2);
            transition: var(--transition);
        }
        
        .play-pause-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 25px rgba(255,255,255,0.3);
        }
        
        .volume-slider-box {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 20px;
        }
        
        .volume-bar-custom {
            flex-grow: 1;
            height: 4px;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            position: relative;
        }
        
        .volume-fill-custom {
            height: 100%;
            background: var(--text-white);
            width: 70%;
            border-radius: 10px;
        }
        
        @media (min-width: 768px) {
            .music-card {
                flex-direction: row;
                align-items: center;
                padding: 40px;
            }
            .album-art-wrapper {
                max-width: 300px;
            }
        }
        
        .welcome-hero {
            padding: 60px 0 20px 0;
            text-align: center;
        }
        
        .welcome-hero h2 {
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .bg-blobs {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            pointer-events: none;
        }
        
        .blob {
            position: absolute;
            filter: blur(80px);
            border-radius: 50%;
            opacity: 0.3;
        }
        
        .blob-1 {
            width: 400px;
            height: 400px;
            background: var(--primary);
            top: -100px;
            left: -100px;
        }
        
        .blob-2 {
            width: 300px;
            height: 300px;
            background: var(--secondary);
            bottom: -50px;
            right: -50px;
        }
    </style>
</head>
<body class="animate-fade-in">

    <!-- Background Decoration -->
    <div class="bg-blobs">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
    </div>

    <?php include 'includes/navbar.php'; ?>

    <main class="container">
        
        <div class="welcome-hero">
            <h2>Olá, <span style="color: var(--secondary);">Explorador Somoro!</span> 😁</h2>
            <p class="text-muted">Pronto para continuar sua jornada musical?</p>
        </div>

        <div class="player-container">
            <div class="glass-panel music-card">
                <!-- Album Art -->
                <div class="album-art-wrapper">
                    <img src="imagens/imagem.png" alt="Album Art">
                </div>
                
                <!-- Player Controls -->
                <div class="player-controls-section ml-md-4 mt-4 mt-md-0">
                    <div class="song-details">
                        <h1>Neon Dreams</h1>
                        <p>Aethelgard • Synthwave Collection</p>
                    </div>
                    
                    <!-- Progress Section -->
                    <div class="progress-section">
                        <div class="custom-progress">
                            <div class="custom-progress-fill"></div>
                        </div>
                        <div class="time-labels">
                            <span>1:42</span>
                            <span>4:15</span>
                        </div>
                    </div>
                    
                    <!-- Controls -->
                    <div class="main-controls">
                        <i class="fas fa-random control-icon" title="Shuffle"></i>
                        <i class="fas fa-step-backward control-icon" title="Previous"></i>
                        <div class="play-pause-btn">
                            <i class="fas fa-play ml-1"></i>
                        </div>
                        <i class="fas fa-step-forward control-icon" title="Next"></i>
                        <i class="fas fa-redo control-icon" title="Repeat"></i>
                    </div>
                    
                    <!-- Volume -->
                    <div class="volume-slider-box">
                        <i class="fas fa-volume-up text-muted"></i>
                        <div class="volume-bar-custom">
                            <div class="volume-fill-custom"></div>
                        </div>
                        <span class="small text-muted">70%</span>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Interactive Player Elements
        const playBtn = document.querySelector('.play-pause-btn');
        const playIcon = playBtn.querySelector('i');
        let isPlaying = false;

        playBtn.addEventListener('click', () => {
            isPlaying = !isPlaying;
            if (isPlaying) {
                playIcon.className = 'fas fa-pause';
                playBtn.style.boxShadow = '0 0 30px var(--primary-glow)';
            } else {
                playIcon.className = 'fas fa-play ml-1';
                playBtn.style.boxShadow = '0 6px 20px rgba(255,255,255,0.2)';
            }
        });

        // Add subtle hover movements
        document.addEventListener('mousemove', (e) => {
            const blobs = document.querySelectorAll('.blob');
            const x = e.clientX / window.innerWidth;
            const y = e.clientY / window.innerHeight;
            
            blobs[0].style.transform = `translate(${x * 30}px, ${y * 30}px)`;
            blobs[1].style.transform = `translate(${x * -30}px, ${y * -30}px)`;
        });
    </script>
</body>
</html>