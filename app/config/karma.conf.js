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

autoWatch = true;

browsers = ['Chrome'];

junitReporter = {
  outputFile: 'test_out/unit.xml',
  suite: 'unit'
};
