# Overview do Fluxo do App de Playlists

Este documento apresenta uma visão geral do fluxo de dados e interações no aplicativo de playlists, que utiliza PHP, HTML, CSS e JavaScript, com armazenamento temporário em sessão.

```
[Usuário]
    │
    ▼
[Frontend: Formulário HTML]
    │
    ├─ Criar playlist
    ├─ Adicionar música
    ├─ Remover música
    ├─ Limpar playlists
    └─ Deletar playlist
    │
    ▼
[POST -> PHP Backend]
    │
    ├─ Sanitiza dados (clean)
    ├─ Valida entradas
    ├─ Atualiza $_SESSION['playlists']
    └─ Atualiza $_SESSION['flash_messages']
    │
    ▼
[Header Location: recarrega página]
    │
    ▼
[Frontend: PHP renderiza HTML]
    ├─ Lista playlists (sidebar e cards)
    ├─ Dropdown de músicas
    └─ Mensagens de sucesso/erro (flash)
    │
    ▼
[Usuário vê resultado]
```

## Observações

* `$_SESSION['playlists']` funciona como banco de dados temporário.
* `$_SESSION['flash_messages']` fornece feedback ao usuário (mensagens de sucesso ou erro).
* Playlist de origem ("Músicas Curtidas") é protegida e não permite alterações.
* As interações são controladas via formulários HTML e JS para seleção e validação.
* A página é recarregada após cada ação para atualizar o estado e limpar mensagens temporárias.
