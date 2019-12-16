<?php include 'functions.php'; ?>

<?php

    // db connect
    $link = MyConnect( $MYSQL_USER, $MYSQL_PASS, $MYSQL_DB);

    // выбираем корректный номер по журналу (попытка устранения дублирования)
    //
    // проверяем есть ли такой номер в журнале  (в текущем году)
    $query_str = "select record_num_thru_year from exp_in_cases where record_num_thru_year='$_POST[record_num]' and YEAR(case_in_date)=$CURRENT_YEAR;";
    $result = MyQuery($link, $query_str);
    // если есть - выбираем следующий по возрастанию
    if($result != null) {
        // выбираем последний номер по журналу (в текущем году)
        $query_str = "select record_num_thru_year from exp_in_cases where YEAR(case_in_date)=$CURRENT_YEAR order by record_num_thru_year desc LIMIT 1;";
        $result = MyQuery($link, $query_str);
        $line = mysqli_fetch_row($result);
        // следующий номер по журналу
        $nextNumber = $line[0] + 1;
    }
    // если такого номера нет в базе - ставим как есть (берем то что есть из формы)
    else {
        $nextNumber = $_POST['record_num'];
    }

    // преобразуем дату из datepicker'а в корректную для mysql (01-12-2014 -> 2014-12-01)
    $pdt = $_POST['case_in_date'];
    $correct_date = $pdt[6].$pdt[7].$pdt[8].$pdt[9]."-".$pdt[3].$pdt[4]."-".$pdt[0].$pdt[1];

    // получаем корректный id работника, кому передано дело
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

    // строка запроса на вставку строки в таблицу exp_in_cases
    $insert_query_str = "insert into exp_in_cases (record_num_thru_year, case_in_date, case_court_id, case_num, case_year, case_books_num, case_person, empl_id, comments ) values ('$nextNumber', '$correct_date', '$_POST[court_id]', '$_POST[case_num]', '$_POST[case_year]', '$_POST[case_book_num]', '$_POST[case_person]', '$correct_empl_id', '$_POST[comments]');";
 
    // --- выполнение запроса
    if($insert_result = mysqli_query($link, $insert_query_str)){
        // debug echo
        echo "$_POST[court_id] - $_POST[court_name] - $correct_date";
    }
    else print "Query failed : " . mysqli_error( $link );

    MyClose( $result, $link );
?>