const path = require('path');

module.exports = {
    entry: './Public/js/lknwp-radio-browser-list.js',
    output: {
        filename: 'lknwp-radio-browser-list.COMPILED.js',
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
