<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<?php if (!$arResult['AUTHORIZED']): ?>
    <section class="student-cabinet student-cabinet--guest">
        <p class="student-cabinet__eyebrow">Личный кабинет</p>
        <h1>Войдите, чтобы продолжить обучение</h1>
        <p>После входа здесь появятся ваши курсы и прогресс по урокам.</p>
        <a class="student-cabinet__button" href="/auth.php?backurl=/profile/">Войти на сайт</a>
    </section>
<?php else: ?>
    <section class="student-cabinet" aria-labelledby="student-cabinet-title">
        <header class="student-cabinet__header">
            <div>
                <p class="student-cabinet__eyebrow">Личный кабинет</p>
                <h1 id="student-cabinet-title">Привет, <?=htmlspecialcharsbx($arResult['USER_NAME'])?></h1>
                <p>Продолжайте обучение с того места, где остановились.</p>
            </div>
            <a class="student-cabinet__logout" href="/auth.php?logout=yes">Выйти</a>
        </header>

        <?php if (!$arResult['COURSES']): ?>
            <p class="student-cabinet__empty">Курсы пока не добавлены.</p>
        <?php else: ?>
            <div class="student-cabinet__grid">
                <?php foreach ($arResult['COURSES'] as $course): ?>
                    <article class="student-course-card">
                        <div class="student-course-card__topline">
                            <span><?=htmlspecialcharsbx($course['DURATION'])?></span>
                            <strong><?=$course['PERCENT']?>%</strong>
                        </div>
                        <h2><?=htmlspecialcharsbx($course['TITLE'])?></h2>
                        <div class="student-course-card__bar"><span style="width: <?=$course['PERCENT']?>%"></span></div>
                        <p>Пройдено уроков: <?=$course['COMPLETED']?> из <?=$course['TOTAL']?></p>
                        <a href="/courses/<?=htmlspecialcharsbx($course['CODE'])?>/">Продолжить курс →</a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
<?php endif; ?>
