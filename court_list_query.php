<?php include 'functions.php'; ?>

<?php

    // db connect
    $link = MyConnect( $MYSQL_USER, $MYSQL_PASS, $MYSQL_DB);
    
    // список районных судов (организаций)
    $query_str = "select rsud.id, rsud.court_name, locality_type.locality_name, rsud.locality from rsud inner join locality_type on rsud.locality_type=locality_type.id order by id;";
    $result = MyQuery($link, $query_str);
    // данные копируем построчно в массив
    $rsudArr = array();
    while ($line = mysqli_fetch_row($result)){
        $rsudArr[] = $line;
    }

    MyClose( $result, $link );

    // Превращаем массив в json-строку для передачи через Ajax-запрос
    echo json_encode($rsudArr);

?>