angular.module('questionServices', ['ngResource']).
    factory('Question', function($resource) {
        var Question = $resource('app_dev.php/questions/:id', {}, {update: { method: 'PUT' }});

//        Question.prototype.update = function(cb) {
//            console.log(this.owner);
//            return Question.update({id: this.id},
//                angular.extend({}, this, {id:undefined}), cb);
//        };

        return Question;
    });

angular.module('formServices', ['ngResource']).
    factory('Form', function($resource) {
        return $resource('app.php/forms/:id', {}, {});
    });