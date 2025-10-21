<?php
// Inicia a sessão do usuário (como uma "memória" temporária)
session_start();

/*
	=== SISTEMA DE PLAYLISTS USANDO ARRAYS ===
	
	O que são Arrays?
	- Arrays são como "listas" ou "caixas" que guardam várias informações
	- Exemplo: ['maçã', 'banana', 'laranja'] é um array de frutas
	- Cada item tem uma posição: 0, 1, 2, etc.
	
	Funcionalidades desta página:
	1) Playlist "Músicas Curtidas" (sempre existe)
	2) Criar novas playlists
	3) Adicionar músicas das Curtidas para outras playlists
	4) Remover músicas de qualquer playlist
	5) Limpar todas as playlists criadas
	6) Remover playlists selecionadas
	7) Mensagens aparecem e desaparecem automaticamente
*/

// === CRIANDO O ARRAY PRINCIPAL DE PLAYLISTS ===
// Verifica se é a primeira vez que o usuário acessa a página
if (!isset($_SESSION['playlists_initialized'])) {
	// Cria o array principal com 3 playlists
	$_SESSION['playlists'] = [
		// Playlist 0: Músicas Curtidas (sempre tem músicas)
		['nome'=>'Músicas Curtidas','musicas'=>['Neon Sky','Digital Horizon','Echoes of Tomorrow','Midnight Pulse','Industrial Dreams']],
		// Playlist 1: Playlist 1 (começa vazia)
		['nome'=>'Playlist 1','musicas'=>[]],
		// Playlist 2: Playlist 2 (começa vazia)
		['nome'=>'Playlist 2','musicas'=>[]]
	];
	// Marca que já foi inicializado
	$_SESSION['playlists_initialized'] = true;
}

// === CONVERSÃO DE ARRAYS ANTIGOS PARA NOVOS ===
// Se o usuário já tinha dados salvos com nomes antigos, converte para os novos
if (isset($_SESSION['playlists']) && !empty($_SESSION['playlists'])) {
	$primeiraPlaylist = $_SESSION['playlists'][0]; // Pega a primeira playlist
	if (isset($primeiraPlaylist['songs']) && !isset($primeiraPlaylist['musicas'])) {
		// Se tem 'songs' mas não tem 'musicas', precisa converter
		foreach ($_SESSION['playlists'] as $i => $playlist) {
			if (isset($playlist['songs'])) {
				$_SESSION['playlists'][$i]['musicas'] = $playlist['songs']; // Copia 'songs' para 'musicas'
				unset($_SESSION['playlists'][$i]['songs']); // Remove o antigo 'songs'
			}
			if (isset($playlist['name'])) {
				$_SESSION['playlists'][$i]['nome'] = $playlist['name']; // Copia 'name' para 'nome'
				unset($_SESSION['playlists'][$i]['name']); // Remove o antigo 'name'
			}
		}
	}
}

// === CRIANDO ARRAY PARA MENSAGENS ===
// Array para guardar mensagens de erro e sucesso
if (!isset($_SESSION['mensagens_flash'])) {
	$_SESSION['mensagens_flash'] = ['erros'=>[], 'sucesso'=>'']; // Array com 2 partes: erros e sucesso
}

// === FUNÇÕES QUE USAM ARRAYS ===

// Função para limpar texto (remove espaços e caracteres especiais)
function limpar($texto){ 
	return trim(htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8')); 
}

// Função que usa array_column() - pega todos os nomes das playlists
function playlistExiste($nome, $playlists) {
	$nomes = array_column($playlists, 'nome'); // Extrai só os nomes: ['Playlist 1', 'Playlist 2', ...]
	$nomesMinusculos = array_map('mb_strtolower', $nomes); // Converte para minúsculas: ['playlist 1', 'playlist 2', ...]
	return in_array(mb_strtolower($nome), $nomesMinusculos); // Verifica se o nome já existe
}

// Função que usa array_map() e in_array() - verifica se música já existe
function musicaExisteNaPlaylist($musica, $musicasPlaylist) {
	$musicasMinusculas = array_map('mb_strtolower', $musicasPlaylist); // Converte todas as músicas para minúsculas
	return in_array(mb_strtolower($musica), $musicasMinusculas); // Verifica se a música já está na lista
}

// === PROCESSAMENTO DE FORMULÁRIOS (USANDO ARRAYS) ===
// Quando o usuário clica em um botão, esta parte é executada
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	$acao = $_POST['acao'] ?? ''; // Pega qual botão foi clicado

	// === CRIAR NOVA PLAYLIST (ADICIONA AO ARRAY) ===
	if ($acao === 'criar_playlist') {
		$nome = limpar($_POST['nome_playlist'] ?? ''); // Pega o nome digitado

		if ($nome === '') {
			// Adiciona erro ao array de erros (usando [])
			$_SESSION['mensagens_flash']['erros'][] = 'O nome da playlist não pode ser vazio.';
		} elseif (playlistExiste($nome, $_SESSION['playlists'])) {
			// Adiciona erro ao array de erros (usando [])
			$_SESSION['mensagens_flash']['erros'][] = 'Já existe uma playlist com esse nome.';
		} else {
			// Adiciona nova playlist ao array principal (usando [])
			$_SESSION['playlists'][] = [
				'nome'  => $nome,
				'musicas' => [] // Array vazio de músicas
			];
			// Adiciona mensagem de sucesso
			$_SESSION['mensagens_flash']['sucesso'] = "Playlist '$nome' criada com sucesso.";
		}
	}

	// === ADICIONAR MÚSICA À PLAYLIST (USA ARRAY_SEARCH) ===
	if ($acao === 'adicionar_musica') {
		$indiceDestino = (int)($_POST['indice_destino'] ?? -1); // Qual playlist (0, 1, 2, etc.)
		$musica = limpar($_POST['musica'] ?? ''); // Qual música foi selecionada

		if (!isset($_SESSION['playlists'][$indiceDestino])) {
			$_SESSION['mensagens_flash']['erros'][] = 'Playlist inválida.';
		} elseif ($indiceDestino === 0) {
			$_SESSION['mensagens_flash']['erros'][] = 'Não é permitido adicionar músicas em Curtidas.';
		} elseif ($musica === '') {
			$_SESSION['mensagens_flash']['erros'][] = 'Nenhuma música selecionada.';
		} elseif (musicaExisteNaPlaylist($musica, $_SESSION['playlists'][$indiceDestino]['musicas'] ?? [])) {
			$_SESSION['mensagens_flash']['erros'][] = "A música '$musica' já está nessa playlist";
		} else {
			// Adiciona música ao array de músicas da playlist (usando [])
			$_SESSION['playlists'][$indiceDestino]['musicas'][] = $musica;
			$_SESSION['mensagens_flash']['sucesso'] = "Música '$musica' adicionada à playlist '{$_SESSION['playlists'][$indiceDestino]['nome']}'.";
		}
	}

	// === REMOVER MÚSICA DA PLAYLIST (USA ARRAY_SEARCH E ARRAY_SPLICE) ===
	if ($acao === 'remover_musica') {
		$indiceDestino = (int)($_POST['indice_destino'] ?? -1); // Qual playlist
		$musica = limpar($_POST['musica'] ?? ''); // Qual música remover

		if (!isset($_SESSION['playlists'][$indiceDestino])) {
			$_SESSION['mensagens_flash']['erros'][] = 'Playlist inválida.';
		} elseif ($indiceDestino === 0) {
			$_SESSION['mensagens_flash']['erros'][] = 'Não é permitido remover músicas de Curtidas';
		} else {
			$musicasPlaylist = $_SESSION['playlists'][$indiceDestino]['musicas'] ?? [];
			// array_search() encontra a posição da música no array
			$indiceMusica = array_search($musica, $musicasPlaylist);
			
			if ($indiceMusica !== false) {
				// array_splice() remove a música da posição encontrada
				array_splice($_SESSION['playlists'][$indiceDestino]['musicas'], $indiceMusica, 1);
				$_SESSION['mensagens_flash']['sucesso'] = "Música '$musica' removida de '{$_SESSION['playlists'][$indiceDestino]['nome']}'.";
			} else {
				$_SESSION['mensagens_flash']['erros'][] = 'Música não encontrada na playlist.';
			}
		}
	}

	// === LIMPAR TODAS AS PLAYLISTS (USA COUNT E FOR) ===
	if ($acao === 'limpar_playlists') {
		$totalPlaylists = count($_SESSION['playlists']); // Conta quantas playlists existem
		for ($i = 1; $i < $totalPlaylists; $i++) { // Começa do 1 (pula a playlist 0 que é Curtidas)
			$_SESSION['playlists'][$i]['musicas'] = []; // Esvazia o array de músicas
		}
		$_SESSION['mensagens_flash']['sucesso'] = 'Todas as playlists criadas foram limpas.';
	}

	// === DELETAR PLAYLIST ESPECÍFICA (USA ARRAY_SPLICE) ===
	if ($acao === 'deletar_playlist') {
		$indiceDeletar = (int)($_POST['indice_deletar'] ?? -1); // Qual playlist deletar

		if ($indiceDeletar <= 0 || !isset($_SESSION['playlists'][$indiceDeletar])) {
			$_SESSION['mensagens_flash']['erros'][] = 'Playlist inválida ou não pode ser removida.';
		} else {
			$nomeDeletado = $_SESSION['playlists'][$indiceDeletar]['nome']; // Salva o nome antes de deletar
			// array_splice() remove a playlist inteira do array
			array_splice($_SESSION['playlists'], $indiceDeletar, 1);
			$_SESSION['mensagens_flash']['sucesso'] = "Playlist '$nomeDeletado' removida com sucesso.";
		}
	}

	header('Location: ' . $_SERVER['PHP_SELF']);
	exit();
}

// === PREPARAR DADOS PARA EXIBIÇÃO ===
// Pega as mensagens dos arrays e limpa para próxima vez
$erros = $_SESSION['mensagens_flash']['erros']; // Array de erros
$sucesso = $_SESSION['mensagens_flash']['sucesso']; // String de sucesso
$_SESSION['mensagens_flash'] = ['erros'=>[], 'sucesso'=>'']; // Limpa os arrays

// === FUNÇÕES QUE USAM ARRAYS PARA EXIBIR DADOS ===

// Função que usa array_map() para criar HTML das músicas
function exibirMusicas($musicas) {
	if (empty($musicas)) {
		echo '<div style="opacity:0.7">Sem músicas ainda.</div>';
	} else {
		// array_map() aplica uma função a cada música do array
		$elementosMusicas = array_map(function($musica) {
			return '<div style="margin-bottom:6px">' . htmlspecialchars($musica, ENT_QUOTES, 'UTF-8') . '</div>';
		}, $musicas);
		// implode() junta todos os elementos em uma string
		echo implode('', $elementosMusicas);
	}
}

// Função que usa array_map() para criar opções do select
function criarOpcoesMusicas($musicas) {
	// array_map() cria uma tag <option> para cada música
	$opcoes = array_map(function($musica) {
		$musicaEscapada = htmlspecialchars($musica, ENT_QUOTES, 'UTF-8');
		return "<option value=\"$musicaEscapada\">$musicaEscapada</option>";
	}, $musicas);
	// implode() junta todas as opções em uma string
	return implode('', $opcoes);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Music - Playlists</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="../css/array.css">
    <link rel="stylesheet" href="../css/header.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <main class="main-content">
        <div class="wrap">
            <div class="left">
                <h2 class="small">Músicas Curtidas</h2>
                <div class="playlist-box no-interact" id="musicasCurtidas">
                    <?php 
                    // Usar array_map para exibir músicas curtidas
                    $musicasCurtidas = $_SESSION['playlists'][0]['musicas'] ?? [];
                    if (!empty($musicasCurtidas)) {
                        $elementosMusicas = array_map(function($musica) {
                            return '<div class="song">
                                        <div class="name">' . htmlspecialchars($musica, ENT_QUOTES, 'UTF-8') . '</div>
                                        <div style="font-size:12px;color:var(--muted)">origem</div>
                                    </div>';
                        }, $musicasCurtidas);
                        echo implode('', $elementosMusicas);
                    } else {
                        echo '<div style="opacity:0.7">Nenhuma música curtida ainda.</div>';
                    }
                    ?>
                </div>

                <h2 class="small">Playlists</h2>
                <div id="menuPlaylists">
                    <?php 
                    // Usar for loop simples ao invés de foreach
                    $totalPlaylists = count($_SESSION['playlists']);
                    for($i = 1; $i < $totalPlaylists; $i++) {
                        $playlist = $_SESSION['playlists'][$i];
                        $totalMusicas = count($playlist['musicas'] ?? []);
                        echo '<div class="playlist-box clickeavel" data-index="' . $i . '" onclick="mostrarMusicas(' . $i . ')">' . 
                             htmlspecialchars($playlist['nome'], ENT_QUOTES, 'UTF-8') . 
                             ' (' . $totalMusicas . ' músicas)</div>';
                    }
                    ?>
                </div>

                <form id="formularioDeletar" method="POST" style="display:none;">
                    <input type="hidden" name="indice_deletar" id="indice_deletar"/>
                    <input type="hidden" name="acao" value="deletar_playlist"/>
                    <button class="small-btn delete-btn" type="submit">Excluir Playlist Selecionada</button>
                </form>
            </div>

            <!-- Nova área para mostrar músicas da playlist selecionada -->
            <div class="center" id="areaMusicas" style="display:none;">
                <h2 class="small" id="tituloPlaylistSelecionada">Músicas da Playlist</h2>
                <div class="playlist-box" id="musicasPlaylistSelecionada">
                    <!-- As músicas serão inseridas aqui via JavaScript -->
                </div>
                <button class="small-btn" onclick="fecharMusicas()" style="margin-top:10px;">Voltar</button>
            </div>

            <div class="right">
                <h2 class="small">Criar nova playlist</h2>
                <?php if(!empty($erros)): ?>
                    <div class="msg error"><?= htmlspecialchars(implode(' | ', $erros), ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
                <?php if($sucesso !== ''): ?>
                    <div class="msg success"><?= htmlspecialchars($sucesso, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
                
                <form id="formularioCriar" class="row" method="POST">
                    <input id="nomePlaylist" name="nome_playlist" placeholder="Nome da nova playlist..." autocomplete="off"/>
                    <input type="hidden" name="acao" value="criar_playlist"/>
                    <button class="primary" type="submit">Criar</button>
                </form>

                <hr style="border:none;height:1px;background:rgba(255,255,255,0.03);margin:12px 0 18px 0">

                <h2 class="small">Playlists criadas</h2>
                <form method="POST" style="margin-bottom:12px;">
                    <input type="hidden" name="acao" value="limpar_playlists"/>
                    <button class="small-btn" type="submit" style="width:100%">Limpar todas as playlists criadas</button>
                </form>

                <div class="playlists-grid">
                    <?php 
                    // Usar for loop simples para exibir todas as playlists
                    $totalPlaylists = count($_SESSION['playlists']);
                    for($i = 0; $i < $totalPlaylists; $i++) {
                        $playlist = $_SESSION['playlists'][$i];
                        echo '<div class="pl-card">
                                <div style="font-weight:700;">' . htmlspecialchars($playlist['nome'], ENT_QUOTES, 'UTF-8') . '</div>
                                <div class="songs-list">';
                        
                        // Usar função para exibir músicas
                        exibirMusicas($playlist['musicas'] ?? []);
                        
                        echo '</div>';

                        if($i !== 0) {
                            echo '<form method="POST" class="add-row">
                                    <input type="hidden" name="indice_destino" value="' . $i . '"/>
                                    <select name="musica" required>
                                        <option value="">Selecionar música...</option>
                                        ' . criarOpcoesMusicas($_SESSION['playlists'][0]['musicas'] ?? []) . '
                                    </select>
                                    <div style="display:flex;gap:6px;margin-top:6px;">
                                        <button class="small-btn" name="acao" value="adicionar_musica" type="submit">Adicionar</button>
                                        <button class="small-btn" name="acao" value="remover_musica" type="submit">Remover</button>
                                    </div>
                                  </form>';
                        } else {
                            echo '<div style="margin-top:10px;font-size:13px;color:var(--muted)">Playlist de origem — não pode adicionar aqui.</div>';
                        }

                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </main>

<script>
// Dados das playlists em JavaScript (copiados do PHP)
const dadosPlaylists = <?php echo json_encode($_SESSION['playlists']); ?>;

// Função para mostrar músicas de uma playlist (estilo iniciante)
function mostrarMusicas(indicePlaylist) {
	// Pegar os dados da playlist
	const playlist = dadosPlaylists[indicePlaylist];
	const nomePlaylist = playlist.nome;
	const musicasPlaylist = playlist.musicas;
	
	// Atualizar o título
	document.getElementById('tituloPlaylistSelecionada').textContent = 'Músicas de: ' + nomePlaylist;
	
	// Preparar HTML das músicas
	let htmlMusicas = '';
	
	// Verificar se tem músicas (estilo iniciante)
	if (musicasPlaylist.length === 0) {
		htmlMusicas = '<div style="opacity:0.7">Esta playlist está vazia.</div>';
	} else {
		// Criar HTML para cada música (usando for simples)
		for (let i = 0; i < musicasPlaylist.length; i++) {
			const musica = musicasPlaylist[i];
			htmlMusicas += '<div class="song">';
			htmlMusicas += '<div class="name">' + musica + '</div>';
			htmlMusicas += '<div style="font-size:12px;color:var(--muted)">música ' + (i + 1) + '</div>';
			htmlMusicas += '</div>';
		}
	}
	
	// Colocar as músicas na tela
	document.getElementById('musicasPlaylistSelecionada').innerHTML = htmlMusicas;
	
	// Mostrar a área de músicas
	document.getElementById('areaMusicas').style.display = 'block';
	
	// Esconder a área principal
	document.getElementById('right').style.display = 'none';
}

// Função para fechar a visualização de músicas (estilo iniciante)
function fecharMusicas() {
	// Esconder área de músicas
	document.getElementById('areaMusicas').style.display = 'none';
	
	// Mostrar área principal novamente
	document.getElementById('right').style.display = 'block';
}

// Validação do formulário de criar playlist
document.getElementById('formularioCriar').addEventListener('submit',function(e){
	var nome=document.getElementById('nomePlaylist').value.trim();
	if(nome.length===0){ e.preventDefault(); alert('Digite um nome para a playlist antes de criar.'); return false;}
	if(nome.length>60){ e.preventDefault(); alert('O nome é muito longo (máx 60 caracteres).'); return false;}
});

// Seleção de playlist para exclusão (toggle) - mantido para funcionalidade existente
const caixasPlaylists = document.querySelectorAll('#menuPlaylists .playlist-box');
const formularioDeletar = document.getElementById('formularioDeletar');
const campoIndiceDeletar = document.getElementById('indice_deletar');

caixasPlaylists.forEach(caixa=>{
	// Adicionar evento de clique duplo para exclusão (para não conflitar com clique simples)
	caixa.addEventListener('dblclick',()=>{
		if(caixa.classList.contains('selected')){
			caixa.classList.remove('selected');
			formularioDeletar.style.display='none';
			campoIndiceDeletar.value='';
		} else {
			caixasPlaylists.forEach(c=>c.classList.remove('selected'));
			caixa.classList.add('selected');
			campoIndiceDeletar.value = caixa.getAttribute('data-index');
			formularioDeletar.style.display='block';
		}
	});
});
</script>
</body>
</html>