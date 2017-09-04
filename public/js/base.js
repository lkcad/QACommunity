(function () {
    'use strict';
    window.his = {
        id: parseInt($('html').attr('user-id'))
    };
    window.helper = {};
    helper.obj_length = function (obj) {
        if (obj)
            return Object.keys(obj).length
    };
    angular.module('xiaohu', ['ui.router', 'common', 'question', 'user', 'answer'])
        .config(function ($interpolateProvider, $stateProvider, $urlRouterProvider) {
            $interpolateProvider.startSymbol('[:');
            $interpolateProvider.endSymbol(':]');
            $urlRouterProvider.otherwise('/home');
            $stateProvider
                .state(
                    'home', {
                        url: '/home',
                        templateUrl: '/tpl/page/home'
                    }
                )
                .state(
                    'login', {
                        url: '/login',
                        templateUrl: '/tpl/page/login'
                    }
                )
                .state(
                    'register', {
                        url: '/register',
                        templateUrl: '/tpl/page/register'
                    }
                )

                .state('question', {
                    abstract: true,
                    url: '/question',
                    template: '<div ui-view></div>',
                    controller: 'QuestionController'
                })

                .state('question.detail', {
                    url: '/detail/:id?answer_id',
                    templateUrl: '/tpl/page/question_detail'
                })
                .state('question.add', {
                    url: '/add',
                    templateUrl: '/tpl/page/question_add'
                })
                .state('user', {
                    url: '/user/:id',
                    templateUrl: '/tpl/page/user'
                })

        })
        .controller('app1', ['$scope', function (scope) {
            scope.name = 'lisi';
        }])
        .controller('BaseController', ['$scope', 'UserService', function (scope, UserService) {
            scope.his = his;
            scope.helper = helper;
            scope.uu=UserService;
        }]);

})();

