<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<?php if ($arResult['ITEMS']): ?>
    <section class="course-lessons" aria-labelledby="course-lessons-title">
        <h2 id="course-lessons-title">Уроки курса</h2>
        <ol>
            <?php foreach ($arResult['ITEMS'] as $item): ?>
                <li>
                    <h3><?=htmlspecialcharsbx($item['TITLE'])?></h3>
                    <p><?=htmlspecialcharsbx($item['DESCRIPTION'])?></p>
                </li>
            <?php endforeach; ?>
        </ol>
    </section>
<?php endif; ?>
