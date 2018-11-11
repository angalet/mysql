
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Страница входа</title>
</head>
<body>
<?php
session_start();
if (!$_SESSION['NAME']) {
    echo $_SESSION['NAME']?>
    <form method="post">
        <p>Авторизоваться <input type="submit" name="auth" value="OK" /></p>
        <p><input type="text" name="login" value="" /> Логин</p>
        <p><input type="text" name="pass" value="" /> Пароль</p>
        <p>Зарегистрироваться <input type="submit" name="reg" value="OK" /></p>
    </form>
<?php }?>
</body>
</html>
<?php
$pdo = new PDO("mysql:host=localhost;dbname=netology01; charset=utf8","admin","1qa2ws3ed");
//$user_from_base = "SELECT id,login,password FROM user WHERE login= ?";

if (isset($_POST["auth"])){
if (!isset($_SESSION['NAME']) and isset($_SERVER['PHP_AUTH_USER'])) {
    echo $_SESSION['NAME']." на авторизацию<br>";
    $user_from_base = "SELECT id,login,password FROM user WHERE login= ?";
    //$sql = "INSERT INTO user (login, password) VALUES (:login, :password)";
    $stmt= $pdo->prepare($user_from_base);
    $stmt->execute([$_SERVER['PHP_AUTH_USER']]);
    $login = $stmt->fetch();
    echo "<pre>";
    print_r($login);
    print_r($_SERVER);
    print_r($_SESSION);
    echo "</pre>";
    if ($_SERVER['PHP_AUTH_USER'] and $_SERVER['PHP_AUTH_USER']==$login["login"] and $_SERVER['PHP_AUTH_PW']==$login["password"]){
        $_SESSION['NAME'] = $login["login"];
        $_SESSION['user_id'] = $login["id"];
        setcookie("user_name", $_SERVER['PHP_AUTH_USER']);
        setcookie("user_auth", "YES");
        echo "вы авторизовались ".$_SESSION['NAME'];
    }
} 
if (!isset($_SESSION['NAME'])) {
    header('WWW-Authenticate: Basic realm="admin"');
    header('HTTP/1.0 401 Unauthorized');
    echo "вы не авторизовались";
    exit;
}
}
/*if  ($_COOKIE['user_auth']=='YES') {
    if ($file[$_SERVER['PHP_AUTH_USER']] and $_SERVER['PHP_AUTH_PW']===$file[$_SERVER['PHP_AUTH_USER']]){
        header("Location: admin.php");
    }
}*/
if (isset($_POST["reg"])){
    echo $_POST["login"]."-".$_POST["pass"]."<br>";
    if ($_POST["login"] and $_POST["pass"]){
        echo $_POST["login"]." ".$_POST["pass"]."<br>";
        $data = [
            'login' => $_POST["login"],
            'password' => $_POST["pass"]
        ];
        print_r($data);
        $sql = "INSERT INTO user (login, password) VALUES (:login, :password)";
        $stmt= $pdo->prepare($sql);
        $stmt->execute($data);
    
    }
}
//echo $_SESSION['NAME']."<br>";
echo $_SESSION['user_id']." user_id<br>";
if ($_SESSION['NAME']){
    echo $_SESSION['NAME']." Вы авторизовались<br>";
    ?>
    <form method="post">
        <p><input type="text" name="task" value="" /> Введите название дела и нажмите добавить</p>
        <p>Добавить дело <input type="submit" name="addtask" value="OK" /></p>
    </form>
    <?php
    if (isset($_POST['addtask']) and $_POST['task']){
        $data = [
            'user_id' => $_SESSION['user_id'],
            'description' => $_POST["task"]
        ];
        $sql = "INSERT INTO task (user_id, description) VALUES (:user_id, :description)";
        $stmt= $pdo->prepare($sql);
        $stmt->execute($data);
    }
    echo "====================================<br>";
    ?>
    <form method='post'>
    <table width="40%" border="1" >
<tr>
    <th>удалить</th>
    <th>Дело</th>
   	<th>Когда</th>
    <th>выполнено/невыполнено</th>
   </tr>
   <?php
    $data = [
        'user_id' => $_SESSION['user_id']
    ];
    $sql = "SELECT id, description, date_added, is_done FROM task WHERE user_id=user_id ORDER BY date_added ";
    $stmt= $pdo->prepare($sql);
    $stmt->execute($data); //$_SESSION['user_id']
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //print_r($tasks);
    foreach ($tasks as $row){
        if ($row['is_done'] == 0) {
            $task_state = "не выполнено";
            $task_state_make = 1;
        }
        else {
            $task_state = "выполнено";
            $task_state_make = 0;
        }
        echo "<tr>
            <td><input type='checkbox' name='gelete_row[]' value=".$row['id']."> ".$row['id']."</td>
            <td>".$row['description']."</td>
            <td>".date("Y M d",strtotime($row['date_added']))."</td>
            <td><a href='/NET/mysql/2/?id=".$row['id']."&done=".$task_state_make."'>".$task_state."</a></td>
            </tr>";
    }
    ?>
    </table>
    <p><input type="submit" name="gelete_row_submit" value="OK" > удалить выбранное</p>
    </form>
    <?php
    if (isset($_POST['gelete_row_submit']) and isset($_POST['gelete_row']) ){
        print_r($_POST['gelete_row']);
        $id_task - NULL;
        $id_task = implode(',',$_POST['gelete_row']);
        echo $id_task;
        $data = [
            //'id_task' => $id_task,
            'user_id' => $_SESSION['user_id']
        ];
        $sql = "DELETE FROM task WHERE user_id=:user_id AND id IN (".$id_task.")";
        //DELETE FROM task WHERE user_id= ... AND id=... LIMIT 1 не стал так телать, хотелось удалять сразу
        //несколько задач
        //$sql = "DELETE FROM task WHERE id IN (:$id_task)";
        $stmt= $pdo->prepare($sql);
        $stmt->execute($data); //тут почему то не работает через $data это  выражение id IN (:$id_task)
        header("Location: /NET/mysql/2/");
    }
    if (isset($_GET['done'])){
        echo $_GET['done']."<br>";
        //UPDATE `task` SET `is_done` = '1' WHERE `task`.`id` = 23;
        //UPDATE task SET is_done=... WHERE user_id= ... AND id=... LIMIT 1
        $data = [
            'id' => $_GET['id'],
            'is_done' => $_GET['done'],
            'user_id' => $_SESSION['user_id']
        ];
        $sql = "UPDATE task SET is_done=:is_done WHERE user_id=:user_id AND id=:id LIMIT 1";
        $stmt= $pdo->prepare($sql);
        $stmt->execute($data);
        header("Location: /NET/mysql/2/");

    }


}



?>