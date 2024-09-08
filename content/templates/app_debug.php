<?php

$pageTitle = 'Страница входа на сайт';

include_once $_SERVER['DOCUMENT_ROOT'] . '/content/templates/page_header.php';
?>

<h1>Отладочная информация</h1>

<pre>
<?php print_r($app) ?>
</pre>



<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/content/templates/page_footer.php';
