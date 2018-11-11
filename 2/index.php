
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Страница входа</title>
</head>
<body>
    <form method="post">
        <p>Авторизоваться <input type="submit" name="auth" value="OK" /></p>
        <p><input type="text" name="login" value="" /> Логин</p>
        <p><input type="text" name="pass" value="" /> Пароль</p>
        <p>Зарегистрироваться <input type="submit" name="reg" value="OK" /></p>
    </form>
</body>
</html>
<?php
$pdo = new PDO("mysql:host=localhost;dbname=netology01; charset=utf8","admin","1qa2ws3ed");
//$user_from_base = "SELECT id,login,password FROM user WHERE login= ?";
session_start();
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
        <p><input type="text" name="task" value="" /> Дело</p>
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
   </tr>
   <?php
    $data = [
        'user_id' => $_SESSION['user_id']
    ];
    $sql = "SELECT id, description, date_added FROM task WHERE user_id=user_id ORDER BY date_added ";
    $stmt= $pdo->prepare($sql);
    $stmt->execute($data); //$_SESSION['user_id']
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //print_r($tasks);
    foreach ($tasks as $row){
        echo "<tr>
            <td><input type='checkbox' name='gelete_row[]' value=".$row['id']."> ".$row['id']."</td>
            <td>".$row['description']."</td>
            <td>".date("Y M d",strtotime($row['date_added']))."</td>
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
            'id_task' => $id_task
        ];
        $sql = "DELETE FROM task WHERE id IN (".$id_task.")";
        //$sql = "DELETE FROM task WHERE id IN (:$id_task)";
        echo $sql."<br>";
        $stmt= $pdo->prepare($sql);
        $stmt->execute(); //тут почему то не работает через $data
        echo $sql;

    }


}



?>