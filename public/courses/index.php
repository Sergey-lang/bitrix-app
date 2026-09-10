<?php
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');

$APPLICATION->SetTitle('Курсы');
$APPLICATION->SetAdditionalCSS('/local/css/course-theme.css');
$APPLICATION->SetAdditionalCSS('/local/components/bitrix-app/course.list/templates/.default/style.css');

$APPLICATION->IncludeComponent(
    'bitrix-app:course.list',
    '',
    [],
    false
);

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
