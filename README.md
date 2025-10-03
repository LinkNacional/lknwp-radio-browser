# 📻 Radio Browser para WP

[![WordPress Plugin](https://img.shields.io/wordpress/plugin/v/lknwp-radio-browser.svg?style=flat-square)](https://wordpress.org/plugins/lknwp-radio-browser/)
[![Downloads](https://img.shields.io/wordpress/plugin/dt/lknwp-radio-browser.svg?style=flat-square)](https://wordpress.org/plugins/lknwp-radio-browser/)
[![Rating](https://img.shields.io/wordpress/plugin/r/lknwp-radio-browser.svg?style=flat-square)](https://wordpress.org/plugins/lknwp-radio-browser/)
[![License](https://img.shields.io/badge/license-GPLv2-blue.svg?style=flat-square)](https://opensource.org/licenses/GPL-2.0)

**Contribuidores:** [linknacional](https://github.com/LinkNacional)  
**Site:** [linknacional.com.br](https://www.linknacional.com.br/)  
**Tags:** radio, streaming, audio, player, music  
**Testado até:** 6.8  
**Versão estável:** 1.0.0  
**Licença:** GPLv2 ou posterior  
**Traduções:** Português (Brasil) / Inglês

Integre milhares de estações de rádio online no seu site WordPress com player responsivo e listas personalizáveis.

---

## ✨ Funcionalidades

- 🌍 **30.000+ Estações:** Acesso ao banco de dados da Radio-Browser.info com estações do mundo todo
- 🎵 **Player HTML5:** Player de áudio moderno e responsivo com controles de volume
- 📱 **Design Responsivo:** Funciona perfeitamente em desktop, tablet e mobile
- 🔍 **Busca Inteligente:** Encontre estações por nome, país ou gênero
- 🌐 **Filtros por País:** Filtre estações por qualquer país do mundo
- 📊 **Múltiplas Ordenações:** Ordene por popularidade, nome, bitrate ou aleatório
- 🔗 **URLs Amigáveis:** URLs SEO-friendly para estações individuais
- 🎯 **Shortcodes Simples:** Integração fácil com shortcodes em qualquer página
- 🛡️ **Proxy de Streaming:** Proxy integrado para streaming suave com suporte CORS
- 📈 **Estatísticas:** Integração com estatísticas de cliques da Radio-Browser.info
- 🎨 **Personalizável:** Classes CSS para customização completa do visual
- ⚡ **Performance:** Carregamento rápido e otimizado

---

## 📝 Descrição

O **Radio Browser para WP** é um plugin [WordPress](https://www.linknacional.com.br/wordpress/) completo para integrar milhares de estações de rádio online ao seu site. Conecta-se diretamente ao banco de dados da [Radio-Browser.info](https://www.radio-browser.info/), oferecendo acesso a mais de 30.000 estações de rádio do mundo inteiro.

Perfeito para blogs de música, sites de rádio, portais de entretenimento, ou qualquer site que deseja oferecer conteúdo de áudio streaming aos visitantes. Com design responsivo e player HTML5 moderno, seus usuários terão uma experiência excepcional em qualquer dispositivo.

---

## ⚙️ Como Usar

### 📋 Lista de Rádios
```
[radio_browser_list]
```

**Parâmetros Disponíveis:**
- `player_page` - Página onde está o player (padrão: "player")
- `countrycode` - Filtrar por país (padrão: "BR")
- `limit` - Número de estações (padrão: 20)
- `sort` - Ordenação: "clickcount", "name", "random", "bitrate"
- `search` - Termo de busca pré-definido
- `hide_country` - Ocultar filtro de país (yes/no)
- `hide_search` - Ocultar campo de busca (yes/no)
- `hide_all_filters` - Ocultar todos os filtros (yes/no)

**Exemplo:**
```
[radio_browser_list player_page="radio-player" countrycode="US" limit="50"]
```

### 🎵 Player de Rádio
```
[radio_browser_player]
```

### 🚀 Configuração Rápida
1. Crie uma **Página de Lista:** Adicione `[radio_browser_list]`
2. Crie uma **Página do Player:** Adicione `[radio_browser_player]`
3. Configure o parâmetro `player_page` na lista para apontar para sua página do player
4. Publique as páginas e comece a transmitir!

---

## 📦 Instalação

1. Baixe o plugin ou instale diretamente do repositório WordPress.
2. No painel administrativo do WordPress, vá em **Plugins > Adicionar Novo**.
3. Clique em **Enviar Plugin** e selecione o arquivo ZIP (se baixou).
4. Clique em **Instalar Agora** e depois em **Ativar Plugin**.
5. Comece a usar os shortcodes imediatamente - sem configuração adicional!

---

## 🎯 Casos de Uso

- 📻 **Sites de Rádio:** Crie um portal completo de rádios online
- 🎵 **Blogs de Música:** Adicione estações relacionadas ao conteúdo
- 🌍 **Portais de Entretenimento:** Ofereça variedade de conteúdo de áudio
- 🏢 **Sites Corporativos:** Ambiente de trabalho com música de fundo
- 📰 **Portais de Notícias:** Adicione rádios jornalísticas
- 🎓 **Sites Educacionais:** Estações educativas e culturais

---

## 🔧 Requisitos Mínimos

- WordPress 5.0 ou superior
- PHP 7.4 ou superior
- Conexão com internet para streaming
- Navegador moderno com suporte HTML5 audio

---

## 📖 Documentação & Suporte

- [Documentação do Plugin](https://wordpress.org/plugins/lknwp-radio-browser/)
- [Link Nacional Especialista em WordPress](https://www.linknacional.com.br/wordpress/)
- [Suporte](https://www.linknacional.com.br/wordpress/suporte/)
- [Radio-Browser.info API](https://www.radio-browser.info/)

---

## 📢 Transforme seu site em uma estação de rádio global com mais de 30.000 estações ao vivo!

---