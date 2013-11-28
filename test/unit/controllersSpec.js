'use strict';

var userId = 1;

describe('FormBuilder controllers', function() {
    beforeEach(function(){
        this.addMatchers({
            toEqualData: function(expected) {
                return angular.equals(this.actual, expected);
            }
        });
    });

    beforeEach(function() {
        module('FormBuilder');
    });

    describe('QuestionTemplateCtrl', function() {
        var scope, ctrl, $httpBackend;
        beforeEach(inject(function(_$httpBackend_, $rootScope, $controller) {
            $httpBackend = _$httpBackend_;
            $httpBackend.expectGET('api/questions').
                respond([{"id":1,"text":"The instructor made it clear what students were expected to learn.","response_type":3,"responses":[{"id":1,"text":"Strongly Disagree"},{"id":2,"text":"Disagree"},{"id":3,"text":"Neutral"},{"id":4,"text":"Agree"},{"id":5,"text":"Strongly Agree"}],"is_public":true,"is_master":true,"owner":1,"metadata":{}},{"id":2,"text":"The instructor communicated the subject matter effectively.","response_type":3,"responses":[{"id":6,"text":"Strongly Disagree"},{"id":7,"text":"Disagree"},{"id":8,"text":"Agree"},{"id":9,"text":"Strongly Agree"}],"is_public":false,"is_master":true,"owner":1,"metadata":{}}]);

            scope = $rootScope.$new();
            ctrl = $controller(QuestionTemplateCtrl, {$rootScope: $rootScope, $scope: scope});
        }));

        afterEach(function() {
            $httpBackend.verifyNoOutstandingExpectation();
            $httpBackend.verifyNoOutstandingRequest();
        });

        it('should list all questions in the bank', function() {
            $httpBackend.flush();
            expect(scope.templates.length).toBe(2);
        });

        it('should set is_master default value true', function() {
            $httpBackend.flush();
            expect(scope.query.is_master).toBe(true);
        });

        it('should broadcast "addQuestionEvent" when adding question', inject(function($rootScope) {
            $httpBackend.flush();
            spyOn($rootScope, "$broadcast");
            scope.addToForm(3)
            expect($rootScope.$broadcast).toHaveBeenCalledWith('addQuestionEvent', {id: 3});
        }));
    });

    describe('QuestionTemplateEditCtrl', function() {
        var scope, ctrl, $httpBackend,
            fixture = function() {
                return {"id":1,"text":"The instructor made it clear what students were expected to learn.","response_type":3,"responses":[{"id":1,"text":"Strongly Disagree"},{"id":2,"text":"Disagree"},{"id":3,"text":"Neutral"},{"id":4,"text":"Agree"},{"id":5,"text":"Strongly Agree"}],"is_public":true,"is_master":true,"owner":1,"metadata":{}};
            };
        beforeEach(inject(function(_$httpBackend_, $rootScope, $controller, $routeParams) {
            $httpBackend = _$httpBackend_;
            $httpBackend.expectGET('api/questions/1').
                respond(fixture());

            $routeParams.templateId = 1;
            scope = $rootScope.$new();
            ctrl = $controller(QuestionTemplateEditCtrl, {$scope: scope});
        }));

        afterEach(function() {
            $httpBackend.verifyNoOutstandingExpectation();
            $httpBackend.verifyNoOutstandingRequest();
        });

        it('should get the question in the bank', function() {
            expect(scope.template).toBe(undefined);
            $httpBackend.flush();
            expect(scope.template).toEqualData(fixture());
        });

        it('should be clean', function() {
            $httpBackend.flush();
            expect(scope.isClean()).toBe(true);
        });

        it('should be able to delete question', inject(function($location) {
            $httpBackend.flush();
            // make sure api request is sent
            $httpBackend.expectDELETE('api/questions/1').
                respond();
            scope.destroy();
            $httpBackend.flush();
            // user is redirected to list page
            expect($location.path()).toBe('/list');
        }));

        it('should save the question', inject(function($location) {
            // ids and owner should be removed
            var putData = {"question": {"text":"The instructor made it clear what students were expected to learn.","response_type":3,"responses":[{"text":"Strongly Disagree"},{"text":"Disagree"},{"text":"Neutral"},{"text":"Agree"},{"text":"Strongly Agree"}],"is_public":true,"is_master":true,"metadata":{}}};
            $httpBackend.flush();
            // make sure api request is sent
            $httpBackend.expectPUT('api/questions/1', putData).
                respond();
            scope.save();
            $httpBackend.flush();
            // user is redirected to home page
            expect($location.path()).toBe('/');
        }));

        it('should add response to question', function() {
            var click = jQuery.Event("click");
            $httpBackend.flush();
            scope.addResponse(click);
            expect(scope.template.responses.length).toBe(6);
            expect(scope.template.responses[5]).toEqualData({'text':''});
        });

        it('should remove response from question', function() {
            var click = jQuery.Event("click");
            $httpBackend.flush();
            scope.removeResponse(scope.template.responses[4]);
            expect(scope.template.responses.length).toBe(4);
        });
    });


    describe('QuestionTemplateNewCtrl', function() {
        var scope, ctrl, $httpBackend,
            fixture = function() {
                return {"text":"The instructor made it clear what students were expected to learn.","response_type":3,"responses":[{"text":"Strongly Disagree"},{"text":"Disagree"},{"text":"Neutral"},{"text":"Agree"},{"text":"Strongly Agree"}],"is_public":true,"is_master":true,"owner":1,"metadata":{}};
            };
        beforeEach(inject(function(_$httpBackend_, $rootScope, $controller, $routeParams) {
            $httpBackend = _$httpBackend_;

            scope = $rootScope.$new();
            ctrl = $controller(QuestionTemplateNewCtrl, {$scope: scope});
        }));

        afterEach(function() {
            $httpBackend.verifyNoOutstandingExpectation();
            $httpBackend.verifyNoOutstandingRequest();
        });

        it('should have empty template by default', function() {
            expect(scope.template).toEqualData({responses:[]});
        });

        it('should save the question template', inject(function($location) {
            scope.template = fixture();
            // change is_master value to test if it's changed to true in controller
            scope.template.is_master = false;
            // make sure api request is sent
            $httpBackend.expectPOST('api/questions', { "question": fixture()}).
                respond();
            scope.save();
            $httpBackend.flush();
            // user is redirected to list page
            expect($location.path()).toBe('/');
            expect(scope.template.is_master).toBe(true);
        }));

        it('should add response to question', function() {
            var click = jQuery.Event("click");
            scope.template = fixture();
            scope.addResponse(click);
            expect(scope.template.responses.length).toBe(6);
            expect(scope.template.responses[5]).toEqualData({'text':''});
        });

        it('should remove response from question', function() {
            var click = jQuery.Event("click");
            scope.template = fixture();
            scope.removeResponse(scope.template.responses[4]);
            expect(scope.template.responses.length).toBe(4);
        });
    });


    describe('FormCtrl', function() {
        var scope, ctrl, $httpBackend,
            fixture = function() {
                return {"id":1,"text":"The instructor made it clear what students were expected to learn.","response_type":3,"responses":[{"id":1,"text":"Strongly Disagree"},{"id":2,"text":"Disagree"},{"id":3,"text":"Neutral"},{"id":4,"text":"Agree"},{"id":5,"text":"Strongly Agree"}],"is_public":true,"is_master":true,"owner":1,"metadata":{}};
            };
        var mockDialogYes = {
            open: function()
            {
                return {
                    then: function(callback) {
                        callback("yes");
                    }
                };
            }
        };
        var mockDialogNo = {
            open: function()
            {
                return {
                    then: function(callback) {
                        callback("no");
                    }
                };
            }
        };
        beforeEach(inject(function(_$httpBackend_, $rootScope, $controller, $routeParams) {
            $httpBackend = _$httpBackend_;

            scope = $rootScope.$new();
            ctrl = $controller(FormCtrl, {$rootScope: $rootScope, $scope: scope});
        }));

        afterEach(function() {
            $httpBackend.verifyNoOutstandingExpectation();
            $httpBackend.verifyNoOutstandingRequest();
        });

        it('should have empty form by default', function() {
            expect(scope.form.id).toBeUndefined();
            expect(scope.form.name).toBe("Untitled Form");
            expect(scope.form.questions.length).toBe(0);
        });

        it('should delete question', function() {
            scope.form.questions = [{"text":"q1"}, {"text":"q2"}];
            // delete the second question
            scope.deleteQuestion(1);
            expect(scope.form.questions.length).toBe(1);
            expect(scope.form.questions[0].text).toBe("q1");
        });

        it('should respond to addQuestion event', inject(function($rootScope) {
            var params = {id: 1};
            $httpBackend.expectGET('api/questions/1').
                respond(fixture());
            //spyOn(scope, "$on");
            $rootScope.$broadcast('addQuestionEvent', params);
            $httpBackend.flush();
            //expect(scope.$on).toHaveBeenCalledWith('addQuestionEvent', params);

            expect(scope.form.questions.length).toBe(1);
            // ids are removed
            expect(scope.form.questions[0].id).toBeUndefined();
            expect(scope.form.questions[0].responses[0].id).toBeUndefined();
            // is_master is reset to undefined
            expect(scope.form.questions[0].is_master).toBeUndefined();
        }));

        it('should reset the form', inject(function($rootScope, $dialog) {
            scope.form.id = 1;
            scope.form.name = "test form";
            scope.form.header = "header";
            scope.form.footer = "footer";
            scope.form.questions = ['a', 'b', 'c'];
            ctrl.resetForm();
            expect(scope.form.id).toBeUndefined();
            expect(scope.form.name).toEqual("Untitled Form");
            expect(scope.form.header).toEqual("<h2>Default Header</h2>");
            expect(scope.form.footer).toEqual("<h2>Default Footer</h2>");
            expect(scope.form.questions.length).toBe(0);
        }));

        it('should have correct load form dialog', inject(function($rootScope, $dialog) {
            var mockDialog = {
                open: function()
                {
                    return {
                        then: function(callback) {
                            callback(1);
                        }
                    };
                }
            };
            spyOn($dialog, 'dialog').andReturn(mockDialog);
            $httpBackend.expectGET('api/forms/1').respond({"name": "form 1", "id": 1});
            ctrl.loadForm();
            $httpBackend.flush();
            expect($dialog.dialog).toHaveBeenCalled();
            expect(scope.form.id).toEqual(1);
            expect(ctrl.form.id).toEqual(1);
            expect(scope.form.name).toEqual('form 1');
            expect(ctrl.form.name).toEqual('form 1');
        }));

        describe("saveEvent", function() {
            var formFixture = function() {
                return {"name":"Untitled Form","header":"<h2>Default Header</h2>","footer":"<h2>Default Footer</h2>","questions":[],"owner":2};
            };
            var callback = jasmine.createSpy('callback');
            var alert_msg;

            beforeEach(function() {
                alert_msg = '_default_';
                spyOn(window, 'alert').andCallFake(function(msg) {
                    alert_msg = msg;
                });
            });

            it('should update form owner id', inject(function($rootScope) {
                $httpBackend.whenPOST('api/forms', {"form": scope.form}).respond(201, '', {'location': 'http://example.com/api/forms/1'});
                $rootScope.$broadcast('saveEvent');
                $httpBackend.flush();
                expect(scope.form.owner).toBe(1);
            }));

            it('should update form with new id and correct owner', inject(function($rootScope) {
                var expected = formFixture();
                expected.id = '1';
                expected.owner = 1;
                $httpBackend.whenPOST('api/forms', {"form": scope.form}).respond(201, '', {'location': 'http://example.com/api/forms/1'});
                $rootScope.$broadcast('saveEvent');
                $httpBackend.flush();
                expect(scope.form).toEqualData(expected);
            }));

            it('should call the callback function', inject(function($rootScope) {
                $httpBackend.whenPOST('api/forms', {"form": scope.form}).respond(201, '', {'location': 'http://example.com/api/forms/1'});
                $rootScope.$broadcast('saveEvent', callback);
                $httpBackend.flush();
                expect(callback).toHaveBeenCalled();
            }));

            it('should update the copy with new saved form', inject(function($rootScope) {
                $httpBackend.whenPOST('api/forms', {"form": scope.form}).respond(201, '', {'location': 'http://example.com/api/forms/1'});
                $rootScope.$broadcast('saveEvent');
                $httpBackend.flush();
                expect(scope.form).toEqualData(ctrl.form);
            }));

            it('should update form when the form has an ID', inject(function($rootScope) {
                scope.form.id = 1;
                scope.form.owner = 1;
                $httpBackend.whenPUT('api/forms/1', {"form": scope.form}).respond(200, '');
                $rootScope.$broadcast('saveEvent', callback);
                $httpBackend.flush();
                expect(scope.form).toEqualData(ctrl.form);
                expect(scope.form.id).toBe(1);
                expect(callback).toHaveBeenCalled();
            }));

            it('should trigger an alert when save failed', inject(function($rootScope) {
                scope.form.id = 1;
                scope.form.owner = 1;
                $httpBackend.whenPUT('api/forms/1', {"form": scope.form}).respond(500, '');
                $rootScope.$broadcast('saveEvent', callback);
                $httpBackend.flush();
                expect(alert_msg).toEqual('Form saving failed!');
            }));

            it('should trigger an alert when no permission to update', inject(function($rootScope) {
                scope.form.id = 1;
                scope.form.owner = 1;
                $httpBackend.whenPUT('api/forms/1', {"form": scope.form}).respond(403, '');
                $rootScope.$broadcast('saveEvent', callback);
                $httpBackend.flush();
                expect(alert_msg).toEqual("You don't have permission to save this form!");
            }));
        });

        describe("newEvent", function() {
            beforeEach(function() {

            });

            it('should reset the form if current form is not dirty', inject(function($rootScope) {
                // make a change on both copies of data
                scope.form.name = "test";
                ctrl.form.name = "test";
                $rootScope.$broadcast('newEvent');
                expect(scope.form.name).toEqual('Untitled Form');
                expect(ctrl.form.name).toEqual('Untitled Form');
            }));

/*            it('should show a dialog if the form is dirty', inject(function($rootScope, $dialog, $document) {
                scope.form.name = "test";
                $rootScope.$broadcast('newEvent');
                scope.$apply();
                console.log($document.find('body > div.modal > div.modal-header').length);
                expect($document.find('body > div.modal-backdrop').css('display')).toBe('block');
            }));
*/
            it('should trigger save API call when user click "yes" on the dialog', inject(function($rootScope, $dialog) {
                spyOn($dialog, 'messageBox').andReturn(mockDialogYes);
                scope.form.name = "test";
                $httpBackend.whenPOST('api/forms', {"form": scope.form}).respond(201, '', {'location': 'http://example.com/api/forms/1'});
                $rootScope.$broadcast('newEvent');
                $httpBackend.flush();
                expect($dialog.messageBox).toHaveBeenCalled();
                expect(scope.form.name).toEqual('Untitled Form');
                expect(ctrl.form.name).toEqual('Untitled Form');
            }));

            it('should reset the form when user click "no" on the dialog', inject(function($rootScope, $dialog) {
                spyOn($dialog, 'messageBox').andReturn(mockDialogNo);
                scope.form.name = "test";
                $rootScope.$broadcast('newEvent');
                expect($dialog.messageBox).toHaveBeenCalled();
                expect(scope.form.name).toEqual('Untitled Form');
                expect(ctrl.form.name).toEqual('Untitled Form');
                $httpBackend.verifyNoOutstandingExpectation();
                $httpBackend.verifyNoOutstandingRequest();
            }));
        });

        describe("loadEvent", function() {
            it('should trigger save event when the form is changed and dialog respond is yes', inject(function($rootScope, $dialog) {
                spyOn($dialog, 'messageBox').andReturn(mockDialogYes);
                spyOn($rootScope, '$broadcast').andCallThrough();
                $httpBackend.whenPOST('api/forms', {"form": scope.form}).respond(201, '', {'location': 'http://example.com/api/forms/1'});
                $httpBackend.expectGET('/partials/form-selector.html').respond();
                $httpBackend.expectGET('api/forms').respond();
                scope.form.name = "test";
                $rootScope.$broadcast('loadEvent');
                $httpBackend.flush();
                expect($rootScope.$broadcast).toHaveBeenCalledWith("saveEvent", jasmine.any(Function));
                expect($dialog.messageBox.calls.length).toEqual(1);
            }));

            it('should not trigger save event when the form is changed and dialog respond is no', inject(function($rootScope, $dialog) {
                spyOn($dialog, 'messageBox').andReturn(mockDialogNo);
                spyOn($rootScope, '$broadcast').andCallThrough();
                $httpBackend.expectGET('/partials/form-selector.html').respond();
                $httpBackend.expectGET('api/forms').respond();
                scope.form.name = "test";
                $rootScope.$broadcast('loadEvent');
                $httpBackend.flush();
                expect($rootScope.$broadcast.calls.length).toEqual(1);
                expect($dialog.messageBox.calls.length).toEqual(1);
            }));

            it('should load the current form when the form is not changed', inject(function($rootScope, $dialog) {
                spyOn($rootScope, '$broadcast').andCallThrough();
                $httpBackend.expectGET('/partials/form-selector.html').respond();
                $httpBackend.expectGET('api/forms').respond();
                $rootScope.$broadcast('loadEvent');
                $httpBackend.flush();
                expect($rootScope.$broadcast.calls.length).toEqual(1);
            }));

        });
    });
});
