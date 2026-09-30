---
name: prepare-release
description: Prepara release do lknwp-radio-browser: atualiza versão em README.txt, README.md, CHANGELOG.md, cabeçalho PHP, constante LKNWP_RADIO_BROWSER_VERSION, fallback e workflows .yml baseado no git log
---

# prepare-release (lknwp-radio-browser)

Atualiza **todos** os arquivos que contêm o número de versão para uma nova release do plugin "Radio Browser Stations".

## Parâmetros (via `arguments`)

O usuário pode passar os valores diretamente: `"version=1.10.0 tested_up=7.1 php=8.2 highlights=..."`. Se algum valor faltar, pergunte.

- **version** — nova versão (Stable tag)
- **tested_up** — versão do WP testada (Tested up to)
- **php** — versão mínima do PHP (Requires PHP; default `8.2`)
- **highlights** — resumo da versão (opcional; se vazio, use o git log como dica)

## Fluxo de execução

### 1. Coletar valores

Se não recebidos via arguments, pergunte um por um. Detecte a versão atual:

```
grep -E "Stable tag:" README.txt
grep -E "define\( 'LKNWP_RADIO_BROWSER_VERSION'" lknwp-radio-browser.php
```

Data de hoje: use `date +%Y-%m-%d` (ou a data local do fuso `America/Sao_Paulo`).

### 2. Capturar git log (apenas como dica)

```bash
LAST_TAG=$(git describe --tags --abbrev=0 2>/dev/null)
if [ -z "$LAST_TAG" ]; then git log -n 10 --oneline; else git log ${LAST_TAG}..HEAD --oneline; fi
```

### 3. Atualizar TODOS os arquivos com versão

A versão aparece em **7 locais** espalhados por **7 arquivos**. Atualize todos.

#### 3a. `README.txt`

- `Stable tag:` → nova versão
- `Tested up to:` e `Requires PHP:` se alterados (o `Requires at least` costuma ficar fixo em `5.0`)
- Adicionar entrada no topo de `== Changelog ==`, **em inglês**, no formato atual do arquivo:
  ```
  = VERSION = *YYYY/MM/DD*
  * Item baseado nos commits
  ```
- ⚠️ O `README.txt` tem **DUAS** seções em que a versão entra: `== Changelog ==` **e** `== Upgrade Notice ==`. Atualize as duas (a `Upgrade Notice` só recebe a versão nova + 1 linha).

#### 3b. `README.md`

- `**Stable version:** X` (cabeçalho do readme)
- `**Tested up to:** X` se alterado

#### 3c. `CHANGELOG.md`

- Adicionar entrada no topo do arquivo, **em português**, no formato atual (`# VERSION - dd/mm/aaaa` + bullets `* ` + linha em branco):
  ```
  # VERSION - dd/mm/aaaa
  * Item baseado nos commits

  # VERSAO_ANTERIOR - dd/mm/aaaa
  ```

#### 3d. `lknwp-radio-browser.php` (raiz)

- `* Version:           X` (cabeçalho do plugin)
- `define( 'LKNWP_RADIO_BROWSER_VERSION', 'X' );` (constante — fonte autoritativa)
- `Requires PHP:` no cabeçalho, se houver; caso contrário mantê-lo apenas no `README.txt`

#### 3e. `Includes/Lknwp_Radio_Browser.php`

- `$this->version = 'X';` — **fallback** (usado só se a constante não existir). Hoje ele **espelha** a versão do plugin; mantenha em sincronia.

#### 3f. `.github/workflows/main.yml`

- `PLUGIN_VERSION: 'X'` (o `custom_tag` deriva dele — não editar)

#### 3g. `.github/workflows/wordpressRelease.yml`

- `DEPLOY_TAG: "X"` (o `VERSION: ${{ env.DEPLOY_TAG }}` deriva — não editar)

## NÃO alterar (pegadinhas)

- `package.json` → `"version": "1.0.0"` é boilerplate do dev container, **não** é a versão do plugin.
- `Languages/*.pot` / `*.po` → o `Project-Id-Version` é só o slug (`lknwp-radio-browser`), **não** contém número de versão.
- `CHANGELOG.md` / `README.txt` mantêm as versões antigas **nas entradas antigas** do changelog — isso é correto.
- Os diretórios têm **nomes com maiúsculas**: o código de idioma fica em `Languages/` (não `languages/`). Não renomear.

## 4. Validação final

Grep com a versão **antiga** (o `grep` ignora diretórios ocultos — rode também dentro de `.github/`):

```
grep -rn "VERSAO_ANTIGA" --include="*.php" --include="*.md" --include="*.txt" --include="*.yml" .
```

Esperado: `CHANGELOG.md` e `README.txt` ainda contêm a versão antiga **apenas** nas entradas antigas do changelog. Qualquer outro arquivo retornando a versão antiga é **erro**.

Grep com a versão **nova**:

```
grep -rn "NOVA_VERSAO" --include="*.php" --include="*.md" --include="*.txt" --include="*.yml" .
```

Deve retornar: `README.txt` (3x: stable tag + changelog + upgrade notice), `README.md` (1x), `CHANGELOG.md` (1x), `.php` raiz (2x: cabeçalho + constante), `Includes/Lknwp_Radio_Browser.php` (1x: fallback), 2 workflows (2x) = **~10 matches** (o `grep` não enxerga `.github/`, confira esses 2 à parte).
