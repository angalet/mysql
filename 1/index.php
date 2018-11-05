<html>
<head>
<meta charset="utf-8">
<style>
TABLE {
    border: 1px solid #ccc; 
}
 TD, TH {
    padding: 5px;
    border: 1px solid #ccc; 
    margin:0;
   }
</style>
<head>
<body>
<h1>Библиотека</h1>
<form method="GET">
    <input type="text" name="isbn" placeholder="по ISBN" value="<?php echo $_GET['isbn']; ?>">
    <input type="text" name="name" placeholder="Название" value="<?php echo $_GET['name']; ?>">
    <input type="text" name="author" placeholder="Автор" value="<?php echo $_GET['author']; ?>">
    <input type="submit" name="search" value="Поиск">
</form>
<?php


?>
<table width="100%" >
<tr>
    <th>Номер</th>
   	 <th>Имя</th>
   	 <th>Автор</th>
     <th>Год</th>
     <th>ISBN</th>
     <th>Жанр</th>
   </tr>
<?php
header('Content-Type: text/html; charset=UTF-8');
$pdo = new PDO("mysql:host=localhost;dbname=netology01","admin","1qa2ws3ed");
if (!isset($_GET['search']) or ($_GET['isbn']==NULL and $_GET['name']==NULL and $_GET['author']==NULL)){   
$sql="SELECT * FROM books";
foreach ($pdo->query($sql) as $row)
{
    echo "<tr>
    <td>".$row['id']."</td>
    <td>".$row['name']."</td>
    <td>".$row['author']."</td> 
    <td>".$row['year']."</td>
    <td>".$row['isbn']."</td>
    <td>".$row['genre']."</td>
    </tr>";
}
}
if (isset($_GET['search']) and ($_GET['isbn']!==NULL or $_GET['name']!==NULL or $_GET['author']!==NULL)){   
    echo "isbn ".$_GET['isbn']."<br>";
    if ($_GET['name']!==''){
    $from_get = 'name COLLATE utf8_general_ci LIKE \'%'.$_GET['name'].'%\'';
    echo "name".$_GET['name'];
    }
    if ($_GET['isbn']!==''){
        $from_get = 'isbn LIKE \'%'.$_GET['isbn'].'%\'';
        echo "isbn".$_GET['isbn'];
        }
    if ($_GET['author']!==''){
        $from_get = 'author LIKE \'%'.$_GET['author'].'%\'';
        echo "author".$_GET['author'];
        }   

    $sql="SELECT * FROM books WHERE  ".$from_get." ";
    echo $sql;
    foreach ($pdo->query($sql) as $row)
    {
        echo "<tr>
        <td>".$row['id']."</td>
        <td>".$row['name']."</td>
        <td>".$row['author']."</td> 
        <td>".$row['year']."</td>
        <td>".$row['isbn']."</td>
        <td>".$row['genre']."</td>
        </tr>";
    }
    }
?>
</table>
<body>
</html>