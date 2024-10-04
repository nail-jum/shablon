<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/content/templates/page_header.php';
?>

<h1>Отладочная информация</h1>

<pre>
<?php
echo '$_FILES' . PHP_EOL;
print_r($_FILES);
echo '$app' . PHP_EOL;
print_r($app);
?>
</pre>



<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/content/templates/page_footer.php';
