<?php
$pageTitle = 'Страница не найдена';
include_once $_SERVER['DOCUMENT_ROOT'] . '/content/templates/page_header.php';
?>

<h1>Страница не найдена</h1>
Страница "<?=$pageRequest->url['path'] ?>" не найдена

<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/content/templates/page_footer.php';
?>
