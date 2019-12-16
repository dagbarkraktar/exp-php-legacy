<?php include 'functions.php'; ?>

<?php

    // db connect
    $link = MyConnect( $MYSQL_USER, $MYSQL_PASS, $MYSQL_DB);
    
    // список фамилий кому передается дело
    $query_str = "select * from employees where status=1 order by empl_id;";
    $result = MyQuery($link, $query_str);
    // данные копируем построчно в массив
    $emplArr = array();
    while ($line = mysqli_fetch_row($result)){
        $emplArr[] = $line;
    }

    MyClose( $result, $link );

    // Превращаем массив в json-строку для передачи через Ajax-запрос
    echo json_encode($emplArr);

?>