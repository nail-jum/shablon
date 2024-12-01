<?php

// Массив с данными в табличной форме
// Можно экспериментировать
// id должны быть уникальными !!!
$baseData = [
    ['id' =>  1, 'content' => 'Ветка 1 (ствол)', 'parent' => 0],
    ['id' =>  2, 'content' => 'Ветка дерева 2', 'parent' => 1],
    ['id' =>  3, 'content' => 'Ветка дерева 3', 'parent' => 1],
    ['id' =>  4, 'content' => 'Ветка дерева 4', 'parent' => 1],
    ['id' =>  5, 'content' => 'Ветка дерева 5', 'parent' => 2],
    ['id' =>  6, 'content' => 'Ветка дерева 6', 'parent' => 12],
    ['id' =>  7, 'content' => 'Ветка дерева 7', 'parent' => 3],
    ['id' =>  8, 'content' => 'Ветка дерева 8', 'parent' => 3],
    ['id' =>  9, 'content' => 'Ветка дерева 9', 'parent' => 3],
    ['id' => 10, 'content' => 'Ветка дерева 10', 'parent' => 4],
    ['id' => 11, 'content' => 'Ветка дерева 11', 'parent' => 1],
    ['id' => 12, 'content' => 'Ветка дерева 12', 'parent' => 4],
    ['id' => 13, 'content' => 'Ветка дерева 13', 'parent' => 99],
];

// Рабочий массив
$data = [];
foreach ($baseData as $item) {
    $data[$item['id']] = $item;
}
// Вспомогательный массив веток
$arrayBranches = [];
foreach ($baseData as $item) {
    $parent_id = $item['parent'];
    if (!isset($arrayBranches[$parent_id])) {
        $arrayBranches[$parent_id] = [];
    }
    $arrayBranches[$parent_id][] = $item['id'];
    if (isset($data[$parent_id])) {
        $data[$parent_id]['items'][] = $item['id'];
    }
}
// Функция формирования древовидного массива
function getTreeItems($idList, $colNumber = 0)
{
    global $arrayBranches;
    global $data;
    $items = [];
    $rowCounts = 0;
    foreach ($idList as $id) {
        if (isset($arrayBranches[$id])) {
            list($subItems, $rowCount) = getTreeItems($arrayBranches[$id], $colNumber + 1);
        } else {
            $rowCount = 1;
            $subItems = [];
        }
        $data[$id]['row-count'] = $rowCount;
        $data[$id]['col-number'] = $colNumber;
        $item['id'] = $id;
        $item['items'] = $subItems;
        $items[] = $item;
        $rowCounts += $rowCount;
    }
    return [$items, $rowCounts];
}
list($arrayTree, $treeRowCount) = getTreeItems($arrayBranches[0]);

$tableArray = [];
$columnCount = 0;
$rowCount = 0;
$jsData = '';
$jsData .= 'tableArray_0 = [' . PHP_EOL;
// Функция формирования массива для вывода данных на экран в виде таблицы
function formTableArray($branch, $colNumber = 0)
{
    global $data;
    global $tableArray;
    global $columnCount;
    global $rowCount;
    global $jsData;
    static $rowNuber = 0;
    //$items = [];
    $columnCount = $colNumber + 1;
    foreach ($branch as $item) {
        if (!isset($tableArray[$rowNuber])) {
            $tableArray[$rowNuber] = [];
        }
        $id = $item['id'];
        $tableArray[$rowNuber][$colNumber] = $id;
        $data[$id]['row-number'] = $rowNuber;
        $jsData .= '{ "row": ' . $rowNuber . ', "col": ' . $colNumber . ', "": "' . $data[$id]['content'] . '"},' . PHP_EOL;
        if (count($item['items'] ?? []) > 0) {
            formTableArray($item['items'], $colNumber + 1);
        } else {
            $rowNuber++;
        }
    }
    $rowCount = $data[$branch[0]['id']]['row-count'];
}
formTableArray($arrayTree);

$jsData .= '];' . PHP_EOL;
$jsData .= 'columnCount = ' . $columnCount . ';' . PHP_EOL;
$jsData .= 'rowCount = ' . $rowCount . ';' . PHP_EOL;

// Формирование массива для JS
$jsData .= 'dataArray = {' . PHP_EOL;
foreach ($data as $id => $row) {
    $jsData .= "\"$id\": {";
    foreach ($row as $key => $val) {
        if (is_array($val)) {
            $jsData .= "\"$key\": [";
            foreach ($val as $subVal) {
                $jsData .= $subVal . ',';
            }
            $jsData .= "], ";
        } else {
            $jsData .= "\"$key\": \"$val\", ";
        }
    }
    $jsData .= "}," . PHP_EOL;
}
$jsData .= "}," . PHP_EOL;

$jsData .= 'tableArray = {' . PHP_EOL;
foreach ($tableArray as $rowNumber => $row) {
    $jsData .= "$rowNumber: {";
    foreach ($row as $colNumber => $id) {
        $jsData .= "$colNumber: $id, ";
    }
    $jsData .= "}," . PHP_EOL;
}
$jsData .= "}," . PHP_EOL;

include_once $_SERVER['DOCUMENT_ROOT'] . '/content/templates/page_header.php';
/*
<!DOCTYPE html>
<html lang="ru">
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
*/
?>
    <style>
        * {
            font-family: Verdana, Geneva, Tahoma, sans-serif;
            font-size: 14px;
        }

        h1 {
            font-size: 16px;
            margin: 12px 0 16px;
            text-align: center;
            color: #082080;
        }

        h2 {
            font-size: 14px;
            margin: 12px 0 16px;
            text-align: center;
            color: #082080;
        }

        table.border {
            border-collapse: collapse;
            /*border: solid 1px gray;*/
        }

        table.border td {
            padding: 2px 8px;
            border: solid 1px gray;
            background-color: #f8f8f8;
        }

        table.border td.noborder {
            padding: 2px 8px;
            border: solid 0px gray;
            background-color: #ffffff;
        }

        .c {
            text-align: center;
        }

        div.flex {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }

        div.flex > div {
            padding: 8px;
        }

        div#tree-image {
            position: relative;
            margin: 0 auto;
            border: solid 1px gray;
        }

        div#tree-image-items {
            position: absolute;
            top: 0;
            left: 0;
        }

        div.tree-item {
            position: absolute;
            box-sizing: border-box;
            width: 160px;
            height: 40px;
            padding-top: 4px;
            border: solid 1px gray;
            background-color: #fff;
            text-align: center;
        }

        div.tree-item:hover {
            background-color: #b8ffff;
            cursor: pointer;
        }

        div.select-direct {
            width: 200px;
            margin: 0 auto 8px;
        }
    </style>

    <script>
        <?= $jsData; ?>
        divWidth = 140;
        divHeight = 40;
        deltaX = 100;
        deltaY = 15;
        let canvas;
        let ctx;

        function drawTree(direction) {
            // Очищаем все пространство холста
            $("#tree-image-items").empty();
            //ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.fillStyle = "#f4f4f4";
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.lineWidth = "1";
            ctx.strokeStyle = "blue";

            $.each(tableArray, function(rowNumber, row) {
                $.each(row, function(colNumber, id) {
                    rowspan = dataArray[id]['row-count'];
                    if (direction == 1) {
                        // прямое дерево
                        dataArray[id]['x'] = deltaX + colNumber * rowStep;
                    } else {
                        // обратное дерево
                        dataArray[id]['x'] = imageWidth - (parseInt(colNumber) + 1) * rowStep;
                    }
                    y = deltaY + (parseInt(rowNumber) + (rowspan - 1) / 2) * topStep;
                    dataArray[id]['y'] = y;
                    dataArray[id]['y1'] = dataArray[id]['y'] + divHeight / 2;
                });
            });

            // графический вывод элементов дерева
            ctx.beginPath();
            $.each(dataArray, function(id, item) {
                items = item['items'] ?? [];
                for (subId of items) {
                    if (direction == 1) {
                        // прямое дерево
                        ctx.moveTo(item['x'] + divWidth, item['y1']);
                        ctx.lineTo(dataArray[subId]['x'], dataArray[subId]['y1']);
                    } else {
                        // обратное дерево
                        ctx.moveTo(item['x'], item['y1']);
                        ctx.lineTo(dataArray[subId]['x'] + divWidth, dataArray[subId]['y1']);
                    }
                }
                var newDiv = $("<div>");
                newDiv.addClass("tree-item");
                newDiv.html(item['content']);
                newDiv.css("top", item['y']);
                newDiv.css("left", item['x']);
                $("#tree-image-items").append(newDiv);
            });
            ctx.stroke(); // Draw it
        } //drawTree()

        $(document).ready(function() {
            imageHeight = rowCount * (divHeight + deltaY) + deltaY;
            imageWidth = columnCount * (divWidth + deltaX) + deltaX;
            $("#tree-image").css("height", imageHeight);
            $("#tree-image").css("width", imageWidth);
            canvas = document.getElementById("tree-image-canvas");
            canvas.setAttribute('width', imageWidth);
            canvas.setAttribute('height', imageHeight);
            ctx = canvas.getContext("2d");

            rowStep = divWidth + deltaX;
            topStep = divHeight + deltaY;

            value = $('input[name="tree-direction"]:checked').val();
            drawTree(value);

            $('input[name="tree-direction"]').on('click', function() {
                value = $('input[name="tree-direction"]:checked').val();
                drawTree(value);
            });

            $(".tree-item").css("width", divWidth);
            $(".tree-item").css("height", divHeight);

            //console.debug(dataArray);
        });
    </script>
</head>

<body>

    <h1>Древовидная структура данных</h1>

    <div class="flex">
        <div>
            <h2>Хранение информации в таблице</h2>
            <?php
            echo '<table class="border">';
            echo '<tr><td>id</td><td>содержание</td><td>родитель</td></tr>';
            foreach ($data as $item) {
                echo '<tr><td>' . $item['id'] . '</td><td>' . $item['content'] . '</td><td class="c">' . $item['parent'] . '</td></tr>';
            }
            echo '</table>';
            ?>
        </div>
        <!--
        <div>
            <h2>Массив веток</h2>
            <?php
            echo '<table class="border">';
            echo '<tr><td>ветка</td><td>id</td></tr>';
            foreach ($arrayBranches as $key => $branch) {
                echo '<tr><td rowspan=' . count($branch) . ' class="c">' . $key . '</td>';
                foreach ($branch as $item) {
                    echo '<td>' . $item . '</td></tr><tr>';
                }
            }
            echo '</table>';
            ?>
        </div>
        <div>
            <h2>Рабочая таблица</h2>
            <?php
            echo '<table class="border">';
            echo '<tr><td>id</td><td>содержание</td><td>родитель</td><td>ветви</td><td>row-count</td><td>col-number</td><td>row-number</td></tr>';
            foreach ($data as $item) {
                echo '<tr><td>' . $item['id'] . '</td><td>' . $item['content'] . '</td><td class="c">' . $item['parent'] . '</td><td>';
                if (isset($item['items'])) {
                    echo implode(', ', $item['items']);
                }
                echo '</td>';
                echo '<td>' . $item['row-count'] . '</td>';
                echo '<td>' . $item['col-number'] . '</td>';
                echo '<td>' . $item['row-number'] . '</td>';
                echo '</tr>';
            }
            echo '</table>';
            ?>
        </div>
        -->
        <div>
            <h2>Вывод прямого дерева в виде таблицы</h2>
            <table class="border">
                <?php
                foreach ($tableArray as $row) {
                    echo '<tr>';
                    foreach ($row as $id) {
                        echo '<td rowspan="' . $data[$id]['row-count'] . '">' . $data[$id]['content'] . '</td>';
                    }
                    echo '</tr>';
                }
                ?>
            </table>
        </div>
        <div>
            <h2>Вывод обратного дерева в виде таблицы</h2>
            <table class="border">
                <?php
                foreach ($tableArray as $row) {
                    echo '<tr>';
                    $ii = $columnCount - 1;
                    while (!isset($row[$ii])) {
                        echo '<td class="noborder"></td>';
                        $ii--;
                    }
                    while (isset($row[$ii])) {
                        $id = $row[$ii];
                        echo '<td rowspan="' . $data[$id]['row-count'] . '">' . $data[$id]['content'] . '</td>';
                        $ii--;
                    }
                    echo '</tr>';
                }
                ?>
            </table>
        </div>
    </div>

    <h2>Вывод дерева в графическом виде (с использованием JavaScript и CSS)</h2>
    <div class="select-direct">
        <input type="radio" name="tree-direction" id="" value="1" checked>прямое дерево<br>
        <input type="radio" name="tree-direction" id="" value="2">обратное дерево
    </div>
    <div id="tree-image">
        <canvas id="tree-image-canvas"></canvas>
        <div id="tree-image-items">1234</div>
    </div>
    <!--
    <table>
        <tr style="vertical-align: top;">
            <td>
                <h2>$data</h2>
                <pre>
                <?php //print_r($data); ?>
                </pre>
            </td>
            <td>
                <h2>$arrayBranches</h2>
                <pre>
                <?php //print_r($arrayBranches); ?>
                </pre>
            </td>
            <td>
                <h2>$arrayTree</h2>
                <pre>
                <?php //print_r($arrayTree); ?>
                </pre>
            </td>
            <td>
                <h2>$tableArray</h2>
                <pre>
                <?php //print_r($tableArray); ?>
                </pre>
            </td>
        </tr>
    </table>
            -->

<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/content/templates/page_footer.php';
/*
</body>
</html>
*/