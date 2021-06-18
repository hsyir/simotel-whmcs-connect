const mix = require('laravel-mix');

mix.js('assets/js/app.js', 'templates/js/simotel.js')
    .sass('assets/sass/app.scss', 'templates/css/simotel.css');
