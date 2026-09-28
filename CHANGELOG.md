# 1.9.1 - 25/09/2026
* Cabeçalho numa única linha: busca à esquerda (até 50% da largura) e bandeira do país + botão "Filtros" à direita, com espaço entre eles.
* O botão "Filtros" agora usa o mesmo estilo pill dos demais controles (blindado contra o CSS de botão do tema).

# 1.9.0 - 25/09/2026
* Cabeçalho da lista reorganizado: linha 1 com a busca (arredondada, largura total, com espaço para digitar — o ícone não sobrepõe mais o placeholder); linha 2 com a bandeira do país + botão "Filtros".
* Gênero, idioma e ordenação agora ficam dentro do painel "Filtros" (abre/fecha).
* Corrigido o padding do campo de busca que era sobrescrito pelo tema.

# 1.8.2 - 25/09/2026
* Sidebar: itens realmente sem fundo, com border-radius e texto alinhado à esquerda (blindado contra o CSS do tema, que aplica background/mín-height em todo <button>).

# 1.8.1 - 25/09/2026
* Sidebar: itens sem fundo, alinhados à esquerda, com degradê da esquerda para a direita no hover e no item selecionado.
* Lista de rádios agora fica dentro de um bloco com scroll próprio (não cresce mais a página).
* Campos de filtro (país/gênero/idioma/ordem) com largura fixa em pill (o select de gênero não estoura mais a largura).

# 1.8.0 - 25/09/2026
* Barra lateral premium na lista: logo "Radio", menu Descobrir/Favoritos/Recentes, seção "Navegar por" (Gêneros/Países/Línguas) com ícones e rodapé com onda animada + "Milhares de rádios, um só lugar.".
* Views funcionais: Favoritos e Recentes filtram as estações (salvas no navegador).
* Filtro de idioma (Línguas) usando o parâmetro language da API Radio-Browser.

# 1.7.0 - 25/09/2026
* Lista de rádios redesenhada: interface premium dark (navy/indigo/roxo), glassmorphism e micro-glow.
* Barra de busca em pill, seleção de país/gênero/ordem em pills e pills de categoria (rock, MPB, eletrônica, sertanejo, pop, jazz, notícias).
* Cards de estação com logo em destaque, gênero, país com bandeira, e chips de bitrate/codec/votos (recursos da API Radio-Browser).
* Favoritar estação (coração, salvo no navegador) e estado "● OUVINDO AGORA" com equalizador animado no card selecionado.
* Segurança: dados vindos da API são escapados ao montar os cards (evita XSS).

# 1.6.1 - 25/09/2026
* Corrigido: as ondas paravam de funcionar após muito tempo tocando (ex.: 30 min) ao pausar e retomar. Agora o AudioContext é retomado e o proxy do stream é reconstruído quando necessário.

# 1.6.0 - 25/09/2026
* Metadados da rádio passam a ser buscados no servidor (endpoint AJAX proxy), eliminando os erros de CORS no console.
* Abaixo das ondas agora exibe capa do álbum, nome da música/artista e o público atual (ouvintes), quando a rádio informa.
* Capa do álbum buscada via iTunes no servidor.
* Proteção contra SSRF: apenas hosts públicos são consultados.
* Limite de requisições por IP no endpoint (anti-abuso).
* Removidos os proxies públicos de CORS do JS (não são mais necessários).

# 1.5.8 - 25/09/2026
* Player: ondas um pouco menores (altura 160->145px), sem alterar a altura do card.

# 1.5.7 - 25/09/2026
* Player: botão de play um pouco menor (158px) e card um pouco mais largo (380px).

# 1.5.6 - 25/09/2026
* Player: card volta ao tamanho anterior; apenas o botão de play foi aumentado (180px) com a bola branca e o ícone proporcionais, e o bloco das waves cresceu para continuar aparecendo ao redor.

# 1.5.5 - 25/09/2026
* Player maior: card alargado (420px), botão de play, capa, ícone, waves e espaçamentos ampliados proporcionalmente.

# 1.5.4 - 25/09/2026
* Player: altura do card aumentada (~1.5x), mantendo a largura. As waves cresceram junto para preencher o bloco.

# 1.5.3 - 25/09/2026
* Player: espaçamento vertical voltou ao de antes e o card foi alargado (~340px). As waves acompanham a nova largura.

# 1.5.2 - 25/09/2026
* Visualizador: barras mais grossas (18 barras) e remoção dos caps de pico brancos.
* Player: mais espaçamento vertical (entre o título da rádio e as demais seções, e no rodapé) para o componente ficar mais alto e menos compacto.

# 1.5.1 - 25/09/2026
* Visualizador (waves) maior e mais parecido com uma onda: bloco mais alto, 38 barras mais finas/arredondadas e caps de pico mais visíveis.

# 1.5.0 - 25/09/2026
* Visualizador (waves) reformulado no estilo waveform suave: 30 barras arredondadas, animação com suavização (sobe rápido/desce devagar), caps de pico que caem com "gravidade" e reflexo espelhado com desvanecimento.
* Player mais compacto: botão de play, capa da rádio, espaçamentos e paddings reduzidos.

# 1.4.3 - 25/09/2026
* Player: botão de play volta ao visual de dois níveis (bola roxa grande + bola branca interna) com o ícone play/pause escuro centralizado.

# 1.4.2 - 25/09/2026
* Player: botão de play agora é um círculo perfeito (box-sizing/aspect-ratio).
* Player: ícone de play/pause redesenhado e centralizado (removido o círculo cinza de fundo herdado do tema antigo).

# 1.4.1 - 25/09/2026
* Player: corrigido o botão de play voltar a ser redondo (o tema aplicava border-radius: 0 em todo <button>).
* Player: corrigido o ícone do botão "Copiar link" que não aparecia (o tema forçava padding/min-height nos botões).
* Player: botão de play volta a ficar verde no hover (para tocar) e vermelho no hover quando está tocando.

# 1.4.0 - 25/09/2026
* Cantos menos arredondados em todo o plugin (player, lista e página de Ajuda), mantendo o estilo arredondado porém mais discreto.

# 1.3.3 - 25/09/2026
* Corrigido o último card da lista ficar maior que os demais: todas as linhas do grid agora têm a mesma altura (grid-auto-rows: 1fr).

# 1.3.2 - 25/09/2026
* Removida a barra de acento que aparecia no topo do card da rádio ao passar o mouse.
* Texto alinhado à esquerda no campo Limit e no botão Order.

# 1.3.1 - 25/09/2026
* Campos de filtro da lista agora crescem para ocupar toda a largura da linha (layout com flexbox), ficando alinhados com a barra de busca.

# 1.3.0 - 25/09/2026
* Formulário de filtros da lista refatorado: campos com altura, raio de borda, fonte e espaçamento uniformes (não são mais afetados pelo tema).
* Layout dos filtros agora é um grid responsivo que ocupa toda a largura.
* Campo de Gênero (Select2) corrigido: ocupa a largura total, placeholder correto e dropdown no tema.
* Labels sem "caps-lock" (caixa alta removida) e tamanhos padronizados.
* Cards das rádios melhorados: logo maior, linha de metadados (país · gênero · bitrate), barra de acento no topo e hover mais rico.

# 1.2.3 - 25/09/2026
* Sombra do hero da página de Ajuda suavizada (removido o glow que irradiava para todos os lados).

# 1.2.2 - 25/09/2026
* Hero da página de Ajuda agora usa o tom de roxo mais claro (mesmo degradê do item ativo do menu lateral e do botão de copiar).

# 1.2.1 - 25/09/2026
* Página de Ajuda: avisos do WordPress (ex.: TGMPA/tema) não são mais injetados dentro do hero (adicionado `wp-header-end`).
* Sombra do hero suavizada para não escurecer os títulos das seções.
* Campo de busca: ícone movido para a direita, sem sobrepor o placeholder; botão de limpar reposicionado.

# 1.2.0 - 25/09/2026
* Página de Ajuda reformulada em seções navegáveis com barra lateral (Getting Started, Player, List, Parameters, Hide Filters, Examples, FAQ).
* Adicionada busca/filtro de conteúdo na documentação.
* Nova seção de FAQ.
* Botão de copiar em cada parâmetro das tabelas.
* Navegação com abas (uma seção por vez) e abertura via hash da URL (#panel-...).

# 1.1.0 - 25/09/2026
* Redesign completo do player e da lista de rádios com tema violeta, degradês, glassmorphism e efeitos de glow.
* Visualizador de áudio atualizado para tons de violeta/magenta.
* Nova paleta e design system de cores (brand) adicionados ao colors.css.
* Refatoração visual da página de ajuda no admin (hero + cards modernos).
* Removidos logs de depuração (error_log) da listagem de rádios.

# 1.0.1 - 05/03/2026
* Novos ícones e banners para o plugin.

# 1.0.0 - 08/10/2025
* Lançamento do plugin.