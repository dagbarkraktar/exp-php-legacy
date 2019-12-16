<?php include 'functions.php'; ?>

<?php
    
    if(isset($_POST['court_name'])){

        $courtName = $_POST['court_name'];

        // db connect
        $link = MyConnect( $MYSQL_USER, $MYSQL_PASS, $MYSQL_DB);
        // строка запроса на вставку строки
        $insert_query_str = "insert into rsud (court_name, locality, locality_type) values ('$courtName', '-', '0');";
     
        // --- выполнение запроса
        if($insert_result = mysqli_query($link, $insert_query_str)){

            $lastId = mysqli_insert_id( $link );
            
            echo $lastId;
        }
        else print "Query failed : " . mysqli_error( $link );

        MyClose( $result, $link );
    }

?>