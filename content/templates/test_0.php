<?php

// Массив с данными в табличной форме
$data = [
     1 => ['id' =>  1, 'content' => 'Ствол дерева', 'parent' => 0],
     2 => ['id' =>  2, 'content' => 'Ветка дерева 1', 'parent' => 1],
     3 => ['id' =>  3, 'content' => 'Ветка дерева 2', 'parent' => 1],
     4 => ['id' =>  4, 'content' => 'Ветка дерева 3', 'parent' => 1],
     5 => ['id' =>  5, 'content' => 'Ветка дерева 4', 'parent' => 2],
     6 => ['id' =>  6, 'content' => 'Ветка дерева 5', 'parent' => 2],
     7 => ['id' =>  7, 'content' => 'Ветка дерева 6', 'parent' => 3],
     8 => ['id' =>  8, 'content' => 'Ветка дерева 7', 'parent' => 3],
     9 => ['id' =>  9, 'content' => 'Ветка дерева 8', 'parent' => 3],
    10 => ['id' => 10, 'content' => 'Ветка дерева 9', 'parent' => 4],
    11 => ['id' => 11, 'content' => 'Ветка дерева 10', 'parent' => 1],
];

// Вспомогательный массив веток
$arrayBranches = [];
foreach ($data as $item) {
    if (!isset($arrayBranches[$item['parent']])) {
        $arrayBranches[$item['parent']] = [];
    }
    $arrayBranches[$item['parent']][] = $item;
}

// Функция формирования древовидного массива
function getTreeItems($parentItem, $colNumber = 0)
{
    global $arrayBranches;
    $items = [];
    $rowCounts = 0;
    foreach ($parentItem as $item) {
        $id = $item['id'];
        if (isset($arrayBranches[$id])) {
            list($subItems, $rowCount) = getTreeItems($arrayBranches[$id], $colNumber + 1);
        } else {
            $rowCount = 1;
            $subItems = [];
        }
        $item['row-count'] = $rowCount;
        $item['col-number'] = $colNumber;
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
$jsTableArray = '';
$jsTableArray .= 'tableArray = [' . PHP_EOL;
// Функция формирования массива для вывода данных на экран в виде таблицы
function formTableArray($branch, $colNumber = 0)
{
    global $tableArray;
    global $columnCount;
    global $rowCount;
    global $jsTableArray;
    static $rowNuber = 0;
    $items = [];
    $columnCount = $colNumber + 1;
    foreach ($branch as $item) {
        if (!isset($tableArray[$rowNuber])) {
            $tableArray[$rowNuber] = [];
        }
        $tableArray[$rowNuber][$colNumber] = [
            'rowspan' => $item['row-count'] ?? 1,
            'content' => $item['content']
        ];
        $jsTableArray .= '{ "row": ' . $rowNuber . ', "col": ' . $colNumber . ', "rowspan": ' . ($item['row-count'] ?? 1) . ', "val": "' . $item['content'] . '"},' . PHP_EOL;
        if (count($item['items'] ?? []) > 0) {
            formTableArray($item['items'], $colNumber + 1);
        } else {
            $rowNuber++;
        }
    }
    $rowCount = $branch[0]['row-count'];
}

formTableArray($arrayTree);
$jsTableArray .= '];' . PHP_EOL;
$jsTableArray .= 'columnCount = ' . $columnCount . ';' . PHP_EOL;
$jsTableArray .= 'rowCount = ' . $rowCount . ';' . PHP_EOL;

?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <script src="/content/js/jquery-3.6.3.min.js"></script>
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
        }

        .c {
            text-align: center;
        }

        div.flex {
            display: flex;
            justify-content: center;
        }

        div.flex>div {
            padding: 8px;
        }

        div#tree-image {
            position: relative;
            border: solid 1px gray;
        }

        div.tree-item {
            /*display: table-cell;*/
            /*display: inline;*/
            position: absolute;
            /*position: relative;*/
            width: 160px;
            height: 40px;
            border: solid 1px gray;
            background-color: #fff;
            text-align: center;
            /*vertical-align: middle;*/
        }
    </style>

    <script>
        <?= $jsTableArray; ?>

        divWidth = 160;
        divHeight = 40;
        deltaX = 100;
        deltaY = 10;

        $(document).ready(function() {
            $("#tree-image").css("height", 10 + rowCount * 50);

            let canvas = document.getElementById("tree-image-canvas");
            canvas.setAttribute('width', 1000);
            canvas.setAttribute('height', 500);

            let ctx = canvas.getContext("2d");
            ctx.lineWidth = "1";
            ctx.strokeStyle = "blue"; // Green path

            rowStep = divWidth + deltaX;
            topStep = divHeight + deltaY;

            ctx.beginPath();

            $.each(tableArray, function(index, value) {
                x = deltaX + value['col'] * rowStep;
                y = deltaY + (value['row'] + (value['rowspan'] - 1) / 2) * topStep;
                x1 = x + divWidth / 2;
                y1 = y + divHeight / 2;
                x2 = x1 + 100;
                y2 = y1;
                ctx.moveTo(x1, y1);
                ctx.lineTo(x2, y2);
                var newDiv = $("<div>");
                newDiv.addClass("tree-item");
                newDiv.html(value['val']);
                newDiv.css("top", y);
                newDiv.css("left", x);
                $("#tree-image").append(newDiv);
            });

            ctx.stroke(); // Draw it

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
        <div>
            <h2>Вспомогательный массив веток</h2>
            <?php
            echo '<table class="border">';
            echo '<tr><td>ветка</td><td>id</td><td>содержание</td><td>родитель</td></tr>';
            foreach ($arrayBranches as $key => $branch) {
                echo '<tr><td rowspan=' . count($branch) . ' class="c">' . $key . '</td>';
                foreach ($branch as $item) {
                    echo '<td>' . $item['id'] . '</td><td>' . $item['content'] . '</td><td class="c">' . $item['parent'] . '</td></tr><tr>';
                }
            }
            echo '</table>';
            ?>
        </div>
        <div>
            <h2>Вывод информации в виде прямого дерева</h2>
            <table class="border">
                <?php
                foreach ($tableArray as $row) {
                    echo '<tr>';
                    foreach ($row as $cell) {
                        echo '<td rowspan="' . $cell['rowspan'] . '">' . $cell['content'] . '</td>';
                    }
                    echo '</tr>';
                }
                ?>
            </table>
        </div>
        <div>
            <h2>Вывод информации в виде обратного дерева</h2>
            <table class="border">
                <?php
                foreach ($tableArray as $row) {
                    echo '<tr>';
                    $ii = $columnCount - 1;
                    while (!isset($row[$ii])) {
                        echo '<td></td>';
                        $ii--;
                    }
                    while (isset($row[$ii])) {
                        echo '<td rowspan="' . $row[$ii]['rowspan'] . '">' . $row[$ii]['content'] . '</td>';
                        $ii--;
                    }
                    echo '</tr>';
                }
                ?>
            </table>
        </div>
    </div>

    <div id="tree-image">
        <canvas id="tree-image-canvas" width="300" height="200"></canvas>
    </div>


    <pre>
    <?php //print_r($arrayTree);
    ?>
    <?php //print_r($arrayBranches);
    ?>
    <?php print_r($tableArray);
    ?>
</pre>

</body>

</html>