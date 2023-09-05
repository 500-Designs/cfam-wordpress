const path = require('path')
const webpack = require('webpack')
const MiniCssExtractPlugin = require('mini-css-extract-plugin')

module.exports = {
    entry: {
        main: './resources/assets/scripts/main.js',
        admin: './resources/assets/scripts/admin.js'
    },
    output: {
        filename: 'scripts/[name].min.js',
        path: path.resolve(__dirname, 'dist'),
        publicPath: '/wp-content/themes/lucera-bootstrap/dist/'
    },
    optimization: {
        minimize: true
    },
    // mode: 'development',
    module: {
        rules: [{
            test: /\.m?js$/,
            exclude: /(node_modules)/,
            use: [{
                loader: 'babel-loader',
                options: { presets: ['@babel/preset-env'] }
            }]
        },
        {
            test: /\.main\.js$/,
            use: 'bundle-loader',
        },
        {
            test: /\.css$/,
            use: [{
                loader: MiniCssExtractPlugin.loader,
            },
            {
                loader: 'css-loader',
                options: {
                    importLoaders: 0
                }
            },
            {
                loader: 'postcss-loader',
                options: require('./postcss.config.js')
            }
            ]
        },
        {
            test: /\.svg|png|gif|jpg$/,
            use: [{
                loader: 'file-loader',
                options: {
                    esModule: false,
                    gifsicle: {
                        enabled: false
                    }
                }
            }]
        }
        ]
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: 'styles/[name].min.css',
        }),
    ],
    externals: {
        'jquery': 'jQuery',
        '$': 'jQuery'
    }
}