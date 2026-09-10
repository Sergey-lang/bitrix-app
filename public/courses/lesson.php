<?php
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');

$courseCode = (string)($_GET['course'] ?? '');
$lessonCode = (string)($_GET['lesson'] ?? '');
$APPLICATION->SetAdditionalCSS('/local/css/course-theme.css');
$APPLICATION->SetAdditionalCSS('/local/components/bitrix-app/lesson.detail/templates/.default/style.css');

$APPLICATION->IncludeComponent(
    'bitrix-app:lesson.detail',
    '',
    ['COURSE_CODE' => $courseCode, 'LESSON_CODE' => $lessonCode],
    false
);

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
