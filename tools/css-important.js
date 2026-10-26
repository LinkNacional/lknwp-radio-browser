// Adiciona !important em todas as declarações dos CSS do componente,
// blindando o layout contra temas que sobrescrevem padding/margin/display etc.
// Uso: node tools/css-important.js [--dry]
const fs = require('fs');
const path = require('path');
const postcss = require('postcss');

const FILES = [
    'Public/css/lknwp-radio-browser-list.css',
    'Public/css/lknwp-radio-browser-player.css',
    'Public/css/lknwp-radio-browser-audio-visualizer.css',
];

// @keyframes/@font-face não aceitam (nem fazem sentido com) !important.
const SKIP_ATRULES = new Set([
    'keyframes', '-webkit-keyframes', '-moz-keyframes', '-o-keyframes',
    'font-face', 'page', 'counter-style', 'property', 'font-feature-values',
]);

// Propriedades controladas por style inline via JS (não podem virar !important,
// senão o CSS vence o inline e a animação/drag quebra).
const INLINE_OWNED = [
    { match: /lkp-visualizer-bar/, props: ['height'] },
    { match: /lkp-volume-display/, props: ['left', 'transform', 'display'] },
    { match: /lknwp-radio-volume-value/, props: ['left', 'transform', 'display'] },
    { match: /lrt-now__wave/, props: ['height'] },
];

function atRuleChain(node) {
    const chain = [];
    let p = node.parent;
    while (p) {
        if (p.type === 'atrule') { chain.push(p.name.toLowerCase()); }
        p = p.parent;
    }
    return chain;
}

function ruleSelector(node) {
    const parts = [];
    let p = node.parent;
    while (p) {
        if (p.type === 'rule') { parts.push(p.selector); }
        p = p.parent;
    }
    return parts.join(' ');
}

function isInlineOwned(node, prop) {
    const sel = ruleSelector(node);
    return INLINE_OWNED.some((entry) => entry.match.test(sel) && entry.props.includes(prop));
}

const dry = process.argv.includes('--dry');
const root = process.cwd();

for (const rel of FILES) {
    const file = path.join(root, rel);
    const css = fs.readFileSync(file, 'utf8');
    const ast = postcss.parse(css, { from: file });

    let added = 0;
    let skippedInline = 0;
    let skippedCustom = 0;

    ast.walkDecls((decl) => {
        if (decl.important) { return; }
        const chain = atRuleChain(decl);
        if (chain.some((name) => SKIP_ATRULES.has(name))) { return; }
        const prop = (decl.prop || '').trim();
        if (prop.startsWith('--')) { skippedCustom++; return; }
        if (isInlineOwned(decl, prop.toLowerCase())) { skippedInline++; return; }
        decl.important = true;
        added++;
    });

    const out = ast.toString();
    // Valida que o resultado continua parseável.
    postcss.parse(out, { from: file });
    if (!dry) { fs.writeFileSync(file, out); }
    console.log(`${rel}: +${added} !important (custom props: ${skippedCustom}, inline-owned: ${skippedInline})${dry ? ' [dry]' : ''}`);
}
