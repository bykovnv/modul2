<?php
namespace App\controllers;
use PDO;
use App\QueryBuilder;
use League\Plates\Engine;
use App\controllers\AuthController; 
use SimpleMail;
use Aura\SqlQuery\QueryFactory;
use JasonGrimes\Paginator;
use DI\ContainerBuilder;

class UserController {

    /* 
     * Controller вывода пользователей на страницу /edit/$id
     * 
     * @param $templates - создаем объект для вывода views 
     * @param $db - создаем объект из нашего класса QueryBuilder для работы с моделью 
     * @param pdo - создаем объект PDO подключаемся к базе
     * @param auth - создаем объект для работы с пользователями на сайте. регистрация, логин, роли и тд 
     * @param int id - распарсенный url, это ID нашего пользователя
     */
    private $templates;
    private $db;
    private $pdo;
    private $auth;
    private $id;


    function __construct(Engine $templates, QueryBuilder $qb, PDO $pdo, \Delight\Auth\Auth $auth)
    {
        
        $this->templates = $templates;
        $this->db = $qb;
        $this->pdo = $pdo;
        $this->auth = $auth;  
        
        $url = explode('/', $_SERVER['REQUEST_URI']);
        if(isset($url[2]))
        {
            $this->id = $url[2];
        }
     }

     /* 
     * Controller вывода пользователей на страницу /users
     * 
     * @param vars 
     */
     public function users()
     {
        function canEditUsers(\Delight\Auth\Auth $auth) {
            return $auth->hasAnyRole(
                \Delight\Auth\Role::MODERATOR,
                \Delight\Auth\Role::SUPER_MODERATOR,
                \Delight\Auth\Role::ADMIN,

            );
        }

         echo $this->templates->render('users', [
             "users" => $this->db->getAll('user'), 
             "auth" => $this->auth,
             "canEditUsers" => canEditUsers($this->auth),
         ]);
     }

     /* 
     * Controller вывода пользователей на страницу /create_user
     * 
     * @param vars 
     */
    public function create()
    {
        function canEditUsers(\Delight\Auth\Auth $auth) {
            return $auth->hasAnyRole(
                \Delight\Auth\Role::MODERATOR,
                \Delight\Auth\Role::SUPER_MODERATOR,
                \Delight\Auth\Role::ADMIN,

            );
        }

        echo $this->templates->render('create_user', [
            'name' => 'Jonathan',
            "users" => $this->db->getAll('user'), 
            "auth" => $this->auth,
            "canEditUsers" => canEditUsers($this->auth),
        ]);
    }

    /*
    * Action добавления пользователя из формы получаемые методом POST
    @ Переадресовавает на страницу create_user  
    */

    public function formInsert()
    {   
        $this->db->insertOne(
            [
                "name" => $_POST['name'],
                "company" => $_POST['company'],
                "phone" => $_POST['phone'],
                "email" => $_POST['email'],
                "adress" => $_POST['adress'],
                "image" => $_POST['image'],
                //"role" => $_POST['role'],
                "pass" => $_POST['pass'],
                //"status" => $_POST['status'],
                "vk" => $_POST['vk'],
                "telegram" => $_POST['telegram'],
                "instagram" => $_POST['instagram']
            ],
            
            'user');
        header('Location: /create_user');
    }


    /* 
     * Controller вывода пользователей на страницу /edit/$id
     * 
     * @param integer $id - цифра из массива $_GET
     */
    public function edit()
    {   
        
        $user = $this->db->getOne('user', $this->id);
        echo $this->templates->render('edit', [
            "user" => $user, 
            "title" => "Редактировать", 
            "id" => $this->id,
            "UserId" => $this->auth->getUserId(), 
            "email" => $this->auth->getEmail(), 
            "username" => $this->auth->getUsername(), 
            "auth" => $this->auth,
            ] );
        
    }

    /*
    * Action изменения данных пользователя из формы получаемые методом POST
    @param string $table - название таблицы 
    @param array $_POST - данные из таблицы из глобального массива $POST
    @param int $id - id пользователя для которого меняем данные
    @return void   
    */
    public function formUpdate()
    {   
        $v = new \Valitron\Validator($_POST);
        $rules = [
            'required' => ['name', 'company' , 'phone', 'adress', ],
            'integer' => ['phone']
        ];
        $data = [
            "id" => $_POST['id'],
            "name" => $_POST['name'],
            "company" => $_POST['company'],
            "phone" => $_POST['phone'],
            "adress" => $_POST['adress'],
        ];
        
        $v->rules($rules);
        if ($v->validate()) {
            $this->db->update('user', $data, $data['id']);
            flash()->message('Данные обновлены');
            header("Location: /profile/" . $data['id']);
        }
        else {
            flash()->error('Поля должны быть заполнены');
            header("Location: /edit/" . $data['id']);
        }
    }
    /* 
     * Controller вывода пользователей на страницу /security/$id
     * 
     * @param integer $id - цифра из массива $_GET
     */
    public function security()
    {   
        
        $user = $this->db->getOne('user', $this->id);
        echo $this->templates->render('security', [
            "user" => $user, 
            "title" => "Редактировать", 
            "id" => $this->id,
            "auth" => $this->auth,
        ]);
    }

    /* 
     * Action для обновления паролей  /security/$id
     *  старый
     * 
     */
    public function securityUpdate(){
        
        $email = $_POST['email'];
        $pasw = $_POST['password'];
        $passConfirm = $_POST['passwordConfirm'];
        $password = password_hash($pasw, PASSWORD_DEFAULT);
        
        $data = [
            "id" => $_POST['id'],
            "email" => $email,
            "pass" => $password,
        ];
        if($pasw !== $passConfirm){
            flash()->error(['Пароли должны совпадать']);
            header("Location: /security/" . $data['id']);
            die;
        }
        else {
            $this->db->update('user', $data, $data['id']);
            flash()->success(['Данные обновленны']);
            header("Location: /profile/" . $data['id']);
        }
    }

    /* 
     * Controller вывода пользователей на страницу /media/$id
     * 
     * @param integer $id - цифра из массива $_GET
     */
    public function media()
    {
        
        $user = $this->db->getOne('user', $this->id);
        echo $this->templates->render('media', [
            "user" => $user, 
            "title" => "Редактировать", 
            "id" => $this->id,
            "auth" => $this->auth,
        ]);
 
    }

    /*
    * Action который меняет аватарку пользователя
    @param int $id - id пользователя
    @param string name - название загружаемой картинки, делаем ее уникальной при помощи функции uniqid
    @param string tpnName - временная папка загружаемой картинки
    @param string $path  путь куда загружаются новые аватарки
    @data array $data - для передачи в метод update для смены аватарки
    */

    public function mediaUpdate()
    {
        $id = $_POST['id'];
        $name = uniqid() . "." . $_FILES['image']['name']; 
        $tmpName = $_FILES['image']['tmp_name'];
        $path = "img/demo/avatars/";
        move_uploaded_file($tmpName, $path . $name); 

        $data = [
            "image" => $name,
        ];
        $this->db->update('user', $data, $id);  
        flash()->info(['Аватар обновлен']);
        header("Location: /users");         
    }  

    /* 
     * Controller вывода пользователей на страницу /login
     * 
     * @param integer $id - цифра из массива $_GET
     */

    public function login()
    {
        
        echo $this->templates->render('login', [
            'name' => 'Jonathan',
            "auth" => $this->auth
        ]);
    }

    /* 
     * Controller вывода пользователей на страницу /profile/$id
     * 
     * @param integer $id - цифра из массива $_GET
     */
    public function profile()
    {   
        function canEditUsers(\Delight\Auth\Auth $auth) {
            return $auth->hasAnyRole(
                \Delight\Auth\Role::MODERATOR,
                \Delight\Auth\Role::SUPER_MODERATOR,
                \Delight\Auth\Role::ADMIN,

            );
        }

        echo $this->templates->render('profile', 
        [
            "user" => $this->db->getOne('user', $this->id), 
            "title" => "Редактировать", 
            "id" => $this->auth->getUserId(), 
            "email" => $this->auth->getEmail(), 
            "username" => $this->auth->getUsername(), 
            "auth" => $this->auth,
            "canEditUsers" => canEditUsers($this->auth),
        ]);
    }

    /* 
     * Controller вывода пользователей на страницу /register
     * 
     * 
     */
    public function register()
    {
        echo $this->templates->render('register', [
            "title" => "Редактировать", 
            "id" => $this->auth->getUserId(), 
            "email" => $this->auth->getEmail(), 
            "username" => $this->auth->getUsername(), 
            "auth" => $this->auth
        ]);
    }

    /* 
     * Controller вывода пользователей на страницу /status/$id
     * 
     * @param integer $id - цифра из массива $_GET
     */
    public function status()
    {   

        echo $this->templates->render('status', [
            "user" => $this->db->getOne('user', $this->id), 
            "auth" => $this->auth,
            "id" => $this->auth->getUserId(),
        ]);
    }

    /* 
     * Action меняет статус на странице /status/$id
     */
    
    public function statusEdit()
    {
        $data = [
            "status" => $_POST['status'],
        ];
        $this->db->update('user', $data, $_POST['id']);
        flash()->info(['Статус изменен']);
        header("Location: /profile/" . $_POST['id']);
    }


    public function paginator2() {
        
        $result = $this->db->getAll('posts');

        echo $this->templates->render('paginator', [
            "posts" => $result,  
        ]);
    }

    public function paginator1() {
        
        $result = $this->db->getAll('posts');
        $queryFactory = new QueryFactory('mysql');
        $select = $queryFactory->newSelect();
        $select->cols(['*'])
        ->from('posts')
        ->setPaging(3)
        ->page($_GET['page'] ?? 1);

        $sth = $this->pdo->prepare($select->getStatement());

        $sth->execute($select->getBindValues());
        $items = $sth->fetchAll(PDO::FETCH_ASSOC);

        $itemsPerPage = 3;
        $currentPage = $_GET['page'] ?? 1;
        $urlPattern = '?page=(:num)';

        $paginator = new Paginator($result, $itemsPerPage, $currentPage, $urlPattern);

        echo $this->templates->render('paginator', [

            "posts" => $result,  
        ]);
    }

    
    

}
 