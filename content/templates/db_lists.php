<?php

$dbInfo = $contentData;

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

include_once('templates/page_header.php');
?>

<h1>Список справочников</h1>

<?= $errorMessage ?>

<table class="table-edit">
    <tr><th>описание</th><th>таблица</th></tr>
<?php foreach ($tables as $table) { ?>
    <tr>
    <td class='vtop'><a href="/table/<?= $table['name'] ?>"><?= $table['title'] ?></a></td>
    <td class='vtop'><?= $table['name'] ?></td>
    </tr>
<?php } ?>
</table>

<pre>
<?php echo print_r($dbInfo, true); ?>
</pre>


<?php include_once('templates/page_footer.php'); ?>