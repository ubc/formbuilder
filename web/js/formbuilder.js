var formBuilderApp = angular.module("FormBuilder", ['questionServices', 'ui']).
    config(function($routeProvider){
        $routeProvider.
            when('/', {controller:QuestionTemplateCtrl, templateUrl:'partials/template-list.html'}).
            when('/newTemplate', {controller:QuestionTemplateNewCtrl, templateUrl:'partials/edit-template.html'}).
            when('/editTemplate/:templateId', {controller:QuestionTemplateEditCtrl, templateUrl:'partials/edit-template.html'}).
            otherwise({redirectTo:'/'});
    });

formBuilderApp.filter("truncate", function () {
    return function (text, length, end) {
        if (isNaN(length))
            length = 10;

        if (end === undefined)
            end = "...";

        if (text.length <= length || text.length - end.length <= length) {
            return text;
        }
        else {
            return String(text).substring(0, length-end.length) + end;
        }

    };
});

//
//formBuilderApp.factory("QuestionList", function() {
//    return {
//        message:"message from question list",
//        questions:[{text:"aaa"},{text:"bbb"}]
//    }
//})

// make the question templates loaded from server draggable
formBuilderApp.directive("draggable", function() {
    return {
        restrict: 'A',
        link: function(scope, element, attrs) {
            var options = scope.$eval(attrs.draggable);
            $(element).draggable(options)
        }
    }
})

formBuilderApp.directive("droppable", function() {
    return {
        restrict: 'A',
        scope: {
            drop:"&"
        },
        link: function(scope, element, attrs) {
            var options = scope.$eval(attrs.droppable);
            $(element).droppable({
                drop: function( event, ui ) {
                    var dragIndex = angular.element(ui.draggable).data('template-id');
                    scope.drop({id:dragIndex});
                    element.removeClass('target-over');
                },
                over:function (event, ui) {
                    element.addClass('target-over');
                    //ui.draggable.addClass('item-over');
                },
                out: function (event, ui) {
                    element.removeClass('target-over');
                    //ui.draggable.removeClass('item-over');
                }
            });
        }
    }
})


// make the question templates loaded from server draggable
formBuilderApp.directive("resizable", function() {
    return {
        restrict: 'A',
        link: function(scope, element, attrs) {
            var options = scope.$eval(attrs.resizable);
            $(element).resizable(options)
            element.bind("mouseenter", function() {
                //element.switchClass('editable', 'editing', 200);
                element.addClass('editing');
            })
            element.bind("mouseleave", function() {
                //element.switchClass('editing', 'editable', 200);
                element.removeClass('editing');
            })
        }
    }
})

formBuilderApp.directive('contenteditable', function() {
        return {
            restrict: 'A', // only activate on element attribute
            require: '?ngModel', // get a hold of NgModelController
            link: function(scope, element, attrs, ngModel) {
                if(!ngModel) return; // do nothing if no ng-model

                // Specify how UI should be updated
                ngModel.$render = function() {
                    element.html(ngModel.$viewValue || '');
                };

                // Listen for change events to enable binding
                element.bind('blur keyup change', function() {
                    scope.$apply(read);
                });
                read(); // initialize

                // Write data to the model
                function read() {
                    ngModel.$setViewValue(element.html());
                }
            }
        };
    });

function QuestionTemplateCtrl($scope, Question) {
    // default filter parameters
    $scope.query = {is_master: true};
    // get the list for question bank
    $scope.templates = Question.query();

    // get userId from global space for ng-show
    $scope.getUserId = function() {
        return userId;
    }
}

function QuestionTemplateEditCtrl($scope, $location, $routeParams, Question) {
    var self = this;
    console.log(Question);
    Question.get({id: $routeParams.templateId}, function(template){
        self.original = template;
        $scope.template = angular.copy(template);
    })

    $scope.isClean = function() {
        return angular.equals(self.original, $scope.template);
    }

    $scope.destroy = function() {
        Question.delete({id: self.original.id});
        $location.path('/list');
    };

    $scope.save = function() {
        // some clean up before saving
        if ($scope.template.response_type == 1 || $scope.template.response_type == 2) {
            $scope.template.responses = undefined;
        }
        if ($scope.template.is_master == false) {
            $scope.template.is_master = undefined;
        }

        // remove owner and id fields from the request
        $scope.template.owner = undefined;
        var id = $scope.template.id;
        $scope.template.id = undefined;

        Question.update({id: id}, $scope.template, function() {
            $location.path('/');
        });

    };

    $scope.addResponse = function ($event) {
        $scope.template.responses.push({text:""});
        $event.preventDefault();
    }

    $scope.removeResponse = function (response) {
        $.each($scope.template.responses, function(i){
            if($scope.template.responses[i] === response) {
                $scope.template.responses.splice(i, 1);
            }
        });
    }
}

function QuestionTemplateNewCtrl($scope, $location, Question) {
    $scope.template = {responses:[]};

    $scope.save = function() {
        // some clean up before saving
        if ($scope.template.response_type == 1 || $scope.template.response_type == 2) {
            $scope.template.responses = undefined;
        }
        if ($scope.template.is_master == false) {
            $scope.template.is_master = undefined;
        }
        Question.save($scope.template, function() {
           $location.path('/');
        });
    }

    $scope.addResponse = function ($event) {
        $scope.template.responses.push({text:""});
        $event.preventDefault();
    }

    $scope.removeResponse = function (response) {
        $.each($scope.template.responses, function(i){
            if($scope.template.responses[i] === response) {
                $scope.template.responses.splice(i, 1);
            }
        });
    }
}

function FormCtrl($scope, Question) {
    $scope.form = {
        questions:[]
    };

    $scope.addQuestion = function(id){
        Question.get({id:id}, function(question){
            $scope.form.questions.push(question);
        });
    }

    //$(".form-element").sortable();
    $("ul, li").disableSelection();
    $("div.editable").hallo({
        plugins: {
            'halloformat': {},
            'halloheadings':{},
            'hallojustify':{},
            'hallolists':{},
            'halloreundo':{}
//            'halloimage':{}
        }
    });
}