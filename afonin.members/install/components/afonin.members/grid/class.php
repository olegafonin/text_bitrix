<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class CAfoninFormComponent extends CBitrixComponent{
	
	private $page_size = 15;
	private $page = 1;

	public function generateArOrder(){
		$this->arParams["AR_ORDER"] = Array();
	}
	
	public function generateArNavStartParams(){
		$request = Bitrix\Main\Context::getCurrent()->getRequest();
		if($request->get("page") !== null)$this->page = $request->get("page");
		$this->arParams["AR_NAV_START_PARAMS"] = Array("iNumPage"=> $this->page,"nPageSize" => $this->page_size);
	}

	public function generateArSelect(){
		$this->arParams["AR_SELECT"] = Array("ID", "IBLOCK_ID", "PROPERTY_*");
	}

	public function generateArFilter(){
		$this->arParams["AR_FILTER"] = Array("IBLOCK_CODE"=>'members', "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
	}

	public function getItems(){
		if (!CModule::IncludeModule("iblock")){
			$this->AbortResultCache();
			ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));

			return;
		}

		$arFilter = $this->arParams["AR_FILTER"];
		$arOrder = $this->arParams["AR_ORDER"];
		$arSelect = $this->arParams["AR_SELECT"];
		$arNavStartParams = $this->arParams["AR_NAV_START_PARAMS"];
		
		$this->arResult["CNT"] = CIBlockElement::GetList(Array(), $arFilter,  array(), false, $arSelect);
		$this->arResult["COUNT_ON_PAGE"] = $this->page_size;
		$this->arResult["PAGE"] = $this->page;

		$this->arResult["ITEMS"] = Array();
	
	
		$rs = CIBlockElement::GetList($arOrder, $arFilter, false, $arNavStartParams, $arSelect);
		while ($ob = $rs->GetNextElement()){
			$arFields = $ob->GetFields();
			$arFields["PROPS"] = $ob->GetProperties();

			$this->arResult["ITEMS"][] = $arFields;
		}
	}


	public function executeComponent(){
		$this->generateArOrder();
		$this->generateArFilter();
		$this->generateArSelect();
		$this->generateArNavStartParams();

		if ($this->startResultCache()){
			$this->getItems();
			$this->includeComponentTemplate();
		}
	}
}

?>