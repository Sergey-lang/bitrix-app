<?php
$_SERVER['DOCUMENT_ROOT'] = '/var/www/html';
chdir($_SERVER['DOCUMENT_ROOT']);
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

if (!CModule::IncludeModule('iblock')) {
    throw new RuntimeException('The iblock module is unavailable.');
}

$coursesIblock = CIBlock::GetList([], [
    'TYPE' => 'content',
    'CODE' => 'courses',
    'CHECK_PERMISSIONS' => 'N',
])->Fetch();
if (!$coursesIblock) {
    throw new RuntimeException('The courses iblock must be seeded first.');
}

$lessonsIblock = CIBlock::GetList([], [
    'TYPE' => 'content',
    'CODE' => 'lessons',
    'CHECK_PERMISSIONS' => 'N',
])->Fetch();

if ($lessonsIblock) {
    $lessonsIblockId = (int)$lessonsIblock['ID'];
} else {
    $lessonsIblockId = (new CIBlock)->Add([
        'ACTIVE' => 'Y',
        'NAME' => 'Уроки',
        'CODE' => 'lessons',
        'IBLOCK_TYPE_ID' => 'content',
        'SITE_ID' => ['s1'],
        'SORT' => 110,
        'VERSION' => 2,
        'API_CODE' => 'Lessons',
        'DESCRIPTION' => 'Уроки учебных курсов.',
        'DESCRIPTION_TYPE' => 'text',
    ]);
    if (!$lessonsIblockId) {
        throw new RuntimeException('Failed to create the lessons iblock.');
    }

    $property = new CIBlockProperty;
    $property->Add([
        'IBLOCK_ID' => $lessonsIblockId,
        'NAME' => 'Курс',
        'CODE' => 'COURSE',
        'PROPERTY_TYPE' => 'E',
        'LINK_IBLOCK_ID' => (int)$coursesIblock['ID'],
        'SORT' => 100,
    ]);
    $property->Add([
        'IBLOCK_ID' => $lessonsIblockId,
        'NAME' => 'Номер урока',
        'CODE' => 'NUMBER',
        'PROPERTY_TYPE' => 'N',
        'SORT' => 200,
    ]);
}

CIBlock::SetPermission($lessonsIblockId, [2 => 'R', 3 => 'R']);

$lessons = [
    'bitrix-start' => [
        ['CODE' => 'project-structure', 'NAME' => 'Структура проекта', 'TEXT' => 'Разбираем каталоги public, bitrix и local.', 'NUMBER' => 10],
        ['CODE' => 'admin-panel', 'NAME' => 'Административная панель', 'TEXT' => 'Знакомимся с разделами управления сайтом.', 'NUMBER' => 20],
    ],
    'components' => [
        ['CODE' => 'component-anatomy', 'NAME' => 'Устройство компонента', 'TEXT' => 'Изучаем class.php, описание и шаблон компонента.', 'NUMBER' => 10],
        ['CODE' => 'component-template', 'NAME' => 'Шаблон компонента', 'TEXT' => 'Выводим данные и подключаем стили.', 'NUMBER' => 20],
    ],
    'iblocks' => [
        ['CODE' => 'iblock-model', 'NAME' => 'Модель инфоблока', 'TEXT' => 'Создаём инфоблоки, свойства и элементы.', 'NUMBER' => 10],
        ['CODE' => 'iblock-query', 'NAME' => 'Запрос к инфоблоку', 'TEXT' => 'Получаем элементы через CIBlockElement.', 'NUMBER' => 20],
    ],
];

$element = new CIBlockElement;
foreach ($lessons as $courseCode => $courseLessons) {
    $course = CIBlockElement::GetList([], [
        'IBLOCK_ID' => (int)$coursesIblock['ID'],
        '=CODE' => $courseCode,
        'CHECK_PERMISSIONS' => 'N',
    ], false, ['nTopCount' => 1], ['ID'])->Fetch();
    if (!$course) {
        continue;
    }

    foreach ($courseLessons as $lesson) {
        if (CIBlockElement::GetList([], [
            'IBLOCK_ID' => $lessonsIblockId,
            '=CODE' => $lesson['CODE'],
            'CHECK_PERMISSIONS' => 'N',
        ], false, ['nTopCount' => 1], ['ID'])->Fetch()) {
            continue;
        }

        if (!$element->Add([
            'IBLOCK_ID' => $lessonsIblockId,
            'ACTIVE' => 'Y',
            'NAME' => $lesson['NAME'],
            'CODE' => $lesson['CODE'],
            'PREVIEW_TEXT' => $lesson['TEXT'],
            'PREVIEW_TEXT_TYPE' => 'text',
            'PROPERTY_VALUES' => [
                'COURSE' => $course['ID'],
                'NUMBER' => $lesson['NUMBER'],
            ],
        ])) {
            throw new RuntimeException('Failed to create lesson: ' . $element->LAST_ERROR);
        }
    }
}

echo "Lessons iblock ready: {$lessonsIblockId}\n";
