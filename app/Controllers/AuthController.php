<?php

class AuthController
{
    public function __construct()
    {
        if (auth()->check()) {
            redirect('/');
        }
    }

    public function loginForm()
    {
        render('login');
    }

    public function login()
    {
        $db = Base::getInstance()->get('DB');
        $user = new UsersRepository($db);
        $token = new JWT;
        if ($user->exists($_POST['username']) && password_verify($_POST['password'], $user->getPassword($_POST['username']))) {
            if ($_POST['remember']) {
                $payload = [
                    'user' => $_POST['username'],
                    'exp' => time() + Base::getInstance()->get('EXTENDED_EXP')
                ];
                setcookie('authorization', $token->encode($payload), time() + Base::getInstance()->get('EXTENDED_EXP'));
            } else {
                $payload = [
                    'user' => $_POST['username'],
                    'exp' => time() + Base::getInstance()->get('DEFAULT_EXP')
                ];
                setcookie('authorization', $token->encode($payload), time() + Base::getInstance()->get('DEFAULT_EXP'));
            }
            Flash::set('success', 'شما با موفقیت وارد شدید.');
            redirect('/');
        } else {
            Flash::set('danger', 'نام کاربری یا رمز عبور نادرست است.');
            redirect('/login');
        }
    }

    public function logout()
    {
        setcookie('authorization', null, -1);
        redirect('/login');
    }
}
