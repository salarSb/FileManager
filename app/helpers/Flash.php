<?php

class Flash
{
    public static function set($type, $message)
    {
        $_SESSION['flash'] = compact('type', 'message');
    }

    public static function get()
    {
        $flash = null;
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            $_SESSION['flash'] = null;
        }

        return $flash;
    }
}