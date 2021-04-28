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
    }
}
