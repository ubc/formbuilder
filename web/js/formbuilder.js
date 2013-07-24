var formBuilderApp = angular.module("FormBuilder", ['questionServices', 'formServices', 'ui', 'ui.bootstrap']).
    config(function($routeProvider){
        $routeProvider.
            when('/', {controller:QuestionTemplateCtrl, templateUrl:'/partials/template-list.html'}).
            when('/newTemplate', {controller:QuestionTemplateNewCtrl, templateUrl:'/partials/edit-template.html'}).
            when('/editTemplate/:templateId', {controller:QuestionTemplateEditCtrl, templateUrl:'/partials/edit-template.html'}).
            otherwise({redirectTo:'/'});
    });

function stringify(object) {
    if (object instanceof String) {
        return object;
    }

    var ret = [];

    for(var prop in object) {
        if (prop == undefined || prop == "") {
            continue;
        }
        ret.push(prop+":"+object[prop]);
    }
    ret = ret.join(";");

    return ret;
}

function objectify(string) {
    if (string instanceof Object) {
        return string;
    }
    var properties = string.split(';');
    var obj = {};
    properties.forEach(function(property) {
        var tup = property.split(':');
        if (tup[0] == undefined || tup[0] == "") {
            return;
        }
        obj[tup[0]] = tup[1];
    });

    return obj;
}

// filter to show the templates owned by user
formBuilderApp.filter("ownership", function() {
    return function (input, is_mine) {
        // no filter
        if (is_mine == undefined || is_mine == false) {
            return input;
        }

        var out = [];
        for(var i = 0; i < input.length; i++) {
            if (input[i].owner == userId) {
                out.push(input[i]);
            }
        }
        return out;
    }
})

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

formBuilderApp.directive("droppable", function($rootScope) {
    return {
        restrict: 'A',
        link: function(scope, element, attrs) {
            var options = scope.$eval(attrs.droppable);
            $(element).droppable({
                accept: ".question-template",
                drop: function( event, ui ) {
                    var dragIndex = angular.element(ui.draggable).data('template-id');
                    $rootScope.$broadcast('addQuestionEvent', {id:dragIndex});
                    element.removeClass('target-over');
                },
                over:function (event, ui) {
                    element.addClass('target-over');
                },
                out: function (event, ui) {
                    element.removeClass('target-over');
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
            // merge the default options with ones in attrs
            var defaultOptions = {
                autoHide: true,
                handles: "e, s, w, se, sw",
                stop: function(event, ui) {
                    var model = eval("scope."+attrs.ngModel);
                    model.metadata = objectify($(element).attr('style'));
                }
            }
            var options = $.extend(defaultOptions, scope.$eval(attrs.resizable));
            // make our element resizable
            $(element).resizable(options);

            // add css effect when mouse entering or leaving the element
            element.bind("mouseenter", function() {
                //element.switchClass('editable', 'editing', 200);
                element.addClass('editing');
                element.children("div.controls").show();
            })
            element.bind("mouseleave", function() {
                //element.switchClass('editing', 'editable', 200);
                element.removeClass('editing');
                element.children("div.controls").hide();
            })
        }
    }
})

formBuilderApp.directive("storable", function() {
    return function(scope, element, attrs) {
        var defaultOptions = {
            handles: "se",
            stop: function(event, ui) {
                scope.question.response_metadata = $(element).attr('style');
            }
        }
        var options = $.extend(defaultOptions);

        // make our element resizable
        $(element).resizable(options);
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

            //read(); // initialize

            // Write data to the model
            function read() {
                ngModel.$setViewValue(element.html());
            }
        }
    };
});

formBuilderApp.directive('formSelector', function(Form) {
    return {
        restrict: 'A',
        require: 'ngModel',
        templateUrl: '/partials/form-selector.html',
        link: function(scope, element, attrs, ngModel) {
            scope.formlist = Form.query();
            // event when a form is selected
            scope.onselect = function(form_id) {
                // update model value
                ngModel.$setViewValue(form_id);
            }
        }
    }
});

formBuilderApp.directive('editable', function() {
    return {
        restrict: 'C',
        link: function(scope, element, attrs) {
            element.hallo({
                plugins: {
                    'halloformat': {},
                    'halloheadings':{},
                    'hallojustify':{},
                    'hallolists':{},
                    'halloreundo':{}
//            'halloimage':{}
                }
            })
        }
    }
})

// this directive enables the rotations on the checkbox and radio responses
formBuilderApp.directive('rotatableResponse', function() {
    return {
        restrict: 'C',
        link: function(scope, element, attrs) {
            var aElement = angular.element('<i class="icon-exchange rotation-handle" ></i>');
            element.parent().children("div.controls").append(aElement);
            aElement.bind('click', function() {
                _.each(scope.question.responses, function(value) {
                    if (_.isString(value.classes)) {
                        value.classes = value.classes.split(' ');
                    }
                    if (value.classes == undefined || value.classes == "") {
                        value.classes = ["inline"];
                    } else if (_.indexOf(value.classes, "inline") != -1) {
                        value.classes = _.without(value.classes, "inline");
                    } else {
                        value.classes.push("inline");
                    }
                })
                scope.$apply();
            })
        }
    }
})

function QuestionTemplateCtrl($rootScope, $scope, Question) {
    // default filter parameters
    $scope.query = {is_master: true};
    // get the list for question bank
    $scope.templates = Question.query();

    // get userId from global space for ng-show
    $scope.getUserId = function() {
        return userId;
    }

    // add a question template to the form
    $scope.addToForm = function(templateId) {
        $rootScope.$broadcast('addQuestionEvent', {id: templateId});
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
        } else {
            // remove ids from responses
            for(var i=0; i<$scope.template.responses.length; i++) {
                $scope.template.responses[i].id = undefined;
            }
        }
        if ($scope.template.is_master == false) {
            $scope.template.is_master = undefined;
        }

        // remove owner and id fields from the request
        $scope.template.owner = undefined;
        var id = $scope.template.id;
        $scope.template.id = undefined;

        Question.update({id: id}, {"question": $scope.template}, function() {
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
        if ($scope.template.is_public == false) {
            $scope.template.is_public = undefined;
        }
        // this is a template question
        $scope.template.is_master = true;
        Question.save({question: $scope.template}, function() {
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

function FormCtrl($scope, $dialog, $rootScope, Question, Form) {

    var self = this;
    $scope.form = new Form();

    var resetForm = function () {
        $scope.form.id = undefined;
        $scope.form.name = "Untitled Form";
        $scope.form.header = "<h2>Default Header</h2>";
        $scope.form.footer = "<h2>Default Footer</h2>";
        $scope.form.questions = [];
        self.form = new Form($scope.form);
    }

    function isDirty () {
        return !angular.equals($scope.form, self.form);
    }

    resetForm();

    $scope.sortableOptions = {
        placeholder: "ui-state-highlight",
        distance: 5,
        handle: ".handle"
    }

    $scope.delete = function (idx) {
        $scope.form.questions.splice(idx, 1);
    };

    $scope.$on('addQuestionEvent', function(event, params) {
        Question.get({id:params.id}, function(question){
            // remove question id
            question.id = undefined;
            // note that to store as a boolean false in the database,
            // symfony data binding treats the existence of the field
            // itself as true, so we must give it no value in order to get
            // it treated as a boolean false.
            if (!question.is_public) { // need to preserve value of is_public
                question.is_public = undefined;
            }
            // a question placed on a form is always a non-master question
            question.is_master = undefined;
            // remove response ids
            for(var j=0; j < question.responses.length; j++) {
                question.responses[j].id = undefined;
            }
            $scope.form.questions.push(question);
        });
    });

    // conflicting with contenteditable
    //$("ul, li").disableSelection();

    /**
     * event handlers
     */
    $scope.$on('saveEvent', function(event, callback) {
        // if the form is not mine, clear ids to create a new form
        if ($scope.form.owner != userId) {
            $scope.form.clearIds();
            $scope.form.owner = userId;
        }

        // save if there is no form id
        if ($scope.form.id == undefined) {
            Form.save({form: $scope.form}, function(response, headers) {
                // parse form id from location
                var location = headers("location");
                $scope.form.id = location.match(/\/forms\/(.*)$/)[1];
                // sync the form to self.form
                self.form = angular.copy($scope.form);

                if (callback != undefined) {
                    callback();
                }
            })
        } else {
            var form_id = $scope.form.id;

            $scope.form.clearIds();

            Form.update({id: form_id}, {form: $scope.form}, function(response) {
                // sync the form to self.form
                self.form = angular.copy($scope.form);

                if (callback != undefined) {
                    callback();
                }
            },
            function(error) {
                switch(error.status) {
                    case 403:
                        alert("You don't have permission to save this form!");
                        break;
                    default:
                        alert("Form saving failed!");
                }
            });

            // restore the form id
            $scope.form.id = form_id;
        }
    });

    $scope.$on('newEvent', function(event) {
        // check if the form is dirty
        if (isDirty()) {
            var msgbox = $dialog.messageBox('Save Form?', 'The form has been changed. Do you want to save it before continue?', [{label:'Yes', result: 'yes'},{label:'Discard', result: 'no'}]);
            msgbox.open().then(function(result){
                if(result === 'yes') {
                    $rootScope.$broadcast('saveEvent', resetForm);
                } else {
                    resetForm();
                }
            });
        } else {
            resetForm();
        }
    })

    $scope.$on('loadEvent', function(event) {
        console.log('load');

        function loadForm() {
            var t = '<div class="modal-header">Forms</div>'+
                '<div class="modal-body" form-selector ng-model="selectedform"></div>' +
                '<div class="modal-footer"><button ng-click="close(result)" class="btn btn-primary" >Close</button></div>'
            '</div>';
            var d = $dialog.dialog({template: t, controller: 'FormLoadController'});
            d.open().then(function(result){
                if (result != undefined) {
                    // user selected a form to load
                    Form.get({id:result}, function(form) {
                        $scope.form = angular.copy(form);
                        $scope.form.questions.forEach(function(value) {
                            if (value.metadata == undefined) {
                                return;
                            }
                        })
                        // sync the form to self.form
                        self.form = angular.copy($scope.form);
                    });
                }
            });
        }

        // check if the form is dirty
        if (isDirty()) {
            var msgbox = $dialog.messageBox('Save Form?', 'The form has been changed. Do you want to save it before continue?', [{label:'Yes', result: 'yes'},{label:'Discard', result: 'no'}]);
            msgbox.open().then(function(result){
                if(result === 'yes') {
                    $rootScope.$broadcast('saveEvent', loadForm);
                } else {
                    loadForm();
                }
            });
        } else {
            loadForm();
        }


    })
}

function MenuCtrl($scope, $rootScope) {
    $scope.save = function() {
        $rootScope.$broadcast('saveEvent');
    }
    $scope.new = function() {
        $rootScope.$broadcast('newEvent');
    }
    $scope.load = function() {
        $rootScope.$broadcast('loadEvent');
    }
    $scope.help = function() {
        $('#introDlg').modal().on('hidden', function () {
            introJs().setOption("showStepNumbers", false).start();
        });
    }
}

function FormLoadController($scope, dialog) {
    // watch if user select a form, then close the dialog
    $scope.$watch('selectedform', function() {
        if ($scope.selectedform != undefined) {
            dialog.close($scope.selectedform);
        }
    })

    // if user close the dialog without selecting a form
    $scope.close = function(result) {
        dialog.close($scope.selectedform);
    }
}
