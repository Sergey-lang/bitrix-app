<?php
define('NO_KEEP_STATISTIC', true);
define('NO_AGENT_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);
$_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__, 3);
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$lang = strtolower((string)($_GET['lang'] ?? 'ru')) === 'en' ? 'en' : 'ru';
$payload = ['success' => true, 'language' => $lang, 'items' => []];

if (!CModule::IncludeModule('iblock')) {
    http_response_code(503);
    echo json_encode(['success' => false, 'error' => 'iblock_unavailable'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$coursesIblock = CIBlock::GetList([], ['TYPE' => 'content', 'CODE' => 'courses'], false, ['nTopCount' => 1], ['ID'])->Fetch();
$lessonsIblock = CIBlock::GetList([], ['TYPE' => 'content', 'CODE' => 'lessons'], false, ['nTopCount' => 1], ['ID'])->Fetch();
if ($coursesIblock) {
    $courseResult = CIBlockElement::GetList(
        ['SORT' => 'ASC', 'ID' => 'ASC'],
        ['IBLOCK_ID' => (int)$coursesIblock['ID'], 'ACTIVE' => 'Y', 'CHECK_PERMISSIONS' => 'Y'],
        false, false,
        ['ID', 'NAME', 'CODE', 'PREVIEW_TEXT', 'PROPERTY_LEVEL', 'PROPERTY_DURATION', 'PROPERTY_TITLE_EN', 'PROPERTY_DESCRIPTION_EN']
    );
    while ($course = $courseResult->GetNext()) {
        $courseId = (int)$course['ID'];
        $title = $lang === 'en' && $course['PROPERTY_TITLE_EN_VALUE'] ? $course['PROPERTY_TITLE_EN_VALUE'] : $course['NAME'];
        $description = $lang === 'en' && $course['PROPERTY_DESCRIPTION_EN_VALUE'] ? $course['PROPERTY_DESCRIPTION_EN_VALUE'] : $course['PREVIEW_TEXT'];
        $item = [
            'id' => $courseId,
            'code' => $course['CODE'],
            'title' => $title,
            'description' => $description,
            'level' => $course['PROPERTY_LEVEL_VALUE'],
            'duration' => $course['PROPERTY_DURATION_VALUE'],
            'url' => '/courses/' . $course['CODE'] . '/',
            'lessons' => [],
        ];
        if ($lessonsIblock) {
            $lessonResult = CIBlockElement::GetList(
                ['PROPERTY_NUMBER' => 'ASC', 'SORT' => 'ASC'],
                ['IBLOCK_ID' => (int)$lessonsIblock['ID'], 'PROPERTY_COURSE' => $courseId, 'ACTIVE' => 'Y', 'CHECK_PERMISSIONS' => 'Y'],
                false, false,
                ['ID', 'NAME', 'CODE', 'PREVIEW_TEXT', 'PROPERTY_NUMBER', 'PROPERTY_TITLE_EN', 'PROPERTY_DESCRIPTION_EN']
            );
            while ($lesson = $lessonResult->GetNext()) {
                $item['lessons'][] = [
                    'id' => (int)$lesson['ID'],
                    'code' => $lesson['CODE'],
                    'title' => $lang === 'en' && $lesson['PROPERTY_TITLE_EN_VALUE'] ? $lesson['PROPERTY_TITLE_EN_VALUE'] : $lesson['NAME'],
                    'description' => $lang === 'en' && $lesson['PROPERTY_DESCRIPTION_EN_VALUE'] ? $lesson['PROPERTY_DESCRIPTION_EN_VALUE'] : $lesson['PREVIEW_TEXT'],
                    'number' => $lesson['PROPERTY_NUMBER_VALUE'],
                    'url' => '/courses/' . $course['CODE'] . '/lessons/' . $lesson['CODE'] . '/',
                ];
            }
        }
        $payload['items'][] = $item;
    }
}

echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
