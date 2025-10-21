<?php
session_start();

/*
	- Single-file app: HTML + CSS + JS + PHP
	- Funcionalidades:
	1) Playlist "Músicas Curtidas" (fixa)
	2) Criar novas playlists
	3) Adicionar músicas das Curtidas
	4) Remover músicas de qualquer playlist
	5) Limpar todas as playlists criadas
	6) Remover playlists selecionadas
	7) Mensagens desaparecem ao recarregar
*/

// Inicialização
if (!isset($_SESSION['playlists_initialized'])) {
	$_SESSION['playlists'] = [
		['name'=>'Músicas Curtidas','songs'=>['Neon Sky','Digital Horizon','Echoes of Tomorrow','Midnight Pulse','Industrial Dreams']],
		['name'=>'Playlist 1','songs'=>[]],
		['name'=>'Playlist 2','songs'=>[]]
	];
	$_SESSION['playlists_initialized'] = true;
}

// Mensagens flash
if (!isset($_SESSION['flash_messages'])) {
	$_SESSION['flash_messages'] = ['errors'=>[], 'success'=>''];
}

// Sanitização
function clean($s){ return trim(htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8')); }

// --- POST Actions ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	$action = $_POST['action'] ?? '';

	if ($action === 'create_playlist') {
		$name = clean($_POST['playlist_name'] ?? '');

		if ($name === '') {
			$_SESSION['flash_messages']['errors'][] = 'O nome da playlist não pode ser vazio.';
		} else {
			$exists = false;

			foreach ($_SESSION['playlists'] as $pl) {
				if (mb_strtolower($pl['name']) === mb_strtolower($name)) {
					$exists = true;
				}
			}

			if ($exists) {
				$_SESSION['flash_messages']['errors'][] = 'Já existe uma playlist com esse nome.';
			} else {
				$_SESSION['playlists'][] = [
					'name'  => $name,
					'songs' => []
				];
				$_SESSION['flash_messages']['success'] = "Playlist '$name' criada com sucesso.";
			}
		}
	}

	if ($action === 'add_song') {
		$target_index = (int)($_POST['target_index'] ?? -1);
		$song = clean($_POST['song'] ?? '');

		if (!isset($_SESSION['playlists'][$target_index])) {
			$_SESSION['flash_messages']['errors'][] = 'Playlist inválida.';
		} elseif ($target_index === 0) {
			$_SESSION['flash_messages']['errors'][] = 'Não é permitido adicionar músicas em Curtidas.';
		} elseif ($song === '') {
			$_SESSION['flash_messages']['errors'][] = 'Nenhuma música selecionada.';
		} else {
			$dest = &$_SESSION['playlists'][$target_index]['songs'];

			foreach ($dest as $s) {
				if (mb_strtolower($s) === mb_strtolower($song)) {
					$_SESSION['flash_messages']['errors'][] = "A música '$song' já está nessa playlist";
					break;
				}
			}

			if (empty($_SESSION['flash_messages']['errors'])) {
				$dest[] = $song;
				$_SESSION['flash_messages']['success'] = "Música '$song' adicionada à playlist '{$_SESSION['playlists'][$target_index]['name']}'.";
			}
		}
	}

	if ($action === 'remove_song') {
		$target_index = (int)($_POST['target_index'] ?? -1);
		$song = clean($_POST['song'] ?? '');

		if (!isset($_SESSION['playlists'][$target_index])) {
			$_SESSION['flash_messages']['errors'][] = 'Playlist inválida.';
		} elseif ($target_index === 0) {
			$_SESSION['flash_messages']['errors'][] = 'Não é permitido remover músicas de Curtidas';
		} else {
			$dest = &$_SESSION['playlists'][$target_index]['songs'];
			$key = array_search($song, $dest);

			if ($key !== false) {
				unset($dest[$key]);
				$dest = array_values($dest);
				$_SESSION['flash_messages']['success'] = "Música '$song' removida de '{$_SESSION['playlists'][$target_index]['name']}'.";
			} else {
				$_SESSION['flash_messages']['errors'][] = 'Música não encontrada na playlist.';
			}
		}
	}

	if ($action === 'clear_playlists') {
		for ($i = 1; $i < count($_SESSION['playlists']); $i++) {
			$_SESSION['playlists'][$i]['songs'] = [];
		}
		$_SESSION['flash_messages']['success'] = 'Todas as playlists criadas foram limpas.';
	}

	if ($action === 'delete_playlist') {
		$delete_index = (int)($_POST['delete_index'] ?? -1);

		if ($delete_index <= 0 || !isset($_SESSION['playlists'][$delete_index])) {
			$_SESSION['flash_messages']['errors'][] = 'Playlist inválida ou não pode ser removida.';
		} else {
			$deleted_name = $_SESSION['playlists'][$delete_index]['name'];
			array_splice($_SESSION['playlists'], $delete_index, 1);
			$_SESSION['flash_messages']['success'] = "Playlist '$deleted_name' removida com sucesso.";
		}
	}

	header('Location: ' . $_SERVER['PHP_SELF']);
	exit();
}

$errors = $_SESSION['flash_messages']['errors'];
$success = $_SESSION['flash_messages']['success'];
$_SESSION['flash_messages'] = ['errors'=>[], 'success'=>''];
?>
<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>Músicas - Playlists</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
:root{--bg:#0b0b0b;--card:#0f1720;--muted:#bfc7d1;--accent:#9728feff;--rounded:12px;--gap:18px;--maxw:1100px;}
*{box-sizing:border-box;}

/* Fundo global e layout base */
body{
	margin:0;
	font-family:"Inter",system-ui,Arial,sans-serif;
	color:#e6eef6;
	display:flex;               /* Centralização */
	align-items:flex-start;
	justify-content:center;
	padding:40px 16px;
	min-height:100vh;
	background: linear-gradient(135deg, #0b0b0b 0%, #1a0b28 50%, #0b0b0b 100%); /* Fundo animado */
	background-size:200% 200%;
	animation:bgGradient 60s ease infinite;
}

/* Animação do background */
@keyframes bgGradient{
	0%{background-position:0% 50%;}
	50%{background-position:100% 50%;}
	100%{background-position:0% 50%;}
}

/* Grid principal: sidebar + conteúdo */
.wrap{
	width:100%;
	max-width:var(--maxw);
	display:grid;
	grid-template-columns:360px 1fr;
	gap:calc(var(--gap)*2);
	align-items:start;
}

/* Container lateral (playlists) */
.left{
	background:var(--card);
	padding:18px;
	border-radius:var(--rounded);
	box-shadow:0 8px 30px rgba(0,0,0,0.6);
	min-height:480px;
}

/* Título de seção */
h2.small{
	margin:0 0 12px 0;
	font-size:18px;
	color:var(--accent);
}

/* Caixa de playlist */
.playlist-box{
	background:rgba(255,255,255,0.02);
	padding:12px;
	border-radius:10px;
	margin-bottom:12px;
	cursor:pointer;
}

/* Playlist ativa */
.playlist-box.selected{
	border:1px solid var(--accent);
}

/* Playlist bloqueada */
.playlist-box.no-interact{
	cursor:default;
}

/* Música individual */
.song{
	display:flex;
	justify-content:space-between;
	align-items:center;
	padding:6px 8px;
	border-radius:8px;
	margin-bottom:6px;
}

/* Nome da música */
.song .name{
	color:var(--muted);
	font-size:14px;
}

/* Botões menores */
.small-btn{
	background:transparent;
	border:1px solid rgba(255,255,255,0.08);
	color:var(--muted);
	padding:6px 10px;
	border-radius:8px;
	cursor:pointer;
	font-size:13px;
}

/* Conteúdo principal */
.right{
	background:var(--card);
	padding:20px;
	border-radius:var(--rounded);
	box-shadow:0 8px 30px rgba(0,0,0,0.6);
	min-height:480px;
}

/* Linha de formulário */
form.row{
	display:flex;
	gap:12px;
	align-items:center;
	margin-bottom:18px;
}

/* Input de texto */
input[type="text"]{
	flex:1;
	padding:10px 12px;
	border-radius:10px;
	border:1px solid rgba(255,255,255,0.06);
	background:rgba(0,0,0,0.25);
	color:#fff;
	font-size:15px;
}

/* Botão principal com animação */
button.primary{
	padding:10px 14px;
	border-radius:10px;
	border:none;
	cursor:pointer;
	font-weight:700;
	color:#fff;
	background:linear-gradient(90deg,#5e00ff,#9728fe,#7f00ff,#9728fe);
	background-size:300% 100%;
	animation:gradientMove 3s ease infinite;
	box-shadow:0 4px 15px rgba(151,40,254,0.4);
	transition:transform 0.3s ease, box-shadow 0.3s ease;
}

button.primary:hover{
	transform:scale(1.08);
	box-shadow:0 8px 25px rgba(151,40,254,0.7);
}

/* Movimento do gradiente do botão */
@keyframes gradientMove{
	0%{background-position:0% 50%;}
	50%{background-position:100% 50%;}
	100%{background-position:0% 50%;}
}

/* Mensagens de status */
.msg{
	padding:10px 12px;
	border-radius:8px;
	margin-bottom:12px;
}

/* Mensagem de erro */
.msg.error{
	background:rgba(255,0,0,0.06);
	color:#ffb4b4;
	border:1px solid rgba(255,0,0,0.08);
}

/* Mensagem de sucesso */
.msg.success{
	background:rgba(16,185,129,0.08);
	color:#8ef0c9;
	border:1px solid rgba(16,185,129,0.08);
}

/* Grid de playlists */
.playlists-grid{
	display:grid;
	grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
	gap:12px;
}

/* Card individual */
.pl-card{
	background:rgba(255,255,255,0.02);
	padding:12px;
	border-radius:10px;
	min-height:80px;
}

/* Lista de músicas dentro do card */
.songs-list{
	margin-top:8px;
	font-size:14px;
	color:var(--muted);
}

/* Região de adicionar música */
.add-row{
	margin-top:10px;
	display:flex;
	flex-direction:column;
	gap:6px;
}

.add-row select{
	width:100%;
}

.add-row button{
	width:48%;
	margin-right:4%;
	margin-top:4px;
}

.add-row button:last-child{
	margin-right:0;
}

/* Botão de delete */
.delete-btn{
	margin-top:10px;
	width:100%;
}

/* Responsividade */
@media(max-width:900px){
	.wrap{
		grid-template-columns:1fr;
	}
}
</style>
</head>
<body>
<div class="wrap">
	<div class="left">
		<h2 class="small">Músicas Curtidas</h2>
		<div class="playlist-box no-interact">
			<?php foreach($_SESSION['playlists'][0]['songs'] as $song): ?>
				<div class="song">
					<div class="name"><?= htmlspecialchars($song,ENT_QUOTES,'UTF-8') ?></div>
					<div style="font-size:12px;color:var(--muted)">origem</div>
				</div>
			<?php endforeach; ?>
		</div>

		<h2 class="small">Playlists</h2>
		<div id="playlistMenu">
			<?php for($i=1;$i<count($_SESSION['playlists']);$i++):
				$pl=$_SESSION['playlists'][$i]; ?>
				<div class="playlist-box" data-index="<?= $i ?>"><?= htmlspecialchars($pl['name'],ENT_QUOTES,'UTF-8') ?> (<?= count($pl['songs']) ?> músicas)</div>
			<?php endfor; ?>
		</div>

		<form id="deleteForm" method="POST" style="display:none;">
			<input type="hidden" name="delete_index" id="delete_index"/>
			<input type="hidden" name="action" value="delete_playlist"/>
			<button class="small-btn delete-btn" type="submit">Excluir Playlist Selecionada</button>
		</form>
	</div>

	<div class="right">
		<h2 class="small">Criar nova playlist</h2>
		<?php if(!empty($errors)): ?><div class="msg error"><?= htmlspecialchars(implode(' | ',$errors),ENT_QUOTES,'UTF-8') ?></div><?php endif; ?>
		<?php if($success!==''): ?><div class="msg success"><?= htmlspecialchars($success,ENT_QUOTES,'UTF-8') ?></div><?php endif; ?>
		<form id="createForm" class="row" method="POST">
			<input id="playlistName" name="playlist_name" placeholder="Nome da nova playlist..." autocomplete="off"/>
			<input type="hidden" name="action" value="create_playlist"/>
			<button class="primary" type="submit">Criar</button>
		</form>

		<hr style="border:none;height:1px;background:rgba(255,255,255,0.03);margin:12px 0 18px 0">

		<h2 class="small">Playlists criadas</h2>
		<form method="POST" style="margin-bottom:12px;">
			<input type="hidden" name="action" value="clear_playlists"/>
			<button class="small-btn" type="submit" style="width:100%">Limpar todas as playlists criadas</button>
		</form>

		<div class="playlists-grid">
			<?php for($i=0;$i<count($_SESSION['playlists']);$i++):
				$pl=$_SESSION['playlists'][$i]; ?>
				<div class="pl-card">
					<div style="font-weight:700;"><?= htmlspecialchars($pl['name'],ENT_QUOTES,'UTF-8') ?></div>
					<div class="songs-list">
						<?php if(count($pl['songs'])===0): ?>
							<div style="opacity:0.7">Sem músicas ainda.</div>
						<?php else: ?>
							<?php foreach($pl['songs'] as $s): ?>
								<div style="margin-bottom:6px"><?= htmlspecialchars($s,ENT_QUOTES,'UTF-8') ?></div>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>

					<?php if($i!==0): ?>
						<form method="POST" class="add-row">
							<input type="hidden" name="target_index" value="<?= $i ?>"/>
							<select name="song" required>
								<option value="">Selecionar música...</option>
								<?php foreach($_SESSION['playlists'][0]['songs'] as $song): ?>
									<option value="<?= htmlspecialchars($song,ENT_QUOTES,'UTF-8') ?>"><?= htmlspecialchars($song,ENT_QUOTES,'UTF-8') ?></option>
								<?php endforeach; ?>
							</select>
							<div style="display:flex;gap:6px;margin-top:6px;">
								<button class="small-btn" name="action" value="add_song" type="submit">Adicionar</button>
								<button class="small-btn" name="action" value="remove_song" type="submit">Remover</button>
							</div>
						</form>
					<?php else: ?>
						<div style="margin-top:10px;font-size:13px;color:var(--muted)">Playlist de origem — não pode adicionar aqui.</div>
					<?php endif; ?>

				</div>
			<?php endfor; ?>
		</div>
	</div>
</div>

<script>
document.getElementById('createForm').addEventListener('submit',function(e){
	var name=document.getElementById('playlistName').value.trim();
	if(name.length===0){ e.preventDefault(); alert('Digite um nome para a playlist antes de criar.'); return false;}
	if(name.length>60){ e.preventDefault(); alert('O nome é muito longo (máx 60 caracteres).'); return false;}
});

// Seleção de playlist para exclusão (toggle)
const playlistBoxes = document.querySelectorAll('#playlistMenu .playlist-box');
const deleteForm = document.getElementById('deleteForm');
const deleteIndexInput = document.getElementById('delete_index');

playlistBoxes.forEach(box=>{
	box.addEventListener('click',()=>{
		if(box.classList.contains('selected')){
			box.classList.remove('selected');
			deleteForm.style.display='none';
			deleteIndexInput.value='';
		} else {
			playlistBoxes.forEach(b=>b.classList.remove('selected'));
			box.classList.add('selected');
			deleteIndexInput.value = box.getAttribute('data-index');
			deleteForm.style.display='block';
		}
	});
});
</script>
</body>
</html>
