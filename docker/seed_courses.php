<?php
$_SERVER['DOCUMENT_ROOT'] = '/var/www/html';
chdir($_SERVER['DOCUMENT_ROOT']);
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

if (!CModule::IncludeModule('iblock')) {
    throw new RuntimeException('The iblock module is unavailable.');
}

$typeId = 'content';
$type = CIBlockType::GetByID($typeId)->Fetch();
if (!$type) {
    $typeId = (new CIBlockType)->Add([
        'ID' => 'content',
        'SECTIONS' => 'Y',
        'IN_RSS' => 'N',
        'SORT' => 500,
        'LANG' => [
            'ru' => ['NAME' => 'Контент', 'SECTION_NAME' => 'Разделы'],
            'en' => ['NAME' => 'Content', 'SECTION_NAME' => 'Sections'],
        ],
    ]);
    if (!$typeId) {
        throw new RuntimeException('Failed to create the content iblock type.');
    }
}

$iblockId = 0;
$iblock = CIBlock::GetList([], ['TYPE' => $typeId, 'CODE' => 'courses', 'CHECK_PERMISSIONS' => 'N'])->Fetch();
if ($iblock) {
    $iblockId = (int)$iblock['ID'];
} else {
    $iblockId = (new CIBlock)->Add([
        'ACTIVE' => 'Y',
        'NAME' => 'Курсы',
        'CODE' => 'courses',
        'IBLOCK_TYPE_ID' => $typeId,
        'SITE_ID' => ['s1'],
        'SORT' => 100,
        'VERSION' => 2,
        'API_CODE' => 'Courses',
        'DESCRIPTION' => 'Учебные курсы портала Bitrix-app.',
        'DESCRIPTION_TYPE' => 'text',
    ]);
    if (!$iblockId) {
        throw new RuntimeException('Failed to create the courses iblock: ' . CIBlock::GetApplicationError());
    }

    $property = new CIBlockProperty;
    foreach ([
        ['NAME' => 'Уровень', 'CODE' => 'LEVEL'],
        ['NAME' => 'Длительность', 'CODE' => 'DURATION'],
    ] as $fields) {
        if (!$property->Add($fields + ['IBLOCK_ID' => $iblockId, 'PROPERTY_TYPE' => 'S', 'SORT' => 100])) {
            throw new RuntimeException('Failed to create course property: ' . $property->LAST_ERROR);
        }
    }
}

// Курсы доступны для чтения авторизованным пользователям и посетителям сайта.
CIBlock::SetPermission($iblockId, [2 => 'R', 3 => 'R']);

$courses = [
    ['CODE' => 'bitrix-start', 'NAME' => 'Старт в 1С-Битрикс', 'LEVEL' => 'Начальный', 'DURATION' => '4 недели', 'TEXT' => 'Структура проекта, административная панель и базовые настройки сайта.'],
    ['CODE' => 'components', 'NAME' => 'Компоненты и шаблоны', 'LEVEL' => 'Средний', 'DURATION' => '6 недель', 'TEXT' => 'Создание собственных компонентов, шаблонов и подключение CSS и JavaScript.'],
    ['CODE' => 'iblocks', 'NAME' => 'Инфоблоки и ORM', 'LEVEL' => 'Средний', 'DURATION' => '5 недель', 'TEXT' => 'Моделирование данных, инфоблоки, ORM и вывод динамического контента.'],
];

$element = new CIBlockElement;
foreach ($courses as $course) {
    if (CIBlockElement::GetList([], ['IBLOCK_ID' => $iblockId, '=CODE' => $course['CODE']], false, false, ['ID'])->Fetch()) {
        continue;
    }

    $elementId = $element->Add([
        'IBLOCK_ID' => $iblockId,
        'ACTIVE' => 'Y',
        'NAME' => $course['NAME'],
        'CODE' => $course['CODE'],
        'PREVIEW_TEXT' => $course['TEXT'],
        'PREVIEW_TEXT_TYPE' => 'text',
        'PROPERTY_VALUES' => [
            'LEVEL' => $course['LEVEL'],
            'DURATION' => $course['DURATION'],
        ],
    ]);
    if (!$elementId) {
        throw new RuntimeException('Failed to create course: ' . $element->LAST_ERROR);
    }
}

echo "Courses iblock ready: {$iblockId}\n";
