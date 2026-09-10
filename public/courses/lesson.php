<?php
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');

$courseCode = (string)($_GET['course'] ?? '');
$lessonCode = (string)($_GET['lesson'] ?? '');
$language = strtolower((string)($_GET['lang'] ?? 'ru')) === 'en' ? 'en' : 'ru';
$APPLICATION->SetAdditionalCSS('/local/css/course-theme.css');
$APPLICATION->SetAdditionalCSS('/local/components/bitrix-app/lesson.detail/templates/.default/style.css');

$APPLICATION->IncludeComponent(
    'bitrix-app:lesson.detail',
    '',
    ['COURSE_CODE' => $courseCode, 'LESSON_CODE' => $lessonCode, 'LANGUAGE' => $language],
    false
);

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
