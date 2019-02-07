<?
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Application;

Loc::loadMessages(__FILE__);

Class afonin_members extends CModule{
	var	$MODULE_ID = 'afonin.members';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;

	function __construct(){
		$arModuleVersion = array();
		include(__DIR__."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = Loc::getMessage("AFONIN_MEMBERS_MODULE_NAME");
		$this->MODULE_DESCRIPTION = Loc::getMessage("AFONIN_MEMBERS_MODULE_DESC");

		$this->PARTNER_NAME = getMessage("AFONIN_MEMBERS_PARTNER_NAME");
		$this->PARTNER_URI = getMessage("AFONIN_MEMBERS_PARTNER_URI");
	}

	function InstallDB($arParams = array()){
		$this->createNecessaryIblocks();
	}

	function UnInstallDB($arParams = array()){
		\Bitrix\Main\Config\Option::delete($this->MODULE_ID);
		$this->deleteNecessaryIblocks();
	}

	function InstallFiles($arParams = array()){
		$path = $this->GetPath()."/install/components";

		if (\Bitrix\Main\IO\Directory::isDirectoryExists($path)){
			CopyDirFiles($path, $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
		}

		if (\Bitrix\Main\IO\Directory::isDirectoryExists($path = $this->GetPath().'/install/files')){
			$this->copyArbitraryFiles();
		}

		return true;
	}

	function UnInstallFiles(){
		\Bitrix\Main\IO\Directory::deleteDirectory($_SERVER["DOCUMENT_ROOT"].'/bitrix/components/'.$this->MODULE_ID.'/');

		if (\Bitrix\Main\IO\Directory::isDirectoryExists($path = $this->GetPath().'/install/files')){
			$this->deleteArbitraryFiles();
		}

		return true;
	}

	function copyArbitraryFiles(){
		$rootPath = $_SERVER["DOCUMENT_ROOT"];
		$localPath = $this->GetPath().'/install/files';

		$dirIterator = new RecursiveDirectoryIterator($localPath, RecursiveDirectoryIterator::SKIP_DOTS);
		$iterator = new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::SELF_FIRST);

		foreach ($iterator as $object){
			$destPath = $rootPath.DIRECTORY_SEPARATOR.$iterator->getSubPathName();
			($object->isDir()) ? mkdir($destPath) : copy($object, $destPath);
		}
	}

	function deleteArbitraryFiles(){
		$rootPath = $_SERVER["DOCUMENT_ROOT"];
		$localPath = $this->GetPath().'/install/files';

		$dirIterator = new RecursiveDirectoryIterator($localPath, RecursiveDirectoryIterator::SKIP_DOTS);
		$iterator = new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::SELF_FIRST);

		foreach ($iterator as $object){
			if (!$object->isDir()){
				$file = str_replace($localPath, $rootPath, $object->getPathName());
				\Bitrix\Main\IO\File::deleteFile($file);
			}
		}
	}

	function createNecessaryIblocks(){
		$iblockType = $this->createIblockType();
		$iblockID = $this->createIblock(
			Array(
				"IBLOCK_TYPE_ID" => $iblockType,
				"ACTIVE" => "Y",
				"LID" => $this->getSitesIdsArray(),
				"NAME" => Loc::getMessage("AFONIN_MEMBERS_IBLOCK_PODPISCHIKI_NAME"),
				"CODE" => "members",
				"SORT" => "500",
				"LIST_PAGE_URL" => "#SITE_DIR#/members/index.php?ID=#IBLOCK_ID#",
				"SECTION_PAGE_URL" => "#SITE_DIR#/members/list.php?SECTION_ID=#SECTION_ID#",
				"DETAIL_PAGE_URL" => "#SITE_DIR#/members/detail.php?ID=#ELEMENT_ID#",
			)
		);
		$prop160ID = $this->createIblockProp(
			Array(
				"IBLOCK_ID" => $iblockID,
				"ACTIVE" => "Y",
				"SORT" => "500",
				"CODE" => "NUM",
				"NAME" => Loc::getMessage("AFONIN_MEMBERS_IBLOCK_PODPISCHIKI_PARAM_160_NAME"),
				"PROPERTY_TYPE" => "N",
				"USER_TYPE" => "Sequence",
				"USER_TYPE_SETTINGS" => 'a:1:{s:5:"write";s:1:"N";}',
				"MULTIPLE" => "N",
				"IS_REQUIRED" => "Y",
			)
		);
		$prop161ID = $this->createIblockProp(
			Array(
				"IBLOCK_ID" => $iblockID,
				"ACTIVE" => "Y",
				"SORT" => "500",
				"CODE" => "EMAIL",
				"NAME" => Loc::getMessage("AFONIN_MEMBERS_IBLOCK_PODPISCHIKI_PARAM_161_NAME"),
				"PROPERTY_TYPE" => "S",
				"USER_TYPE" => "",
				"MULTIPLE" => "N",
				"IS_REQUIRED" => "N",
			)
		);
		$prop162ID = $this->createIblockProp(
			Array(
				"IBLOCK_ID" => $iblockID,
				"ACTIVE" => "Y",
				"SORT" => "500",
				"CODE" => "NAME",
				"NAME" => Loc::getMessage("AFONIN_MEMBERS_IBLOCK_PODPISCHIKI_PARAM_162_NAME"),
				"PROPERTY_TYPE" => "S",
				"USER_TYPE" => "",
				"MULTIPLE" => "N",
				"IS_REQUIRED" => "N",
			)
		);
		$prop163ID = $this->createIblockProp(
			Array(
				"IBLOCK_ID" => $iblockID,
				"ACTIVE" => "Y",
				"SORT" => "500",
				"CODE" => "DATE_ADD",
				"NAME" => Loc::getMessage("AFONIN_MEMBERS_IBLOCK_PODPISCHIKI_PARAM_163_NAME"),
				"PROPERTY_TYPE" => "S",
				"USER_TYPE" => "Date",
				"MULTIPLE" => "N",
				"IS_REQUIRED" => "N",
			)
		);
		$prop164ID = $this->createIblockProp(
			Array(
				"IBLOCK_ID" => $iblockID,
				"ACTIVE" => "Y",
				"SORT" => "500",
				"CODE" => "DATE_EDIT",
				"NAME" => Loc::getMessage("AFONIN_MEMBERS_IBLOCK_PODPISCHIKI_PARAM_164_NAME"),
				"PROPERTY_TYPE" => "S",
				"USER_TYPE" => "Date",
				"MULTIPLE" => "N",
				"IS_REQUIRED" => "N",
			)
		);
	}

	function deleteNecessaryIblocks(){
		$this->removeIblockType();
	}

	function isVersionD7(){
		return CheckVersion(\Bitrix\Main\ModuleManager::getVersion('main'), '14.00.00');
	}

	function GetPath($notDocumentRoot = false){
		if ($notDocumentRoot){
			return str_ireplace(Application::getDocumentRoot(), '', dirname(__DIR__));
		}else{
			return dirname(__DIR__);
		}
	}

	function getSitesIdsArray(){
		$ids = Array();
		$rsSites = CSite::GetList($by = "sort", $order = "desc");
		while ($arSite = $rsSites->Fetch()){
			$ids[] = $arSite["LID"];
		}

		return $ids;
	}

	function createIblockType(){
		global $DB, $APPLICATION;
		CModule::IncludeModule("iblock");

		$iblockType = "afonin_members_iblock_type";
		$db_iblock_type = CIBlockType::GetList(Array("SORT" => "ASC"), Array("ID" => $iblockType));
		if (!$ar_iblock_type = $db_iblock_type->Fetch()){
			$arFieldsIBT = Array(
				'ID'       => $iblockType,
				'SECTIONS' => 'Y',
				'IN_RSS'   => 'N',
				'SORT'     => 500,
				'LANG'     => Array(
					'en' => Array(
						'NAME' => Loc::getMessage("AFONIN_MEMBERS_IBLOCK_TYPE_NAME_EN"),
					),
					'ru' => Array(
						'NAME' => Loc::getMessage("AFONIN_MEMBERS_IBLOCK_TYPE_NAME_RU"),
					)
				)
			);

			$obBlocktype = new CIBlockType;
			$DB->StartTransaction();
			$resIBT = $obBlocktype->Add($arFieldsIBT);
			if (!$resIBT){
				$DB->Rollback();
				$APPLICATION->ThrowException(Loc::getMessage("AFONIN_MEMBERS_IBLOCK_TYPE_ALREADY_EXISTS"));
			}else{
				$DB->Commit();

				return $iblockType;
			}
		}
	}

	function removeIblockType(){
		global $APPLICATION, $DB;
		CModule::IncludeModule("iblock");

		$iblockType = "afonin_members_iblock_type";

		$DB->StartTransaction();
		if (!CIBlockType::Delete($iblockType)){
			$DB->Rollback();
			$APPLICATION->ThrowException(Loc::getMessage("AFONIN_MEMBERS_IBLOCK_TYPE_DELETION_ERROR"));
		}
		$DB->Commit();
	}

	function createIblock($params){
		global $APPLICATION;
		CModule::IncludeModule("iblock");

		$ib = new CIBlock;

		$resIBE = CIBlock::GetList(Array(), Array('TYPE' => $params["IBLOCK_TYPE_ID"], 'SITE_ID' => $params["SITE_ID"], "CODE" => $params["CODE"]));
		if ($ar_resIBE = $resIBE->Fetch()){
			$APPLICATION->ThrowException(Loc::getMessage("AFONIN_MEMBERS_IBLOCK_ALREADY_EXISTS"));

			return false;
		}else{
			$ID = $ib->Add($params);

			return $ID;
		}

		return false;
	}

	function createIblockProp($arFieldsProp){
		CModule::IncludeModule("iblock");
		$ibp = new CIBlockProperty;

		return $ibp->Add($arFieldsProp);
	}

	function createIblockElement($arFields){
		CModule::IncludeModule("iblock");
		$el = new CIBlockElement;

		if ($PRODUCT_ID = $el->Add($arFields)){
			return $PRODUCT_ID;
		}

		return false;
	}

	function DoInstall(){

		global $APPLICATION;
		if ($this->isVersionD7()){
			\Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);

			$this->InstallDB();
			$this->InstallFiles();
		}else{
			$APPLICATION->ThrowException(Loc::getMessage("AFONIN_MEMBERS_INSTALL_ERROR_VERSION"));
		}

		$APPLICATION->IncludeAdminFile(Loc::getMessage("AFONIN_MEMBERS_INSTALL"), $this->GetPath()."/install/step.php");
	}

	function DoUninstall(){

		global $APPLICATION;

		$context = Application::getInstance()->getContext();
		$request = $context->getRequest();

		$this->UnInstallFiles();
		$this->UnInstallDB();

		\Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);

		$APPLICATION->IncludeAdminFile(Loc::getMessage("AFONIN_MEMBERS_UNINSTALL"), $this->GetPath()."/install/unstep.php");
	}
}
?>