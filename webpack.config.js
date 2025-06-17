var Encore = require('@symfony/webpack-encore');
var dotenv = require('dotenv');

Encore
    .configureDefinePlugin(options => {
        const env = dotenv.config();

        if (env.error) {
            throw env.error;
        }

        options['process.env'].DATABASE_URL = JSON.stringify(env.parsed.DATABASE_URL);
    })

    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    // uncomment to create hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())
    .enableReactPreset()

    // uncomment to define the assets of the project
    .addEntry('js/app_jobflix', './assets/js/app_jobflixv2.js')
    .addEntry('js/app', './assets/js/app.js')
    .addEntry('js/app_public', './assets/js/app_public.js')
    .addEntry('js/debug', './assets/js/debug.js')
    .addEntry('js/head', './assets/js/recaptcha.js')

    .addStyleEntry('css/app', './assets/less/app.less')

    //public_css
    .addStyleEntry('css/app_jobflix', './assets/less/app_jobflix.less')
    .addStyleEntry('css/app_public', './assets/less/app_public.less')
    .addStyleEntry('css/app_public_index', './assets/less/app_public_index.less')
    .addStyleEntry('css/amiens', './assets/less/events/amiens.less')
    .addStyleEntry('css/arras_st_laurent_blangy', './assets/less/events/arras_st_laurent_blangy.less')
    .addStyleEntry('css/boulogne_sur_mer', './assets/less/events/boulogne_sur_mer.less')
    .addStyleEntry('css/brest', './assets/less/events/brest.less')
    .addStyleEntry('css/caen', './assets/less/events/caen.less')
    .addStyleEntry('css/cherbourg_en_cotentin', './assets/less/events/cherbourg_en_cotentin.less')
    .addStyleEntry('css/compiegne', './assets/less/events/compiegne.less')
    .addStyleEntry('css/dunkerque', './assets/less/events/dunkerque.less')
    .addStyleEntry('css/evreux', './assets/less/events/evreux.less')
    .addStyleEntry('css/le_havre', './assets/less/events/le_havre.less')
    .addStyleEntry('css/lomme', './assets/less/events/lomme.less')
    .addStyleEntry('css/rennes', './assets/less/events/rennes.less')
    .addStyleEntry('css/rouen', './assets/less/events/rouen.less')
    .addStyleEntry('css/valenciennes', './assets/less/events/valenciennes.less')
    .addStyleEntry('css/lorient', './assets/less/events/lorient.less')
    .addStyleEntry('css/lille', './assets/less/events/lille.less')
    .addStyleEntry('css/reims', './assets/less/events/reims.less')
    .addStyleEntry('css/lille_getout', './assets/less/events/lille_getout.less')
    .addStyleEntry('css/paris', './assets/less/events/paris.less')
    .addStyleEntry('css/boulogne', './assets/less/events/boulogne.less')
    .addStyleEntry('css/alencon', './assets/less/events/alencon.less')
    .addStyleEntry('css/valenciennes_anzin', './assets/less/events/valenciennes_anzin.less')
    .addStyleEntry('css/spotlight', './assets/less/events/spotlight.less')
    .addStyleEntry('css/nantes', './assets/less/events/nantes.less')
    .addStyleEntry('css/vannes', './assets/less/events/vannes.less')
    .enableReactPreset()
    .enableLessLoader()
    .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
