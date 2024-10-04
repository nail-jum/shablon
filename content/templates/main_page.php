<?php

global $app;
$orderList      = $app->contentData['orderList']['data'];
//$orderColumns   = $app->contentData['orderList']['columns'];
//$primaryKey     = $app->contentData['orderList']['primary_key'];

include_once $_SERVER['DOCUMENT_ROOT'] . '/content/templates/page_header.php';
?>

<br>
<table class="orders">
    <thead>
        <tr>
            <th>Наименование</th>
            <th>Менеджер</th>
            <th>Дата создания</th>
            <th>Дата завершения планируемая</th>
            <th>Примечание</th>
        </tr>
    </thead>
    <?php foreach ($orderList['data'] as $orderId => $order) { ?>
        <tr>
            <td><?= $order['name'] ?></td>
            <td><?= $order['manager'] ?></td>
            <td><?= $order['date_create'] ?></td>
            <td><?= $order['date_end_plan'] ?></td>
            <td><?= $order['comment'] ?></td>
        </tr>
    <?php } ?>
</table>

<pre>
<?php //print_r($orderList);
?>
</pre>
<?php
//echo $contentData;
?>



<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/content/templates/page_footer.php';
