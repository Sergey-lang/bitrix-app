<?php
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');

$code = (string)($_GET['code'] ?? '');
$APPLICATION->SetAdditionalCSS('/local/components/bitrix-app/course.detail/templates/.default/style.css');

$APPLICATION->IncludeComponent(
    'bitrix-app:course.detail',
    '',
    ['CODE' => $code],
    false
);

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
