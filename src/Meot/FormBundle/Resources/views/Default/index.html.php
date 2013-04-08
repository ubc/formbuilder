<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Form Builder</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">

        <link rel="stylesheet" href="css/bootstrap.min.css">
        <style>
            body {
                padding-top: 60px;
                padding-bottom: 40px;
            }
        </style>
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css" />
        <link rel="stylesheet" href="css/bootstrap-responsive.min.css">
        <link rel="stylesheet" href="css/font-awesome.min.css">
        <!--[if IE 7]>
        <link rel="stylesheet" href="css/font-awesome-ie7.min.css">
        <![endif]-->
        <link rel="stylesheet" href="css/angular-ui.min.css">
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="css/formbuilder.css">
		<link rel="stylesheet" href="css/formbuilder_print.css" media="print">

        <script src="js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.9.1.min.js"><\/script>')</script>
        <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-ui-1.10.1.min.js"><\/script>')</script>

        <script src="js/vendor/bootstrap.min.js"></script>

        <script src="js/main.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.0.5/angular.min.js"></script>
        <script src="http://code.angularjs.org/1.0.5/angular-resource.min.js"></script>
        <script src="js/vendor/angular-ui.min.js"></script>
        <script src="js/vendor/ui-bootstrap-tpls-0.2.0.min.js"></script>
        <script src="js/vendor/underscore-min.js"></script>
        <script src="js/services.js"></script>
        <script src="js/formbuilder.js" type="text/javascript"></script>
        <script src="js/vendor/rangy-core.js"></script>
        <script src="js/vendor/hallo.js"></script>
        <script>
            var userId = 1;
        </script>

    </head>
    <body ng-app="FormBuilder" ng-cloak>
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]-->

        <div class="navbar navbar-inverse navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container-fluid">
                    <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="brand" href="#">Form Builder</a>
                    <div class="nav-collapse collapse" ng-controller="MenuCtrl">
                        <p class="navbar-text pull-right">
                            Logged in as <a href="#" class="navbar-link">Username</a>&nbsp;&nbsp;
                            <a href="#">Logout</a>
                        </p>
                        <ul class="nav">
                            <li><a href ng-click="new()">New</a></li>
                            <li><a href ng-click="save()">Save</a></li>
                            <li><a href ng-click="load()">Load</a></li>
                        </ul>
                    </div><!--/.nav-collapse -->
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row-fluid">
                <div class="span3" id="panel">
                    <div class="well sidebar-nav" ng-view>

                    </div><!--/.well -->
                </div><!--/span-->
                <div class="span9" ng-controller="FormCtrl">
                    <h2 class="editable pull-left" contenteditable="true" ng-model="form.name"></h2>
                    <button id="printBtn" class="btn pull-right" onclick="window.print();"><i class="icon-print">Print</i></button>
                    <div class="clearfix"></div>
                    <div class="hero-unit" id="form-paper" droppable drop="addQuestion(id)">
                        <div class="editable" contenteditable="true" ng-model="form.header"></div>
                        <ul id="question-list" ui-sortable="sortableOptions" ng-model="form.questions" editable-ul>
                            <li resizable='{containment: "parent"}' ng-repeat="question in form.questions" ng-switch="question.response_type" ng-style="question.metadata" ng-model="question">
                                <div class="handle pull-left"><i class="icon-arrow-up"></i><br/><i class="icon-arrow-down"></i></div>
                                <div class="editable" contenteditable="true" ng-model="question.text"></div>
                                <div ng-switch-when="1" class="response-input"><input type="text"/></div>
                                <div ng-switch-when="2" class="response-textarea"><textarea storable ng-style="question.response_metadata"></textarea></div>
                                <div ng-switch-when="3" class="response-radio">
                                    <label class="radio" ng-class="response.classes" ng-repeat="response in question.responses">
                                        <input type="radio" disabled><span class="editable" contenteditable="true" ng-model="response.text"></span>
                                    </label>
                                </div>
                                <div ng-switch-when="4" class="response-checkbox">
                                    <label class="checkbox" ng-class="response.classes" ng-repeat="response in question.responses">
                                        <input type="checkbox" disabled><span class="editable" contenteditable="true" ng-model="response.text"></span>
                                    </label>
                                </div>
                            </li>
                        </ul>
                        <div class="editable" contenteditable="true" ng-model="form.footer"></div>
                    </div>
                    {{form}}
                </div><!--/span-->
            </div><!--/row-->

            <footer>
            <hr>
                <p>&copy; <a href="http://ctlt.ubc.ca/">Centre for Teaching, Learning and Technology, UBC</a> 2013</p>
            </footer>

        </div><!--/.fluid-container-->
    </body>
</html>
