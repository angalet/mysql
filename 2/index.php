
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

if (isset($_POST["auth"])){
if (!isset($_SESSION['NAME']) and isset($_SERVER['PHP_AUTH_USER'])) {
    echo $_SESSION['NAME']." на авторизацию<br>";
    $user_from_base = "SELECT id,login,password FROM user WHERE login= ?";
    $stmt= $pdo->prepare($user_from_base);
    $stmt->execute([$_SERVER['PHP_AUTH_USER']]);
    $login = $stmt->fetch();
    echo "<pre>";
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
if ($_SESSION['NAME']){
    echo " Вы авторизовались, <b>".$_SESSION['NAME']."</b>!<br>";
    ?>
    <form method="post">
        <p><input type="text" name="task" value="" /> Введите название дела и нажмите добавить</p>
        <p>Добавить дело <input type="submit" name="addtask" value="OK" /></p>
    </form>
    <?php
    if (isset($_POST['addtask']) and $_POST['task']){
        $data = [
            'user_id' => $_SESSION['user_id'],
            'description' => $_POST["task"],
            'assigned_user_id' => $_SESSION['user_id']
        ];
        $sql = "INSERT INTO task (user_id, description, assigned_user_id) VALUES (:user_id, :description, :assigned_user_id)";
        $stmt= $pdo->prepare($sql);
        $stmt->execute($data);
    }
    echo "<h1>Список задач пользователя ".$_SESSION['NAME']."</h1><br>";
    ?>
    <form method='post'>
    <table width="80%" border="1" >
<tr>
    <th width='30'>удалить</th>
    <th>Дело</th>
   	<th>Когда</th>
    <th>выполнено/<br>невыполнено</th>
    <th>Исполнитель</th>
   </tr>
   <?php
    $data = [
        'user_id' => $_SESSION['user_id']
    ];
    $sql = "SELECT id, description, date_added, is_done, assigned_user_id FROM task WHERE user_id=:user_id ORDER BY date_added ";
    $stmt= $pdo->prepare($sql);
    $stmt->execute($data); 
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $users_from_base = "SELECT login, id FROM user ";
    $stmt= $pdo->prepare($users_from_base);
    $stmt->execute();
    $assignedUserList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($tasks as $row){
        if ($row['is_done'] == 0) {
            $task_state = "не выполнено";
            $task_state_make = 1;
        }
        else {
            $task_state = "выполнено";
            $task_state_make = 0;
        }
        ?><tr>
            <td><input type='checkbox' name='gelete_row[]' value="<?php echo $row['id'] ?>" /></td>
            <td><?php echo  $row['id']." ".$row['description'] ?></td>
            <td><?php echo date("Y M d",strtotime($row['date_added'])) ?></td>
            <td><a href='/NET/mysql/2/?id=<?php echo $row['id'] ?>&done=<?php echo $task_state_make ?>'><?php echo $task_state ?></a></td>
            <td><input name='task_id[]' type='hidden' value="<?php echo $row['id'] ?>"> 
            <select name='assigned_user_id[<?php echo $row['id'] ?>]' >
            <?php foreach ($assignedUserList as $assignedUser){ ?>
              <option <?php if ($row['assigned_user_id'] == $assignedUser['id']){?>
                selected <?php } ?> value=<?php echo $assignedUser['id'] ?> >
                <?php echo $assignedUser['login'] ?>
              </option>
              <?php } ?>
            </select></td>
            </tr>
            <?php
    }
    $data = [
        'user_id' => $_SESSION['user_id']
    ];
    $sql = "SELECT t.id, t.description, date_added, is_done, assigned_user_id, t.user_id, u.login as name1, ut.login  FROM task t 
    JOIN user u ON u.id=t.assigned_user_id 
    JOIN user ut ON ut.id=t.user_id
    WHERE t.user_id <>:user_id and t.assigned_user_id = :user_id";
    $stmt= $pdo->prepare($sql);
    $stmt->execute($data); //$_SESSION['user_id']
    $assigned_tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($assigned_tasks as $assigned_task){
        if ($assigned_task['is_done'] == 0) {
            $task_state = "не выполнено";
            $task_state_make = 1;
        }
        else {
            $task_state = "выполнено";
            $task_state_make = 0;
        }
    ?><tr>
            <td><input type='checkbox' disabled   /></td>
            <td><?php echo $assigned_task['id']." ".$assigned_task['description'] ?></td>
            <td><?php echo date("Y M d",strtotime($assigned_task['date_added'])) ?></td>
            <td><a href='/NET/mysql/2/?id=<?php echo $assigned_task['id'] ?>&done=<?php echo $task_state_make ?>&assigned_=1'><?php echo $task_state ?></a></td>
            <td><?php echo $assigned_task['login']."->".$assigned_task['name1'] ?></td>
            </tr>
            <?php
    }
    $data = [
        'user_id' => $_SESSION['user_id']
    ];
    $user_count_from_base = "SELECT count(*) FROM task t WHERE t.user_id =:user_id  OR t.assigned_user_id = :user_id";
    $stmt= $pdo->prepare($user_count_from_base);
    $stmt->execute($data);
    $user_count  = $stmt->fetch();
    ?>
    <tr>    
            <td></td>
            <td>Количество задач</td>
            <td><?php echo $user_count[0] ?></td>
    </tr>
    </table>
    <p><input type="submit" name="gelete_row_submit" value="OK" > удалить выбранное</p>
    <p><input type="submit" name="deleg_to_user" value="OK" > делегировать выбранное</p>
    </form>
    <?php
    if (isset($_POST['gelete_row_submit']) and isset($_POST['gelete_row']) ){
        print_r($_POST['gelete_row']);
        $id_task = NULL;
        $id_task = implode(',',$_POST['gelete_row']);
        echo $id_task;
        $data = [
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
    if (isset($_POST['deleg_to_user'])){  //and isset($_POST['assigned_user_id[]']
        print_r($_POST['assigned_user_id']);
        print_r($_POST['task_id']);
        $assigned_users_id_from_form =$_POST['assigned_user_id'];
        foreach ($assigned_users_id_from_form as $key => $assigned_user_id_from_form){
            $data = [
                'user_id' => $_SESSION['user_id'],
                'id' => $key,
                'assigned_user_id' => $assigned_user_id_from_form
            ];
            $sql = "UPDATE task SET assigned_user_id=:assigned_user_id WHERE id=:id AND user_id=:user_id";
            $stmt= $pdo->prepare($sql);
            $stmt->execute($data);
            
        }
        header("Location: /NET/mysql/2/");
    }
    if (isset($_GET['done'])){
        echo $_GET['done']."<br>";
        print_r($_GET);
        if ($_GET['assigned_']==1) $user_id_or_assig = 'assigned_user_id';
        else $user_id_or_assig = 'user_id';
        $data = [
            'id' => $_GET['id'],
            'is_done' => $_GET['done'],
            $user_id_or_assig => $_SESSION['user_id']
        ];
        $sql = "UPDATE task SET is_done=:is_done WHERE ".$user_id_or_assig."=:".$user_id_or_assig." AND id=:id LIMIT 1";
        $stmt= $pdo->prepare($sql);
        $stmt->execute($data);
        header("Location: /NET/mysql/2/");
    }
}
?>