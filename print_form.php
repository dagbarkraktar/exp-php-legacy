<?php include 'functions.php'; ?>

<style type="text/css">

    table.printform { 
        font: 10pt sans-serif; 
        margin: 1em 1em 1em 0;
        border-collapse: collapse;
    }

    .printform td, th { 
        border: 1px #000 solid;
        padding: 0.2em; 
    }

    .printform th { 
        background: #fff;
        font-weight: bold;
        text-align: center;
    }

</style>

<?php

  // считываем то что выделено галочками
  // в массиве rChk[] id выбранных записей
  $rChk = $_POST['rowChecked'];
  if(empty($rChk)){
      //echo("You didn't select any rows.");
      echo("Не выбраны строки для печати.");
  } // end if(empty($rChk))
  else{

      // кол-во выбранных строк
      $N = count($rChk);
      //echo("You selected $N row(s): ");
      //for($i=0; $i < $N; $i++) echo($rChk[$i] . " ");
    
      // выводим данные по выбранным записям
      // db connect
      $link = MyConnect( $MYSQL_USER, $MYSQL_PASS, $MYSQL_DB);

      // склеиваем строку запроса
      $query_str = "select exp_in_cases.*, rsud.court_name, locality_type.locality_name, rsud.locality, employees.fio from exp_in_cases inner join rsud on exp_in_cases.case_court_id=rsud.id inner join locality_type on rsud.locality_type=locality_type.id inner join employees on exp_in_cases.empl_id=employees.empl_id";
      $query_str = $query_str . " where record_id='$rChk[0]'";
      if($N > 1){
        for($k=1; $k < $N; $k++){
          $query_str = $query_str . " or record_id='$rChk[$k]'";
        }
      }
      $query_str = $query_str . " order by record_num_thru_year desc;";

      // выполнение запроса
      $result = MyQuery($link, $query_str);

      // данные копируем построчно в массив
      $ResultArr = array();
      while ($line = mysqli_fetch_row($result)){
          $ResultArr[] = $line;
      }
      MyClose( $result, $link );

      print "<center><h2>Список поступивших дел</h2></center>";
      print "<center><table class='printform'>";
      print "<tr>";
      print "<th>№ п/п</th>";
      print "<th>Дата<br>поступления</th>";
      print "<th>Поступило из</th>";
      print "<th>Номер<br>дела</th>";
      
      print "<th>Содержание</th>";
      print "<th>Кол-во<br>томов</th>";
      print "<th>Кому<br>передано</th>";
      print "<th>Дата<br>передачи</th>";
      print "<th>Роспись<br>о получении</th>";
      print "<th>Примечание</th>";
      print "</tr>";
      // данные построчно
      foreach ($ResultArr as $ln){
        print "<tr>";
        print "<td align='center'>$ln[1]</td>"; // номер
        //print "<td align='center'>$ln[2]</td>"; // дата поступления
        // читабельная дата поступления
        $pdt = $ln[2];
        $readable_date = $pdt[8].$pdt[9]."-".$pdt[5].$pdt[6]."-".$pdt[0].$pdt[1].$pdt[2].$pdt[3];
        print "<td align='center'>$readable_date</td>";
        print "<td>$ln[10] ($ln[11]$ln[12])</td>"; // поступило из
        print "<td align='center'>№$ln[4]/$ln[5]</td>"; // номер дела
        print "<td>$ln[7]</td>"; // содержание (в отношении/по иску)
        print "<td align='center'>$ln[6]</td>"; // кол-во томов
        print "<td>$ln[13] $ln[14] $ln[15]</td>"; // 
        print "<td></td>"; //
        print "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br>&nbsp;</td>"; //
        print "<td>$ln[9]</td>"; //
        print "</tr>";
      }
      print "</table></center>";

  } // end else (empty($rChk))

?>