<?php

$errorMessage = '';
if ($contentData['status'] == 'error') {
    $errorMessage .= '<div class="error"><h2>Ошибка</h2>' . implode('<br>', $contentData['message']) . '</div>';
}
if ($contentData['status'] == 'warning') {
    $errorMessage .= '<div class="warning"><h2>Предупреждение</h2>' . implode('<br>', $contentData['message']) . '</div>';
}

include_once('templates/page_header.php');
?>

<h1>Таблица "<?= $table['title'] ?>"</h1>



<?php include_once('templates/page_footer.php'); ?>
