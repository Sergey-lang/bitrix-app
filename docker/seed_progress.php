<?php
$_SERVER['DOCUMENT_ROOT'] = '/var/www/html';
chdir($_SERVER['DOCUMENT_ROOT']);
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$connection = \Bitrix\Main\Application::getConnection();
$connection->queryExecute(
    'CREATE TABLE IF NOT EXISTS b_bitrix_app_course_progress (' .
    'ID int unsigned NOT NULL AUTO_INCREMENT,' .
    'USER_ID int unsigned NOT NULL,' .
    'COURSE_ID int unsigned NOT NULL,' .
    'LESSON_ID int unsigned NOT NULL,' .
    'COMPLETED_AT datetime NOT NULL,' .
    'PRIMARY KEY (ID),' .
    'UNIQUE KEY UX_COURSE_PROGRESS (USER_ID, COURSE_ID, LESSON_ID),' .
    'KEY IX_COURSE_PROGRESS_USER (USER_ID, COURSE_ID)' .
    ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
);

echo "Progress table ready\n";
