<?php

$current_theme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'dark';


$new_theme = ($current_theme === 'dark') ? 'light' : 'dark';

setcookie('theme', $new_theme, time() + (30 * 24 * 60 * 60), '/');

$redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
header('Location: ' . $redirect);
exit;
