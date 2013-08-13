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

        autoWatch: false,

        singleRun: true,
        browsers: ['PhantomJS', 'Firefox'],
        frameworks: ["jasmine"],

        reporters: ['dots', 'junit', 'coverage'],

        preprocessors: {
            // source files, that you wanna generate coverage for
            // do not include tests or libraries
            // (these files will be instrumented by Istanbul)
            'web/js/*.js': ['coverage']
        },

        junitReporter: {
            outputFile: 'app/build/logs/js_unit.xml',
            suite: 'unit'
        },

        coverageReporter: {
            type : 'cobertura',
            dir : 'app/build/jscoverage/'
        },

        reportSlowerThan: 500,
    });
};
