<?php

$login = $_POST['login'] ?? '';
$password = $_POST['password'] ?? '';

// Определение страницы перенаправления
if (isset($_POST['requestUrl'])) {
    $requestUrl = $_POST['requestUrl'];
} elseif (isset($_GET['prevUrl'])) {
    $requestUrl = 'http://' . $_SERVER['SERVER_NAME'] . $_GET['prevUrl'];
} else {
    $requestUrl = 'http://' . $_SERVER['SERVER_NAME'];
}

// Проверка пользователя
if ((strlen($login) > 0) && (strlen($password) > 0)) {
    $user->logon($login, $password);
}

// Перенаправление на страницу сайта
if ($user->isInit) {
    header('Location: ' . $requestUrl);
}

$pageTitle = 'Утилиты';
$add_page_header = '
<script>
$(document).ready(function() {
    $("#login").focus();
});
</script>
';
require_once $_SERVER['DOCUMENT_ROOT'] . '/content/page_header.php';

?>

    <h2>Авторизация</h2>
    <form method="post" action="/user_logon">
        <div class="form-div">
            <div class="form-item">
                <div class="label">
                    Имя
                </div>
                <div class="input">
                    <input type="text" name="login" id="login" value="<?= $login ?>">
                </div>
            </div>
            <div class="form-item">
                <div class="label">
                    Пароль
                </div>
                <div class="input">
                    <input type="password" name="password" id="password" value="<?= $password ?>">
                </div>
            </div>
            <input type="hidden" name="requestUrl" value="<?= $requestUrl ?>">
            <input type="submit" value="Отправить">
        </div>
    </form>

    <pre>
        <?php //print_r($request) ?>
    </pre>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/content/page_footer.php';
?>
