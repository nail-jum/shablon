<?php

// Страница вывода заказов со списком изделий

global $app;
$orderList      = $app->contentData['orderList'];
$hatchTypes     = $app->contentData['hatchTypes'];
$lockingTypes   = $app->contentData['lockingTypes'];
$colors         = $app->contentData['colors'];

include_once $_SERVER['DOCUMENT_ROOT'] . '/content/templates/page_header.php';
?>

<style>
    div.order-info {
        min-width: 200px;
    }

    div.order-info table {
        border: 0px;
        border-collapse: collapse;
        margin: 0;
        font-size: 14px;
        color: #404040;
        display: none;
    }

    div.order-info table td,
    div.order-info table th {
        border: 0px;
        padding: 2px 2px;
    }

    .order-title-name {
        cursor: pointer;
    }

    .order-info-name {
        font-size: 12px;
        font-weight: bold;
        color: #202020;
        cursor: pointer;
    }
</style>

<script>
    $(document).ready(function() {
        $(".order-info-name").on("click", function() {
            id = this.id.substr(2);
            $(".div_" + id).children("table").toggle();
            return false;
        });
        $(".order-title-name").on("click", function() {
            $(".order-info").children("table").toggle();
            return false;
        });
    });
</script>

<br>
<table class="orders">
    <thead>
        <tr>
            <th colspan="2">Заказ</th>
            <th colspan="12">Изделия</th>
        </tr>
        <tr>
            <th class="order-title-name">Наименование</th>
            <th>Дата<br>завершения</th>
            <th>Наименование</th>
            <th>Кол-во</th>
            <th>Ширина</th>
            <th>Высота</th>
            <th>Тип люка</th>
            <th>Тип запирания</th>
            <th>Кол-во<br>створок</th>
        </tr>
    </thead>
    <?php foreach ($orderList as $orderId => $order) { ?>
        <?php $productCount = count($order['products']); ?>
        <tr>
            <td rowspan="<?= $productCount ?>">
                <?php
                ?>
                <span class="order-info-name" id="a_<?= $order['order_id'] ?>"><?= $order['name'] ?></span>
                <div class="order-info div_<?= $order['order_id'] ?>">
                    <table>
                        <tr>
                            <td class="r">Дата&nbsp;создания:</td>
                            <td class="l"><?= MyFuncDatetime::mysql2date($order['date_create']) ?></td>
                        </tr>
                        <tr>
                            <td class="r">Менеджер:</td>
                            <td class="l"><?= $order['manager'] ?></td>
                        </tr>
                        <tr>
                            <td class="r">Кол-во створок:</td>
                            <td class="l"><?= $order['flaps_count'] ?></td>
                        </tr>
                    </table>
                </div>
            </td>
            <td rowspan="<?= $productCount ?>"><?= MyFuncDatetime::mysql2date($order['date_end_plan']) ?></td>
            <?php foreach ($order['products'] as $product) { ?>
                <?php
                $color = $colors[$product['color_id']] ?? $colors[1];
                $flaps_count = (int) $product['flaps_count'] ?? 0;
                ?>
                <td>
                    <?= $order['name'] . '-' . $product['number'] ?>
                    <div class="order-info div_<?= $order['order_id'] ?>">
                        <table>
                            <tr>
                                <td class="r">Код&nbsp;двери:</td>
                                <td class="l"><?= $product['door_code'] ?></td>
                            </tr>
                            <tr>
                                <td class="r">Цвет:</td>
                                <td class="l"><?= $color['name'] ?></td>
                                <td style="width:30px; background-color: <?= $color['value'] ?>;"></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td class="l" colspan="2"><?= $color['description'] ?></td>
                            </tr>
                            <tr>
                                <td class="r">Кол-во створок:</td>
                                <td class="l"><?= $flaps_count ?></td>
                            </tr>
                            <tr>
                                <td class="r">Всего створок:</td>
                                <td class="l"><?= $flaps_count * $product['count'] ?></td>
                            </tr>
                            <tr>
                                <td class="r">Примечание:</td>
                                <td class="l"><?= $product['comment'] ?></td>
                            </tr>
                        </table>
                    </div>
                </td>
                <td class="c"><?= $product['count'] ?></td>
                <td class="c"><?= $product['width'] ?></td>
                <td class="c"><?= $product['height'] ?></td>
                <td><?= $hatchTypes[$product['hatch_type_id']]['name'] ?></td>
                <td><?= $lockingTypes[$product['lock_type_id']]['description'] ?? '' ?></td>
                <td class="c"><?= $product['flaps_count'] ?></td>
        </tr>
        <tr>
        <?php } ?>
        </tr>
    <?php } ?>
</table>


<pre>
<?php
//print_r($orderList);
?>
</pre>
<?php
//echo $contentData;
?>



<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/content/templates/page_footer.php';
