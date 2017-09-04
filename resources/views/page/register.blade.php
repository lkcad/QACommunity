<div class="register-home" ng-controller="SignUpController">
    <div class="card">
        <h2>注册</h2>
        <form ng-submit="User.signUp()" name="signup_form" class="form-horizontal">
            <div class="form-group">
                <label for="username" class="col-sm-3 control-label">用户名</label>
                <div class="col-sm-9 "
                     ng-class="{trur:'has-error'}[signup_form.username.$touched&&(User.signup_username_exists||signup_form.username.$error)]">
                    <input name="username" type="text" class="form-control" ng-minlength="2" ng-maxlength="46" required
                           placeholder="用户名" ng-model="User.signup_data.username" ng-model-options="{debounce:300}">
                </div>

                <div class="input-error-set" ng-if="signup_form.username.$touched" class="col-sm-2 control-label">
                    <span ng-if="signup_form.username.$error.required">用户名为必填项</span>
                    <span ng-if="signup_form.username.$error.maxlength||signup_form.username.$error.minlength">
                        长度不和要求
                    </span>
                    <span ng-if="User.signup_username_exists"> 用户名已存在</span>
                </div>

            </div>
            <div>
                <div class="form-group">
                    <label for="password" class="col-sm-3 control-label">密码</label>
                    <div class="col-sm-9"
                         ng-class="{true:'has-error'}[signup_form.password.$touched&&(
                    User.signup_data.password!=User.signup_data.password2||signup_form.password.$error.maxlength
                    ||signup_form.password.$error.minlength)]">
                        <input name="password" ng-minlength="2" ng-maxlength="12" required class="form-control"
                               placeholder="密码" type="password" ng-model="User.signup_data.password">
                    </div>

                    <div class="input-error-set" ng-if="signup_form.password.$touched" class="col-sm-2 control-label">
                        <span ng-if="signup_form.password.$error.required">密码名为必填项</span>
                        <span ng-if="signup_form.password.$error.maxlength||signup_form.password.$error.minlength">
                        密码长度不和要求
                    </span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password" class="col-sm-3 control-label">确认密码</label>
                    <div class="col-sm-9"
                         ng-class="{true:'has-error'}[signup_form.password.$touched&&(
                    User.signup_data.password!=User.signup_data.password2||signup_form.password.$error.maxlength
                    ||signup_form.password.$error.minlength)]"
                    >
                        <input name="password" ng-minlength="2" ng-maxlength="12" required class="form-control"
                               placeholder="确认密码" type="password" ng-model="User.signup_data.password2">
                    </div>
                    <div class="input-error-set" ng-if="signup_form.password.$touched" class="col-sm-2 control-label">
                        <span ng-if="User.signup_data.password!=User.signup_data.password2">
                        密码不一致
                    </span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-6 col-sm-4">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" ng-model="agree"> 同意协议
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-6 col-sm-4">
                    <button type="submit" class="btn btn-default"
                            ng-disabled="signup_form.$invalid||User.signup_data.password!=User.signup_data.password2||!agree">
                        注册
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
