<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

final class BitrixAppCourseListComponent extends CBitrixComponent
{
    public function executeComponent(): void
    {
        $this->arResult = [
            'TITLE' => 'Курсы',
            'DESCRIPTION' => 'Практические курсы для изучения 1С-Битрикс.',
            'ITEMS' => [
                [
                    'CODE' => 'bitrix-start',
                    'TITLE' => 'Старт в 1С-Битрикс',
                    'LEVEL' => 'Начальный',
                    'DURATION' => '4 недели',
                    'DESCRIPTION' => 'Структура проекта, административная панель и базовые настройки сайта.',
                ],
                [
                    'CODE' => 'components',
                    'TITLE' => 'Компоненты и шаблоны',
                    'LEVEL' => 'Средний',
                    'DURATION' => '6 недель',
                    'DESCRIPTION' => 'Создание собственных компонентов, шаблонов и подключение CSS и JavaScript.',
                ],
                [
                    'CODE' => 'iblocks',
                    'TITLE' => 'Инфоблоки и ORM',
                    'LEVEL' => 'Средний',
                    'DURATION' => '5 недель',
                    'DESCRIPTION' => 'Моделирование данных, инфоблоки, ORM и вывод динамического контента.',
                ],
            ],
        ];

        $this->includeComponentTemplate();
    }
}
