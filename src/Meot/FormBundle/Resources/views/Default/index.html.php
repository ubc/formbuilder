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

        <style>
            body {
                padding-top: 42px;
            }
        </style>
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css" />
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="css/font-awesome.min.css">
        <!--[if IE 7]>
        <link rel="stylesheet" href="css/font-awesome-ie7.min.css">
        <![endif]-->
        <link rel="stylesheet" href="css/angular-ui.min.css">
        <link rel="stylesheet" href="css/chardinjs.css">
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="css/formbuilder.css">
		<link rel="stylesheet" href="css/formbuilder_print.css" media="print">

        <script src="js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>

        <!-- Non-minified versions during development -->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.js"></script>

        <script src="js/vendor/bootstrap.js"></script>
        <script src="js/vendor/chardinjs.min.js"></script>

        <script src="js/main.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.0.5/angular.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.0.5/angular-resource.js"></script>
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

        <!-- Top Navbar -->
        <div class="navbar navbar-inverse navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container-fluid">
                    <!-- Left aligned elements -->
                    <a class="brand" href="#">Form Builder</a>
                    <ul class="nav" ng-controller="MenuCtrl">
                        <li><a title="New Form" href="#" ng-click="new()"><i class="icon-file icon-large"></i></a></li>
                        <li><a title="Save Form" href="#" ng-click="save()"><i class="icon-save icon-large"></i></a></li>
                        <li><a title="Load Form" href="#" ng-click="load()"><i class="icon-folder-open icon-large"></i></a></li>
                        <li><a title="Print Form" href="#" onclick="window.print();"><i class="icon-print icon-large"></i></a></li>
                        <li><a title="Shwo Help" href="#" ng-click="help()"><i class="icon-question-sign icon-large"></i></a></li>
                    </ul>
                    <!-- Right aligned elements -->
                    <ul class="nav pull-right">
                        <li><a href="#"><i class="icon-user"></i>Username</a></li>
                        <li><a href="#"><i class="icon-signout icon-large"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div id="panel" ng-view data-intro="Question bank, where you can drag questions to your form." data-position="right">
        </div>
        <!-- Main Content -->
        <div id="formcontent" ng-controller="FormCtrl">
            <h2 class="editable pull-left" contenteditable="true" ng-model="form.name" data-intro="Form name" data-position="right"></h2>
            <div class="clearfix"></div>
            <div class="hero-unit" id="form-paper" droppable drop="addQuestion(id)">
                <div class="editable" contenteditable="true" ng-model="form.header"  data-intro="Form header" data-position="bottom"></div>
                <ul id="question-list" ui-sortable="sortableOptions" ng-model="form.questions" editable-ul>
                    <li resizable='{containment: "parent"}' ng-repeat="question in form.questions" ng-switch="question.response_type" ng-style="question.metadata" ng-model="question">
                        <button ng-click="delete($index)"><i class="icon-remove"></i></button>
                        <div class="handle pull-left"><i class="icon-arrow-up"></i><br/><i class="icon-arrow-down"></i></div>
                        <div class="editable" contenteditable="true" ng-model="question.text"></div>
                        <div ng-switch-when="1" class="response-input"><input type="text"/></div>
                        <div ng-switch-when="2" class="response-textarea"><textarea storable ng-style="question.response_metadata" class="responseText"></textarea></div>
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
                <div class="editable" contenteditable="true" ng-model="form.footer"  data-intro="Form footer" data-position="bottom"></div>
            </div>
            {{form}}
            <footer>
            <hr>
                <p>&copy; <a href="http://ctlt.ubc.ca/">Centre for Teaching, Learning and Technology, UBC</a> 2013</p>
            </footer>
        </div><!--/formpanel-->
    </body>
</html>
