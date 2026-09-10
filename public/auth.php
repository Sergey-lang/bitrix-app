<?
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

if (is_string($_REQUEST["backurl"]) && mb_strpos($_REQUEST["backurl"], "/") === 0)
{
	LocalRedirect($_REQUEST["backurl"]);
}

$APPLICATION->SetTitle("Авторизация");
$APPLICATION->SetAdditionalCSS('/local/css/student-cabinet.css');
?>
<section class="student-cabinet student-cabinet--guest">
    <p class="student-cabinet__eyebrow">Учебный портал</p>
    <h1>Вход выполнен</h1>
    <p>Теперь можно открыть личный кабинет и продолжить обучение.</p>
    <a class="student-cabinet__button" href="/profile/">Открыть кабинет</a>
</section>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
