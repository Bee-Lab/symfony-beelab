let Encore = require('@symfony/webpack-encore');

Encore
    // directory where all compiled assets will be stored
    .setOutputPath('public/build/')

    // what's the public path to this directory (relative to your project's document root dir)
    .setPublicPath('/build')

    // empty the outputPath dir before each build
    .cleanupOutputBeforeBuild()

    // will output as public/build/*.js
    .addEntry('js/app', './assets/js/app.js')

    // will output as public/build/*.css
    .addStyleEntry('css/app', './assets/scss/app.scss')

    // allow sass/scss files to be processed
    .enableSassLoader()

    // allow to debug sources in development
    .enableSourceMaps(!Encore.isProduction())

    // create hashed filenames (e.g. app.abc123.css)
    .enableVersioning()

    // avoid runtime.js
    .disableSingleRuntimeChunk()
;

// export the final configuration
module.exports = Encore.getWebpackConfig();
