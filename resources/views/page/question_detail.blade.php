<div ng-controller="QuestionDetailController" class="question_detail">

    <div class="card">
        <h3>[:Question.current_question.title:]</h3>

        <div class="desc">
            [:Question.current_question.desc:]
        </div>


        <span class="answer_number">回答数[:Question.current_question.answers_with_user_info.length:]</span>


        <span ng-if="his.id==Question.current_question.user_id" class="btn"
              ng-click="Question.show_update_form=!Question.show_update_form"
        >  <span ng-if="Question.show_update_form">取消</span>修改问题</span>


        <form ng-submit="Question.update()" name="question_add_form"
              ng-if="Question.show_update_form"
                           class="question_detail_form"
        >
            <div class="form-group">
                <label for="question_detail_title">问题标题</label>
                <input type="text" name="title"
                       minlength="2"
                       maxlength="55"
                       id="question_detail_title"
                       class="form-control"
                       required ng-model="Question.current_question.title">
            </div>
            <div class="form-group">
                <label for="question_detail_desc">问题描述</label>
                <textarea class="form-control" id="question_detail_desc" name="desc" ng-model="Question.current_question.desc"></textarea>

            </div>
            <div class="input-group">
                <button type="submit" class="btn btn-default" ng-disabled="question_add_form.$invalid">
                    提交
                </button>
            </div>
        </form>


        <div class="answer-block">

            <div ng-if="!helper.obj_length(Question.current_question.answers_with_user_info)">还没有回答,快来作答吧</div>

            <div ng-if="!Question.current_answer_id|| Question.current_answer_id==item.id"
                 ng-repeat="item in Question.current_question.answers_with_user_info">


                <div class="answer_set">
                    <div class="answer">
                        <div ui-sref="user({id:item.user.id})">[:item.user.username:]</div>
                        <p>[:item.content:]</p>
                    </div>


                    <div ng-if="item.user.id==his.id">
                        <a class="btn" ng-click="Answer.answer_form=item">编辑</a>
                        <a class="btn" ng-click="Answer.delete(item.id)">删除</a>
                    </div>

                    <div>
                        <a href="" ui-sref="question.detail({id:Question.current_question.id,answer_id:item.id})">
                            [:item.updated_at:]</a>
                    </div>

                    <div ng-click="item.show_comment=!item.show_comment">
                        <span ng-if="item.show_comment">取消</span>评论
                    </div>


                    <div comment-block ng-if="item.show_comment" answer-id="item.id">
                        评论dsgdshdshsdhdsh
                    </div>

                    <div class="vote">
                        <div class="up" ng-click="Question.vote({id:item.id,vote:1})">
                            赞[:item.upvote_count:]
                        </div>
                        <div class="down" ng-click="Question.vote({id:item.id,vote:2})">
                            踩[:item.downvote_count:]
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <form class="answer_form" name="answer_form"
              ng-submit="Answer.add_or_update(Question.current_question.id)">
            <div class="input-group">
                  <textarea name="content"
                            ng-model="Answer.answer_form.content"
                            required
                            placeholder="输入答案"
                            style="height: 120px;width: 100%">
                  </textarea><br>
                <button type="submit" class="btn btn-default" ng-disabled="answer_form.$invalid">提交</button>
            </div>
        </form>

    </div>
</div>
<style>

</style>
