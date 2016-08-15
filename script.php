<? require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php"); ?>
<?
$rsMess = \CEventMessage::GetList($by = "site_id", $order = "desc", Array());
while ($arMess = $rsMess->GetNext()) {
    $newFields = [];

    if (preg_match("/(#SALE_EMAIL#|#DEFAULT_EMAIL_FROM#)/is", $arMess["EMAIL_FROM"], $match)) {
        $newFields["EMAIL_FROM"] = "#SITE_NAME# <" . $match[1] . ">";
    }

    if (preg_match("/(#SERVER_NAME#:)/is", $arMess["SUBJECT"], $match)) {
        $newFields["SUBJECT"] = trim(preg_replace("/(#SERVER_NAME#:)/is", "", $arMess["SUBJECT"]));
    }

    if (preg_match("/(#SITE_NAME#:)/is", $arMess["SUBJECT"], $match)) {
        $newFields["SUBJECT"] = trim(preg_replace("/(#SITE_NAME#:)/is", "", $arMess["SUBJECT"]));
    }

    if (preg_match("/(Интернет-магазина)/is", $arMess["MESSAGE"], $match)) {
        $newFields["MESSAGE"] = preg_replace("/(Интернет-магазина)/is", "#SITE_NAME#", $arMess["MESSAGE"]);
    }

    if (preg_match("/(#SALE_EMAIL#|#DEFAULT_EMAIL_FROM#)/is", $arMess["EMAIL_FROM"], $match)) {
        $newFields["MESSAGE"] = html_entity_decode($arMess["MESSAGE"]);
        $newFields["MESSAGE"] = preg_replace("#&lt;#is", "<", $newFields["MESSAGE"]);
        $newFields["MESSAGE"] = preg_replace("#&gt;#is", ">", $newFields["MESSAGE"]);
        $newFields["MESSAGE"] = preg_replace("#&quot;#is", "\"", $newFields["MESSAGE"]);
    }

    if (count($newFields)) {
        $newFields["BODY_TYPE"] = $arMess["BODY_TYPE"];
        $em = new CEventMessage;
        if ($em->Update($arMess["ID"], $newFields)) {
            cl($arMess["ID"]);
            cl($newFields);
        } else {
            cl($em->LAST_ERROR);
        }
    }
}
?>
