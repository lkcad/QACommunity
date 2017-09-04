;(function () {
    'use strict'
    angular.module('common', [])
        .service('TimelineService', [
            '$http',
            'AnswerService',
            function (http, AnswerService) {
                var me = this;
                me.data = [];
                me.current_page = 1;
                me.no_more_data = false;
                me.get = function (conf) {
                    if (me.pending || me.no_more_data) return;
                    me.pending = true;
                    conf = conf || {page: me.current_page}
                    http.post('/api/timeline', conf)
                        .then(function (r) {
                            if (r.data.status) {
                                if (r.data.data.length) {
                                    me.data = me.data.concat(r.data.data);
                                    // 统计每一条回答的票数

                                    me.data = AnswerService.count_vote(me.data)
                                    me.current_page++;
                                } else {
                                    me.no_more_data = true;
                                }
                            } else {
                                console.log('network error1')
                            }

                        }, function (e) {
                            console.log('network error')
                        })
                        .finally(function () {
                            me.pending = false;
                        });
                };
                // 时间线中投票
                me.vote = function (conf) {
                    /*调用核心投票功能*/
                    var $r = AnswerService.vote(conf)
                    if ($r)
                        $r.then(function (r) {
                            /*成功->更新数据*/
                            if (r) {
                                AnswerService.update_data(conf.id);
                            }
                        })
                }

                me.reset_state = function () {
                    me.data = [];
                    me.current_page = 1;
                    me.no_more_data = false;
                }

            }])
        .controller('HomeController', [
            '$scope',
            'TimelineService',
            'AnswerService',
            function (scope, TimelineService, AnswerService) {
                var $win;
                scope.Timeline = TimelineService;
                TimelineService.reset_state();
                TimelineService.get();
                $win = $(window)
                $win.on('scroll', function () {
                    if ($win.scrollTop() - $(document).height() + $win.height() > -30) {
                        TimelineService.get()
                    } else {
                    }
                })

                /*监控回答数据的变化*/
                scope.$watch(function () {
                    return AnswerService.data;
                }, function (newVal) {
                    var timeline_data = TimelineService.data;
                    for (var k in newVal) {
                        /*更新时间线中的回答数据*/
                        for (var i = 0; i < timeline_data.length; i++) {
                            if (k == timeline_data[i].id) {
                                timeline_data[i] = newVal[k];
                            }
                        }
                    }
                    TimelineService.data = AnswerService.count_vote(TimelineService.data)
                }, true)
            }])
})();