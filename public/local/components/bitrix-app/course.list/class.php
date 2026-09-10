<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

final class BitrixAppCourseListComponent extends CBitrixComponent
{
    public function executeComponent(): void
    {
        $language = strtolower((string)($_GET['lang'] ?? 'ru')) === 'en' ? 'en' : 'ru';
        if (!CModule::IncludeModule('iblock')) {
            $this->arResult = ['TITLE' => 'Курсы', 'DESCRIPTION' => '', 'ITEMS' => [], 'LANGUAGE' => $language];
            $this->includeComponentTemplate();
            return;
        }

        $iblock = CIBlock::GetList(
            [],
            ['TYPE' => 'content', 'CODE' => 'courses'],
            false,
            ['nTopCount' => 1],
            ['ID']
        )->Fetch();

        $items = [];
        if ($iblock) {
            $result = CIBlockElement::GetList(
                ['SORT' => 'ASC', 'ID' => 'ASC'],
                ['IBLOCK_ID' => (int)$iblock['ID'], 'ACTIVE' => 'Y', 'CHECK_PERMISSIONS' => 'Y'],
                false,
                false,
                ['ID', 'NAME', 'CODE', 'PREVIEW_TEXT', 'PROPERTY_LEVEL', 'PROPERTY_DURATION', 'PROPERTY_TITLE_EN', 'PROPERTY_DESCRIPTION_EN']
            );

            while ($item = $result->GetNext()) {
                $items[] = [
                    'CODE' => $item['CODE'],
                    'TITLE' => $language === 'en' && $item['PROPERTY_TITLE_EN_VALUE'] ? $item['PROPERTY_TITLE_EN_VALUE'] : $item['NAME'],
                    'LEVEL' => $item['PROPERTY_LEVEL_VALUE'],
                    'DURATION' => $item['PROPERTY_DURATION_VALUE'],
                    'DESCRIPTION' => $language === 'en' && $item['PROPERTY_DESCRIPTION_EN_VALUE'] ? $item['PROPERTY_DESCRIPTION_EN_VALUE'] : $item['PREVIEW_TEXT'],
                ];
            }
        }

        $this->arResult = [
            'TITLE' => 'Курсы',
            'DESCRIPTION' => $language === 'en' ? 'Practical courses for learning 1C-Bitrix.' : 'Практические курсы для изучения 1С-Битрикс.',
            'ITEMS' => $items,
            'LANGUAGE' => $language,
        ];

        $this->includeComponentTemplate();
    }
}
