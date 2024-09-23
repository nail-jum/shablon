<?php

global $app;
$orderList      = $app->contentData['orderList']['data'];
//$orderColumns   = $app->contentData['orderList']['columns'];
//$primaryKey     = $app->contentData['orderList']['primary_key'];

include_once $_SERVER['DOCUMENT_ROOT'] . '/content/templates/page_header.php';
?>
<style>
    div.orders {
        display: flex;
        justify-content: center;
    }

    div.order-list {
        flex-grow: 0;
        min-width: 300px;
        margin: 10px 5px 10px 10px;
        padding: 10px;
        border: dotted 1px grey;
    }

    div.order-list>div {
        margin: 10px;
        /*font-weight: bold;*/
    }

    div.order-content {
        flex-grow: 1;
        /*min-width: 400px;*/
        margin: 10px 10px 10px 5px;
        padding: 10px;
        border: dotted 1px grey;
    }

    div.order-content-list>div {
        margin: 10px;
    }
</style>

<script>
    function selectOrder(orderId) {
        $(".order-content-list").hide();
        $("#order_" + orderId).show();
        $(".order-list-link").css('color', '#082080');
        $("#a_" + orderId).css('color', 'red');
    }

    $(document).ready(function() {
        $(".order-list-link").on("click", function() {
            [temp, rowId] = this.id.split('_');
            selectOrder(rowId);
        });

        $(".order-content-list").hide();
        $(".order-content-list").first().show();

    });
</script>

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
