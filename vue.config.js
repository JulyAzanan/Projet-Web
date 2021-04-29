module.exports = {
    configureWebpack: {
        module: {
            rules: [
                {
                    test: /\.musicxml/,
                    use: 'raw-loader',
                }
            ]
        }
    },
    publicPath: process.env.NODE_ENV === 'production'
    ? '/~mael.acier/musegit/'
    : '/'
}
