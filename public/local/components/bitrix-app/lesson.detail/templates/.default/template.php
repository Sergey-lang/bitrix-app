<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<?php if ($arResult['NOT_FOUND']): ?>
    <article class="lesson-detail lesson-detail--not-found">
        <h1>Урок не найден</h1>
        <a href="/courses/">Вернуться к курсам</a>
    </article>
<?php else: ?>
    <article class="lesson-detail">
        <div class="lesson-detail__toolbar">
            <a href="/courses/<?=htmlspecialcharsbx($arResult['COURSE']['CODE'])?>/?lang=<?=htmlspecialcharsbx($arResult['LANGUAGE'])?>">← Все уроки курса</a>
            <span>Урок <?=htmlspecialcharsbx($arResult['LESSON']['NUMBER'])?></span>
        </div>
        <p class="lesson-detail__eyebrow"><?=htmlspecialcharsbx($arResult['COURSE']['TITLE'])?></p>
        <h1><?=htmlspecialcharsbx($arResult['LESSON']['TITLE'])?></h1>
        <div class="lesson-detail__text"><?=htmlspecialcharsbx($arResult['LESSON']['TEXT'])?></div>

        <?php if (!$arResult['AUTHORIZED']): ?>
            <p class="lesson-detail__notice">Войдите, чтобы отмечать уроки пройденными.</p>
            <a class="lesson-detail__button" href="/auth.php?backurl=<?=urlencode($_SERVER['REQUEST_URI'])?>">Войти на сайт</a>
        <?php elseif ($arResult['COMPLETED']): ?>
            <p class="lesson-detail__complete">✓ Урок пройден</p>
        <?php else: ?>
            <form method="post">
                <?=bitrix_sessid_post()?>
                <button class="lesson-detail__button" type="submit">Отметить урок пройденным</button>
            </form>
        <?php endif; ?>
    </article>
<?php endif; ?>
