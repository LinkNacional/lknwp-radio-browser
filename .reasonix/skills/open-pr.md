---
name: open-pr
description: Abre PR de dev → main no lknwp-radio-browser (título VERSION - repo (resumo); corpo com metadados e CHANGELOG)
---

# open-pr (lknwp-radio-browser)

Abre um Pull Request de `dev` → `main` via `gh pr create`, no padrão Link Nacional, para o repositório `lknwp-radio-browser`.

## Parâmetros (via `arguments`)

O usuário pode passar: `version=1.10.0 tested_up=6.8 summary=Novo layout da lista + FAQ`. Qualquer valor ausente é extraído do código.

- **version** — versão da release (Stable tag / cabeçalho PHP)
- **tested_up** — WP testado até
- **summary** — resumo CURTO usado no TÍTULO. Se ausente, derive da entrada mais recente do `CHANGELOG.md` (NÃO do `git log`).

## Fluxo de execução

### 1. Extrair metadados (se não vierem nos arguments)

```bash
# Cabeçalho PHP (fonte da verdade da versão)
grep -m1 -E "^\s*\*\s*Version:" lknwp-radio-browser.php
grep -m1 -E "^\s*\*\s*Requires PHP:" lknwp-radio-browser.php

# README.txt (Tested up to / Stable tag)
grep -m1 -i "^Tested up to:" README.txt
grep -m1 -i "^Stable tag:" README.txt

# Nome do repositório no GitHub (derivado do remote)
REPO_NAME=$(basename -s .git "$(git config --get remote.origin.url)")
# → lknwp-radio-browser
```

### 2. Ler o changelog da versão atual (fonte do resumo e dos bullets)

⚠️ **Regra anti-redundância.** NÃO use `git log` para gerar o resumo — o range de commits está dessincronizado e traz itens de versões já publicadas. Leia a entrada mais recente do changelog:

```bash
# Preferir CHANGELOG.md (português).
head -n 20 CHANGELOG.md
```

A entrada mais recente tem o formato `# VERSION - dd/mm/aaaa` seguido de bullets `* ...`.

- **TÍTULO**: resuma esses bullets em uma frase curta (≤ ~14 palavras).
- **CORPO (seção CHANGELOG)**: copie os bullets do `CHANGELOG.md` (português), sem hash e sem reescrever.

### 3. Montar TÍTULO

Formato exato (obrigatório) — **com um espaço antes do parêntese**:

```
VERSION - REPO_NAME (RESUMO_CURTO)
```

Exemplo:

```
1.10.0 - lknwp-radio-browser (Novo layout da lista, FAQ e melhorias no player)
```

### 4. Montar CORPO

Use exatamente este gabarito. Só variam `{VERSION}` e `{TESTED_UP}` no cabeçalho e os bullets do CHANGELOG; o restante é fixo:

```markdown
# Radio Browser Stations
Contribuidores: linknacional
Link: https://www.linknacional.com.br/wordpress/
Tags: radio, streaming, audio, player, music
Testado até: {TESTED_UP}
Versão estável: {VERSION}
Licença: GPLv2 ou posterior
URI da Licença: https://opensource.org/licenses/MIT
Traduções: Português(Brasil) / Inglês

Exiba e reproduza milhares de estações de rádio online do Radio-Browser.info com um player moderno e uma lista customizável.

## Descrição

O Radio Browser Stations integra milhares de **estações de rádio online** ao seu site WordPress, conectando-se à base do [Radio-Browser.info](https://www.radio-browser.info/) (30.000+ estações) com um player de áudio responsivo e listas de estações customizáveis.

Ideal para blogs de música, sites de rádio, portais de entretenimento ou qualquer site que queira oferecer conteúdo de áudio aos visitantes.

**Recursos principais**

- **Base global de rádios:** acesso a 30.000+ estações do Radio-Browser.info
- **Player de áudio moderno:** player HTML5 responsivo com controles de volume
- **Listas customizáveis:** exiba estações com filtros e ordenação
- **Busca inteligente:** encontre estações por nome, país ou gênero
- **URLs amigáveis para SEO:** URLs limpas para cada estação
- **Design responsivo:** funciona em desktop, tablet e celular
- **Integração fácil:** shortcodes simples para listas e player
- **Proxy de streaming:** proxy embutido para transmissão suave (com CORS)
- **Filtro por país:** filtre estações por país
- **Múltiplas ordenações:** por popularidade, nome, bitrate ou aleatório

**Dependências**

Este plugin depende do WordPress 5.0+ e PHP 7.4+. Não há dependência de outros plugins.

**Instruções de uso**

1. Ative o plugin — nenhuma configuração adicional é necessária.
2. Use o shortcode da **lista de estações** para exibir a lista com filtros.
3. Use o shortcode do **player** na página que tocará a estação selecionada.
4. Personalize os filtros e o layout pelos atributos dos shortcodes (veja a página de ajuda no admin).

Pronto! Seus visitantes já podem ouvir rádios online direto no site.

## Instalação

1. Baixe o plugin.
2. No painel administrativo do WordPress, vá para Plugins > Adicionar Novo.
3. Clique em "Enviar Plugin" e selecione o arquivo ZIP do plugin que você baixou.
4. Clique em "Instalar Agora" e, em seguida, em "Ativar Plugin".

## CHANGELOG:

{BULLETS copiados da entrada mais recente do CHANGELOG.md, no formato "* Item". NÃO invente a partir do git log.}
```

### 5. Abrir o PR

Sempre `dev` → `main`:

```bash
gh pr create \
  --base main \
  --head dev \
  --title "VERSION - REPO_NAME (RESUMO_CURTO)" \
  --body "$(cat <<'EOF'
...corpo...
EOF
)"
```

### 6. Confirmar

Mostre a URL retornada pelo `gh` e o comando usado. Se o PR já existir para `dev` → `main`, o `gh` vai avisar — não force `--force` sem pedir.

## Regras

- **Nunca** edite arquivos do repo para abrir o PR (é só `gh pr create`).
- Título SEMPRE no formato `VERSION - lknwp-radio-browser (resumo)`, **com um espaço antes do parêntese**.
- `REPO_NAME` é o nome do repositório no GitHub (`lknwp-radio-browser`).
- Corpo SEMPRE com o cabeçalho de metadados, Descrição, Instalação e a seção `## CHANGELOG:` com os bullets da versão.
- Se `version` / `tested_up` divergirem entre o cabeçalho PHP e o `README.txt`, use o **cabeçalho PHP** e avise.
- Nunca inclua hashes de commit no corpo.
- Bullets do corpo SEMPRE vindos da entrada mais recente do `CHANGELOG.md` — nunca do `git log`.
