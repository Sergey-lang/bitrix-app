<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<section class="course-progress" aria-labelledby="course-progress-title">
    <h2 id="course-progress-title">Прогресс курса</h2>
    <?php if (!$arResult['AUTHORIZED']): ?>
        <p>Войдите на сайт, чтобы отмечать пройденные уроки.</p>
    <?php elseif (!$arResult['LESSONS']): ?>
        <p>Уроки пока не добавлены.</p>
    <?php else: ?>
        <p class="course-progress__summary">Пройдено: <?=$arResult['PERCENT']?>%</p>
        <ol class="course-progress__lessons">
            <?php foreach ($arResult['LESSONS'] as $lesson): ?>
                <li class="<?= $lesson['COMPLETED'] ? 'is-completed' : '' ?>">
                    <span><?=htmlspecialcharsbx($lesson['TITLE'])?></span>
                    <?php if ($lesson['COMPLETED']): ?>
                        <strong>Готово</strong>
                    <?php else: ?>
                        <form method="post">
                            <?=bitrix_sessid_post()?>
                            <input type="hidden" name="lesson_id" value="<?=$lesson['ID']?>">
                            <button type="submit">Отметить пройденным</button>
                        </form>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ol>
    <?php endif; ?>
</section>
