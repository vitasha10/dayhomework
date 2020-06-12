<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Домашка</title>
    <link href="https://fonts.googleapis.com/css?family=Didact+Gothic&display=swap" rel="stylesheet"> <link href="https://fonts.googleapis.com/css?family=Comfortaa:300&display=swap" rel="stylesheet"><style>
        body{
            height:auto;
            min-height:100vh;
            background: #54F9F5;
            background: -webkit-linear-gradient(top left, #54F9F5, #F75BFF) no-repeat;
            background: -moz-linear-gradient(top left, #54F9F5, #F75BFF) no-repeat;
            background: linear-gradient(to bottom right, #54F9F5, #F75BFF) no-repeat;
        }
        .photo{
            height:auto;
            width:60vw;
        }
        *{
            font-family: 'Didact Gothic', sans-serif;
            font-size:3vh;
            
        }
        a{
            display:block;
            text-decoration:none;
            link-style:none;
            color:blue;
            line-height:10vh;
            font-size:5vh;
            text-align:center;
        }
        h1{
            line-height:10vh;
            font-size:5vh;
            display:block;
            text-align:center;
        }
        table{
            width:100%;
        }
        .table{
            display:block;
            width:80vw;
            margin:0 auto;
            text-align:center;
            border-color:blue;
        }
        .exit{
            position:absolute;
            z-index:2;
            font-size:3vh;
            margin-left:5vw;
        }
        form{
            text-align:center;
        }
        input{
            background: -webkit-linear-gradient(top left, #54F9F5, #F75BFF) no-repeat;
            background: -moz-linear-gradient(top left, #54F9F5, #F75BFF) no-repeat;
            background: linear-gradient(to bottom right, #54F9F5, #F75BFF) no-repeat;
        
        }
        .like{
            height:5vh;
            width:5vh;
        }
        @media screen and (min-width: 250px) and (max-width: 1300px){     
            .exit{
                position:relative;
                display:block;
                font-size:3vh;
                margin-left:5vw;
            } 
        }
    </style>
</head>
<body>
<?php
$host = '';  // Хост, у нас все локально
$user = '';    // Имя созданного вами пользователя
$pass = ''; // Установленный вами пароль пользователю
$db_name = '';   // Имя базы данных
$link = mysqli_connect($host, $user, $pass, $db_name); // Соединяемся с базой

// Ругаемся, если соединение установить не удалось
if (!$link) {
  echo 'Не могу соединиться с БД. Код ошибки: ' . mysqli_connect_errno() . ', ошибка: ' . mysqli_connect_error();
  exit;
}
if(isset($_GET['dell'])){
    $sql = mysqli_query($link, "DELETE FROM raspisanie WHERE id={$_GET['dell']}");
}
$date=date("d-m");

$todate = substr(date('d-m', strtotime($order->date.'1 day')),0,10);
if(isset($_POST['like'])){
    $sql = mysqli_query($link, "INSERT INTO `likes` (`date`) VALUES ('{$_POST['like']}')");
}

if(isset($_POST["newgdz"])){
    $gdz = $_POST['gdz'];
    if($_FILES['inputfile']['name']!=''){
        $destiation_dir = dirname(__FILE__) .'/files/'.$_FILES['inputfile']['name']; // Директория для размещения файла
        move_uploaded_file($_FILES['inputfile']['tmp_name'], $destiation_dir ); // Перемещаем файл в желаемую директорию
        $gdz = $_POST['gdz']."<br><img class=\'photo\' src=\'http://robotsandfuture.ru/dayhomework/files/{$_FILES['inputfile']['name']}\'>";
    }
    $sql = mysqli_query($link, "INSERT INTO `gdz` (`date`, `predmet`, `gdz`) VALUES ('{$_POST['date']}', '{$_POST['predmet']}', '{$gdz}')");
}
if(isset($_POST["newras"])){
    $sql = mysqli_query($link, "INSERT INTO `raspisanie` (`date`, `number`, `predmet`, `dz`) VALUES ('{$_POST['date']}', '{$_POST['number']}', '{$_POST['predmet']}', '{$_POST['dz']}')");
}

if(isset($_POST['day'])){
    $day = $_POST['day'];
    $day = sprintf("%02d", $day);
    $month = $_POST['month'];
    $month = sprintf("%02d", $month);
    echo "<meta http-equiv='refresh' content='0;URL=?date={$day}-{$month}'>";
}
$sql = mysqli_query($link, "SELECT * FROM `raspisanie` WHERE `date`='{$_GET['date']}' ORDER BY `number`");
$sql1 = mysqli_query($link, "SELECT * FROM `gdz` WHERE `date`='{$_GET['date']}'");
$likes = mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(*) FROM likes"));
if(isset($_GET['date'])){
    echo "<a class='exit' href='/dayhomework'>На главную</a>";
    echo "<h1>Расписание на {$_GET['date']}:</h1><br><div class='table'><table border='1'>"; 
    while($raspisanie = mysqli_fetch_array($sql)){
        echo "<tr>
        <td>{$raspisanie['number']}</td>
        <td>{$raspisanie['predmet']}: </td>
        <td>{$raspisanie['dz']}</td>
        <td><a href='?dell={$raspisanie['id']}'>Удалить</a></td>
        </tr>";
    }
    echo "</table></div>";
    echo "<h1>ГДЗ на {$_GET['date']}:</h1><br><div class='table'><table border='1'>"; 
    while($raspisanie = mysqli_fetch_array($sql1)){
        echo "<tr>
        <td>{$raspisanie['predmet']}: </td>
        <td>{$raspisanie['gdz']}</td>
        </tr>";
    }
    echo "</table></div>";
    echo "
    <form action='' method='post'>
        <input type='hidden' name='like' value='{$_GET['date']}'>
        <input class='like' type='image' src='like.jpg'>
    </form>
    ";
    echo $likes[0]." лайков!";
}else if(isset($_GET['new'])){
    echo "<a class='exit' href='/dayhomework'>На главную</a>";
    echo "
    <form enctype='multipart/form-data' action='' method='post'>
        <input type='text' name='date' placeholder='01-01(день, месяц)' required><br>
        <input type='text' name='predmet' placeholder='Предмет' required><br>
        <textarea name='gdz' placeholder='ГДЗ' cols='30' rows='10' required></textarea><br>
        <input type='file' name='inputfile' accept='image/*,image/jpeg'>
        <input type='submit' name='newgdz' value='Отправить'>
    </form>
    ";
}else if(isset($_GET['newra'])){
    echo "<a class='exit' href='/dayhomework'>На главную</a>";
    echo "
    <form action='' method='post'>
        <input type='text' name='date' placeholder='01-01(день, месяц)' required><br>
        <input type='text' name='predmet' placeholder='Предмет' required><br>
        <input type='number' name='number' placeholder='Номер урока' required><br>
        <textarea name='dz' placeholder='ДЗ' cols='30' rows='10' required></textarea><br>
        <input type='submit' name='newras' value='Отправить'>
    </form>
    ";
}else{
    echo "<a href='?date={$date}'>На сегодня, на {$date}</a>";
    echo "<a href='?date={$todate}'>На завтра, на {$todate}</a>";
    echo "<br>";
    echo "
    <form action='' method='post'>
        <input type='number' min='01' name='day' max='31' step='1' placeholder='день' value='' required>
        <input type='number' min='01' name='month' max='12' step='1' placeholder='месяц' value='' required>
        <input type='submit' value='открыть день'>
    </form>
    ";
    echo "<br>";
    echo "<a href='?new'>Добавить гдз</a>";
    echo "<a href='?newra'>Добавить расписание</a>";
}
?>


    
</body>
</html>