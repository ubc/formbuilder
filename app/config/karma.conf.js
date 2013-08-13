module.exports = function(config) {
    config.set({
        basePath: '../../',

        files: [
            'web/js/vendor/angular.js',
            'web/js/vendor/angular-*.js',
            'test/lib/angular/angular-mocks.js',
            'web/js/vendor/jquery*.js',
            'web/js/**/*.js',
            'test/unit/**/*.js'
        ],

        autoWatch: true,

        browsers: ['Chrome'],

        frameworks: ["jasmine"],

        junitReporter: {
            outputFile: 'js_unit.xml',
            suite: 'unit'
        },

        reportSlowerThan: 500,
    });
};
