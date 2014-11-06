<?php
require_once(__DIR__ . '/../lib/vendor/autoload.php');

if(!isset($_SESSION['name'])) {
    if(!isset($_POST['password'])) {
        session_destroy();
        $_SESSION = array();
        \Bookshelf\Utility\User::showLoginForm();
        exit();
    }
    else {
        if(Bookshelf\Utility\User::isAuthenticated($_POST['name'], $_POST['password'])) {
            $_SESSION['name'] = $_POST['name'];
        }
        else {
            \Bookshelf\Utility\ErrorHandler::throwError('Wrong username or password.', \Bookshelf\Utility\ErrorLevel::ERROR);
            \Bookshelf\Utility\ErrorHandler::displayErrors();
            header('Location: index.php');
        }
    }
}
