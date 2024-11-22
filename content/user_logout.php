<?php

$user->logout();
header('Location: http://' . $_SERVER['SERVER_NAME'] . '/user_logon');
