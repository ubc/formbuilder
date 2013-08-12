basePath = '../../';

files = [
  JASMINE,
  JASMINE_ADAPTER,
  'web/js/vendor/angular.js',
  'web/js/vendor/angular-*.js',
  'test/lib/angular/angular-mocks.js',
  'web/js/vendor/jquery*.js',
  'web/js/**/*.js',
  'test/unit/**/*.js'
];

autoWatch = false;

singleRun = true;
reporters = ['dots', 'junit'];
browsers = ['PhantomJS', 'Firefox'];

junitReporter = {
  outputFile: 'app/build/logs/js_unit.xml',
  suite: 'unit'
};
