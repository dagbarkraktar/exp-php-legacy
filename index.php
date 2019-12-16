<?php include 'functions.php'; ?>

<script type="text/javascript" src="lib/jquery.js"></script>
<script src="lib/ui/jquery-ui.js"></script>
<link rel="stylesheet" href="lib/ui/themes/smoothness/jquery-ui.css">

<style type="text/css">
    
    body {
        margin: 0; /* Убираем отступы */
        font: 12px Arial,Helvetica,sans-serif;
    }

    .page {
        margin: 5px; /* Отступы вокруг элемента */
        /*background: #fd0;  Цвет фона */
        padding: 25px; /* Поля вокруг текста */
    }

    .add_record_block {
        margin: 10px auto 10px;
        padding: 25px;
        background: none repeat scroll 0% 0% #f7f7f7; /* фон */
        border-radius: 10px;
        border: 1px solid #aaa;  /* Параметры рамки */
    }

    .add_record_block form {
        background: none repeat scroll 0% 0% #f7f7f7; /* фон */
        border-radius: 10px;
        /*border: 1px solid #f00;  Параметры рамки */
    }

    .add_record_block label {
        /*position: absolute;
        right: 0px;*/
        /*color: #9C9C9C;*/
        color: #000;
        font-size: 11px;
        text-decoration: none;
    }

    .add_record_block input, select{
        border-radius: 5px;
        margin-left: 3px;
        margin-right: 3px;
        margin-bottom: 10px;
        padding: 3px;

        /*border: 1px solid #f00;  Параметры рамки */
    }

    .button {
        border-radius: 5px;
        margin-left: 3px;
        margin-right: 3px;
        margin-bottom: 10px;
        padding: 3px;

        text-align: center;
        text-shadow: 0px 1px 0px #FFF;
    }

    .data_block {
        margin: 10px auto 10px;
        padding: 25px;
        /*background: none repeat scroll 0% 0% #fff; фон */
        border-radius: 10px;
        border: 1px solid #aaa; /* Параметры рамки */
    }

    .error_field {
        background: none repeat scroll 0% 0% #ff7777; /* фон */
    }

    .tbltext tr td {
        font-size: 9pt;
        font: Arial, Helvetica, sans-serif;
        text-align: center;

        padding:5px;
        border-top: 1px solid #ffffff;
        border-bottom:1px solid #e0e0e0;
        border-left: 1px solid #e0e0e0;
        background: #fafafa;
    }

    .tbltext th {
        font-size: 9pt;
        font: Arial, Helvetica, sans-serif;
        text-align: center;
        font-weight: bold;

        padding:5px;
        border-top: 1px solid #ffffff;
        border-bottom:1px solid #d0d0d0;
        border-left: 1px solid #d0d0d0;
        background: #eaeaea;
    }

</style>

<script type="text/javascript">

    // ID записи журнала в которой редактируем сотрудника 
    var selectedRecordID;

    // текущий год
    var currentYear=<?php print $CURRENT_YEAR; ?>;

    // инициализация владок (используются вкладки jQuery UI)
    $(function() {
        $("#tabs").tabs({
            // функция вызывается при создании вкладок
            // (при этом автоматически активируется вкладка по умолчанию)
            create: function( event, ui ) {
                //alert("tabs created");
                ShowTableData(currentYear,$('#table_data_box_2019'));
            }, // end create()

            // функция вызывается перед активацией вкладки
            beforeActivate: function( event, ui ) {
                /*
                if (ui.newPanel.is("#tab-2016")) {}
                else if(ui.newPanel.is("#tab-2015")){}
                    */
            }, // end beforeActivate()

            // функция вызывается при активации вкладки
            activate: function( event, ui ) {
                // разбираемся какая вкладка активирована
                // 2019
                if (ui.newPanel.is("#tab-2019")) {
                    //alert("2019 tab activating");
                    ShowTableData(currentYear,$('#table_data_box_2019'));
                }
                // 2018
                else if(ui.newPanel.is("#tab-2018")){
                    //alert("2017 tab activating");
                    ShowTableData(2018,$('#table_data_box_2018'));
                }
                // 2017
                else if(ui.newPanel.is("#tab-2017")){
                    //alert("2017 tab activating");
                    ShowTableData(2017,$('#table_data_box_2017'));
                }
                // 2016
                else if(ui.newPanel.is("#tab-2016")){
                    //alert("2016 tab activating");
                    ShowTableData(2016,$('#table_data_box_2016'));
                }
                // 2015
                else if(ui.newPanel.is("#tab-2015")){
                    //alert("2015 tab activating");
                    ShowTableData(2015,$('#table_data_box_2015'));
                }
            } // end activate()
        });
    }); // end tabs init


    // при загрузке страницы
    $(document).ready(function(){

        // загружаем список сотрудников в select "Передано"
        ReloadEmployeeList(0);
        // загружаем список организаций в select "Поступило из"
        ReloadCourtList(0);

        // инициализируем диалог выбора сотрудника
        $("#empl_dlg").dialog({ 
            autoOpen: false, 
            modal: true, 
            title: "Выбор сотрудника",
            buttons:{
                // обработчик кнопки "Выбрать"
                "Выбрать": function(){
                    var sel = document.getElementById('empl_dlg_select');

                    // AJAX-вызов скрипта для изменения фио сотрудника
                    $.ajax({
                        url: 'update-record.php',
                        type: 'post',
                        // document.forms["имя формы"].имя списка.selectedIndex
                        data: "record_id=" + selectedRecordID + "&empl_id=" + sel.selectedIndex,
                        // при успешной отправке выводим ответ
                        success: function(response){
                            //alert("AJAX Ответ: " + response);
                            // перегружаем страницу
                            window.location.reload();
                        }
                    }); // end ajax

                    // закрываем диалог
                    $(this).dialog( "close" );
                },
                // обработчик кнопки "Отмена"
                "Отмена": function(){
                    // закрываем диалог
                    $(this).dialog( "close" );
                }

            }
        });

        // инициализируем диалог добавления сотрудника
        $("#empl_add_dlg").dialog({ 
            autoOpen: false,
            modal: true,
            width: 400,
            title: "Добавить сотрудника в справочник",
            buttons:{
                // обработчик кнопки "Добавить"
                "Добавить": function(){
                    var emplFIO = document.getElementById('empl_add_fio').value;
                    //alert(emplFIO); // тест

                    // AJAX-вызов скрипта для добавления сотрудника в справочник
                    $.ajax({
                        url: 'empl_add_query.php',
                        type: 'post',
                        data: "fio=" + emplFIO,
                        // при успешной отправке получаем ответ = ID
                        success: function(response){
                            // последний ID добавленный в справочник
                            var lastId = Number(response);
                            // обновляем список сотрудников в select "Передано"
                            ReloadEmployeeList(lastId);
                        }
                    }); // end ajax

                    // закрываем диалог
                    $(this).dialog( "close" );
                },
                // обработчик кнопки "Отмена"
                "Отмена": function(){
                    // закрываем диалог
                    $(this).dialog( "close" );
                }
            }
        });
        

        // инициализируем диалог добавления суда (организации)
        $("#court_add_dlg").dialog({ 
            autoOpen: false,
            modal: true,
            width: 400,
            title: "Добавить организацию в справочник",
            buttons:{
                // обработчик кнопки "Добавить"
                "Добавить": function(){
                    var courtName = document.getElementById('court_add_name').value;
                    //alert(courtName); // тест

                    // AJAX-вызов скрипта для добавления сотрудника в справочник
                    $.ajax({
                        url: 'court_add_query.php',
                        type: 'post',
                        data: "court_name=" + courtName,
                        // при успешной отправке получаем ответ = ID
                        success: function(response){
                            //alert(response);
                            // последний ID добавленный в справочник
                            var lastId = Number(response);
                            // обновляем список организаций в select "Поступило из"
                            ReloadCourtList(lastId);
                        }
                    }); // end ajax

                    // закрываем диалог
                    $(this).dialog( "close" );
                },
                // обработчик кнопки "Отмена"
                "Отмена": function(){
                    // закрываем диалог
                    $(this).dialog( "close" );
                }
            }
        });

        // флажки для проверки информации введенной в форму
        var notError = new Array( 0, 0, 0, 0, 0, 0, 0 );
        /*
        * проверка полей на корректность заполнения
        */
        function checkFields()
        {
            // номер по журналу
            var r_num = document.forms["add-record-form"].record_num;
            if(r_num.value == ''){
                notError[0] = 0;
                r_num.style.backgroundColor='ff7777';
                r_num.style.color='fff';
            }
            else{
                notError[0] = 1;
                r_num.style.backgroundColor='fff';
                r_num.style.color='black';    
            }

            // дата поступления
            var ci_date = document.forms["add-record-form"].case_in_date; 
            if(ci_date.value == ''){
                notError[1] = 0;
                ci_date.style.backgroundColor='ff7777';
                ci_date.style.color='fff';
            }
            else{
                notError[1] = 1;
                ci_date.style.backgroundColor='fff';
                ci_date.style.color='black';    
            }

            // наименование суда (организации)
            var c_name = document.forms["add-record-form"].court_name;
            if(c_name.selectedIndex == 0){
                notError[2] = 0;
                c_name.style.backgroundColor='ff7777';
                c_name.style.color='fff';
            }
            else{
                notError[2] = 1;
                c_name.style.backgroundColor='fff';
                c_name.style.color='black';    
            }

            // номер дела
            var c_num = document.forms["add-record-form"].case_num;
            if(c_num.value == ''){
                notError[3] = 0;
                c_num.style.backgroundColor='ff7777';
                c_num.style.color='fff';
            }
            else{
                notError[3] = 1;
                c_num.style.backgroundColor='fff';
                c_num.style.color='black';    
            }

            // год дела
            var c_year = document.forms["add-record-form"].case_year;
            if(c_year.value == ''){
                notError[4] = 0;
                c_year.style.backgroundColor='ff7777';
                c_year.style.color='fff';
            }
            else{
                notError[4] = 1;
                c_year.style.backgroundColor='fff';
                c_year.style.color='black';    
            }

            // в отношении/по иску кого
            var c_person = document.forms["add-record-form"].case_person;
            if(c_person.value == ''){
                notError[5] = 0;
                c_person.style.backgroundColor='ff7777';
                c_person.style.color='fff';
            }
            else{
                notError[5] = 1;
                c_person.style.backgroundColor='fff';
                c_person.style.color='black';    
            }

            // кол-во томов
            var cb_num = document.forms["add-record-form"].case_book_num;
            if(cb_num.value == ''){
                notError[6] = 0;
                cb_num.style.backgroundColor='ff7777';
                cb_num.style.color='fff';
            }
            else{
                notError[6] = 1;
                cb_num.style.backgroundColor='fff';
                cb_num.style.color='black';    
            }
        }

        // при изменении значений
        $('#record_num, #case_in_date, #court_name, #case_num, #case_year, #case_person, #case_book_num').change(function(){

            // проверка полей на корректность ввода
            checkFields();

        }); // end change()

        // отправляем AJAX по нажатию кнопки submit
        $('form#add-record-form').submit(function(event){
            
            // запрещаем стандартное поведение для кнопки submit
            event.preventDefault();
            // проверяем поля
            checkFields();

            // получаем текущую дату
            var currentDate = $( "#case_in_date" ).datepicker( "getDate" );

            // если все поля правильно заполнены - отправляем AJAX
            // every() - проверяет элементы массива
            if(notError.every(function(x){ return x > 0; })){
                $.ajax({
                    url: 'add-record.php',
                    type: 'post',
                    // document.forms["имя формы"].имя списка.selectedIndex
                    data: $(this).serialize()+"&court_id="+document.forms["add-record-form"].court_name.selectedIndex+"&empl_id="+document.forms["add-record-form"].empl_name.selectedIndex,
                    // при успешной отправке выводим ответ
                    success: function(response){
                        //alert("AJAX Ответ: " + response); 
                        window.location.reload();
                    }
                }); // end ajax

            } // end if
            // иначе возвращаем false (форма не отправляется)
            else return false;

        }); // end submit()

    }); // end (document).ready()
 
    // инициализация календарика   
    $(function() {
        $( "#case_in_date" ).datepicker({
            dateFormat: "dd-mm-yy (DD)",
            firstDay: 1, /* первый день - понедельник */
            dayNamesMin: [ "Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб" ],
            monthNames:  [ "Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"],
            dayNames: [ "Воскресенье", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота" ]
            /*,
            onClose: function(dateText, inst) {
                if($(this).val() == null)
                    $(this).css("background-color","fff");
            }*/
        });
    });

    /*
    *  Отображение таблицы с данными года=year в блоке tableDataBox
    */
    function ShowTableData(year,tableDataBox)
    {
        // AJAX запрос
        $.ajax({
            // скрипт выполняющий наш запрос
            url: 'list_query.php', 
            type: 'post',
            // параметры передаваемые в скрипт
            data: "year="+year,

            // перед отправкой ajax-запроса
            beforeSend: function(){
                // очищаем блок <div>
                tableDataBox.empty();
                // на время загрузки данных выводим сообщение
                tableDataBox.append("Идёт загрузка данных <img src='lib/ajax-loader-progress-mini.gif'>");
            },
            // при успешной отправке выводим ответ
            success: function(response){

                // парсим JSON ответ скрипта в массив
                result_arr = jQuery.parseJSON(response);
                //alert(result_arr); // тест

                // очищаем блок <div>
                tableDataBox.empty();
                // добавляем форму (нужна для обработки чекбоксов формы печати)
                tableDataBox.append("<form id='print_sel_form_"+year+"' action='print_form.php' method='post'>");
                // добавляем в форму кнопку submit
                $("#print_sel_form_"+year).append("<input id='submit_pf' type='submit' name='submit_pf' class='button' style='width:180px' value='Печать'/>");

                // добавляем в этот блок таблицу с id=table_data
                $("#print_sel_form_"+year).append("<table id='table_data_"+year+"' class='tbltext' width='100%'>");
                // добавляем в таблицу строку с заголовком
                $("#table_data_"+year).append("<tr align='center'><th colspan='11'>ЖУРНАЛ ПОСТУПИВШИХ ДЕЛ</th></tr>");
                $("#table_data_"+year).append("<tr align='center'><th>№</th><th>Дата<br>поступления</th><th>Поступило из</th><th colspan=2>№ Дела</th><th>Кол-во<br>томов</th><th>Содержание</th><th colspan='2'>Передано</th><th>Примечание</th><th></th></tr>");
                
                // построчный разбор данных из массива result_arr
                // каждая строка массива попадает в data[]
                $.each(result_arr, function(index,data){
                    var td_str = ""; 
                    td_str = td_str + "<td>"+data[1]+"</td>";
                    var readable_date = data[2][8]+data[2][9]+"-"+data[2][5]+data[2][6]+"-"+data[2][0]+data[2][1]+data[2][2]+data[2][3];
                    td_str = td_str + "<td>"+readable_date+"</td>";
                    td_str = td_str + "<td>"+data[10]+" ("+data[11]+data[12]+")</td>";
                    td_str = td_str + "<td>"+data[4]+"</td>";
                    td_str = td_str + "<td>"+data[5]+"</td>";
                    td_str = td_str + "<td>"+data[6]+"</td>";
                    td_str = td_str + "<td>"+data[7]+"</td>";
                    // если empl_id == 0 (пока не выбрано кому передать входящее дело)
                    if(data[8] == 0){
                        td_str = td_str + "<td><b><i>- - - - -</i></td><td><input type='button' value=' + ' onClick='SelectEmployeeDlg(" + data[0] + "," + data[8] + ")'></b></td>";  
                    }
                    // или показываем ФИО или название отдела
                    else {
                        td_str = td_str + "<td><b><i>"+data[13]+"</i></td><td><input type='button' value='изм' onClick='SelectEmployeeDlg(" + data[0] + "," + data[8] + ")'></b></td>";
                    }
                    // примечание
                    td_str = td_str + "<td>"+data[9]+"</td>";
                    // чекбокс для вывода на печать
                    td_str = td_str + "<td><input type='checkbox' name='rowChecked[]' value='"+data[0]+"' /></td>";

                    // добавляем строку с данными в таблицу
                    $("#table_data_"+year).append("<tr align='center'>"+td_str+"</tr>");
                }); // end each()

            }, // end success()
            // обработка ошибки загрузки данных
            error: function(xhr, str){
                // очищаем блок <div>
                tableDataBox.empty();
                // выводим сообщение об ошибке
                tableDataBox.append("<h3 style='color:red'>Ошибка загрузки данных!</h3>");
            } // end error()
        }); // end $.ajax({
    }

    /* 
     * Выбор работника в модальном диалоге
     * список работников получаем через ajax-запрос
     * record_id - id записи журнала в которой корректируем работника
     * empl_id - id работника, который был выбран первоначально
     */
    function SelectEmployeeDlg( record_id, empl_id ) {

        // ID записи журнала в которой редактируем сотрудника        
        selectedRecordID = record_id;

        // открываем диалог выбора
        $("#empl_dlg").dialog('open');

        // AJAX запрос
        $.ajax({
            url: 'empl_list_query.php', /* скрипт выполняющий наш запрос (список работников) */
            type: 'post',

            // при успешной отправке выводим ответ
            success: function(response){

                // парсим JSON ответ скрипта в массив
                result_arr = jQuery.parseJSON(response);

                // выводим в диалог список работников
                // очищаем блок <div>
                $('#empl_dlg').empty();
                // добавляем список
                $("#empl_dlg").append("<select id='empl_dlg_select' name='empl_dlg_select' style='width:250px'>");
                $("#empl_dlg_select").append("<option>выберите сотрудника</option>");
                
                // построчный разбор данных из массива result_arr
                // каждая строка массива попадает в data[]
                $.each(result_arr, function(index,data){
                    var option_str = ""; 
                    // если есть id сотрудника, то выбираем его в списке
                    if(data[0] == empl_id) {
                        // выбранный пункт
                        option_str = "<option selected>"+data[1]+"</option>";
                    }
                    else {
                        // обычный пункт списка
                        option_str = "<option>"+data[1]+"</option>";
                    }
                    // добавляем этот пункт в список
                    $("#empl_dlg_select").append(option_str);

                }); // end each()
            } // end success()
        }); // end $.ajax({
    }

    /*
    * загружаем список сотрудников в select "Передано"
    * empl_id - id сотрудника который будет выбран в списке
    */
    function ReloadEmployeeList( empl_id ){

        // AJAX запрос
        $.ajax({
            url: 'empl_list_query.php', /* скрипт выполняющий наш запрос (список работников) */
            type: 'post',

            // при успешной отправке выводим ответ
            success: function(response){

                // парсим JSON ответ скрипта в массив
                result_arr = jQuery.parseJSON(response);

                // выводим в диалог список работников
                // очищаем блок
                $('#empl_name').empty();
                // добавляем информационную строку
                $("#empl_name").append("<option selected>выберите сотрудника</option>");
                
                // построчный разбор данных из массива result_arr
                // каждая строка массива попадает в data[]
                $.each(result_arr, function(index,data){
                    var option_str = ""; 
                    // если есть id сотрудника, то выбираем его в списке
                    if(data[0] == empl_id) {
                        // выбранный пункт
                        option_str = "<option selected>"+data[1]+"</option>";
                    }
                    else {
                        // обычный пункт списка
                        option_str = "<option>"+data[1]+"</option>";
                    }
                    // добавляем этот пункт в список
                    $("#empl_name").append(option_str);

                }); // end each()
            } // end success()
        }); // end $.ajax({
    }

    /*
    * загружаем список организаций в select "Поступило из"
    * court_id - id организации который будет выбран в списке
    */
    function ReloadCourtList( court_id ){

        // AJAX запрос
        $.ajax({
            url: 'court_list_query.php', /* скрипт выполняющий наш запрос (список работников) */
            type: 'post',

            // при успешной отправке выводим ответ
            success: function(response){

                // парсим JSON ответ скрипта в массив
                result_arr = jQuery.parseJSON(response);

                // выводим в диалог список работников
                // очищаем блок
                $('#court_name').empty();
                // добавляем информационную строку
                $("#court_name").append("<option selected>выберите организацию</option>");
                
                // построчный разбор данных из массива result_arr
                // каждая строка массива попадает в data[]
                $.each(result_arr, function(index,data){
                    var option_str = ""; 
                    // если есть id организации, то выбираем его в списке
                    if(data[0] == court_id) {
                        // выбранный пункт
                        option_str = "<option selected>"+data[1]+" ("+data[2]+data[3]+")</option>";
                    }
                    else {
                        // обычный пункт списка
                        option_str = "<option>"+data[1]+" ("+data[2]+data[3]+")</option>";
                    }
                    // добавляем этот пункт в список
                    $("#court_name").append(option_str);

                }); // end each()
            } // end success()
        }); // end $.ajax({
    }

    /* 
     * добавить организацию в справочник
     * 
     */
    function AddCompany() {
        // открываем диалог
        $("#court_add_dlg").dialog('open');
    }

    /* 
     * добавить сотрудника в справочник
     */
    function AddEmployee() {
        // открываем диалог
        $("#empl_add_dlg").dialog('open');
    }

</script>

<?php
    //
    // запрашиваем последний номер по журналу для вычисления следующего
    //
    $link = MyConnect( $MYSQL_USER, $MYSQL_PASS, $MYSQL_DB);

    // строка запроса (с учетом текущего года)
    $query_str = "select record_num_thru_year from exp_in_cases where YEAR(case_in_date)=$CURRENT_YEAR order by record_num_thru_year desc LIMIT 1;";

    // результат запроса
    if($result = MyQuery($link, $query_str)) {
        $line = mysqli_fetch_row($result);
        // следующий номер по журналу + 1 от последнего номера
        $nextNumber = $line[0] + 1;
    }
    else {
        // в начале года следующий номер по журналу = 1
        $nextNumber = 1;
    }
    // закрываем коннект
    MyClose( $result, $link );
?>

<!-- ФОРМА ВВОДА -->
<div class='page'>
    <div class='add_record_block'>
        <form id='add-record-form'>
            <div>
                <label for="record_num">№ по журналу:</label>
                <input id="record_num" type="text" tabindex="1" name="record_num" maxlength="4" style='width:50px' value='<?php print $nextNumber; ?>'></input>
                <label for="case_in_date">Дата поступления:</label>
                <input id="case_in_date" type="text" tabindex="2" name="case_in_date" maxlength="26" style='width:170px'></input>

                <label for="court_name">Поступило из:</label>
                <select id="court_name" tabindex="3" name="court_name" style='width:280px'>
                </select>
                <input id='add_company' type='button' value=' + ' onClick='AddCompany()'>
            </div>
            <div>
                <label for="case_num">№ дела:</label>
                <input id="case_num" type="text" tabindex="4" name="case_num" maxlength="12" style='width:80px'></input>
                <label for="case_year">Год:</label>
                <input id="case_year" type="text" tabindex="5" name="case_year" maxlength="4" style='width:40px' value="<?php print $CURRENT_YEAR; ?>"></input>

                <label for="case_person">Содержание (в отношении/по иску):</label>
                <input id="case_person" type="text" tabindex="6" name="case_person" maxlength="250" style='width:340px'>
                <label for="case_book_num">Кол-во томов:</label>
                <input id="case_book_num" type="text" tabindex="7" name="case_book_num" maxlength="3" style='width:30px' value='1'>
            </div>
            <div>
                <label for="empl_name">Передано:</label>
                <select id="empl_name" tabindex="8" name="empl_name" style='width:170px' />
                <input id='add_employee' type='button' value=' + ' onClick='AddEmployee()'>
                <label for="comments">Примечание:</label>
                <input id="comments" type="text" tabindex="9" name="comments" maxlength="250" style='width:290px'>

                <input id="submit" type="submit" tabindex="10" name="submit" class='button' style='width:180px' value='Добавить в журнал'>
            </div>

        </form>
    </div>


    <!-- БЛОК ВКЛАДОК -->
    <div id="tabs">
        <!-- ЗАГОЛОВКИ ВКЛАДОК -->
        <ul>
            <li><a href="#tab-2019">2019</a></li>
            <li><a href="#tab-2018">2018</a></li>
            <li><a href="#tab-2017">2017</a></li>
            <li><a href="#tab-2016">2016</a></li>
            <li><a href="#tab-2015">2015</a></li>
        </ul>
        <!-- СОДЕРЖИМОЕ ВКЛАДКИ 2019 -->
        <div id="tab-2019">
            <!-- БЛОК ДЛЯ ВЫВОДА ТАБЛИЦ С ДАННЫМИ -->
            <div class='data_block'>
                <!-- блок для вывода таблицы  -->
                <div id='table_data_box_2019'>&nbsp;</div>
            </div>
        </div>

        <!-- СОДЕРЖИМОЕ ВКЛАДКИ 2018 -->
        <div id="tab-2018">
            <!-- БЛОК ДЛЯ ВЫВОДА ТАБЛИЦ С ДАННЫМИ -->
            <div class='data_block'>
                <!-- блок для вывода таблицы  -->
                <div id='table_data_box_2018'>&nbsp;</div>
            </div>
        </div>

        <!-- СОДЕРЖИМОЕ ВКЛАДКИ 2017 -->
        <div id="tab-2017">
            <!-- БЛОК ДЛЯ ВЫВОДА ТАБЛИЦ С ДАННЫМИ -->
            <div class='data_block'>
                <!-- блок для вывода таблицы  -->
                <div id='table_data_box_2017'>&nbsp;</div>
            </div>
        </div>

        <!-- СОДЕРЖИМОЕ ВКЛАДКИ 2016 -->
        <div id="tab-2016">
            <!-- БЛОК ДЛЯ ВЫВОДА ТАБЛИЦ С ДАННЫМИ -->
            <div class='data_block'>
                <!-- блок для вывода таблицы  -->
                <div id='table_data_box_2016'>&nbsp;</div>
            </div>
        </div>

        <!-- СОДЕРЖИМОЕ ВКЛАДКИ 2015 -->
        <div id="tab-2015">
            <!-- БЛОК ДЛЯ ВЫВОДА ТАБЛИЦ С ДАННЫМИ -->
            <div class='data_block'>
                <!-- блок для вывода таблицы  -->
                <div id='table_data_box_2015'>&nbsp;</div>
            </div>
        </div>

    </div>


    <!-- блок для вывода модального диалога выбора работника -->
    <div id='empl_dlg'></div>

    <!-- блок для вывода модального диалога с формой добавления суда (организации) -->
    <div id='court_add_dlg'>
        <label for='court_add_name'>Наименование организации:</label>
        <input id='court_add_name' type='text' maxlength="128" style='width:320px'>
    </div>

    <!-- блок для вывода модального диалога с формой добавления сотрудника в справочник -->
    <div id='empl_add_dlg'>
        <label for='empl_add_fio'>ФИО:</label>
        <input id='empl_add_fio' type='text' maxlength="128" style='width:320px'>
    </div>

</div>
