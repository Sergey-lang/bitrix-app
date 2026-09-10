<?php
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');

$APPLICATION->SetTitle('Личный кабинет');
$APPLICATION->SetAdditionalCSS('/local/css/student-cabinet.css');

$APPLICATION->IncludeComponent(
    'bitrix-app:student.profile',
    '',
    [],
    false
);

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
