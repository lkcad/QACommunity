<div ng-controller="QuestionAddController">
    <div class="question_add_panel">

        <form ng-submit="Question.add()" name="question_add_form">
            <div class="form-group">
                <label for="">问题标题</label>
                <input type="text" name="title"
                       minlength="2"
                       maxlength="55"
                       class="form-control"
                       required ng-model="Question.new_question.title">
            </div>
            <div class="form-group">
                <label for="">问题描述</label>
                <textarea name="desc" class="form-control" ng-model="Question.new_question.desc"></textarea>

            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-default form-control" ng-disabled="question_add_form.$invalid">
                    提交
                </button>
            </div>
        </form>

    </div>
</div>
