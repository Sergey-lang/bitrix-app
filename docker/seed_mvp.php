<?php
$_SERVER['DOCUMENT_ROOT'] = '/var/www/html';
chdir($_SERVER['DOCUMENT_ROOT']);
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

if (!CModule::IncludeModule('iblock')) {
    throw new RuntimeException('The iblock module is unavailable.');
}

$coursesIblock = CIBlock::GetList([], ['TYPE' => 'content', 'CODE' => 'courses'], false, ['nTopCount' => 1], ['ID'])->Fetch();
$lessonsIblock = CIBlock::GetList([], ['TYPE' => 'content', 'CODE' => 'lessons'], false, ['nTopCount' => 1], ['ID'])->Fetch();
if (!$coursesIblock || !$lessonsIblock) {
    throw new RuntimeException('Run seed_courses.php and seed_lessons.php first.');
}

function ensureStringProperty(int $iblockId, string $code, string $name, int $sort): void
{
    $property = CIBlockProperty::GetList([], ['IBLOCK_ID' => $iblockId, 'CODE' => $code])->Fetch();
    if (!$property && !(new CIBlockProperty)->Add([
        'IBLOCK_ID' => $iblockId,
        'NAME' => $name,
        'CODE' => $code,
        'PROPERTY_TYPE' => 'S',
        'SORT' => $sort,
    ])) {
        throw new RuntimeException('Failed to create property ' . $code);
    }
}

ensureStringProperty((int)$coursesIblock['ID'], 'TITLE_EN', 'Название на английском', 300);
ensureStringProperty((int)$coursesIblock['ID'], 'DESCRIPTION_EN', 'Описание на английском', 310);
ensureStringProperty((int)$lessonsIblock['ID'], 'TITLE_EN', 'Название урока на английском', 300);
ensureStringProperty((int)$lessonsIblock['ID'], 'DESCRIPTION_EN', 'Описание урока на английском', 310);

$courseTranslations = [
    'bitrix-start' => ['Bitrix Start', 'Project structure, administration panel and basic site settings.'],
    'components' => ['Components and Templates', 'Create custom components, templates and connect CSS and JavaScript.'],
    'iblocks' => ['Information Blocks and ORM', 'Model data, information blocks, ORM and dynamic content output.'],
];
foreach ($courseTranslations as $code => $translation) {
    $item = CIBlockElement::GetList([], ['IBLOCK_ID' => (int)$coursesIblock['ID'], '=CODE' => $code, 'CHECK_PERMISSIONS' => 'N'], false, ['nTopCount' => 1], ['ID'])->Fetch();
    if ($item) {
        CIBlockElement::SetPropertyValuesEx((int)$item['ID'], (int)$coursesIblock['ID'], [
            'TITLE_EN' => $translation[0], 'DESCRIPTION_EN' => $translation[1],
        ]);
    }
}

$lessonTranslations = [
    'project-structure' => ['Project Structure', 'Explore the public, bitrix and local directories.'],
    'admin-panel' => ['Administration Panel', 'Learn the main site management sections.'],
    'component-anatomy' => ['Component Anatomy', 'Study class.php, the description and the template.'],
    'component-template' => ['Component Template', 'Render data and connect styles.'],
    'iblock-model' => ['Information Block Model', 'Create information blocks, properties and elements.'],
    'iblock-query' => ['Information Block Query', 'Fetch elements through CIBlockElement.'],
];
foreach ($lessonTranslations as $code => $translation) {
    $item = CIBlockElement::GetList([], ['IBLOCK_ID' => (int)$lessonsIblock['ID'], '=CODE' => $code, 'CHECK_PERMISSIONS' => 'N'], false, ['nTopCount' => 1], ['ID'])->Fetch();
    if ($item) {
        CIBlockElement::SetPropertyValuesEx((int)$item['ID'], (int)$lessonsIblock['ID'], [
            'TITLE_EN' => $translation[0], 'DESCRIPTION_EN' => $translation[1],
        ]);
    }
}

$editorGroup = CGroup::GetList('ID', 'ASC', ['STRING_ID' => 'PORTAL_EDITOR'])->Fetch();
if (!$editorGroup) {
    $editorId = (new CGroup)->Add([
        'NAME' => 'Редакторы портала',
        'STRING_ID' => 'PORTAL_EDITOR',
        'ACTIVE' => 'Y',
        'C_SORT' => 200,
    ]);
    if (!$editorId) {
        throw new RuntimeException('Failed to create editor group.');
    }
} else {
    $editorId = (int)$editorGroup['ID'];
}

foreach ([(int)$coursesIblock['ID'], (int)$lessonsIblock['ID']] as $iblockId) {
    CIBlock::SetPermission($iblockId, [2 => 'R', 3 => 'R', $editorId => 'W']);
}

echo "MVP fields and editor group ready\n";
