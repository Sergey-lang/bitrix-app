<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

final class BitrixAppCourseLessonsComponent extends CBitrixComponent
{
    public function executeComponent(): void
    {
        $this->arResult = ['ITEMS' => []];
        $code = trim((string)($this->arParams['COURSE_CODE'] ?? ''));

        if ($code !== '' && CModule::IncludeModule('iblock')) {
            $course = CIBlock::GetList([], ['TYPE' => 'content', 'CODE' => 'courses'], false, ['nTopCount' => 1], ['ID'])->Fetch();
            $lessons = CIBlock::GetList([], ['TYPE' => 'content', 'CODE' => 'lessons'], false, ['nTopCount' => 1], ['ID'])->Fetch();

            if ($course && $lessons) {
                $courseElement = CIBlockElement::GetList([], [
                    'IBLOCK_ID' => (int)$course['ID'],
                    '=CODE' => $code,
                    'ACTIVE' => 'Y',
                    'CHECK_PERMISSIONS' => 'Y',
                ], false, ['nTopCount' => 1], ['ID'])->Fetch();

                if ($courseElement) {
                    $result = CIBlockElement::GetList(
                        ['PROPERTY_NUMBER' => 'ASC', 'SORT' => 'ASC'],
                        [
                            'IBLOCK_ID' => (int)$lessons['ID'],
                            'PROPERTY_COURSE' => $courseElement['ID'],
                            'ACTIVE' => 'Y',
                            'CHECK_PERMISSIONS' => 'Y',
                        ],
                        false,
                        false,
                        ['ID', 'NAME', 'PREVIEW_TEXT', 'PROPERTY_NUMBER']
                    );

                    while ($item = $result->GetNext()) {
                        $this->arResult['ITEMS'][] = [
                            'TITLE' => $item['NAME'],
                            'DESCRIPTION' => $item['PREVIEW_TEXT'],
                            'NUMBER' => $item['PROPERTY_NUMBER_VALUE'],
                        ];
                    }
                }
            }
        }

        $this->includeComponentTemplate();
    }
}
