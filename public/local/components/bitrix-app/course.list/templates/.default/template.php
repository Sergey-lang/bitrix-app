<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<section class="course-list" aria-labelledby="course-list-title">
    <header class="course-list__header">
        <a class="course-list__account" href="/profile/">Личный кабинет →</a>
        <h1 id="course-list-title"><?=htmlspecialcharsbx($arResult['TITLE'])?></h1>
        <p><?=htmlspecialcharsbx($arResult['DESCRIPTION'])?></p>
    </header>

    <div class="course-list__grid">
        <?php foreach ($arResult['ITEMS'] as $item): ?>
            <article class="course-card">
                <p class="course-card__level"><?=htmlspecialcharsbx($item['LEVEL'])?></p>
                <h2><?=htmlspecialcharsbx($item['TITLE'])?></h2>
                <p><?=htmlspecialcharsbx($item['DESCRIPTION'])?></p>
                <footer class="course-card__footer">
                    <span><?=htmlspecialcharsbx($item['DURATION'])?></span>
                    <a href="/courses/<?=htmlspecialcharsbx($item['CODE'])?>/">Подробнее</a>
                </footer>
            </article>
        <?php endforeach; ?>
    </div>
</section>
