<?php

class UsersController
{
    public function __construct()
    {
        if (!auth()->check()) {
            redirect('/login');
        }
    }

    public function addUserForm()
    {
        render('add_user');
    }

    public function addUser()
    {
        $db = Base::getInstance()->get('DB');
        $user = new UsersRepository($db);
        if ($user->exists($_POST['username'])) {
            Flash::set('danger', 'نام کاربری واردشده در حال حاضر موجود است.');
            redirect('/add_user');
        } elseif ($_POST['password'] != $_POST['password_confirm']) {
            Flash::set('danger', 'رمز عبور واردشده و تکرار آن با یکدیگر مطابقت ندارند.');
            redirect('/add_user');
        } else {
            $user->add($_POST['username'], $_POST['password']);
            Flash::set('success', 'کاربر با موفقیت اضافه شد.');
            redirect('/add_user');
        }
    }

    public function changePasswordForm()
    {
        render('change_password');
    }

    public function changePassword()
    {
        $db = Base::getInstance()->get('DB');
        $user = new UsersRepository($db);
        $username = auth()->user();
        if ($_POST['password'] != $_POST['password_confirm']) {
            Flash::set('danger', 'رمز عبور واردشده و تکرار آن با یکدیگر مطابقت ندارند.');
            redirect('/change_password');
        } else {
            $user->changePassword($username, $_POST['password']);
            Flash::set('success', 'رمز عبور با موفقیت تغییر یافت.');
            redirect('/change_password');
        }
    }
}
