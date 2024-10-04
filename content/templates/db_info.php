<?php

$dbInfo = $app->contentData;

$errorMessage = '';
if ($dbInfo['status'] == 'error') {
    $errorMessage .= '<div class="error"><h2>Ошибка</h2>' . implode('<br>', $dbInfo['message']);
    if ($dbInfo['connected'] == false) {
        $errorMessage .= '<br><br><button onclick="location.href=\'/db/create\';">Создать базу данных</button>';
    }
    $errorMessage .= '</div>';
}
if ($dbInfo['status'] == 'warning') {
    $errorMessage .= '<div class="warning"><h2>Предупреждение</h2>' . implode('<br>', $dbInfo['message']) . '</div>';
}

$tables = $dbInfo['data'] ?? [];    // структура реальной базы данных
//$dbConfig = DB_CONF;                // шаблон конфигурации базы данных


// Создание сводной таблицы с информацией о базе данных
// (не доделано)

//$pageTitle = 'Информация о базе данных';
include_once('templates/page_header.php');
?>

<script>
    function showInfo(data) {
        array = data["array"] ?? [];
        link = data["link"] ?? '';
        let text = '';
        if (link.length > 0) {
            text += "<a href=\"" + link + "\">Скачать файл</a><br>";
        }
        text += array2table(array, "border");
        $("#api-result").html(text);
    }

    $(document).ready(function() {

        // для вывода запроса и результата (при отладке)
        showDebugInfo = true;

        $("#debug-info").hide();
        $("#show-debug-info").on("click", function() {
            $("#debug-info").toggle();
        });

        // Отправка запроса
        $("#btn-submit").on("click", function() {
            // метод обработки выбран
            let formData = new FormData();
            formData.append("file-source", $("#file-source")[0].files[0]);
            //method = $('input[name="method"]:checked').val();
            //link = "/api/excel-processing/" + method;
            link = "/api/db-load";
            apiRequest("FILE", link, showInfo, formData);
        });
    });
</script>

<h1>Информация о базе данных</h1>

<?= $errorMessage ?>

<table class="table-edit">
    <tr>
        <th rowspan="2"></th>
        <th rowspan="2">описание</th>
        <th rowspan="2">таблица</th>
        <th colspan="5">поле</th>
    </tr>
    <tr>
        <th>имя</th>
        <th>тип</th>
        <th>notnull</th>
        <th>autoinc</th>
        <th>default</th>
    </tr>
    <?php
    $nn = 0;
    foreach ($tables as $table) {
        $nn++;
        $fieldsCount = count($table['fields']);
        $fldNum = 0;
        foreach ($table['fields'] as $field) {
            echo '<tr>';
            if ($fldNum == 0) {
                // выводить только с первым полем
                echo "<td rowspan={$fieldsCount} class='vtop'>{$nn}</td>";
                //echo "<td rowspan={$fieldsCount} class='vtop'>{$table['title']}</td>";
                echo "<td rowspan={$fieldsCount} class='vtop'></td>";
                echo "<td rowspan={$fieldsCount} class='vtop'><a href=\"/table/{$table['name']}\">{$table['name']}</a></td>";
            }
            echo '<td>' . $field['name'] . '</td>';
            echo '<td>' . $field['type'] . '</td>';
            echo '<td>' . $field['notnull'] . '</td>';
            echo '<td>' . $field['pk'] . '</td>';
            echo '<td>' . $field['dflt_value'] . '</td>';
            echo '</tr>';
            $fldNum++;
        }
    }
    ?>
</table>

<div class="form-div">
    Загрузка начальных данных
    <form action="/db/load" method="post" enctype="multipart/form-data">
        <div>
            Файл для загрузки: <input type="file" name="file-source" id="file-source"><br>
        </div>
        <br>
        <input type="button" value="Обработать через API" id="btn-submit"><br><br>
        <input type="submit" value="Отправить"></p>
    </form>
</div>


<a id="show-debug-info" href="#">Debug Info</a>
<div id="debug-info">
    <pre>
    <?php echo print_r($dbInfo, true); ?>
    </pre>
</div>


<?php include_once('templates/page_footer.php'); ?>