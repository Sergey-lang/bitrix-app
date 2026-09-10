<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

final class BitrixAppCourseListComponent extends CBitrixComponent
{
    public function executeComponent(): void
    {
        if (!CModule::IncludeModule('iblock')) {
            $this->arResult = ['TITLE' => 'Курсы', 'DESCRIPTION' => '', 'ITEMS' => []];
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
                ['ID', 'NAME', 'CODE', 'PREVIEW_TEXT', 'PROPERTY_LEVEL', 'PROPERTY_DURATION']
            );

            while ($item = $result->GetNext()) {
                $items[] = [
                    'CODE' => $item['CODE'],
                    'TITLE' => $item['NAME'],
                    'LEVEL' => $item['PROPERTY_LEVEL_VALUE'],
                    'DURATION' => $item['PROPERTY_DURATION_VALUE'],
                    'DESCRIPTION' => $item['PREVIEW_TEXT'],
                ];
            }
        }

        $this->arResult = [
            'TITLE' => 'Курсы',
            'DESCRIPTION' => 'Практические курсы для изучения 1С-Битрикс.',
            'ITEMS' => $items,
        ];

        $this->includeComponentTemplate();
    }
}
