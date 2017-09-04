<div class="home" ng-controller="HomeController">
    <h1>最近动态</h1>
    <div class="hr"></div>
    <div class="item-set">
        <div ng-repeat="item in Timeline.data track by $index" class="item clearfix">
            <div class="item-content">
                <div class="content-act"
                     ui-sref="user({id:item.user.id})"
                     ng-if="item.question_id">[:item.user.username:]添加了回答
                </div>
                <div class="content-act" ng-if="!item.question_id">[:item.user.username:]添加了提问</div>
                <div ng-if="item.question_id" class="title" ui-sref="question.detail({id:item.question.id})">
                    [:item.question.title:]
                </div>
                <div class="title" ui-sref="question.detail({id:item.id})">[:item.title:]</div>
                <div class="contet-owner">[:item.user.username:]</div>

                <div class="content-main" ng-if="item.question_id">
                    <div class="home_content">
                        [:item.content:]
                    </div>

                    <div class="content-date">
                        <a ui-sref="question.detail({id:item.question_id,answer_id:item.id})">
                            [:item.updated_at:]
                        </a>
                    </div>
                </div>

                <div class="action-set">
                    <div ng-click="item.show_comment=!item.show_comment" ng-if="item.question_id">
                        <a href=""><span ng-if="item.show_comment">隐藏</span>评论</a>
                    </div>
                </div>
                <div ng-if="item.show_comment" comment-block answer-id="item.id">
                </div>
            </div>

            <div class="vote" ng-if="item.question_id">
                <div class="up" ng-click="Timeline.vote({id:item.id,vote:1})">赞[:item.upvote_count:]</div>
                <div class="down" ng-click="Timeline.vote({id:item.id,vote:2})">踩[:item.downvote_count:]</div>
            </div>
        </div>
        <div ng-if="Timeline.no_more_data">没有更多</div>
        <div ng-if="Timeline.pending">加载中</div>
    </div>
</div>
