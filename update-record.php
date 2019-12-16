<?php include 'functions.php'; ?>

<?php

    // db connect
    $link = MyConnect( $MYSQL_USER, $MYSQL_PASS, $MYSQL_DB);

    // id записи
    $rec_id = $_POST['record_id'];


    // список фамилий кому передается дело
    $query_str = "select empl_id from employees where status=1 order by empl_id;";
    $result = MyQuery($link, $query_str);
    // данные копируем построчно в массив
    $emplArr = array();
    while ($line = mysqli_fetch_row($result)){
        $emplArr[] = $line;
    }
    // корректный id
    $idx = $_POST['empl_id']-1;
    $correct_empl_id = $emplArr[$idx][0];


    // строка запроса на изменение полей строки
    $update_query_str = "update exp_in_cases set exp_in_cases.empl_id='$correct_empl_id' where record_id='$rec_id' limit 1";

    // --- выполнение запроса
    if($update_result = mysqli_query($link, $update_query_str)){
        // debug echo
        //echo "record_id=$rec_id ($_POST[record_id]) empl_id=$correct_empl_id";
    }
    else print "Query failed : " . mysqli_error( $link );

    MyClose( $result, $link );
?>