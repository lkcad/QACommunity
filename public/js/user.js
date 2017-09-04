;(function () {
    'use strict';
    angular.module('user', ['ui.router', 'answer'])
        .service('UserService', [
            '$http',
            '$state',
            function (http, state) {
                var me = this;
                me.signup_data = {};
                me.login_data = {};
                me.data = {};
                me.signUp = function () {
                    http.post('api/user', me.signup_data)
                        .then(function (r) {
                            if (r.data.status) {
                                me.signup_data = {};
                                state.go('login');
                            }
                        }, function (e) {
                        });
                };
                me.username_exists = function () {
                    http.post('api/user/exists', {username: me.signup_data.username})
                        .then(function (r) {
                            me.signup_username_exists = !!(r.data.status && r.data.data.count);
                        }, function (e) {
                        })
                };

                me.login = function () {
                    http.post('api/login', me.login_data)
                        .then(function (r) {
                            if (r.data.status) {
                                location.href = '/';
                                // state.go('home');
                            } else {
                                me.login_failed = true;
                                console.log("error")
                            }
                        }, function (e) {
                        });
                };

                me.logout = function () {
                    http.post('api/logout')
                        .then(function (r) {
                            if (r.data.status) {
                                location.href = '/';
                            } else {
                                console.log("error")
                            }
                        }, function (e) {
                        });
                };

                me.read = function (param) {
                    return http.post('/api/user/read', param)
                        .then(function (r) {

                            if (r.data.status) {
                                me.current_user = r.data.data;
                                me.data[param.id] = r.data.data;
                                console.log(me.current_user)
                            } else {
                                if (r.data.msg == 'longin required') {
                                    state.go('login')
                                }
                            }
                        }, function (e) {
                        });
                };
            }])
        .controller('SignUpController', ['UserService', '$scope', function ($UserService, scope) {
            scope.User = $UserService;
            scope.$watch(function () {
                    return $UserService.signup_data
                },
                function (newVal, oldVal) {
                    if (newVal.username != oldVal.username) {
                        $UserService.username_exists();
                    }
                },
                true)
        }])
        .controller('LoginController', ['$scope', 'UserService', function (scope, UserService) {
            scope.User = UserService;

        }])
        .controller('userController', [
            '$scope',
            'UserService',
            '$stateParams',
            'AnswerService',
            'QuestionService',
            function (scope, UserService, $stateParams, AnswerService, QuestionService) {
                scope.User = UserService;
                UserService.read($stateParams);
                AnswerService.read({user_id: $stateParams.id})
                    .then(function (r) {
                        if (r) {
                            UserService.his_answer = r;
                        }
                    });
                QuestionService.read({user_id: $stateParams.id})
                    .then(function (r) {
                        if (r) {

                            UserService.his_questions = r;
                        }
                    });
            }])
        .controller('logoutController', ['$scope', 'UserService', function (scope, UserService) {
            scope.User = UserService;
        }]);

})();