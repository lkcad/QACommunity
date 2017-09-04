<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    //    创建问题
    public function add()
    {
        if (!(user_ins()->is_log_in())) {
            return ['status' => 0, 'msg' => 'login required'];
        };
        if (!rq('title')) {
            return ['status' => 0, 'msg' => 'required title'];
        }
        $this->title = rq('title');
        if (rq('desc')) {
            $this->desc = rq('desc');
        }
        $this->user_id = session('user_id');
        return $this->save() ?
            ['status' => 1, 'id' => $this->id] :
            ['status' => 0, 'msg' => 'db insert failed'];
    }

//    更新api
    public function change()
    {
        if (!(user_ins()->is_log_in())) {
            return ['status' => 0, 'msg' => 'login required'];
        };

        if (!rq('id')) {
            return ['status' => 0, 'msg' => 'id is required'];
        }
        $question = $this->find(rq('id'));
//        判断问题是否存在
        if (!$question) {
            return ['status' => 0, 'msg' => 'question not exist'];
        }
        if ($question->user_id != session('user_id')) {
            return ['status' => 0, 'msg' => 'permission denied'];
        }
        if (rq('title')) {
            $question->title = rq('title');
        }
        if (rq('desc')) {
            $question->desc = rq('desc');
        }
        return $question->save() ?
            ['status' => 1] :
            ['status' => 0, 'msg' => 'db insert failed'];
    }


    public function read_by_user_id($user_id)
    {
        $user = user_ins()->find($user_id);

        if (!$user) {
            return err('user not exists');
        }
        $r = $this->where('user_id', $user_id)
            ->get()->keyBy('id');
        return suc($r->toArray());
    }


    public function read()
    {
//        请求参数中是否有id，有的话返回id对应数据
        if (rq('id')) {
            $r = $this
                ->with('answers_with_user_info')
                ->find(rq('id'));
            return ['status' => 1, 'data' => $r];
        }

        if (rq('user_id')) {
            $user_id = rq('user_id') == 'self' ?
                session('user_id') : rq('user_id');
            return $this->read_by_user_id($user_id);
        }

        list($limit, $skip) = paginate(rq('page'), rq('limit'));
//        返回collection数据，larval转换成json
        $r = $this
            ->orderBy('created_at')
            ->limit($limit)
            ->skip($skip)
            ->get(['id', 'title', 'desc', 'user_id', 'created_at', 'updated_at'])
            ->keyBy('id');
        return ['status' => 1, 'data' => $r];
    }

    public function remove()
    {
        if (!(user_ins()->is_log_in())) {
            return ['status' => 0, 'msg' => 'login required'];
        };

        if (!rq('id')) {
            return ['status' => 0, 'msg' => 'id is required'];
        }
        $question = $this->find(rq('id'));
        if (!$question) {
            return ['status' => 0, 'msg' => 'question not exist'];
        }
        if (session('user_id') != $question->user_id) {
            return ['status' => 0, 'msg' => 'permission denied'];
        }

        return $question->delete() ?
            ['status' => 1] :
            ['status' => 0, 'msg' => 'db delete failed'];
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function answers()
    {
        return $this->hasMany('App\Answer');
    }

    public function answers_with_user_info()
    {
        return $this
            ->answers()
            ->with('user')
            ->with('users');
    }
}
