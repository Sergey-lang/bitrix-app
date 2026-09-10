<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

final class BitrixAppLessonDetailComponent extends CBitrixComponent
{
    public function executeComponent(): void
    {
        global $USER, $APPLICATION;

        $this->arResult = [
            'NOT_FOUND' => true,
            'AUTHORIZED' => $USER->IsAuthorized(),
            'COMPLETED' => false,
            'COURSE' => null,
            'LESSON' => null,
        ];
        $courseCode = trim((string)($this->arParams['COURSE_CODE'] ?? ''));
        $lessonCode = trim((string)($this->arParams['LESSON_CODE'] ?? ''));

        if ($courseCode === '' || $lessonCode === '' || !CModule::IncludeModule('iblock')) {
            $this->includeComponentTemplate();
            return;
        }

        $courseIblock = CIBlock::GetList([], ['TYPE' => 'content', 'CODE' => 'courses'], false, ['nTopCount' => 1], ['ID'])->Fetch();
        $lessonIblock = CIBlock::GetList([], ['TYPE' => 'content', 'CODE' => 'lessons'], false, ['nTopCount' => 1], ['ID'])->Fetch();
        if (!$courseIblock || !$lessonIblock) {
            $this->includeComponentTemplate();
            return;
        }

        $course = CIBlockElement::GetList([], [
            'IBLOCK_ID' => (int)$courseIblock['ID'], '=CODE' => $courseCode,
            'ACTIVE' => 'Y', 'CHECK_PERMISSIONS' => 'Y',
        ], false, ['nTopCount' => 1], ['ID', 'NAME', 'CODE'])->Fetch();
        if (!$course) {
            CHTTP::SetStatus('404 Not Found');
            $this->includeComponentTemplate();
            return;
        }

        $lesson = CIBlockElement::GetList([], [
            'IBLOCK_ID' => (int)$lessonIblock['ID'], '=CODE' => $lessonCode,
            'PROPERTY_COURSE' => $course['ID'], 'ACTIVE' => 'Y', 'CHECK_PERMISSIONS' => 'Y',
        ], false, ['nTopCount' => 1], ['ID', 'NAME', 'CODE', 'PREVIEW_TEXT', 'PROPERTY_NUMBER'])->GetNext();
        if (!$lesson) {
            CHTTP::SetStatus('404 Not Found');
            $this->includeComponentTemplate();
            return;
        }

        $this->arResult['NOT_FOUND'] = false;
        $this->arResult['COURSE'] = ['TITLE' => $course['NAME'], 'CODE' => $course['CODE']];
        $this->arResult['LESSON'] = [
            'ID' => (int)$lesson['ID'],
            'TITLE' => $lesson['NAME'],
            'CODE' => $lesson['CODE'],
            'NUMBER' => $lesson['PROPERTY_NUMBER_VALUE'],
            'TEXT' => $lesson['PREVIEW_TEXT'],
        ];

        if ($this->arResult['AUTHORIZED']) {
            $connection = \Bitrix\Main\Application::getConnection();
            $userId = (int)$USER->GetID();
            $courseId = (int)$course['ID'];
            $lessonId = (int)$lesson['ID'];
            try {
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_bitrix_sessid()) {
                    $connection->queryExecute(sprintf(
                        'INSERT IGNORE INTO b_bitrix_app_course_progress (USER_ID, COURSE_ID, LESSON_ID, COMPLETED_AT) VALUES (%d, %d, %d, NOW())',
                        $userId, $courseId, $lessonId
                    ));
                    LocalRedirect($APPLICATION->GetCurPageParam('', []));
                }
                $completed = $connection->query(sprintf(
                    'SELECT ID FROM b_bitrix_app_course_progress WHERE USER_ID = %d AND COURSE_ID = %d AND LESSON_ID = %d',
                    $userId, $courseId, $lessonId
                ))->fetch();
                $this->arResult['COMPLETED'] = (bool)$completed;
            } catch (Throwable $exception) {
                // The local progress table is created by docker/seed_progress.php.
            }
        }

        $this->includeComponentTemplate();
    }
}
