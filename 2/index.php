
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
$user_from_base = "SELECT id,login,password FROM user WHERE login= ?";
session_start();
if (isset($_POST["auth"])){
if (!isset($_SESSION['NAME']) and isset($_SERVER['PHP_AUTH_USER'])) {
    $user_from_base = "SELECT id,login FROM user WHERE login= ?";
    //$sql = "INSERT INTO user (login, password) VALUES (:login, :password)";
    $stmt= $pdo->prepare($user_from_base);
    $stmt->execute([$_SERVER['PHP_AUTH_USER']]);
    $login = $stmt->fetch();
    //print_r($login)."<br>";
    if ($_SERVER['PHP_AUTH_USER'] and $_SERVER['PHP_AUTH_USER']==$login["login"] and $_SERVER['PHP_AUTH_PWD']==$login["password"]){
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
echo $_SESSION['NAME'];
echo $_SESSION['user_id'];
if ($_SESSION['NAME']){
    echo " Вы авторизовались<br>";
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

}



?>