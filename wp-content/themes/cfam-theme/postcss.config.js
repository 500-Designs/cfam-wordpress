module.exports = {
    plugins: [
        require('postcss-easy-import'),
        require('postcss-mixins'),
        require('precss'),
        require('postcss-hexrgba'),
        require('postcss-object-fit-images'),
        require('cssnano')({
            preset: 'default'
        }),
        require('autoprefixer')({
            'browsers': ['> 1%', 'last 2 versions']
        }),
    ]
}