<style>

</style>


<div ng-controller="userController" class="user_info_panel">

    <div class="user_base_info">
        <h3>用户详情</h3>
        <div>用户名 [:User.current_user.username:]</div>
        <div>介绍 [:User.current_user.intro||'暂无介绍':]</div>
    </div>

    <div class="user_question">
        <h3>用户提问</h3>
        <div ng-repeat="(key,value) in User.his_questions" class="content">
            问题[:key:]:[:value.title:]
        </div>
    </div>


    <div class="user_answer">
        <h3>用户回答</h3>
        <div ng-repeat="(key,value) in User.his_answer" class="content">
            <div>
                <strong>回答[:key:]：[:value.question.title:]</strong>
            </div>
            <div>内容：[:value.content:]<br></div>
            <div>更新时间[:value.updated_at:]</div>
        </div>
    </div>
</div>

