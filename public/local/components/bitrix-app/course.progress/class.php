<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

final class BitrixAppCourseProgressComponent extends CBitrixComponent
{
    public function executeComponent(): void
    {
        global $USER, $APPLICATION;
        $this->arResult = ['AUTHORIZED' => $USER->IsAuthorized(), 'LESSONS' => [], 'PERCENT' => 0];
        $code = trim((string)($this->arParams['COURSE_CODE'] ?? ''));

        if ($code === '' || !CModule::IncludeModule('iblock')) {
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
            'IBLOCK_ID' => (int)$courseIblock['ID'], '=CODE' => $code,
            'ACTIVE' => 'Y', 'CHECK_PERMISSIONS' => 'Y',
        ], false, ['nTopCount' => 1], ['ID'])->Fetch();
        if (!$course) {
            $this->includeComponentTemplate();
            return;
        }

        $lessons = [];
        $result = CIBlockElement::GetList(
            ['PROPERTY_NUMBER' => 'ASC', 'SORT' => 'ASC'],
            ['IBLOCK_ID' => (int)$lessonIblock['ID'], 'PROPERTY_COURSE' => $course['ID'], 'ACTIVE' => 'Y', 'CHECK_PERMISSIONS' => 'Y'],
            false, false, ['ID', 'NAME', 'PROPERTY_NUMBER']
        );
        while ($lesson = $result->GetNext()) {
            $lessons[(int)$lesson['ID']] = [
                'ID' => (int)$lesson['ID'],
                'TITLE' => $lesson['NAME'],
                'NUMBER' => $lesson['PROPERTY_NUMBER_VALUE'],
                'COMPLETED' => false,
            ];
        }

        if ($this->arResult['AUTHORIZED'] && $lessons) {
            $connection = \Bitrix\Main\Application::getConnection();
            $sql = sprintf(
                "SELECT LESSON_ID FROM b_bitrix_app_course_progress WHERE USER_ID = %d AND COURSE_ID = %d",
                (int)$USER->GetID(),
                (int)$course['ID']
            );
            try {
                $completed = $connection->query($sql);
                while ($row = $completed->fetch()) {
                    if (isset($lessons[(int)$row['LESSON_ID']])) {
                        $lessons[(int)$row['LESSON_ID']]['COMPLETED'] = true;
                    }
                }
            } catch (Throwable $exception) {
                // The local progress table is created by docker/seed_progress.php.
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_bitrix_sessid() && (int)($_POST['lesson_id'] ?? 0)) {
                $lessonId = (int)$_POST['lesson_id'];
                if (isset($lessons[$lessonId])) {
                    $connection->queryExecute(sprintf(
                        "INSERT IGNORE INTO b_bitrix_app_course_progress (USER_ID, COURSE_ID, LESSON_ID, COMPLETED_AT) VALUES (%d, %d, %d, NOW())",
                        (int)$USER->GetID(), (int)$course['ID'], $lessonId
                    ));
                    LocalRedirect($APPLICATION->GetCurPageParam('', []));
                }
            }
        }

        $this->arResult['LESSONS'] = array_values($lessons);
        $total = count($lessons);
        $completed = count(array_filter($lessons, static fn(array $lesson): bool => $lesson['COMPLETED']));
        $this->arResult['PERCENT'] = $total ? (int)round($completed * 100 / $total) : 0;
        $this->includeComponentTemplate();
    }
}
