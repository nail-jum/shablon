<?php

$dbInfo = $app->contentData;

$errorMessage = '';
if (($dbInfo['status'] ?? '') == 'error') {
    $errorMessage .= '<div class="error"><h2>Ошибка</h2>' . implode('<br>', $dbInfo['message']) . '</div>';
}
if (($dbInfo['status'] ?? '') == 'warning') {
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
    /*
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
    */
</script>

<h1>Загрузка данных из excel файла</h1>

<div class="form-div">
    <form action="/db/load-orders" method="post" enctype="multipart/form-data">
        Загрузка заказов (Учёт заказов в производство.xlsx):
        <input type="file" name="file-source" id="file-source">
        <input type="submit" value="Загрузить"></p>
        <!--
        <input type="button" value="Обработать через API" id="btn-submit"><br><br>
        -->
    </form>
</div>

<div class="form-div">
    <form action="/db/load-materials" method="post" enctype="multipart/form-data">
        Загрузка материалов:
        <input type="file" name="file-source">
        <input type="submit" value="Загрузить"></p>
    </form>
</div>

<div class="form-div">
    <form action="/db/load-semiproducts" method="post" enctype="multipart/form-data">
        Загрузка полуфабрикатов:
        <input type="file" name="file-source">
        <input type="submit" value="Загрузить"></p>
    </form>
</div>

<!--
<a id="show-debug-info" href="#">Debug Info</a>
<div id="debug-info">
    <pre>
    <?php echo print_r($dbInfo, true); ?>
    </pre>
</div>
-->


<?php include_once('templates/page_footer.php'); ?>