<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Список записей");
$APPLICATION->IncludeComponent("afonin.members:grid", ".default", array(),
	false
);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>