<?php include 'functions.php'; ?>

<?php
    
    if(isset($_POST['fio'])){

        $emplFIO = $_POST['fio'];

        // db connect
        $link = MyConnect( $MYSQL_USER, $MYSQL_PASS, $MYSQL_DB);
        // строка запроса на вставку строки
        $insert_query_str = "insert into employees (fio, status) values ('$emplFIO', '1');";
     
        // --- выполнение запроса
        if($insert_result = mysqli_query($link, $insert_query_str)){

            $lastId = mysqli_insert_id( $link );
            
            echo $lastId;
        }
        else print "Query failed : " . mysqli_error( $link );

        MyClose( $result, $link );
    }

?>