<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

final class BitrixAppStudentProfileComponent extends CBitrixComponent
{
    public function executeComponent(): void
    {
        global $USER;

        $this->arResult = [
            'AUTHORIZED' => $USER->IsAuthorized(),
            'USER_NAME' => '',
            'COURSES' => [],
        ];

        if (!$this->arResult['AUTHORIZED'] || !CModule::IncludeModule('iblock')) {
            $this->includeComponentTemplate();
            return;
        }

        $this->arResult['USER_NAME'] = trim((string)$USER->GetFullName()) ?: (string)$USER->GetLogin();
        $courseIblock = CIBlock::GetList([], ['TYPE' => 'content', 'CODE' => 'courses'], false, ['nTopCount' => 1], ['ID'])->Fetch();
        $lessonIblock = CIBlock::GetList([], ['TYPE' => 'content', 'CODE' => 'lessons'], false, ['nTopCount' => 1], ['ID'])->Fetch();
        if (!$courseIblock || !$lessonIblock) {
            $this->includeComponentTemplate();
            return;
        }

        $courses = [];
        $courseResult = CIBlockElement::GetList(
            ['SORT' => 'ASC', 'ID' => 'ASC'],
            ['IBLOCK_ID' => (int)$courseIblock['ID'], 'ACTIVE' => 'Y', 'CHECK_PERMISSIONS' => 'Y'],
            false, false, ['ID', 'NAME', 'CODE', 'PROPERTY_DURATION']
        );
        while ($course = $courseResult->GetNext()) {
            $courseId = (int)$course['ID'];
            $courses[$courseId] = [
                'CODE' => $course['CODE'],
                'TITLE' => $course['NAME'],
                'DURATION' => $course['PROPERTY_DURATION_VALUE'],
                'TOTAL' => 0,
                'COMPLETED' => 0,
                'PERCENT' => 0,
            ];
        }

        if (!$courses) {
            $this->includeComponentTemplate();
            return;
        }

        $lessonResult = CIBlockElement::GetList(
            [],
            ['IBLOCK_ID' => (int)$lessonIblock['ID'], 'ACTIVE' => 'Y', 'CHECK_PERMISSIONS' => 'Y'],
            false, false, ['ID', 'PROPERTY_COURSE']
        );
        while ($lesson = $lessonResult->GetNext()) {
            $courseId = (int)$lesson['PROPERTY_COURSE_VALUE'];
            if (isset($courses[$courseId])) {
                $courses[$courseId]['TOTAL']++;
            }
        }

        try {
            $connection = \Bitrix\Main\Application::getConnection();
            $progress = $connection->query(sprintf(
                'SELECT COURSE_ID, COUNT(*) AS COMPLETED FROM b_bitrix_app_course_progress WHERE USER_ID = %d GROUP BY COURSE_ID',
                (int)$USER->GetID()
            ));
            while ($row = $progress->fetch()) {
                $courseId = (int)$row['COURSE_ID'];
                if (isset($courses[$courseId])) {
                    $courses[$courseId]['COMPLETED'] = min((int)$row['COMPLETED'], $courses[$courseId]['TOTAL']);
                }
            }
        } catch (Throwable $exception) {
            // The local progress table is created by docker/seed_progress.php.
        }

        foreach ($courses as &$course) {
            $course['PERCENT'] = $course['TOTAL'] ? (int)round($course['COMPLETED'] * 100 / $course['TOTAL']) : 0;
        }
        unset($course);

        $this->arResult['COURSES'] = array_values($courses);
        $this->includeComponentTemplate();
    }
}
