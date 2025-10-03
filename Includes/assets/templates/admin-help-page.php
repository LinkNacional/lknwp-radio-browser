<?php
/**
 * Template for Admin Help Page
 * 
 * Variables available:
 * - $plugin_name: Plugin name
 * - $version: Plugin version
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1>LKN Radio Browser - Como Usar</h1>
    
    <div class="lknwp-radio-admin-content">
        
        <!-- Radio Player Shortcode -->
        <div class="lknwp-radio-shortcode-section">
            <h2>🎵 Shortcode do Player de Rádio</h2>
            <p>Use este shortcode para exibir o player de rádio em uma página específica:</p>
            
            <div class="lknwp-radio-code-block">
                <code>[radio_browser_player]</code>
                <button class="lknwp-radio-copy-btn" onclick="copyToClipboard('[radio_browser_player]', this)">Copiar</button>
            </div>
            
            <div class="lknwp-radio-info">
                <h4>📋 Como funciona:</h4>
                <ul>
                    <li>Crie uma página (ex: "Player" com slug "player")</li>
                    <li>Adicione o shortcode <code>[radio_browser_player]</code></li>
                    <li>O player receberá automaticamente os parâmetros da URL</li>
                    <li>Funciona com links vindos da lista de rádios</li>
                </ul>
            </div>
        </div>

        <!-- Radio List Shortcode -->
        <div class="lknwp-radio-shortcode-section">
            <h2>📻 Shortcode da Lista de Rádios</h2>
            <p>Use este shortcode para exibir uma lista de rádios com filtros:</p>
            
            <div class="lknwp-radio-code-block">
                <code>[radio_browser_list player_page="player"]</code>
                <button class="lknwp-radio-copy-btn" onclick="copyToClipboard('[radio_browser_list player_page=&quot;player&quot;]', this)">Copiar</button>
            </div>

            <div class="lknwp-radio-info">
                <h4>⚙️ Parâmetros Básicos:</h4>
                <table class="lknwp-radio-params-table">
                    <tr>
                        <td><code>player_page</code></td>
                        <td>Slug da página do player (obrigatório)</td>
                        <td><code>"player"</code></td>
                    </tr>
                    <tr>
                        <td><code>countrycode</code></td>
                        <td>Código do país (BR, US, FR, etc.)</td>
                        <td><code>"BR"</code></td>
                    </tr>
                    <tr>
                        <td><code>limit</code></td>
                        <td>Número de rádios a exibir</td>
                        <td><code>20</code></td>
                    </tr>
                    <tr>
                        <td><code>sort</code></td>
                        <td>Ordenação (clickcount, name, random, bitrate)</td>
                        <td><code>"clickcount"</code></td>
                    </tr>
                    <tr>
                        <td><code>reverse</code></td>
                        <td>Ordem reversa (1 ou 0)</td>
                        <td><code>"1"</code></td>
                    </tr>
                    <tr>
                        <td><code>search</code></td>
                        <td>Termo de busca</td>
                        <td><code>""</code></td>
                    </tr>
                </table>
            </div>

            <div class="lknwp-radio-info">
                <h4>🎛️ Parâmetros para Esconder Filtros:</h4>
                <table class="lknwp-radio-params-table">
                    <tr>
                        <td><code>hide_country</code></td>
                        <td>Esconder campo País</td>
                        <td><code>"yes"</code> ou <code>"no"</code></td>
                    </tr>
                    <tr>
                        <td><code>hide_limit</code></td>
                        <td>Esconder campo Limite</td>
                        <td><code>"yes"</code> ou <code>"no"</code></td>
                    </tr>
                    <tr>
                        <td><code>hide_sort</code></td>
                        <td>Esconder campo Ordenar</td>
                        <td><code>"yes"</code> ou <code>"no"</code></td>
                    </tr>
                    <tr>
                        <td><code>hide_order</code></td>
                        <td>Esconder botão Ordem</td>
                        <td><code>"yes"</code> ou <code>"no"</code></td>
                    </tr>
                    <tr>
                        <td><code>hide_search</code></td>
                        <td>Esconder campo Buscar</td>
                        <td><code>"yes"</code> ou <code>"no"</code></td>
                    </tr>
                    <tr>
                        <td><code>hide_button</code></td>
                        <td>Esconder botão Buscar</td>
                        <td><code>"yes"</code> ou <code>"no"</code></td>
                    </tr>
                    <tr>
                        <td><code>hide_all_filters</code></td>
                        <td>Esconder todos os filtros</td>
                        <td><code>"yes"</code> ou <code>"no"</code></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Examples Section -->
        <div class="lknwp-radio-shortcode-section">
            <h2>💡 Exemplos de Uso</h2>
            
            <div class="lknwp-radio-example">
                <h4>Lista completa com filtros (padrão):</h4>
                <div class="lknwp-radio-code-block">
                    <code>[radio_browser_list player_page="player"]</code>
                    <button class="lknwp-radio-copy-btn" onclick="copyToClipboard('[radio_browser_list player_page=&quot;player&quot;]')">Copiar</button>
                </div>
            </div>

            <div class="lknwp-radio-example">
                <h4>Lista limpa sem filtros:</h4>
                <div class="lknwp-radio-code-block">
                    <code>[radio_browser_list player_page="player" hide_all_filters="yes"]</code>
                    <button class="lknwp-radio-copy-btn" onclick="copyToClipboard('[radio_browser_list player_page=&quot;player&quot; hide_all_filters=&quot;yes&quot;]')">Copiar</button>
                </div>
            </div>

            <div class="lknwp-radio-example">
                <h4>Apenas busca por texto:</h4>
                <div class="lknwp-radio-code-block">
                    <code>[radio_browser_list player_page="player" hide_country="yes" hide_limit="yes" hide_sort="yes" hide_order="yes"]</code>
                    <button class="lknwp-radio-copy-btn" onclick="copyToClipboard('[radio_browser_list player_page=&quot;player&quot; hide_country=&quot;yes&quot; hide_limit=&quot;yes&quot; hide_sort=&quot;yes&quot; hide_order=&quot;yes&quot;]')">Copiar</button>
                </div>
            </div>

            <div class="lknwp-radio-example">
                <h4>Configuração fixa (rádios dos EUA, 10 estações, sem filtros):</h4>
                <div class="lknwp-radio-code-block">
                    <code>[radio_browser_list player_page="player" countrycode="US" limit="10" sort="clickcount" hide_all_filters="yes"]</code>
                    <button class="lknwp-radio-copy-btn" onclick="copyToClipboard('[radio_browser_list player_page=&quot;player&quot; countrycode=&quot;US&quot; limit=&quot;10&quot; sort=&quot;clickcount&quot; hide_all_filters=&quot;yes&quot;]')">Copiar</button>
                </div>
            </div>
        </div>

        <!-- Setup Instructions -->
        <div class="lknwp-radio-shortcode-section">
            <h2>🚀 Como Configurar</h2>
            <div class="lknwp-radio-info">
                <h4>1. Criar as páginas:</h4>
                <ol>
                    <li>Vá em <strong>Páginas > Adicionar Nova</strong></li>
                    <li>Crie uma página chamada "Rádios" com slug "radios"</li>
                    <li>Adicione o shortcode: <code>[radio_browser_list player_page="player"]</code></li>
                    <li>Crie outra página chamada "Player" com slug "player"</li>
                    <li>Adicione o shortcode: <code>[radio_browser_player]</code></li>
                </ol>

                <h4>2. Adicionar ao menu:</h4>
                <ol>
                    <li>Vá em <strong>Aparência > Menus</strong></li>
                    <li>Adicione a página "Rádios" ao menu</li>
                    <li>A página "Player" não precisa estar no menu (é acessada automaticamente)</li>
                </ol>

                <h4>3. Personalizar (opcional):</h4>
                <ul>
                    <li>Use os parâmetros <code>hide_*</code> para esconder filtros desnecessários</li>
                    <li>Configure valores padrão com <code>countrycode</code>, <code>limit</code>, etc.</li>
                    <li>Personalize os estilos via CSS do tema</li>
                </ul>
            </div>
        </div>
    </div>
</div>