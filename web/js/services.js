angular.module('questionServices', ['ngResource']).
    factory('Question', function($resource) {
        return $resource('app_dev.php/questions/:id/:action', {id:"@id"}, {
            update: { method: 'PUT' },
            copy: {method: "POST", params: {action: "copy"}}
        });
    });

angular.module('formServices', ['ngResource']).
    factory('Form', function($resource) {
        var Form = $resource('app_dev.php/forms/:id', {id:"@id"}, {
            update: { method: 'PUT' }
        });

        // set up default values
        Form.prototype.id = undefined;
        Form.prototype.name = "Untitled Form";
        Form.prototype.header = "<h2>Default Header</h2>";
        Form.prototype.footer = "<h2>Default Footer</h2>";
        Form.prototype.questions = [];

        return Form;
    });