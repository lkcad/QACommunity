<div ng-controller="LoginController">
    <h2>登陆</h2>
    <form class="form-horizontal" name="login_form" ng-submit="User.login()">
        <div class="form-group">
            <label for="username" class="col-sm-3 control-label">用户名</label>
            <div class="col-sm-9">
                <input type="text" name="username" class="form-control" id="username" placeholder="用户名"
                       required ng-model="User.login_data.username">
            </div>
        </div>
        <div class="form-group " ng-class="{true:'has-error'}[User.login_failed]">
            <label for="password" class="col-sm-3  control-label">密码</label>
            <div class="col-sm-9">
                <input type="password" name="password" class="form-control" id="password" placeholder="密码"
                       required ng-model="User.login_data.password">
            </div>
            {{--<div ng-show="User.login_failed" class="col-sm-2 control-label">登陆错误</div>--}}
        </div>

        <div class="form-group">
            <div class="col-sm-offset-6 col-sm-4">
                <div class="checkbox">
                    <label>
                        <input type="checkbox"> 记住密码
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-6 col-sm-4">
                <button type="submit" class="btn btn-default"
                        ng-disabled="login_form.username.$error.required|| login_form.username.$error.required">
                    登陆
                </button>
            </div>
        </div>
    </form>
</div>

