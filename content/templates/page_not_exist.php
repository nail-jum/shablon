<?php
$pageTitle = 'Страница не найдена';
include_once $_SERVER['DOCUMENT_ROOT'] . '/content/templates/page_header.php';
?>

<h1>Страница не найдена</h1>
Страница "<?=$app->url['path'] ?>" не найдена

<?php
echo '<pre>';
//echo '$pageName = ' . $pageName . PHP_EOL;
//print_r($app);
echo '</pre>';
include_once $_SERVER['DOCUMENT_ROOT'] . '/content/templates/page_footer.php';
?>
