<?php
namespace controllers;

use models\Admin;

class LoginController {

    public function login(){
        view('login.login');
    }

    public function dologin(){

        $username = $_POST['username'];
        $password = $_POST['password'];

        $model = new Admin;
        
        try {

            $model->login($username,$password);
            redirect('/');
        }catch(\Exception $e) {

            redirect('/login/login');
        }
    }

    // 退出
    public function logout(){

        $model = new Admin;
        $model->logout();
        redirect('/login/login');
    }
}