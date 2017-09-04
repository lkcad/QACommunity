;(function () {
    'use strict';
    angular.module('answer', [])
        .service('AnswerService', [
            '$http', '$state',
            function (http, state) {
                var me = this;
                me.data = {};
                me.answer_form = {}
                /**
                 * 统计
                 * @param answers 问题会自动跳过，回答才会被计入统计
                 * @returns {*}
                 */
                me.count_vote = function (answers) {
                    for (var i = 0; i < answers.length; i++) {
                        /*封装单个数据*/
                        var votes, item = answers[i];
                        /*不是回答，跳过*/
                        if (!item['question_id']) continue;

                        me.data[item.id] = item;
                        if (item['pivot']) {

                            continue;
                        }

                        votes = item['users'];
                        /*默认为0*/
                        item.upvote_count = 0;
                        item.downvote_count = 0;
                        for (var j = 0; j < votes.length; j++) {
                            /*获取pivot元素中的用户投票信息，统计*/
                            var v = votes[j];
                            if (v['pivot'].vote === 1) {
                                item.upvote_count++;
                            }
                            if (v['pivot'].vote === 2) {
                                item.downvote_count++;
                            }
                        }

                    }
                    return answers;
                };

                me.vote = function (conf) {
                    if (!conf.id || !conf.vote) {
                        console.log('id and vote are required')
                    }
                    var answer = me.data[conf.id];
                    var users = answer.users;

                    if (answer.user_id == his.id) {
                        console.log('you are voting self');
                        return false;
                    }
                    //判断当前用户是否投过相同的票
                    for (var i = 0; i < answer.users.length; i++) {
                        if (users[i].id == his.id && conf.vote == users[i].pivot.vote) {
                            conf.vote = 3;
                        }
                    }

                    return http.post('/api/answer/vote', conf)
                        .then(function (r) {
                            //console.log(r.data.msg)
                            if (r.data.status) {
                                return true
                            } else if (r.data.msg == 'log required') {

                                state.go('login')
                            } else
                                return false;


                        }, function (e) {
                            return false;
                        });
                };
                me.update_data = function (id) {
                    /*if (angular.isNumber(input)) {
                        var id=input;
                    }
                    if (angular.isArray(input)) {
                        var id_set=input;
                    }*/

                    return http.post('/api/answer/read', {id: id})
                        .then(function (r) {
                            me.data[id] = r.data.data;
                        }, function (e) {

                        })


                };


                me.read = function (params) {
                    return http.post('api/answer/read', params)
                        .then(function (r) {
                            if (r.data.status) {

                                me.data = angular.merge({}, me.data, r.data.data);
                                return r.data.data;
                            }
                            return false

                        }, function () {
                        });
                };
                me.add_or_update = function (question_id) {
                    if (!question_id) {
                        console.log('question_id required');
                        return;
                    }
                    me.answer_form.question_id = question_id;
                    if (me.answer_form.id) {
                        http.post('api/answer/change', me.answer_form)
                            .then(function (r) {
                                if (r.data.status) {
                                    me.answer_form = {};
                                    console.log('update successfully')
                                    state.reload()
                                }
                            })

                    } else {
                        http.post('api/answer/add', me.answer_form)
                            .then(function (r) {
                                if (r.data.status) {
                                    me.answer_form = {};
                                    console.log('1');
                                    state.reload()
                                }
                            })
                    }
                }
                me.delete = function (id) {
                    if (!id) {
                        console.log('id is required');
                        return;
                    }

                    http.post('api/answer/remove', {id: id})
                        .then(function (r) {
                            if (r.data.status) {
                                console.log('deleted successfully')
                                state.reload()
                            }
                        });
                }

                me.add_comment = function (answer) {
                    return http.post('api/comment/add', me.new_comment)
                        .then(function (r) {
                            if (r.data.status) {
                                return true
                            } else {
                                return false;
                            }
                        })
                }

            }])
        .directive('commentBlock', [
            'AnswerService', '$http',
            function (AnswerService, http) {
                var o = {};
                o.templateUrl = 'comment.tpl'
                o.scope = {
                    answer_id: '=answerId' //=当作angular expression @当作字符串
                }
                o.link = function (sco, els, attr) {
                    sco.Answer = AnswerService;
                    sco._ = {};
                    sco.data = {};
                    sco.helper = helper;

                    function get_comment_list() {
                        return http.post('api/comment/read', {answer_id: sco.answer_id})
                            .then
                            (function (r) {
                                if (r.data.status) {
                                    sco.data = angular.merge({}, sco.data, r.data.data)
                                }
                            });

                    }

                    if (sco.answer_id) {
                        get_comment_list();
                    }

                    sco._.add_comment = function () {
                        AnswerService.new_comment.answer_id = sco.answer_id;
                        AnswerService.add_comment()
                            .then(function (r) {
                                if (r) {
                                    AnswerService.new_comment = {}
                                    get_comment_list();
                                }
                            })
                    }
                };
                return o;

            }]);
})();