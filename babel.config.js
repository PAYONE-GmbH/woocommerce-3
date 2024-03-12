module.exports = function babelConfig(api) {
    api.cache(true);

    return {
        presets: [
            [
                '@babel/preset-env',
                {
                    useBuiltIns: 'entry',
                    targets: '>0.25%',
                    corejs: '3',
                    modules: 'commonjs',
                },
            ],
            '@babel/preset-react',
            '@babel/preset-typescript',
        ],
    };
};
