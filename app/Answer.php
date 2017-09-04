<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    public function add()
    {
//        登陆检查
        if (!user_ins()->is_log_in()) {
            return ['status' => 0, 'msg' => 'log inrequired'];
        }
//        检查参数
        if (!rq('question_id') || !rq('content')) {
            return ['status' => 0, 'msg' => 'question_id and content are required'];
        }
//        检查问题存在
        $question = question_ins()->find(rq('question_id'));
        if (!$question) {
            return ['status' => 0, 'msg' => 'question not exist'];
        }
//检测已经回答
        $answered = $this
            ->where(['question_id' => rq('question_id'), 'user_id' => session('user_id')])
            ->count();
        if ($answered) {
            return ['status' => 0, 'msg' => 'duplicate answers'];
        }
        $this->content = rq('content');
        $this->question_id = rq('question_id');
        $this->user_id = session('user_id');
        return $this->save() ?
            ['status' => 1, 'id' => $this->id] :
            ['status' => 0, 'msg' => 'db insert failed'];
    }

    public function change()
    {
        //        登陆检查
        if (!user_ins()->is_log_in()) {
            return ['status' => 0, 'msg' => 'log inrequired'];
        }
        //        检查参数
        if (!rq('id')) {
            return ['status' => 0, 'msg' => 'question_id  are required'];
        }
        if (!rq('content')) {
            return ['status' => 0, 'msg' => 'content are required'];
        }

        $answer = $this->find(rq('id'));
        if ($answer->user_id != session('user_id')) {
            return ['status' => 0, 'msg' => 'permission denied'];
        }

        $answer->content = rq('content');
        return $answer->save() ?
            ['status' => 1] :
            ['status' => 0, 'msg' => 'db insert failed'];
    }

    public function read_by_user_id($user_id)
    {
        $user = user_ins()->find($user_id);

        if (!$user) {
            return err('user not exists');
        }
        $r = $this
            ->with('question')
            ->where('user_id', $user_id)
            ->get()->keyBy('id');
        return suc($r->toArray());
    }

//    查看回答
    public function read()
    {
        if (!rq('id') && !rq('question_id') && !rq('user_id')) {
            return ['status' => 0, 'msg' => 'id or question_id is required'];
        }


        if (rq('user_id')) {
            $user_id = rq('user_id') === 'self' ?
                session('user_id') :
                rq('user_id');
            return $this->read_by_user_id($user_id);
        }

        if (rq('id')) {
            $answer = $this
                ->with('user')
                ->with('users')
                ->find(rq('id'));
            if (!$answer) {
                return ['status' => 0, 'msg' => 'answer not exists '];
            }
            $answer = $this->count_vote($answer);

            return ['status' => 1, 'data' => $answer];
        }

        if (!question_ins()->find(rq('question_id'))) {
            return ['status' => 0, 'msg' => 'question not exists '];
        }

        $answers = $this
            ->where('question_id', rq('question_id'))
            ->get()
            ->keyBy('id');
        return ['status' => 1, 'data' => $answers];
    }

    public function remove()
    {
        // return '待实现';
        if (!user_ins()->is_log_in()) {
            return ['status' => 0, 'msg' => 'login required'];
        }
        if (!rq('id')) {
            return ['status' => 0, 'msg' => 'id is required'];

        }
        $answer = $this->find(rq('id'));
        if (!$answer) {
            return ['status' => 0, 'msg' => 'answer not exists'];
        }
        if ($answer->user_id != session('user_id')) {
            return ['status' => 0, 'msg' => 'permission required'];
        }

        //要删除对应的评论
        comment_ins()->where('answer_id', $answer->id)->delete();
        //把对应的赞删除
        $answer->users()->newPivotStatement()->where('answer_id', $answer->id)->delete();

        return $answer->delete() ?
            ['status' => 1] :
            ['status' => 0, 'masg' => 'db delete failed'];
    }


    public function vote()
    {
        if (!user_ins()->is_log_in()) {
            return ['status' => 0, 'msg' => 'log required'];
        }
        if (!rq('id') || !rq('vote')) {
            return ['status' => 0, 'msg' => 'id ang vote required'];
        }

        $answer = $this->find(rq('id'));
        if (!$answer) {
            return ['status' => 0, 'msg' => 'answer not exists'];
        }
        /*1赞同 2反对 3：清空*/
        $vote = rq('vote');
        if ($vote != 1 && $vote != 2 && $vote != 3) {
            return err('invalid');
        }

        //        检查用户是否投过,如果投过，删除投票
        $voto_ins = $answer->users()
            ->newPivotStatement()
            ->where('user_id', session('user_id'))
            ->where('answer_id', rq('id'))
            ->delete();
        if ($vote == 3) {
            return ['status' => 1];
        }
        $answer->users()->attach(session('user_id'), ['vote' => $vote]);

        return ['status' => 1];

    }

    public function count_vote($answer)
    {
        $upvote_count = 0;
        $downvote_count = 0;
        foreach ($answer->users as $user) {
            if ($user->pivot->vote == 1) {
                $upvote_count++;
            } else {
                $downvote_count++;
            }
        }
        $answer->upvote_count = $upvote_count;
        $answer->downvote_count = $downvote_count;
        return $answer;
    }

    public function users()
    {
        return $this
            ->belongsToMany('App\User')
            ->withPivot('vote')
            ->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function question()
    {
        return $this->belongsTo('App\Question');
    }

    public function comment()
    {
        return $this->belongsTo('App\Comment');
    }
}
