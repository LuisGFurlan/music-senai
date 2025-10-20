<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music - Senai</title>
</head>
<body style="
    margin:0; 
    background:#000; 
    color:#fff; 
    font-family:Arial;">
    <header style="
        display:flex; 
        justify-content:space-between; 
        align-items:center; 
        padding:10px; 
        border-bottom:3px solid #5a0ea1; 
        background:#000;">
        <div style="display:flex; align-items:center; gap:10px;">
            <div style="
                width:80px; 
                height:80px; 
                border:3px solid #5a0ea1; 
                border-radius:50%; 
                display:flex; 
                justify-content:center; 
                align-items:center; 
                font-size:48px;
            ">MS</div>
        </div>
        <nav style="display:flex;  gap:20px;">
            <div style="
                padding:10px 20px; 
                border:3px solid #5a0ea1; 
                border-radius:15px; 
                display:flex; 
                align-items:center; 
                gap:5px;
            ">Início</div>
            <div style="
                padding:10px 20px; 
                border:3px solid #5a0ea1; 
                border-radius:15px; 
                display:flex; 
                align-items:center; 
                gap:5px;
            ">Desafio</div>
            <div style="
                padding:10px 20px; 
                border:3px solid #5a0ea1; 
                border-radius:15px; 
                display:flex; 
                align-items:center; 
                gap:5px;
            ">Criar</div>
        </nav>
    </header>

    <main style="text-align:center; padding:20px;">
        <h2>Olá {nome_usuario}! Seja bem vindo! 😁</h2>
        <h3>Continuar ouvindo?</h3>
        <div style="
            display:flex; 
            justify-content:center; 
            align-items:center; 
            gap:50px;">
            <div style="border:3px solid #5a0ea1; border-radius:15px; padding:10px;">
                <img src="imagem.jpg" style="width:300px; height:300px; border-radius:5px;">
                <div>
                    <input type="range" style="width:100%;">
                    <div style="
                        display:flex; 
                        justify-content:space-around; 
                        padding:10px; 
                        font-size:24px;
                    "></div>
                </div>
            </div>
            <div style="width:600px; height:300px;"><!-- placeholder for waves --></div>
        </div>
    </main>

    <footer style="border-top:3px solid #5a0ea1; text-align:center; padding:10px;">Footer</footer>
</body>
</html>
