<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

if ($arResult['NOT_FOUND'] ?? false): ?>
    <section class="course-detail course-detail--not-found">
        <h1>Курс не найден</h1>
        <p>Проверьте ссылку или вернитесь к <a href="/courses/">списку курсов</a>.</p>
    </section>
<?php else: ?>
    <article class="course-detail">
        <a class="course-detail__back" href="/courses/">← Все курсы</a>
        <p class="course-detail__level"><?=htmlspecialcharsbx($arResult['ITEM']['LEVEL'])?></p>
        <h1><?=htmlspecialcharsbx($arResult['ITEM']['TITLE'])?></h1>
        <p class="course-detail__duration">Длительность: <?=htmlspecialcharsbx($arResult['ITEM']['DURATION'])?></p>
        <div class="course-detail__description">
            <?=htmlspecialcharsbx($arResult['ITEM']['DESCRIPTION'])?>
        </div>
        <?php
        $APPLICATION->SetAdditionalCSS('/local/components/bitrix-app/course.lessons/templates/.default/style.css');
        $APPLICATION->IncludeComponent(
            'bitrix-app:course.lessons',
            '',
            ['COURSE_CODE' => $arResult['ITEM']['CODE']],
            false
        );
        ?>
    </article>
<?php endif; ?>
