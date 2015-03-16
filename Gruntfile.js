module.exports = function(grunt) {
    grunt.initConfig({
        // Склеиваем
        concat: {
            main: {
                src: [
                    'js/jquery-1.11.2.min.js',
                    'bootstrap/js/bootstrap.js',
                    'plugins/modernizr.js',
                    'plugins/rs-plugin/js/jquery.themepunch.tools.min.js',
                    'plugins/rs-plugin/js/jquery.themepunch.revolution.min.js',
                    //'plugins/isotope/isotope.pkgd.min.js',
                    'plugins/owl-carousel/owl.carousel.js',
                    'plugins/magnific-popup/jquery.magnific-popup.min.js',
                    'plugins/jquery.appear.js',
                    'plugins/jquery.countTo.js',
                    'plugins/jquery.parallax-1.1.3.js',
                    'plugins/jquery.validate.js',
                    'js/template.js',
                    'js/custom.js',
                    'templates/_system/common_js/main.js',
                    'system/ext/jquery/jquery-ui.js',
                    'system/ext/jquery/jquery.form.js',
                    'system/ext/jquery/jquery.validate.min.js',
                    'system/ext/jquery/jquery.autocomplete.pack.js',
                    'templates/_system/common_js/autoupload_functions.js',
                    'system/ext/jquery/jquery.highlight.js',
                    'system/ext/jquery/imagesize.js',
                    'system/ext/jquery/multilist/jquery.multiselect.min.js',
                    'templates/_system/common_js/multilist_functions.js',
                    'templates/_system/common_js/jquery.poshytip.min.js',
                    'templates/_system/common_js/floatnumbers_functions.js'
                ],
                dest: 'build/scripts.js'
            }
        },
        // Сжимаем
        uglify: {
            main: {
                files: {
                    // Результат задачи concat
                    'build/scripts.min.js': '<%= concat.main.dest %>'
                }
            }
        }
    });

    // Загрузка плагинов, установленных с помощью npm install
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');

    // Задача по умолчанию
    grunt.registerTask('default', ['concat', 'uglify']);
};