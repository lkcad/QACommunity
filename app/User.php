<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Request;

class User extends Authenticatable
{
    //    注册api


    public function signup()
    {
        $username = Request::get('username');
        $password = Request::get('password');
        /*检查用户名密码是否为空*/
        if (!($username && $password)) {
            return ['status' => 0, 'msg' => 'username not null'];
        }

        $user_exists = $this
            ->where('username', $username)
            ->exists();
        /*检查用户名是否重复*/
        if ($user_exists) {
            return ['status' => 1, 'msg' => 'user has exists'];
        }
        /*加密密码*/
//        $hashed_password = Hash::make($password);
        $hashed_password = bcrypt($password);

        /*存入数据库*/
        $user = $this;
        $user->password = $hashed_password;
        $user->username = $username;
        if ($user->save()) {
            return ['status' => 1, 'id' => $user->id];
        } else {
            return ['status' => 0, 'msg' => 'db insert failed'];
        }
        return 1;
    }

    public function login()
    {
        $username = Request::get('username');
        $password = Request::get('password');
        /*检查用户名密码是否为空*/
        if (!($username && $password)) {
            return ['status' => 0, 'msg' => 'username not null'];
        }
        //检查用户
        $user = $this->where('username', $username)->first();
        if (!$user) {
            return ['status' => 0, 'msg' => 'user not exists'];
        }
        $hashed_password = $user->password;
        if (!Hash::check($password, $hashed_password)) {
            return ['status' => 0, 'msg' => 'invalid password'];
        }
//        将用户信息写入session();
        session()->put('username', $user->username);
        session()->put('user_id', $user->id);
        //dd(session()->all());
        return ['status' => 1, 'id' => $user->id];

    }

//    检测用户是否登陆
    public function is_log_in()
    {
        return is_log_in();
    }

//    登出api
    public function logout()
    {
//        session()->flush(); //清空所有

//        session()->put('username', null);
//        session()->put('user_id', null);

        //删除username
        session()->forget('username');
        session()->forget('user_id');
//        return redirect('/');
        return ['status' => 1];

//        $username = session()->pull('username');

//        可以一直嵌套下去
//        session()->set('person.name', 'xiaoming');
//        session()->set('person.friend.han.age', '20');
    }

    public function change_password()
    {
        if (!$this->is_log_in()) {
            return ['status' => 0, 'msg' => 'login required'];
        }
        if (!rq('old_password') || !rq('new_password')) {
            return ['status' => 0, 'msg' => 'old_password and new_password required'];
        }
        $user = $this->find(session('user_id'));

        if (!Hash:: check(rq('old_password'), $user->password)) {
            return ['status' => '0', 'msg' => 'invalid old_password'];
        }
        $user->password = bcrypt(rq('new_password'));
        return
            $user->save() ?
                ['status' => 1] :
                ['status' => 0, 'msg' => 'db update failed'];

    }

//    找回密码
    function reset_password()
    {
        if ($this->is_robot()) {
            return err('max frequency reached');
        }

        if (!rq('phone')) {
            return err('phone is required');
        }
        $user = $this->where('phone', rq('phone'))->first();
        if (!$user) {
            return err('invalid phone number');
        }

        //生成验证码；
        $captcha = $this->generate_captcha();
        $user->phone_captcha = $captcha;
        if ($user->save()) {
            $this->send_sms();
            $this->update_robot_time();
            return suc();
        } else {
            err('db update failed');
        }
    }


    public function send_sms()
    {
        return true;
    }

    public function validate_reset_password()
    {
        if ($this->is_robot(2)) {
            return err('max frequency reached');
        }
        if (!rq('phone') || !rq('phone_captcha') || !rq('new_password')) {
            return err('phone and new_password and phone_captcha required');
        }
        $user = $this->where([
            'phone' => rq('phone'),
            'phone_captcha' => rq('phone_captcha')
        ])->first();
        if (!$user) {
            return err('invalid phone or invalid phone_captcha');
        }
        $user->password = bcrypt(rq('new_password'));
        $this->update_robot_time();
        return $user->save() ?
            suc() :
            err('db update failed');
    }

    public function generate_captcha()
    {
        return rand(1000, 9999);
    }

    public function answer()
    {
        return $this
            ->belongsToMany('App\Answer')
            ->withPivot('vote')
            ->withTimestamps();
    }

    public function questions()
    {
        return $this
            ->belongsToMany('App\Questions')
            ->withPivot('vote')
            ->withTimestamps();
    }

    public function is_robot($time = 10)
    {
        if (!session('last_sms_time')) {
            return false;
        }
        $current_time = time();
//
        $last_action_time = session('$last_action_time');
        return ($current_time - $last_action_time > $time);
    }

    public function update_robot_time()
    {
        session()->set('$last_action_time', time());
    }

    public function read()
    {
        if (!rq('id')) {
            return err('id');
        }

        if (rq('id') === 'self') {
            if (!$this->is_log_in()) {
                return err('longin required');
            }
            $id = session('user_id');
        } else
            $id = rq('id');

        $get = ['id', 'username', 'avatar_url', 'intro'];
        $user = $this->find($id, $get);
        $data = $user->toArray();

        $answer_count = answer_ins()->where('user_id', rq('id'))->count();
        $question_count = question_ins()->where('user_id', rq('id'))->count();

//        $answer_count = $user->answers()->count();
//        $question_count = $user->questions()->count();

        $data['answer_count'] = $answer_count;
        $data['question_count'] = $question_count;
        return suc($data);
    }

    public function exists()
    {
        return suc(['count' => $this->where(rq())->count()]);
    }

}
