<?php include 'functions.php'; ?>

<?php
    // по какому году делаем запрос
    if(isset($_POST['year'])){
        // передан через форму
        $selectedYear = $_POST['year'];
    }
    else{
        // по умолчанию текущий год
        $selectedYear = $CURRENT_YEAR;
    }

    // db connect
    $link = MyConnect( $MYSQL_USER, $MYSQL_PASS, $MYSQL_DB);

    // запрос
    $query_str = "select exp_in_cases.*, rsud.court_name, locality_type.locality_name, rsud.locality, employees.fio from exp_in_cases inner join rsud on exp_in_cases.case_court_id=rsud.id inner join locality_type on rsud.locality_type=locality_type.id inner join employees on exp_in_cases.empl_id=employees.empl_id where YEAR(case_in_date)=$selectedYear order by record_num_thru_year desc;";
    
    $result = MyQuery($link, $query_str);

    // данные копируем построчно в массив
    $ResultArr = array();
    while ($line = mysqli_fetch_row($result)){
        $ResultArr[] = $line;
    }
    MyClose( $result, $link );

    // Превращаем массив в json-строку для передачи через Ajax-запрос
    echo json_encode($ResultArr);

?>