<?php
session_start();

// === INITIALIZATION & LOGIC (Preserved from original) ===
if (!isset($_SESSION['playlists_initialized'])) {
	$_SESSION['playlists'] = [
		['nome'=>'Músicas Curtidas','musicas'=>['Neon Sky','Digital Horizon','Echoes of Tomorrow','Midnight Pulse','Industrial Dreams']],
		['nome'=>'Playlist 1','musicas'=>[]],
		['nome'=>'Playlist 2','musicas'=>[]]
	];
	$_SESSION['playlists_initialized'] = true;
}

// Data conversion for older sessions
if (isset($_SESSION['playlists']) && !empty($_SESSION['playlists'])) {
	$primeiraPlaylist = $_SESSION['playlists'][0];
	if (isset($primeiraPlaylist['songs']) && !isset($primeiraPlaylist['musicas'])) {
		foreach ($_SESSION['playlists'] as $i => $playlist) {
			if (isset($playlist['songs'])) {
				$_SESSION['playlists'][$i]['musicas'] = $playlist['songs'];
				unset($_SESSION['playlists'][$i]['songs']);
			}
			if (isset($playlist['name'])) {
				$_SESSION['playlists'][$i]['nome'] = $playlist['name'];
				unset($_SESSION['playlists'][$i]['name']);
			}
		}
	}
}

if (!isset($_SESSION['mensagens_flash'])) {
	$_SESSION['mensagens_flash'] = ['erros'=>[], 'sucesso'=>''];
}

function limpar($texto){ 
	return trim(htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8')); 
}

function playlistExiste($nome, $playlists) {
	$nomes = array_column($playlists, 'nome');
	$nomesMinusculos = array_map('mb_strtolower', $nomes);
	return in_array(mb_strtolower($nome), $nomesMinusculos);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$acao = $_POST['acao'] ?? '';

	if ($acao === 'criar_playlist') {
		$nome = limpar($_POST['nome_playlist'] ?? '');
		if ($nome === '') {
			$_SESSION['mensagens_flash']['erros'][] = 'O nome da playlist não pode ser vazio.';
		} elseif (playlistExiste($nome, $_SESSION['playlists'])) {
			$_SESSION['mensagens_flash']['erros'][] = 'Já existe uma playlist com esse nome.';
		} else {
			$_SESSION['playlists'][] = ['nome' => $nome, 'musicas' => []];
			$_SESSION['mensagens_flash']['sucesso'] = "Playlist '$nome' criada com sucesso.";
		}
	}

	if ($acao === 'adicionar_musica') {
		$indiceDestino = (int)($_POST['indice_destino'] ?? -1);
		$musica = limpar($_POST['musica'] ?? '');
		if (!isset($_SESSION['playlists'][$indiceDestino])) {
			$_SESSION['mensagens_flash']['erros'][] = 'Playlist inválida.';
		} elseif ($indiceDestino === 0) {
			$_SESSION['mensagens_flash']['erros'][] = 'Não é permitido adicionar músicas em Curtidas.';
		} elseif ($musica === '') {
			$_SESSION['mensagens_flash']['erros'][] = 'Nenhuma música selecionada.';
		} elseif (in_array($musica, $_SESSION['playlists'][$indiceDestino]['musicas'] ?? [])) {
			$_SESSION['mensagens_flash']['erros'][] = "A música '$musica' já está nessa playlist";
		} else {
			$_SESSION['playlists'][$indiceDestino]['musicas'][] = $musica;
			$_SESSION['mensagens_flash']['sucesso'] = "Música '$musica' adicionada à playlist '{$_SESSION['playlists'][$indiceDestino]['nome']}'.";
		}
	}

	if ($acao === 'remover_musica') {
		$indiceDestino = (int)($_POST['indice_destino'] ?? -1);
		$musica = limpar($_POST['musica'] ?? '');
		if (!isset($_SESSION['playlists'][$indiceDestino])) {
			$_SESSION['mensagens_flash']['erros'][] = 'Playlist inválida.';
		} elseif ($indiceDestino === 0) {
			$_SESSION['mensagens_flash']['erros'][] = 'Não é permitido remover músicas de Curtidas';
		} else {
			$musicasPlaylist = $_SESSION['playlists'][$indiceDestino]['musicas'] ?? [];
			$indiceMusica = array_search($musica, $musicasPlaylist);
			if ($indiceMusica !== false) {
				array_splice($_SESSION['playlists'][$indiceDestino]['musicas'], $indiceMusica, 1);
				$_SESSION['mensagens_flash']['sucesso'] = "Música '$musica' removida de '{$_SESSION['playlists'][$indiceDestino]['nome']}'.";
			} else {
				$_SESSION['mensagens_flash']['erros'][] = 'Música não encontrada na playlist.';
			}
		}
	}

	if ($acao === 'limpar_playlists') {
		$totalPlaylists = count($_SESSION['playlists']);
		for ($i = 1; $i < $totalPlaylists; $i++) {
			$_SESSION['playlists'][$i]['musicas'] = [];
		}
		$_SESSION['mensagens_flash']['sucesso'] = 'Todas as playlists criadas foram limpas.';
	}

	if ($acao === 'deletar_playlist') {
		$indiceDeletar = (int)($_POST['indice_deletar'] ?? -1);
		if ($indiceDeletar <= 0 || !isset($_SESSION['playlists'][$indiceDeletar])) {
			$_SESSION['mensagens_flash']['erros'][] = 'Playlist inválida ou não pode ser removida.';
		} else {
			$nomeDeletado = $_SESSION['playlists'][$indiceDeletar]['nome'];
			array_splice($_SESSION['playlists'], $indiceDeletar, 1);
			$_SESSION['mensagens_flash']['sucesso'] = "Playlist '$nomeDeletado' removida com sucesso.";
		}
	}

	header('Location: ' . $_SERVER['PHP_SELF']);
	exit();
}

$erros = $_SESSION['mensagens_flash']['erros'];
$sucesso = $_SESSION['mensagens_flash']['sucesso'];
$_SESSION['mensagens_flash'] = ['erros'=>[], 'sucesso'=>''];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Music - Playlists | Premium Experience</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    
    <!-- Bootstrap 4.6 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Custom Design System -->
    <link rel="stylesheet" href="../css/style.css">

    <style>
        .page-header {
            padding: 40px 0;
            text-align: left;
        }
        
        .section-card {
            padding: 25px;
            height: 100%;
            transition: var(--transition);
        }
        
        .playlist-info-card {
            background: rgba(255,255,255,0.03);
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid rgba(255,255,255,0.05);
            cursor: pointer;
            transition: var(--transition);
        }
        
        .playlist-info-card:hover {
            background: rgba(255,255,255,0.08);
            transform: translateX(5px);
            border-color: var(--primary);
        }
        
        .song-item {
            background: rgba(255,255,255,0.02);
            border-radius: 10px;
            padding: 12px 15px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .song-item i {
            color: var(--secondary);
            margin-right: 12px;
        }
        
        .form-control-custom {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            color: white;
            border-radius: 12px;
            padding: 12px;
        }
        
        .form-control-custom:focus {
            background: rgba(255,255,255,0.1);
            border-color: var(--primary);
            color: white;
            box-shadow: 0 0 10px var(--primary-glow);
        }
        
        .custom-select-box {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            color: white;
            border-radius: 12px;
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
        }
        
        .custom-select-box option {
            background: #24243e;
        }
        
        .playlist-grid-card {
            background: rgba(255,255,255,0.03);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 30px;
            border: 1px solid rgba(255,255,255,0.05);
            height: 100%;
        }
        
        .alert-custom {
            border-radius: 12px;
            background: rgba(138, 43, 226, 0.1);
            border: 1px solid var(--primary);
            color: white;
        }
    </style>
</head>
<body class="animate-fade-in">

    <?php include '../includes/navbar.php'; ?>

    <main class="container py-5">
        
        <!-- Alerts -->
        <?php if(!empty($erros)): ?>
            <div class="alert alert-danger alert-dismissible fade show glass-panel border-danger" role="alert" style="background: rgba(220, 53, 69, 0.1);">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <?= htmlspecialchars(implode(' | ', $erros), ENT_QUOTES, 'UTF-8') ?>
                <button type="button" class="close text-white" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
        
        <?php if($sucesso !== ''): ?>
            <div class="alert alert-success alert-dismissible fade show glass-panel border-success" role="alert" style="background: rgba(40, 167, 69, 0.1);">
                <i class="fas fa-check-circle mr-2"></i>
                <?= htmlspecialchars($sucesso, ENT_QUOTES, 'UTF-8') ?>
                <button type="button" class="close text-white" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Sidebar: Navigation & Favorites -->
            <div class="col-lg-4">
                <div class="glass-panel section-card mb-4">
                    <h3 class="h5 mb-4 font-weight-bold text-uppercase" style="letter-spacing: 1px; color: var(--secondary);">
                        <i class="fas fa-heart mr-2"></i> Músicas Curtidas
                    </h3>
                    <div class="musicas-curtidas-list" style="max-height: 400px; overflow-y: auto; padding-right: 5px;">
                        <?php 
                        $musicasCurtidas = $_SESSION['playlists'][0]['musicas'] ?? [];
                        if (!empty($musicasCurtidas)) {
                            foreach ($musicasCurtidas as $musica) {
                                echo '<div class="song-item">
                                        <div><i class="fas fa-music"></i> ' . htmlspecialchars($musica, ENT_QUOTES, 'UTF-8') . '</div>
                                        <span class="badge badge-pill" style="background: rgba(255,255,255,0.1); font-weight: 300;">Library</span>
                                      </div>';
                            }
                        } else {
                            echo '<div class="text-muted text-center py-4">Nenhuma música curtida ainda.</div>';
                        }
                        ?>
                    </div>

                    <h3 class="h5 mt-5 mb-4 font-weight-bold text-uppercase" style="letter-spacing: 1px; color: var(--secondary);">
                        <i class="fas fa-list mr-2"></i> Suas Playlists
                    </h3>
                    <div id="playlistList">
                        <?php 
                        $totalPlaylists = count($_SESSION['playlists']);
                        for($i = 1; $i < $totalPlaylists; $i++) {
                            $playlist = $_SESSION['playlists'][$i];
                            $totalMusicas = count($playlist['musicas'] ?? []);
                            echo '<div class="playlist-info-card" onclick="mostrarMusicas(' . $i . ')">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="font-weight-600">' . htmlspecialchars($playlist['nome'], ENT_QUOTES, 'UTF-8') . '</div>
                                        <span class="badge badge-primary badge-pill" style="background: var(--primary);">' . $totalMusicas . '</span>
                                    </div>
                                    <div class="small text-muted mt-1">Playlist Personalizada</div>
                                  </div>';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="col-lg-8">
                <!-- Action Header -->
                <div class="glass-panel section-card mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <h3 class="h5 mb-3 font-weight-bold">GERENCIAR BIBLIOTECA</h3>
                            <form id="formularioCriar" method="POST" class="form-inline w-100">
                                <div class="input-group w-100">
                                    <input type="text" id="nomePlaylist" name="nome_playlist" class="form-control form-control-custom" placeholder="Nome da nova playlist..." autocomplete="off">
                                    <div class="input-group-append">
                                        <input type="hidden" name="acao" value="criar_playlist">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-plus mr-1"></i> Criar
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-5 mt-3 mt-md-0 text-md-right">
                            <form method="POST">
                                <input type="hidden" name="acao" value="limpar_playlists">
                                <button class="btn btn-outline-light btn-sm" type="submit" style="opacity: 0.7; border-radius: 10px;">
                                    <i class="fas fa-broom mr-1"></i> Esvaziar Todas
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Playlist Details Area (Hidden by default) -->
                <div id="areaMusicas" class="glass-panel section-card mb-4" style="display:none;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="h4 font-weight-bold" id="tituloPlaylistSelecionada">Músicas da Playlist</h3>
                        <button class="btn btn-link text-muted" onclick="fecharMusicas()"><i class="fas fa-times"></i></button>
                    </div>
                    <div id="musicasPlaylistSelecionada">
                        <!-- JS content -->
                    </div>
                    
                    <form id="formularioDeletar" method="POST" class="mt-4 pt-3 border-top border-secondary">
                        <input type="hidden" name="indice_deletar" id="indice_deletar">
                        <input type="hidden" name="acao" value="deletar_playlist">
                        <button class="btn btn-danger btn-sm" type="submit" style="border-radius: 8px;">
                            <i class="fas fa-trash-alt mr-1"></i> Deletar Esta Playlist
                        </button>
                    </form>
                </div>

                <!-- Playlist Grid View -->
                <div class="row">
                    <?php 
                    for($i = 0; $i < $totalPlaylists; $i++) {
                        $playlist = $_SESSION['playlists'][$i];
                        $musicas = $playlist['musicas'] ?? [];
                        ?>
                        <div class="col-md-6 mb-4">
                            <div class="playlist-grid-card glass-panel">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="p-3 bg-primary rounded mr-3" style="background: <?= $i==0 ? 'var(--secondary)' : 'var(--primary)' ?> !important;">
                                        <i class="fas <?= $i==0 ? 'fa-heart' : 'fa-compact-disc text-white' ?>"></i>
                                    </div>
                                    <div>
                                        <h4 class="h5 mb-0 font-weight-bold"><?= htmlspecialchars($playlist['nome']) ?></h4>
                                        <small class="text-muted"><?= count($musicas) ?> faixas</small>
                                    </div>
                                </div>
                                
                                <div class="songs-mini-list mb-3" style="max-height: 120px; overflow-y: auto;">
                                    <?php if (empty($musicas)): ?>
                                        <div class="small text-muted italic">Playlist vazia</div>
                                    <?php else: ?>
                                        <?php foreach (array_slice($musicas, 0, 3) as $musica): ?>
                                            <div class="small mb-1">• <?= htmlspecialchars($musica) ?></div>
                                        <?php endforeach; ?>
                                        <?php if(count($musicas) > 3): ?>
                                            <div class="small text-muted font-italic">...e mais <?= count($musicas)-3 ?></div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>

                                <?php if($i !== 0): ?>
                                    <form method="POST" class="mt-auto">
                                        <input type="hidden" name="indice_destino" value="<?= $i ?>">
                                        <select name="musica" class="custom-select-box" required>
                                            <option value="">Adicionar música...</option>
                                            <?php foreach ($musicasCurtidas as $m): ?>
                                                <option value="<?= htmlspecialchars($m) ?>"><?= htmlspecialchars($m) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-primary btn-sm flex-fill mr-1" name="acao" value="adicionar_musica" type="submit">
                                                <i class="fas fa-plus"></i> Add
                                            </button>
                                            <button class="btn btn-outline-danger btn-sm flex-fill ml-1" name="acao" value="remover_musica" type="submit">
                                                <i class="fas fa-minus"></i> Remove
                                            </button>
                                        </div>
                                    </form>
                                <?php else: ?>
                                    <div class="small text-center py-2 text-muted border-top border-secondary">Playlist padrão do sistema</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const dadosPlaylists = <?php echo json_encode($_SESSION['playlists']); ?>;

        function mostrarMusicas(indicePlaylist) {
            const playlist = dadosPlaylists[indicePlaylist];
            document.getElementById('tituloPlaylistSelecionada').textContent = 'Músicas: ' + playlist.nome;
            document.getElementById('indice_deletar').value = indicePlaylist;
            
            let htmlMusicas = '';
            if (playlist.musicas.length === 0) {
                htmlMusicas = '<div class="text-muted italic py-3">Esta playlist está vazia. Comece a adicionar músicas das suas curtidas!</div>';
            } else {
                playlist.musicas.forEach((musica, i) => {
                    htmlMusicas += `
                        <div class="song-item animate-fade-in" style="animation-delay: ${i * 0.1}s">
                            <div><i class="fas fa-music"></i> ${musica}</div>
                            <span class="small text-muted">FAIXA ${i + 1}</span>
                        </div>`;
                });
            }
            
            document.getElementById('musicasPlaylistSelecionada').innerHTML = htmlMusicas;
            document.getElementById('areaMusicas').style.display = 'block';
            window.scrollTo({ top: document.getElementById('areaMusicas').offsetTop - 100, behavior: 'smooth' });
        }

        function fecharMusicas() {
            document.getElementById('areaMusicas').style.display = 'none';
        }

        document.getElementById('formularioCriar').addEventListener('submit', function(e) {
            const nome = document.getElementById('nomePlaylist').value.trim();
            if (nome.length === 0) { 
                e.preventDefault(); 
                alert('Dê um nome legal para a sua playlist! 🎵');
            }
        });
    </script>
</body>
</html>
