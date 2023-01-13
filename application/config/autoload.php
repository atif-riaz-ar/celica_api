<?php
defined('BASEPATH') or exit('No direct script access allowed');
$autoload['model'] = array(
	"AdminModel",
	"AnnouncementModel",
	"BankModel",
	"CartModel",
	"CashBankModel",
	"ConversionModel",
	"CommissionModel",
	"CountryModel",
	"CurrencyModel",
	"DeliveriesModel",
	"DistributionModel",
	"DownlineModel",
	"HelperModel",
	"GroupModel",
	"InvoiceModel",
	"LanguageModel",
	"LedgerModel",
	"MemberModel",
	"MenuModel",
	"NetworkModel",
	"OrderModel",
	"PaymentModel",
	"PdfModel",
	"ProductModel",
	"PackagePurchaseStripeModel",
	"PackagePurchaseWalletModel",
	"ProductPurchaseCodModel",
	"ProductPurchaseHitpayModel",
	"ProductPurchaseStripeModel",
	"ProductPurchaseWalletModel",
	"RankModel",
	"ReportModel",
	"SaleModel",
	"SessionModel",
	"SettingModel",
	"SignupModel",
	"StarModel",
	"SystemModel",
	"TopupModel",
	"TransferModel",
	"WithdrawalModel"
);
$autoload['helper'] = array(
	"file",
	"ci",
	"password",
	"response",
	"sms",
	"time",
	"url"
);
$autoload['libraries'] = array("database");
$autoload['packages'] = array();
$autoload['drivers'] = array();
$autoload['config'] = array();
$autoload['language'] = array();

