<!doctype html>
<html lang="zh" ng-app="xiaohu" user-id="{{session('user_id')}}" ng-controller="BaseController">
<head>
    <meta charset="utf-8">
    <title>乐享问答</title>

    <link rel="stylesheet" href="/css/base.css">
    <link rel="stylesheet" href="/node_modules/normalize-css/normalize.css">
    <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">


    <script src="/node_modules/jquery/dist/jquery.js"></script>
    <script src="/node_modules/angular/angular.js"></script>
    <script src="/node_modules/angular-ui-router/release/angular-ui-router.js"></script>
    <script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"
            integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
            crossorigin="anonymous"></script>
    <script src="/js/base.js"></script>
    <script src="/js/common.js"></script>

    <script src="/js/user.js"></script>
    <script src="/js/question.js"></script>
    <script src="/js/answer.js"></script>

</head>
<body style="min-width: 800px">
<header class="navbar navbar-default" role="navigation">

    <div class="container-nav">
        <div class="navbar-header">
            <a ui-sref="home" class="navbar-brand">乐享问答</a>
        </div>
        <form class="navbar-form navbar-left" ng-submit="Question.go_add_question()" role="search"
              ng-controller="QuestionAddController">
            <div class="form-group">
                <input type="text" class="form-control" placeholder="Search" ng-model="Question.new_question.title">
            </div>
            <button type="submit" class="btn btn-default">提问</button>
        </form>
        <div class="right">
            <ul class="nav navbar-nav">
                <li class=""><a href="#" ui-sref="home">首页</a></li>
                @if(is_log_in())
                    <li class=""><a href="" ui-sref="user({id:his.id})">{{session('username')}}</a></li>
                    <li class="" ng-controller="logoutController"><a href="" ng-click="User.logout()">登出</a></li>
                    {{--<li class=""><a href="" ng-click="uu.logout()">登出2</a></li>--}}
                @else
                    <li class=""><a href="#" ui-sref="login">登陆</a></li>
                    <li class=""><a href="#" ui-sref="register">注册</a></li>
                @endif
            </ul>
        </div>
    </div>
</header>

<div class="row container-main center-block">
    <div ui-view="" class="col-sm-12"></div>
    {{--<div class="col-sm-3  home_page_right">--}}
    {{--<div class="">3333333</div>--}}
    {{--</div>--}}

</div>

<script type="text/ng-template" id="comment.tpl">
    <div class="comment-direct">
        <div class="comment-block">
            <div class="comment-item-set">
                <div ng-if="!helper.obj_length(data)">暂无评论</div>
                <div class="comment-item clearfix" ng-if="helper.obj_length(data)"
                     ng-repeat="item in data">
                    <div class="user">[:item.user.username:]:</div>
                    <span class="comment-content">
                    [:item.content:]
                </span>
                </div>

            </div>
        </div>
        <div class="input-group">
            <form action="" class="form-horizontal" ng-submit="_.add_comment()">
                <input type="text" class="text" ng-model="Answer.new_comment.content" placeholder="请输入评论">
                <button type="submit" class="btn btn-default">提交</button>
            </form>
        </div>
    </div>
</script>
</body>
</html>
