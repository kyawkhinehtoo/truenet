<?php

use App\Http\Controllers\PackageController;
use App\Http\Controllers\IncidentAlertController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PublicIpController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PortController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\SNPortController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\SmsGatewayController;
use App\Http\Controllers\RadiusController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\DailyReceiptController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BillingConfiguration;
use App\Http\Controllers\DBBackupController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SlaController;
use App\Http\Controllers\PopController;
use App\Http\Controllers\SubcomController;
use App\Http\Controllers\TownshipController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\EquiptmentController;

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\VoipController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\IncidentTaskController;

use Illuminate\Support\Facades\Route;

Route::redirect('/','/dashboard');

Route::group(['middleware'=>['auth','role']],function(){

	Route::resource('/user',UserController::class);
	Route::resource('/sla',SlaController::class);
	Route::resource('/pop',PopController::class);
	Route::resource('/port',PortController::class);
	Route::resource('/snport',SNPortController::class);
	Route::resource('/subcom',SubcomController::class);
	Route::resource('/township',TownshipController::class);
	Route::resource('/city',CityController::class);
	Route::resource('/equiptment',EquiptmentController::class);
	Route::resource('/menu',MenuController::class);
	Route::resource('/package',PackageController::class);
	Route::resource('/project',ProjectController::class);
	Route::resource('/status',StatusController::class);
	Route::resource('/role',RoleController::class);
	Route::resource('/voip',VoipController::class);
	Route::get('/activity-log', [ActivityLogController::class,'index'])->name('activity-log.index');
	Route::post('/activity-log', [ActivityLogController::class,'index']);
	Route::resource('/setting',SystemSettingController::class);
    Route::get('/generateSN',[SNPortController::class,'generateSN']);
	Route::delete('/snport/group/{id}',[SNPortController::class,'deleteGroup']);
	Route::delete('/port/group/{id}',[PortController::class,'deleteGroup']);
});
    Route::group(['middleware'=>'auth'],function(){
    Route::resource('/customer',CustomerController::class);
    Route::resource('/incident',IncidentController::class);

    Route::get('/dashboard',[DashboardController::class,'show'])->name('dashboard');
	Route::post('/uploadData',[FileController::class,'upload'])->name('upload');
    Route::get('importExportView',[ExcelController::class,'importExportView'])->name('importExportView');
    Route::post('/exportExcel',[ExcelController::class,'exportExcel'])->name('exportExcel');
	Route::post('importExcel',[ExcelController::class,'importExcel'])->name('importExcel');
	Route::post('updateExcel',[ExcelController::class,'updateExcel'])->name('updateExcel');

    Route::post('/port',[PortController::class,'index'])->name('port.search');
	Route::post('/port-store',[PortController::class,'store'])->name('port.store');
    Route::get('updateDNView',[ExcelController::class,'updateDNView'])->name('updateDNView');
	Route::get('updateSNView',[ExcelController::class,'updateSNView'])->name('updateSNView');
	Route::post('importDN',[ExcelController::class,'importDN'])->name('importDN');
	Route::post('importSN',[ExcelController::class,'importSN'])->name('importSN');
	Route::get('updatePackageView',[ExcelController::class,'updatePackageView'])->name('updatePackageView');
	Route::post('importPackage',[ExcelController::class,'importPackage'])->name('importPackage');


	Route::get('/getpackage/{id}',[PackageController::class,'getBundle']);
	Route::get('/incidentOverdue',[IncidentAlertController::class,'getOverdue']);
	Route::get('/incidentRemain',[IncidentAlertController::class,'getRemain']);
	Route::get('/getTask/{id}',[IncidentController::class,'getTask']);
	Route::get('/getLog/{id}',[IncidentController::class,'getLog']);
	Route::get('/getHistory/{id}',[IncidentController::class,'getHistory']);
	Route::get('/getFile/{id}',[IncidentController::class,'getFile']);
	Route::get('/getCustomerHistory/{id}',[CustomerController::class,'getHistory']);
	Route::get('/getCustomerFile/{id}',[IncidentController::class,'getCustomerFile']);
	Route::get('/getCustomerIp/{id}',[PublicIpController::class,'getCustomerIp']);
	Route::delete('/deleteFile/{id}',[IncidentController::class,'deleteFile']);
	Route::post('/addTask',[IncidentController::class,'addTask']);
	Route::put('/editTask/{id}',[IncidentController::class,'editTask']);
	Route::post('/getMenu',[MenuController::class,'getMenu']);
	Route::get('/getDnId/{id}',[PortController::class,'getSNByDN']);
	Route::get('/getDNInfo/{id}',[PortController::class,'getDNInfo']);
	Route::get('/getOLTByPOP/{id}',[PortController::class,'getOLTByPOP']);
	Route::get('/getDNByOLT/{id}',[PortController::class,'getDNByOLT']);
	Route::post('/customer/search/',[CustomerController::class,'show']);
	Route::get('/incidentlist',[IncidentController::class,'getIncident']);
	Route::get('/getCustomer/{id}',[IncidentController::class,'getCustomer']);

	//Routeforexport/downloadtabledatato.csv,.xlsor.xlsx



	Route::resource('/port',PortController::class);

	Route::resource('/snport',SNPortController::class);
	Route::get('/generateSN',[SNPortController::class,'generateSN']);
	Route::delete('/snport/group/{id}',[SNPortController::class,'deleteGroup']);
	Route::delete('/port/group/{id}',[PortController::class,'deleteGroup']);


	//Billing
	Route::get('tempImportView',[ExcelController::class,'tempImportView'])->name('tempImportView');
	Route::get('updateContractView',[ExcelController::class,'updateContractView'])->name('updateContractView');
	Route::get('updateTownshipView',[ExcelController::class,'updateTownshipView'])->name('updateTownshipView');
	Route::post('updateContract',[ExcelController::class,'updateContract'])->name('updateContract');
	Route::post('updateTownship',[ExcelController::class,'updateTownship'])->name('updateTownship');
	Route::post('updateTempExcel',[ExcelController::class,'updateTemp'])->name('updateTempExcel');
	Route::post('importPayment',[ExcelController::class,'importPayment'])->name('importPayment');
	Route::get('updateCustomerView',[ExcelController::class,'updateCustomerView'])->name('updateCustomerView');
	Route::post('updateCustomer',[ExcelController::class,'updateCustomer'])->name('updateCustomer');
	Route::post('/exportBillingExcel',[ExcelController::class,'exportBillingExcel'])->name('exportBillingExcel');
	Route::post('/exportTempBillingExcel',[ExcelController::class,'exportTempBillingExcel'])->name('exportTempBillingExcel');
	Route::post('/exportRevenue',[ExcelController::class,'exportRevenue'])->name('exportRevenue');
	Route::get('/billGenerator',[BillingController::class,'BillGenerator'])->name('billGenerator');
	Route::post('/updateTemp',[BillingController::class,'updateTemp'])->name('updateTemp');
	Route::post('/updateInvoice',[BillingController::class,'updateInvoice'])->name('updateInvoice');
	Route::post('/createInvoice',[BillingController::class,'createInvoice'])->name('createInvoice');
	Route::delete('/deleteInvoice/{id}',[BillingController::class,'destroyInvoice'])->name('deleteInvoice');

	Route::post('/doGenerate',[BillingController::class,'doGenerate']);
	Route::post('/saveFinal',[BillingController::class,'saveFinal']);
	//Route::post('/showbill',[BillingController::class,'showBill'])->name('showbill');
	Route::get('/showbill',[BillingController::class,'showBill'])->name('showbill');

	Route::get('/tempBilling',[BillingController::class,'goTemp'])->name('tempBilling');
	Route::post('/tempBilling/search/',[BillingController::class,'goTemp']);
	Route::post('/truncateBilling',[BillingController::class,'destroyall']);
	Route::resource('/billing',BillingController::class);

	//BillingPDF
	Route::get('/pdfpreview1/{id}',[BillingController::class,'preview_1']);
	Route::get('/pdfpreview2/{id}',[BillingController::class,'preview_2']);
	Route::get('/showInvoice/{id}',[BillingController::class,'showInvoice']);
	Route::get('/ReceiptTemplate/{id}',[ReceiptController::class,'template']);
	Route::post('/getSinglePDF/{id}',[BillingController::class,'makeSinglePDF']);
	Route::post('/getReceiptPDF/{id}',[ReceiptController::class,'makeReceiptPDF']);
	Route::post('/sendSingleEmail/{id}',[BillingController::class,'sendSingleEmail']);
	Route::post('/getAllPDF',[BillingController::class,'makeAllPDF']);
	Route::post('/sendSingleSMS/{id}',[BillingController::class,'sendSingleSMS']);
	Route::post('/sendAllSMS',[BillingController::class,'sendAllSMS']);
	Route::post('/sendBillReminder',[BillingController::class,'sendBillReminder']);

	//BillingReceipt
	Route::post('/saveReceipt',[ReceiptController::class,'store']);
	Route::post('/receipt/search',[ReceiptController::class,'show']);
	Route::resource('/receipt',ReceiptController::class);
	Route::get('/saveSingle',[BillingController::class,'saveSingle']);
	Route::get('/runSummery',[ReceiptController::class,'runReceiptSummery']);
	Route::get('/gettclmax',[CustomerController::class,'gettclmaxid']);
	Route::get('/getmkmax',[CustomerController::class,'getmkmaxid']);


	//EmailTemplate
	Route::resource('/template',EmailTemplateController::class);

	//SMSGatweay
	Route::resource('/smsgateway',SmsGatewayController::class);

	//RadiusGateway
	Route::resource('/radiusconfig',RadiusController::class);
	Route::get('/fillAllRadius',[RadiusController::class,'autofillRadius']);

	//Radius
	Route::get('/getRadiusInfo/{id}',[RadiusController::class,'getRadiusInfo']);
	Route::get('/getRadiusServices',[RadiusController::class,'getRadiusServices']);
	Route::post('/enableRadius/{id}',[RadiusController::class,'enableRadiusUser']);
	Route::post('/saveRadius/{id}',[RadiusController::class,'saveRadius']);
	Route::post('/createRadius/{id}',[RadiusController::class,'createRadius']);
	Route::post('/disableRadius/{id}',[RadiusController::class,'disableRadiusUser']);
	Route::get('/showRadius',[RadiusController::class,'display'])->name('showRadius');
	Route::post('/showRadius',[RadiusController::class,'display']);
	Route::post('/RadiusExport',[ExcelController::class,'exportRadiusReportExcel'])->name('RadiusExport');
	Route::post('/tempDeactivate/{id}',[RadiusController::class,'tempDeactivate']);
	Route::post('/tempActivate/{id}',[RadiusController::class,'tempActivate']);

	//Announcement
	Route::get('/announcement/listall',[AnnouncementController::class,'listAll'])->name('announcement.list');
	Route::get('/announcement/show',[AnnouncementController::class,'showAll']);
	Route::resource('/announcement',AnnouncementController::class);
	Route::post('/announcement/show',[AnnouncementController::class,'showAll']);
	Route::get('/announcement/detail/{id}',[AnnouncementController::class,'detail'])->name('announcement.detail');
	Route::post('/announcement/detail/{id}',[AnnouncementController::class,'detail']);
	Route::get('/announcement/log/{id}',[AnnouncementController::class,'log'])->name('announcement.log');
	Route::post('/announcement/detail/{id}/update',[AnnouncementController::class,'update']);
	Route::post('/announcement/detail/{id}/send',[AnnouncementController::class,'send']);
	Route::post('/exportAnnouncementLogExcel',[ExcelController::class,'exportAnnouncementLog'])->name('exportAnnouncementLog');

	//test
	Route::get('/testCustomer',[CustomerController::class,'preg_test']);
	//ServiceRequest
	Route::resource('/servicerequest',ServiceRequestController::class);

	//Reports

	Route::resource('/dailyreceipt',DailyReceiptController::class);
	Route::post('/dailyreceipt/show',[DailyReceiptController::class,'index']);
	Route::get('/dailyreceipt/show',[DailyReceiptController::class,'index'])->name('dailyreceipt');
	Route::post('/exportReceipt',[ExcelController::class,'exportReceipt'])->name('exportReceipt');

	Route::get('/incidentReport',[ReportController::class,'incidentReport'])->name('incidentReport');
	Route::post('/incidentReport',[ReportController::class,'incidentReport']);
	Route::get('/getIncidentDetail/{id}/{date}',[ReportController::class,'getIncidentDetail']);
	Route::post('/exportIncidentReportExcel',[ExcelController::class,'exportIncidentReportExcel'])->name('exportIncidentReportExcel');

	//BillConfiguration
	Route::resource('/billconfig',BillingConfiguration::class);

	//Utils
	Route::get('/sanitiseAllPhone',[BillingController::class,'sanitiseAllPhone']);

	//POPs
	Route::get('/getPackages/{id}',[PackageController::class,'getPackage']);

	Route::resource('/publicIP',PublicIpController::class);

	Route::get('/publicIpReport',[ReportController::class,'PublicIpReport'])->name('publicIpReport');
	Route::post('/publicIpReport',[ReportController::class,'PublicIpReport']);
	Route::post('/exportPublicIpReportExcel',[ExcelController::class,'exportPublicIpReportExcel'])->name('exportPublicIpReportExcel');
	Route::resource('/test',TestController::class);
	Route::post('/doTestPDF',[TestController::class,'makeSinglePDF']);
	Route::post('/delTestPDF',[TestController::class,'destroyPDF']);

	//SNReport
	Route::get('/dnSnReport',[ReportController::class,'dnSNReport'])->name('dnSnReport');
	Route::post('/dnSnReport',[ReportController::class,'dnSNReport']);

	//DBBackup
	Route::get('/dbbackup',[DBBackupController::class,'index'])->name('dbbackup.index');
	Route::post('/dbbackup',[DBBackupController::class,'update'])->name('dbbackup.update');
	Route::post('/dbbackup-store',[DBBackupController::class,'backup'])->name('dbbackup.store');

		//Incident Task
	Route::get('/mytask', [IncidentTaskController::class,'index'])->name('mytask.index');
	Route::get('/mytask/{id}', [IncidentTaskController::class,'index']);

	Route::get('sync_radius',[CustomerController::class,'syncRadius'])->name('sync_radius');

	//Bill Reminder Route 
	Route::get('/bill-reminders', [App\Http\Controllers\BillReminderController::class, 'index'])->name('bill-reminders.index');
	Route::post('/bill-reminders/create-reminder', [App\Http\Controllers\BillReminderController::class, 'createReminder'])->name('bill-reminders.create-reminder');
	Route::post('/bill-reminders/empty-reminder', [App\Http\Controllers\BillReminderController::class, 'emptyReminder'])->name('bill-reminders.empty-reminder');
	Route::delete('/bill-reminders/{id}', [App\Http\Controllers\BillReminderController::class, 'destroy'])->name('bill-reminders.destroy');

	Route::post('/bill-reminders/delete-selected', [App\Http\Controllers\BillReminderController::class, 'deleteSelected'])->name('bill-reminders.delete-selected');

	Route::put('/bill-reminders/{id}', [App\Http\Controllers\BillReminderController::class, 'update'])->name('bill-reminders.update');
	Route::post('/bill-reminders/send', [App\Http\Controllers\BillReminderController::class, 'sendReminders'])->name('bill-reminders.send');
	Route::post('/bill-reminders/send-all', [App\Http\Controllers\BillReminderController::class, 'sendAllReminders'])->name('bill-reminders.send-all');
	Route::post('/bill-reminders/clear-no-phone', [App\Http\Controllers\BillReminderController::class, 'clearNoPhoneCustomers'])->name('bill-reminders.clear-no-phone');
	Route::post('/bill-reminders/download-excel', [App\Http\Controllers\ExcelController::class, 'exportBillReminders'])->name('bill-reminders.download-excel');
	Route::get('/bill-reminders/upload',[ExcelController::class,'uploadBillReminderView'])->name('bill-reminders.upload-view');
	Route::post('importBillReminder',[ExcelController::class,'importBillReminder'])->name('importBillReminder');
	
	Route::get('/auto-expire',[App\Http\Controllers\CustomerController::class, 'expireToSuspend'])->name('auto-expire');
});

Route::get('/s/{shortURLKey}', '\AshAllenDesign\ShortURL\Controllers\ShortURLController');
