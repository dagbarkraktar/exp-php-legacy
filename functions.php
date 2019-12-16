<?php

    header('Content-type: text/html; charset=utf-8');

    // текущий год
    $CURRENT_YEAR = 2019;

    // mysql server addr
    $MYSQL_HOST = "192.168.10.210";
    $MYSQL_DB = "expeddb";
    $MYSQL_USER = "...";
    $MYSQL_PASS = "...";

    // -----------------------------------------------------------
    // функция осуществляет соединение с MySQL и обработку ошибок
    // возвращает линк на соединение с БД
    function MyConnect($user, $pass, $db)
    {
        // --- соединяемся с mysql и выбираем базу данных
        $f_link = mysqli_connect($MYSQL_HOST, $user, $pass, $db);

        // проверяем есть ли ошибки при соединении
        if (mysqli_connect_errno()) printf("Connect failed: %s\n", mysqli_connect_error());
        
        // задаем кодировку соединения с сервером
        mysqli_query($f_link, 'SET NAMES utf8');

        // возвращаем линк
        return $f_link;
    }
    // -----------------------------------------------------------
    // функция выполняет запрос из строки $f_query_str
    // возвращает результат запроса
    function MyQuery($f_link, $f_query_str)
    {
        // --- выполнение запроса
        if($f_result = mysqli_query($f_link, $f_query_str)) return $f_result;
        else print "Query failed : " . mysqli_error( $f_link );
    }
    // -----------------------------------------------------------
    // функция очищает память от результата и закрывает соединение с БД
    function MyClose($f_result, $f_link)
    {
        // освобождаем память от результатов
        if($f_result) mysqli_free_result($f_result);
        // закрываем соединение с mysql
        mysqli_close($f_link);
    }
?>