<?php

global $app;
$orderList = $app->contentData['orderList'];

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
    $(document).ready(function() {
        $(".order-list-link").on("click", function() {
            [temp, row_id] = this.id.split('_');
            $(".order-content-list").hide();
            $("#order_" + row_id).show();
            //alert(row_id);
        });

        $(".order-content-list").hide();
        $(".order-content-list").first().show();

    });

</script>

<div class="orders">
    <div class="order-list">
        <h2>Заказы</h2>
        <?php foreach ($orderList as $orderId => $order) { ?>
            <div>
                <a href="#" class="order-list-link" id="a_<?= $orderId ?>">
                    <?= $order['date'] ?>
                    <?= $order['name'] ?>
                </a>
            </div>
        <?php } ?>
    </div>
    <div class="order-content">
        <h2>Состав заказа</h2>
        <?php foreach ($orderList as $orderId => $order) { ?>
            <div class="order-content-list" id="order_<?= $orderId ?>">
                <?php foreach ($order['products'] as $product) { ?>
                    <div>
                        <a href="#" id="a_<?= $orderId ?>">
                            <?= $product  ?>
                        </a>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>

</div>

<?php
//echo 'Hello<br>';
//echo $contentData;
?>



<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/content/templates/page_footer.php';
