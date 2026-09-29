const path = require('path');

module.exports = {
    entry: {
        'lknwp-radio-browser-list': './Public/js/lknwp-radio-browser-list.js',
        'lknwp-radio-browser-list-legacy': './Public/js/lknwp-radio-browser-list-legacy.js',
    },
    output: {
        filename: '[name].COMPILED.js',
        path: path.resolve(__dirname, 'Public/jsCompiled'),
    },
    module: {
        rules: [
            {
                test: /\.css$/i,
                use: ['style-loader', 'css-loader'],
            },
        ],
    },
    resolve: {
        extensions: ['.js'],
    },
    externals: {
        jquery: 'jQuery'
    },
    mode: 'production',
};
