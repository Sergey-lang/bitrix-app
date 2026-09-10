<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

final class BitrixAppCourseDetailComponent extends CBitrixComponent
{
    public function executeComponent(): void
    {
        $code = trim((string)($this->arParams['CODE'] ?? ''));
        $language = strtolower((string)($_GET['lang'] ?? 'ru')) === 'en' ? 'en' : 'ru';
        $this->arResult = ['ITEM' => null, 'LANGUAGE' => $language];

        if ($code !== '' && CModule::IncludeModule('iblock')) {
            $iblock = CIBlock::GetList(
                [],
                ['TYPE' => 'content', 'CODE' => 'courses'],
                false,
                ['nTopCount' => 1],
                ['ID']
            )->Fetch();

            if ($iblock) {
                $item = CIBlockElement::GetList(
                    [],
                    [
                        'IBLOCK_ID' => (int)$iblock['ID'],
                        '=CODE' => $code,
                        'ACTIVE' => 'Y',
                        'CHECK_PERMISSIONS' => 'Y',
                    ],
                    false,
                    ['nTopCount' => 1],
                    ['ID', 'NAME', 'CODE', 'PREVIEW_TEXT', 'PROPERTY_LEVEL', 'PROPERTY_DURATION', 'PROPERTY_TITLE_EN', 'PROPERTY_DESCRIPTION_EN']
                )->GetNext();

                if ($item) {
                    $this->arResult['ITEM'] = [
                        'CODE' => $item['CODE'],
                        'TITLE' => $language === 'en' && $item['PROPERTY_TITLE_EN_VALUE'] ? $item['PROPERTY_TITLE_EN_VALUE'] : $item['NAME'],
                        'LEVEL' => $item['PROPERTY_LEVEL_VALUE'],
                        'DURATION' => $item['PROPERTY_DURATION_VALUE'],
                        'DESCRIPTION' => $language === 'en' && $item['PROPERTY_DESCRIPTION_EN_VALUE'] ? $item['PROPERTY_DESCRIPTION_EN_VALUE'] : $item['PREVIEW_TEXT'],
                    ];
                }
            }
        }

        if (!$this->arResult['ITEM']) {
            CHTTP::SetStatus('404 Not Found');
            $this->arResult['NOT_FOUND'] = true;
            $this->arResult['TITLE'] = 'Курс не найден';
        } else {
            $this->arResult['TITLE'] = $this->arResult['ITEM']['TITLE'];
        }

        $this->includeComponentTemplate();
    }
}
