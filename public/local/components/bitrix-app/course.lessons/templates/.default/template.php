<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<?php if ($arResult['ITEMS']): ?>
    <section class="course-lessons" aria-labelledby="course-lessons-title">
        <h2 id="course-lessons-title">Уроки курса</h2>
        <div class="course-lessons__list">
            <?php foreach ($arResult['ITEMS'] as $item): ?>
                <article class="course-lessons__item">
                    <span class="course-lessons__number" aria-hidden="true"><?=htmlspecialcharsbx($item['NUMBER'])?></span>
                    <h3><a href="/courses/<?=htmlspecialcharsbx($arParams['COURSE_CODE'])?>/lessons/<?=htmlspecialcharsbx($item['CODE'])?>/?lang=<?=htmlspecialcharsbx($arParams['LANGUAGE'] ?? 'ru')?>"><?=htmlspecialcharsbx($item['TITLE'])?></a></h3>
                    <p><?=htmlspecialcharsbx($item['DESCRIPTION'])?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>
