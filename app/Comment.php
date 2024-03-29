<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public function add()
    {
//        检查登陆
        if (!user_ins()->is_log_in()) {
            return ['satus' => 0, 'msg' => 'login required'];
        }
//        检查内容
        if (!rq('content')) {
            return ['status' => 0, 'msg' => 'empty comment'];
        }
//问题和答案只能有一个
        if ((!rq('question_id') && !rq('answer_id')) || (rq('question_id') && rq("answerid"))) {
            return ['status' => 0, 'msg' => 'question_id or answer_id is required'];
        }
        if (rq('question_id')) {
            $question = question_ins()->find(rq('question_id'));
            if (!$question) {
                return ['status' => 0, 'msg' => 'question not exists'];
            }
            $this->question_id = rq('question_id');
        } else {
            $answer = answer_ins()->find(rq('answer_id'));
            if (!$answer) {
                return ['status' => 0, 'msg' => 'answer not exists'];
            }
            $this->answer_id = rq('answer_id');
        }
//检查回复的评论
        if (rq('reply_to')) {
            $target = $this->find(rq('reply_to'));
            if (!$target) {
                return ['status' => 0, 'msg' => 'target comment not exists'];
            }
            if ($target->user_id == session('user_id')) {
                return ['status' => 0, 'msg' => 'can not reply to yourself'];
            }
            $this->reply_to = rq('reply_to');
        }
        $this->content = rq('content');
        $this->user_id = session('user_id');

        return $this->save() ?
            ['status' => 1, 'id' => $this->id] :
            ['status' => 0, 'msg' => 'db insert failed'];

    }

    public function read()
    {
        if (!rq('question_id') && !rq('answer_id')) {
            return ['status' => 0, 'msg' => 'question id or answer_id is reeuired'];
        }
        if (rq('question_id')) {
            $question = question_ins()->with('user')->find(rq('question_id'));
            if (!$question) {
                return ['status' => 0, 'msg' => 'question not exists'];
            }
            $data = $this->with('user')
                ->where('question_id', rq('question_id'));

        } else {
            $answer = answer_ins()->with('user')->find(rq('answer_id'));
            if (!$answer) {
                return ['status' => 0, 'msg' => '$answer not exists'];
            }
            $data = $this->with('user')
                ->where('answer_id', rq('answer_id'));

        }
        $data = $data->get()->keyBy('id');

        return ['status' => 1, 'data' => $data];

    }

    public function remove()
    {
        if (!user_ins()->is_log_in()) {
            return ['status' => 0, 'msg' => 'login required'];
        }
        if (!rq('id')) {
            return ['status' => 0, 'msg' => 'id is required'];
        }

        $comment = $this->find(rq('id'));

        if (!$comment) {
            return ['status' => 0, 'msg' => 'comment is not exists'];
        }
        if ($comment->user_id != session('user_id')) {
            return ['status' => 0, 'msg' => 'permission denied'];
        }

//        将回复全部删除
        $this->where('reply_to', rq('id'))->delete();

        return $comment->delete() ?
            ['status' => 1] :
            ['status' => 0, 'msg' => 'db delete failed'];
    }

    public function user()
    {
        return $this->belongsTo('App\User');

    }
}
