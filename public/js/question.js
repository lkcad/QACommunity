;(function () {
    'use strict';
    angular.module('question', [])
        .service('QuestionService', ['$http', '$state', 'AnswerService', function (http, state, AnswerService) {
            var me = this;
            me.new_question = {};
            me.data = {}
            me.go_add_question = function () {
                state.go('question.add')
            };
            me.add = function () {
                if (!me.new_question.title) {
                    return
                }
                http.post('api/question/add', me.new_question)
                    .then(function (r) {
                        if (r.data.status) {
                            me.new_question = {};
                            state.go('home');
                        }
                    }, function (e) {

                    });
            }
            me.read = function (params) {
                return http.post('api/question/read', params)
                    .then(function (r) {

                        if (r.data.status) {
                            if (params.id) {
                                me.data[params.id] = r.data.data;
                                me.current_question = r.data.data;
                                me.its_answers = me.current_question.answers_with_user_info;
                                me.its_answers = AnswerService.count_vote(me.its_answers)
                                console.log(me.its_answers)
                            }
                            else {
                                me.data = angular.merge({}, me.data, r.data.data);

                            }
                            return r.data.data;
                        }
                        return false;
                    }, function () {

                    })
            }
            me.vote = function (conf) {
                var $r = AnswerService.vote(conf)
                if ($r)
                    $r.then(function (r) {
                        if (r) {
                            me.update_answer(conf.id)
                        }
                        console.log(r)


                    })
            }
            me.update_answer = function (answer_id) {
                http.post('/api/answer/read', {id: answer_id})
                    .then(function (r) {
                        if (r.data.status) {
                            for (var i = 0; i < me.its_answers.length; i++) {
                                var answer = me.its_answers[i];
                                if (answer.id == answer_id) {
                                    me.its_answers[i] = r.data.data;
                                    AnswerService.data[answer_id] = r.data.data;
                                }
                            }
                        }
                    })
            }
            me.update = function () {
                if (!me.current_question.title) {
                    console.log("title is required");
                    return false;
                }
                return http.post('/api/question/change', me.current_question)
                    .then(function (r) {
                        if (r.data.status) {
                            me.show_update_form = false;
                        }
                    })

            }
        }])
        .controller('QuestionController', [
            '$scope',
            'QuestionService',
            function (scope, QuestionService) {
                scope.Question = QuestionService;
            }])
        .controller('QuestionAddController', [
            '$scope',
            'QuestionService',
            '$state',
            function (scope, QuestionService, state) {
                scope.Question = QuestionService;
                if (!his.id) {
                    state.go('login');
                }
            }])
        .controller('QuestionDetailController', [
            '$scope',
            'QuestionService',
            '$stateParams',
            'AnswerService',
            function (scope, QuestionService, stateParams, AnswerService) {
                scope.Answer = AnswerService;
                QuestionService.read(stateParams);
                if (stateParams.answer_id) {
                    QuestionService.current_answer_id = stateParams.answer_id;
                } else {
                    QuestionService.current_answer_id = null;
                }
            }])
})();