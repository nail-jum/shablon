<?php

$errorMessage = '';
if ($contentData['status'] == 'error') {
    $errorMessage .= '<div class="error"><h2>Ошибка</h2>' . implode('<br>', $contentData['message']) . '</div>';
}
if ($contentData['status'] == 'warning') {
    $errorMessage .= '<div class="warning"><h2>Предупреждение</h2>' . implode('<br>', $contentData['message']) . '</div>';
}

$actionConf = [
    'new'  => ['title' => 'новая запись', 'submit-action' => 'add'],
    'edit' => ['title' => 'редактирование записи', 'submit-action' => 'upd'],
    'copy' => ['title' => 'копирование записи', 'submit-action' => 'add'],
    'del'  => ['title' => 'удаление записи', 'submit-action' => 'del'],
];

$refererPage = $_SERVER['HTTP_REFERER'] ?? '/';     // предыдущая страница

// запрос данных таблицы
$table = $contentData['data'] ?? [];        // данные таблицы
$tableName  = $table['name'] ?? [];
$rowColumns = $table['columns'] ?? [];      // информация о полях таблицы
$rowData    = $table['data'] ?? [];         // значения полей
$action     = $table['action'] ?? [];       // текущее действие
$newAction  = $table['new-action'] ?? [];   // завершающее действие
// доступность полей для редактирования
if ($action == 'del') {
    $disabled = ' disabled';
} else {
    $disabled = '';
}

include_once('templates/page_header.php');
?>

<style>
    input.btn_delete {
        /*font-weight: bold;*/
        color: red;
    }
</style>

<h1>Таблица "<?= $table['title'] ?>"</h1>
<?= $errorMessage ?>

<form action="db_table_row_submit.php" method="post">
    <div class="row-edit">
        <h2><?= $actionConf[$action]['title'] ?></h2>
        <table>
            <?php foreach ($rowColumns as $column) { ?>
                <tr>
                    <td class="r">
                        <?= $column['title'] ?>:
                    </td>
                    <td>
                        <input type="text" name="<?= $column['name'] ?>" id="" value="<?= $rowData[$column['name']] ?? '' ?>" style="width:500px" <?=$disabled ?>>
                    </td>
                </tr>
            <?php } ?>
            <tr>
                <td colspan="2" class="c">
                    <br>
                    <?php if ($action == 'del') { ?>
                        <input type="submit" value="Удалить" class="btn_delete">
                    <?php } else { ?>
                        <input type="submit" value="Сохранить">
                    <?php } ?>
                    <input type="button" value="Отмена" onclick="location.href='<?= $refererPage ?>';">
                </td>
            </tr>
        </table>
    </div>
    <input type="hidden" name="_table-name" value="<?= $tableName ?>">
    <input type="hidden" name="_row-id" value="<?= $rowId ?>">
    <input type="hidden" name="_action" value="<?= $actionConf[$action]['submit-action'] ?>">
    <input type="hidden" name="_referer-page" value="<?= $refererPage ?>">

</form>

<pre>
action = <?= $action ?>

<?php echo print_r($table, true); ?>
</pre>