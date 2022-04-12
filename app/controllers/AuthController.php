<?php
namespace App\controllers;
use PDO;
use App\QueryBuilder;
use League\Plates\Engine;
use SimpleMail;


class AuthController {
   
    private $db;
    private $auth;
    private $pdo;
    private $id;
    


    function __construct(QueryBuilder $qb, PDO $pdo, \Delight\Auth\Auth $auth)
    {
        $this->db = $qb;
        $this->pdo = $pdo;
        $this->auth = $auth;
        //получаем цифровой id из урла
        // я лучше не нашел решение, так как была проблема с передачей $vars в php di
        $url = explode('/', $_SERVER['REQUEST_URI']);
        if(isset($url[2]))
        {
            $this->id = $url[2];
        }
    }
    /* 
     * Action - при помощи которого ADMIN создает пользователя на страницу /create_user
     * Вносим данные в основную таблицу users при помощи компонента Auth и в доп. таблицу user, которые мы создали сами.
     * Переходим по ссылке для подтверждения email
     * Выводим Flash сообщение о регистрации
     * @param integer $id - цифра из массива $_GET
     */
    public function createuser(){
        try {
            $userId = $this->auth->register($_POST['email'], $_POST['password'], $_POST['name'], function ($selector, $token) {
                $url = '/verification?selector=' . \urlencode($selector) . '&token=' . \urlencode($token);
                echo 'Перейти  <a href="' . $url . '">'.'по ссылке</a> для подтверждения вашего email';
            });
            $this->db->insertOne(
                [
                    "user_id" => $userId,
                    "name" => $_POST['name'],
                    "company" => $_POST['company'],
                    "phone" => $_POST['phone'],
                    "adress" => $_POST['adress'],
                    "email" => $_POST['email'],
                    "vk" => $_POST['vk'],
                    "telegram" => $_POST['telegram'],
                    "instagram" => $_POST['instagram'],
                ],
                'user');
                echo 'We have signed up a new user with the ID ' . $userId;
        }
        catch (\Delight\Auth\InvalidEmailException $e) {
            die('Invalid email address');
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            die('Invalid password');
        }
        catch (\Delight\Auth\UserAlreadyExistsException $e) {
            die('User already exists');
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            die('Too many requests');
        }
    }

    /* 
     * Action - регистрации нового пользователя на главной странице
     * Вносим данные в основную таблицу users при помощи компонента Auth и в доп. таблицу user, которые мы создали сами.
     * После регистрации отправлям письмо для верификации email
     * Переадресация на страницу /login
     * Выводим Flash сообщение о регистрации
     */

    public function signup()
    {   
        try {
            $userId = $this->auth->register($_POST['email'], $_POST['password'], $_POST['name'], function ($selector, $token) {
                $url = '/verification?selector=' . \urlencode($selector) . '&token=' . \urlencode($token);
                echo 'Перейти  <a href="' . $url . '">'.'по ссылке</a> для подтверждения вашего email';
                
            });   
            
            SimpleMail::make()
            ->setTo($_POST['email'], $_POST['name'])
            ->setFrom('doodee@doodee.ru', 'Doodee.ru')
            ->setSubject('Подтверждение вашего email')
            ->setMessage('Перейдите  <a href="http://alla.doodee.ru' . $url . '">'.'по ссылке</a> для подтверждения вашего email')
            ->setHtml()
            ->send();

            $this->db->insertOne(
                [
                    "user_id" => $userId,
                    "name" => $_POST['name'],
                    "email" => $_POST['email'],
                ],
                'user');
                echo 'We have signed up a new user with the ID ' . $userId;
        }
        catch (\Delight\Auth\InvalidEmailException $e) {
            die('Invalid email address');
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            die('Invalid password');
        }
        catch (\Delight\Auth\UserAlreadyExistsException $e) {
            die('User already exists');
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            die('Too many requests');
        }
    }

    /* 
     * Controller подтверждаем email.
     * из массива $_GET - принимает selectror и token.  
     * После регистрации отправлям письмо для верификации email и переадресовываем на страницу /login
     * Выводим сообщение что email подтвержден
     * @return bool
     */
    public function verification()
    {
        try {
            $this->auth->confirmEmail($_GET['selector'], $_GET['token']);
            flash()->message('Email был подтвержден');
            header("Location: /login");
        }
        catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
            die('Invalid token');
        }
        catch (\Delight\Auth\TokenExpiredException $e) {
            die('Token expired');
        }
        catch (\Delight\Auth\UserAlreadyExistsException $e) {
            die('Email address already exists');
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            die('Too many requests');
        }
    }
    /* 
     * Action - вход на сайте по email и паролю
     * @param array $_POST - принимает из формы.  
     * После входа переадресовываем на страницу профиля
     * 
     */
    public function signin()
    {
        if (isset($_POST['remember']))
        {
            $rememberDuration = (int) (60 * 60 * 24 * 365.25);
        }
        else {
            $rememberDuration = null;
        }
        try {
            $this->auth->login($_POST['email'], $_POST['password'], $rememberDuration);
            flash()->message('Вход успешно совершен');
            header("Location: /profile/" . $this->auth->getUserId());
        }
        catch (\Delight\Auth\InvalidEmailException $e) {
            die('Wrong email address');
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            die('Wrong password');
        }
        catch (\Delight\Auth\EmailNotVerifiedException $e) {
            die('Email not verified');
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            die('Too many requests');
        }
    }

    /* 
     * Action - выход на сайте
     * Переадресация на страницу /login
     * Вывод сообщения об успешном выходе
     */
    public function logout()
    {
        $this->auth->logOut();
        try {
            $this->auth->logOutEverywhereElse();
        }
        catch (\Delight\Auth\NotLoggedInException $e) {
            flash()->message('Выход успешно совершен');
            header("Location: /login");
            die('Not logged in');
        }
        try {
            $this->auth->logOutEverywhere();
        }
        catch (\Delight\Auth\NotLoggedInException $e) {
            flash()->message('Выход успешно совершен');
            header("Location: /login");
            die('Not logged in');
        }
        flash()->message('Выход успешно совершен');
        header("Location: /login");

    }

     /* 
     * Action - смены пароля
     * @param array $_POST - принимает из формы.  
     * После смены переадресовываем на страницу профиля
     * Выводим сообщение об успешном изменении пароля
     */
    public function changePassword()
    {
        
        try {
            $this->auth->changePassword($_POST['oldPassword'], $_POST['newPassword']);
            flash()->message('Пароль успешно изменен');
            header("Location: /profile/" . $this->auth->getUserId());
            
        }
        catch (\Delight\Auth\NotLoggedInException $e) {
            die('Not logged in');
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            die('Invalid password(s)');
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            die('Too many requests');
        }
        flash()->message('Пароль успешно изменен');
        header("Location: /login" . $this->auth->getUserId());
    }
    /* 
     * Action - удаление пользователя из таблицы users
     * Удалять может только ADMIN
     * @param int $id принимает из $_GET;
     * После удаления переадресовываем на страницу всех пользователей
     * Выводим сообщение об успешном удалении.
     */
    public function deleteUser()
    {
        try {
            $this->auth->admin()->deleteUserById($this->id);
        }
        catch (\Delight\Auth\UnknownIdException $e) {
            die('Unknown ID');
        }
        $this->db->deleteOne('user', $this->id);
        flash()->message('Пользователь удален' );
        header("Location: /users");
    }

    public static function makeRole($userId)
    {
        try {
            $this->auth->admin()->addRoleForUserById($userId, \Delight\Auth\Role::ADMIN);
        }
        catch (\Delight\Auth\UnknownIdException $e) {
            die('Unknown user ID');
        }
    }
}
