# 1.1.3 - 23/10/2026
* Botão play/pause na capa do card "Tocando agora", sincronizado com o player do rodapé.
* CSS da lista e do player blindado contra estilos de temas (`!important` em todas as regras, resets de elementos, sliders e Select2).
* Corrige a seta do Select2 que subia para fora do campo quando o WooCommerce está ativo.
* Corrige a barra de ferramentas no mobile: o botão de tema vazava para fora do componente em telas estreitas.
* Nova seção "Aplicativo" na barra lateral com o botão oficial do Google Play (app Android).
* Corrige a área clicável do botão da loja, que era maior que a imagem do badge.

# 1.1.2 - 07/10/2026
* Corrige a linha em branco antes do `<?php` no arquivo principal que podia corromper a saída do sitemap do WordPress.

# 1.1.1 - 05/10/2026
* Filtro de país agora usa como padrão o país do locale do WordPress (ex.: pt_BR → BR), e o visitante pode trocá-lo com a escolha lembrada no navegador.
* Proxy same-origin do áudio (AJAX) para o visualizador funcionar mesmo quando a rádio não envia cabeçalhos CORS.
* Scripts e estilos do plugin só são carregados nas páginas que usam de fato os shortcodes do plugin.

# 1.1.0 - 30/09/2026
* Redesign completo do player e da lista de rádios: tema violeta, degradês, glassmorphism e efeitos de glow.
* Sidebar premium na lista: logo "Radio", menu Descobrir/Favoritos/Recentes e seção "Navegar por" (Gêneros/Países/Idiomas) com ícones.
* Views funcionais de Favoritos e Recentes (salvas no navegador) e bloco "Continue ouvindo".
* Filtros por país, gênero e idioma, pills de categoria (rock, MPB, eletrônica, sertanejo, pop, jazz, notícias) e ordenação (populares, nome, bitrate, aleatório).
* Player com metadados ao vivo: capa do álbum, música/artista e audiência (ouvintes) obtidos no servidor via proxy AJAX (sem erros de CORS).
* Visualizador de áudio (waveform) animado e alternância de tema claro/escuro.
* Cartões de estação com logo, gênero, bandeira do país e chips de bitrate/codec/votos.
* Hardening de UI contra o CSS do tema (botão de play redondo e ícones/campos com estilo próprio).
* Segurança: dados da API escapados na montagem dos cartões; proxy de metadados com proteção SSRF e limite de taxa por IP.
* Página de ajuda no admin reconstruída em seções navegáveis, com busca, FAQ e botões de copiar.
* Aviso (apenas para quem pode editar) quando o atributo `layout` é inválido; visitantes veem o layout legacy.

# 1.0.1 - 05/03/2026
* Novos ícones e banners para o plugin.

# 1.0.0 - 08/10/2025
* Lançamento do plugin.