<?php

$errorMessage = '';
/*
if ($contentData['status'] == 'error') {
    $errorMessage .= '<div class="error"><h2>Ошибка</h2>' . implode('<br>', $contentData['message']);
    if ($contentData['connected'] == false) {
        $errorMessage .= '<br><br><button onclick="location.href=\'/db/create\';">Создать базу данных</button>';
    }
    $errorMessage .= '</div>';
}
if ($contentData['status'] == 'warning') {
    $errorMessage .= '<div class="warning"><h2>Предупреждение</h2>' . implode('<br>', $contentData['message']) . '</div>';
}
*/
//$table = $contentData['data'] ?? [];    // данные таблицы
$table = $contentData ?? [];    // данные таблицы

$tableName    = $table->tableName ?? '';
$tableTitle   = $table->tableTitle ?? '';
$tableColumns = $table->fields ?? [];
$tableData    = $table->rows ?? [];

//$pageTitle = 'Редактирование таблицы ' . $tableName;
include_once('templates/page_header.php');
?>

<script>
    $(document).ready(function() {
        form_link = "/table/<?= $tableName ?>";
        user_key = "user-key";
        $(".edit-icon").on("click", function() {
            [btn_action, row_id] = this.id.split('-');
            [temp, action] = btn_action.split('_');
            link = form_link + '/' + (row_id ?? '');
            $("#form_row_edit").attr("action", link);
            $("#form_row_edit").find("input[name=_action]").val(action);
            $("#form_row_edit").find("input[name=_user_key]").val(user_key);
            $("#form_row_edit").submit();
            //действия
        });
    });
</script>

<style>
    img.edit-icon {
        cursor: pointer;
    }
</style>

<pre>
    <?php //print_r($contentData);
    ?>
</pre>

<h1>Таблица "<?= $tableTitle ?>"</h1>

<?= $errorMessage ?>

<form id="form_row_edit" action="" method="get">
    <input type="hidden" name="_action" value="">
    <input type="hidden" name="_user_key" value="">
</form>

<table class="table-edit">
    <tr>
        <th></th>
        <?php foreach ($tableColumns as $column) { ?>
            <th><?= $column['title'] ?></th>
        <?php } ?>
        <th colspan=3><img class="edit-icon" id="btn_new" src="/img/icon_add.gif" alt="Добавить"></th>
    </tr>
    <?php foreach ($tableData as $id => $row) { ?>
        <tr>
            <td></td>
            <?php foreach ($tableColumns as $column) { ?>
                <td class="<?= $column['class'] ?>"><?= $row[$column['name']] ?></td>
            <?php } ?>
            <td class="w10"><img class="edit-icon" id="btn_edit-<?= $id ?>" src="/img/icon_edit.gif" alt="Редактировать" title="Редактировать"></td>
            <td class="w10"><img class="edit-icon" id="btn_copy-<?= $id ?>" src="/img/icon_copy.gif" alt="Копировать" title="Копировать"></td>
            <td class="w10"><img class="edit-icon" id="btn_del-<?= $id ?>" src="/img/icon_del.gif" alt="Удалить" title="Удалить"></td>
        </tr>
    <?php } ?>
</table>


<?php include_once('templates/page_footer.php'); ?>
<?php
