angular.module('questionServices', ['ngResource']).
    factory('Question', function($resource) {
        return $resource('api/questions/:id/:action', {id:"@id"}, {
            update: { method: 'PUT' },
            copy: {method: "POST", params: {action: "copy"}}
        });
    });

angular.module('formServices', ['ngResource']).
    factory('Form', function($resource) {
        var Form = $resource('api/forms/:id', {id:"@id"}, {
            update: { method: 'PUT' }
        });

        // set up default values
        Form.prototype.id = undefined;
        Form.prototype.name = "Untitled Form";
        Form.prototype.header = "<h2>Default Header</h2>";
        Form.prototype.footer = "<h2>Default Footer</h2>";
        Form.prototype.questions = [];
        Form.prototype.clearIds = function() {
            this.id = undefined;

            var questions = this.questions.slice(0);

            for (var i = 0; i < this.questions.length; i++) {
                // clean up the question ids
                this.questions[i].id = undefined;
                // clean up metadata to convert object to string so that backend will be happy
//                if ($scope.form.questions[i].metadata instanceof Object) {
//                    $scope.form.questions[i].metadata = stringify($scope.form.questions[i].metadata);
//                }
                // clean up the response Ids
                for(var j=0; j < this.questions[i].responses.length; j++) {
                    this.questions[i].responses[j].id = undefined;
                }
            }
        }

        return Form;
    });
