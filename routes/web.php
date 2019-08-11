<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::any('/js/localization.js', 'LocalizationController@localization')->name('localization');

Route::get('/currentCSRFToken', 'CSRFTokenController')->name('currentCSRFToken');


Route::prefix('ccms')->group(function () {
    $adminAreaSPAController = "SPA\\AdminAreaSPAController";

    // Authentication Routes...
    $this->post('login', 'AdminAuth\LoginController@login');
    $this->post('logout', 'AdminAuth\LoginController@logout')->name('admin.logout');

    $adminRoutes = [
        "welcome" => "/",
        "login" => "/login",
    ];

    foreach ($adminRoutes as $name => $path)
        Route::get($path, $adminAreaSPAController)->name("admin." . $name);

    Route::middleware(\App\Http\Middleware\AdminAuthenticate::class)->group(function () use ($adminAreaSPAController) {
        Route::get('/jsapi/currentUser', 'AdminAPI\\UserController@currentUser')->name('admin.currentUser');

        $adminRoutes = [
        ];

        foreach ($adminRoutes as $name => $path)
            Route::get($path, $adminAreaSPAController)->name("admin." . $name);

        Route::patch("/zones/packages/{zoneHasComputeInstancePackage}", "ZoneHasPackageController@updatePackage")->name("zones.packages.update");
        Route::delete("/zones/packages/{zoneHasComputeInstancePackage}", "ZoneHasPackageController@deletePackage")->name("zones.packages.delete");
        Route::get("/zones/{zone}/computeNodes", $adminAreaSPAController)->name("zones.computeNodes");
        Route::post("/zones/{zone}/packages/massDestroy", "ZoneHasPackageController@massDestroy")->name("zones.packages.massDestroy");
        Route::post("/zones/{zone}/packages/{computeInstancePackage}", "ZoneHasPackageController@assignPackage")->name("zones.packages.assign");

        Route::get("/certificates/list", "CertificateController@listCertificates")->name("certificates.list");
        Route::post("/certificates/massDestroy", "CertificateController@massDestroy")->name("certificates.massDestroy");

        Route::post("/operationRequests/query", "ComputeResourceOperationRequestController@query")->name("admin.operationRequests.query");

        // ---BEGIN--- 管理员计算实例部分 ---BEGIN---
        Route::get("/computeInstances", "ComputeInstance\\AdminIndexController@index")
            ->middleware(["admin.autoSPA", "can:index," . \App\ComputeInstance::class])
            ->name("admin.computeInstances.index")
        ;
        Route::get("/computeNodes/{computeNode}/computeInstances", "ComputeInstance\\AdminIndexController@indexByNode")
            ->middleware(["admin.autoSPA", "can:index," . \App\ComputeInstance::class])
            ->name("computeNodes.computeInstances")
        ;
        Route::prefix("computeInstances")->group(function () use ($adminAreaSPAController) {
            Route::post("/operationRequests/query","ComputeInstance\\OperationRequest\\OperationRequestController@computeInstanceQueryForAdmin")
                ->name("admin.computeInstanceOperationRequests.query")
            ;

            Route::post("/massDestroy", "ComputeInstance\\DestroyRequestController@massDestroyForAdmin")
                ->middleware("can:delete," . \App\ComputeInstance::class)
                ->name("admin.computeInstances.operation.massDestroy")
            ;

            Route::middleware("can:adminOperate," . \App\ComputeInstance::class)->group(function () {
                Route::post('/power/massOn', "ComputeInstance\\PowerController@massOnForAdmin")->name("admin.computeInstances.power.mass.on");
                Route::post('/power/massReset', "ComputeInstance\\PowerController@massResetForAdmin")->name("admin.computeInstances.power.mass.reset");
                Route::post('/power/massOff', "ComputeInstance\\PowerController@massOffForAdmin")->name("admin.computeInstances.power.mass.off");
            });

            Route::get("{computeInstance}", "ClientAPI\\ComputeInstance\\Controller@showForAdmin")->middleware(["admin.autoSPA", "can:index," . \App\ComputeInstance::class])->name("admin.computeInstances.show");

            Route::prefix("{computeInstance}")->group(function () use ($adminAreaSPAController) {
                Route::get('/statistics', "ComputeInstance\\ComputeInstanceController@statistics")
                    ->middleware(["admin.autoSPA", "can:index," . \App\ComputeInstance::class])
                    ->name("admin.computeInstances.statistics")
                ;


                Route::get("/console", "ComputeInstance\\ComputeInstanceController@console")
                    ->middleware("can:adminOperate," . \App\ComputeInstance::class)
                    ->name("admin.computeInstances.console")
                ;

                // SPA
                Route::get('dashboard', $adminAreaSPAController);
                Route::get('volumes', $adminAreaSPAController);
                Route::get('virtualMedias', $adminAreaSPAController);
                Route::get('network', $adminAreaSPAController);
                Route::get('settings', $adminAreaSPAController);
                Route::get('histories', $adminAreaSPAController);
                Route::get('administrator', $adminAreaSPAController);

                Route::get('/networkInterfaces', "ComputeInstance\\NetworkInterface\\NetworkInterfaceController@index")
                    ->middleware("can:index," . \App\ComputeInstance::class)
                    ->name("admin.computeInstances.networkInterfaces")
                ;

                Route::get('/operationRequests', "ComputeResourceOperationRequestIndexController@indexByComputeInstance")
                    ->middleware("can:index," . \App\ComputeInstance::class)
                    ->name("admin.computeInstances.operationRequests")
                ;

                Route::middleware("can:adminOperate," . \App\ComputeInstance::class)->group(function () {
                    Route::patch('/basic', "ComputeInstance\\ComputeInstanceController@updateBasic")->name("admin.computeInstances.updateBasic");
                    Route::get('/password', "ClientAPI\\ComputeInstance\\Controller@password")->name("admin.computeInstances.show.password");
                });

                Route::get('/settingAvailableData', "ComputeInstance\\ComputeInstanceController@settingAvailableData")->name("admin.computeInstance.settingAvailableData");

                Route::middleware("can:update," . \App\ComputeInstance::class)->group(function () {
                    Route::patch('/customSize', "ComputeInstance\\AdminController@changeCustomSize")->name("admin.computeInstances.changeCustomSize");
                    Route::patch('/advanceSettings', "ComputeInstance\\AdminController@changeAdvanceSettings")->name("admin.computeInstances.changeAdvanceSettings");
                });

                Route::post("/forceDelete", "ComputeInstance\\AdminController@forceDelete")
                    ->middleware("can:delete," . \App\ComputeInstance::class)
                    ->name("admin.computeInstances.forceDelete")
                ;

                Route::prefix("operation")->group(function () {
                    Route::post("/newVolume", "LocalVolume\\LocalVolumeController@newVolume")->name("admin.localVolumes.new");

                    // Compute instance operation requests
                    Route::middleware(["can:adminOperate," . \App\ComputeInstance::class, \App\Http\Middleware\ComputeInstance\OperationRequestLimit::class])->group(function () {
                        Route::post('/power/on', "ComputeInstance\\PowerController@on")->name("admin.computeInstances.power.on");
                        Route::post('/power/off', "ComputeInstance\\PowerController@off")->name("admin.computeInstances.power.off");
                        Route::post('/power/reset', "ComputeInstance\\PowerController@reset")->name("admin.computeInstances.power.reset");

                        // Delete compute instance
                        Route::post('/destroy', "ComputeInstance\\DestroyRequestController@destroy")->name("admin.computeInstances.operation.destroy");

                        // Change virtual media
                        Route::post("/CDROM/changeMedia", "ComputeInstance\\VirtualMediaController@changeCDROMMedia")->name("admin.computeInstances.operation.cdroms.changeMedia");
                        Route::post("/Floppy/changeMedia", "ComputeInstance\\VirtualMediaController@changeFloppyMedia")->name("admin.computeInstances.operation.floppies.changeMedia");

                        // Reconfigure
                        Route::post("/reconfigure", "ComputeInstance\\ComputeInstanceController@reconfigure")->name("admin.computeInstances.operation.reconfigure");

                        Route::patch('/hostname', "ComputeInstance\\ComputeInstanceController@changeHostname")->name("admin.computeInstances.changeHostname");
                        Route::patch('/osPassword', "ComputeInstance\\ComputeInstanceController@resetOSPassword")->name("admin.computeInstances.resetOSPassword");
                        Route::post('/osNetwork', "ComputeInstance\\ComputeInstanceController@reconfigureOSNetwork")->name("admin.computeInstances.reconfigureOSNetwork");
                        Route::patch('/package/{computeInstancePackageId}', "ComputeInstance\\ComputeInstanceController@changePackage")->name("admin.computeInstances.changePackage");

                        // Save advance setting
                        Route::patch("/advanceSettings", "ComputeInstance\\ComputeInstanceController@saveAdvanceSettings")->name("admin.computeInstances.operation.saveAdvanceSettings");

                        // Change public image
                        Route::patch("/publicImage", "ComputeInstance\\ComputeInstanceController@changePublicImage")->name("admin.computeInstances.operation.changePublicImage");
                    });
                });
            });
        });
        // ---END--- 管理员计算实例部分 ---END---

        // ---BEGIN--- 管理员本地卷部分 ---BEGIN---
        Route::get("/volumes", "LocalVolume\\LocalVolumeIndexController@indexForAdmin")
            ->middleware(["admin.autoSPA", "can:index," . \App\ComputeInstance\LocalVolume::class])
            ->name("admin.volumes.index")
        ;

        Route::get("/computeNodes/{computeNode}/localVolumes", "LocalVolume\\LocalVolumeIndexController@indexByNode")
            ->middleware(["admin.autoSPA", "can:index," . \App\ComputeInstance\LocalVolume::class])
            ->name("computeNodes.localVolumes")
        ;

        Route::get("/volumes/byComputeInstance/{computeInstance}", "ComputeInstance\\Volume\\LocalVolumeIndexController@indexByComputeInstance")
            ->middleware("can:index," . \App\ComputeInstance\LocalVolume::class)
            ->name("admin.volumes.index.by.computeInstance")
        ;

        Route::prefix("localVolumes")->group(function () {
            Route::post("/massDetach", "LocalVolume\\LocalVolumeController@massDetachForAdmin")
                ->middleware("can:detach," . \App\ComputeInstance\LocalVolume::class)
                ->name("admin.localVolumes.operation.massDetach")
            ;
            Route::post("/massRelease", "LocalVolume\\LocalVolumeController@massReleaseForAdmin")
                ->middleware("can:release," . \App\ComputeInstance\LocalVolume::class)
                ->name("admin.localVolumes.operation.massRelease")
            ;

            Route::prefix("{localVolume}")->group(function () {
                Route::get("/attachableInstances", "LocalVolume\\LocalVolumeIndexController@attachableInstances")
                    ->middleware("can:attach," . \App\ComputeInstance\LocalVolume::class)
                    ->name("admin.volumes.attachableInstances")
                ;

                Route::patch("/toggleProtectMode", "LocalVolume\\LocalVolumeController@toggleProtectMode")
                    ->middleware("can:toggleProtectMode," . \App\ComputeInstance\LocalVolume::class)
                    ->name("admin.localVolumes.toggleProtectMode")
                ;
                Route::patch("/togglePrimaryBootableDisk", "LocalVolume\\LocalVolumeController@togglePrimaryBootableDisk")
                    ->middleware("can:togglePrimaryBootableDisk," . \App\ComputeInstance\LocalVolume::class)
                    ->name("admin.localVolumes.togglePrimaryBootableDisk")
                ;

                Route::prefix("operation")->group(function () {
                    Route::patch("/changeBus",  "LocalVolume\\LocalVolumeController@changeBus")
                        ->middleware("can:changeBus," . \App\ComputeInstance\LocalVolume::class)
                        ->name("admin.localVolumes.operation.changeBus")
                    ;

                    Route::middleware(\App\Http\Middleware\LocalVolume\OperationRequestLimit::class)->group(function () {
                        // resize
                        Route::post("/resize", "LocalVolume\\LocalVolumeController@resize")
                            ->middleware("can:resize," . \App\ComputeInstance\LocalVolume::class)
                            ->name("admin.localVolumes.operation.resize")
                        ;

                        Route::post("/detach", "LocalVolume\\LocalVolumeController@detach")
                            ->middleware("can:detach," . \App\ComputeInstance\LocalVolume::class)
                            ->name("admin.localVolumes.operation.detach")
                        ;

                        Route::post("/release", "LocalVolume\\LocalVolumeController@release")
                            ->middleware("can:release," . \App\ComputeInstance\LocalVolume::class)
                            ->name("admin.localVolumes.operation.release")
                        ;

                        Route::post("/attachTo/{computeInstance}", "LocalVolume\\LocalVolumeController@attach")
                            ->middleware(["can:attach," . \App\ComputeInstance\LocalVolume::class, "can:adminOperate," . \App\ComputeInstance::class])
                            ->name("admin.localVolumes.operation.attach")
                        ;
                    });
                });
            });
        });
        // ---END--- 管理员本地卷部分 ---END---

        // ---BEGIN--- 管理员IP部分 ---BEGIN---
        Route::middleware("can:allocate," . \App\IPPool\IPv4Assignment::class)->group(function () {
            Route::post("/publicIPv4Addresses/allocateFor/{computeInstance}", "IPPool\\IPv4AssignmentController@allocatePublicForInstanceAsAdmin")->name("admin.publicIPv4Addresses.allocate");
            Route::post("/publicIPv6Addresses/allocateFor/{computeInstance}", "IPPool\\IPv6AssignmentController@allocatePublicForInstanceAsAdmin")->name("admin.publicIPv6Addresses.allocate");
        });

        Route::post("/publicIPv4Addresses/{ipv4Assignment}/unbind", "IPPool\\IPv4AssignmentController@unbindWithKeyForAdmin")
            ->middleware("can:unbind," . \App\IPPool\IPv4Assignment::class)
            ->name("admin.publicIPv4Addresses.unbind")
        ;
        Route::post("/publicIPv4Addresses/{ipv4Assignment}/release", "IPPool\\IPv4AssignmentController@releaseWithKeyForAdmin")
            ->middleware("can:release," . \App\IPPool\IPv4Assignment::class)
            ->name("admin.publicIPv4Addresses.release")
        ;
        Route::get("/publicIPv4Addresses/{ipv4Assignment}/bindableInstances", "IPPool\\IPv4AssignmentController@bindableInstances")
            ->middleware("can:bind," . \App\IPPool\IPv4Assignment::class)
            ->name("admin.publicIPv4Addresses.bindableInstances")
        ;
        Route::post("/publicIPv4Addresses/{ipv4Assignment}/bindTo/{computeInstance}", "IPPool\\IPv4AssignmentController@bind")
            ->middleware("can:bind," . \App\IPPool\IPv4Assignment::class)
            ->name("admin.publicIPv4Addresses.bind")
        ;
        Route::post("/publicIPv4Addresses/{ipv4Assignment}/convert", "IPPool\\IPv4AssignmentController@convert")
            ->middleware("can:convert," . \App\IPPool\IPv4Assignment::class)
            ->name("admin.publicIPv4Addresses.convert")
        ;

        Route::post("/publicIPv6Addresses/{ipv6Assignment}/unbind", "IPPool\\IPv6AssignmentController@unbindWithKeyForAdmin")
            ->middleware("can:unbind," . \App\IPPool\IPv6Assignment::class)
            ->name("admin.publicIPv6Addresses.unbind")
        ;
        Route::post("/publicIPv6Addresses/{ipv6Assignment}/release", "IPPool\\IPv6AssignmentController@releaseWithKeyForAdmin")
            ->middleware("can:release," . \App\IPPool\IPv6Assignment::class)
            ->name("admin.publicIPv6Addresses.release")
        ;
        Route::get("/publicIPv6Addresses/{ipv6Assignment}/bindableInstances", "IPPool\\IPv6AssignmentController@bindableInstances")
            ->middleware("can:bind," . \App\IPPool\IPv6Assignment::class)
            ->name("admin.publicIPv6Addresses.bindableInstances")
        ;
        Route::post("/publicIPv6Addresses/{ipv6Assignment}/bindTo/{computeInstance}", "IPPool\\IPv6AssignmentController@bind")
            ->middleware("can:bind," . \App\IPPool\IPv6Assignment::class)
            ->name("admin.publicIPv6Addresses.bind")
        ;
        Route::post("/publicIPv6Addresses/{ipv6Assignment}/convert", "IPPool\\IPv6AssignmentController@convert")
            ->middleware("can:convert," . \App\IPPool\IPv6Assignment::class)
            ->name("admin.publicIPv6Addresses.convert")
        ;
        // ---BEGIN--- 管理员IP部分 ---BEGIN---

        Route::get("/users/{user}/login", "User\\UserController@login")->name("users.login");

        Route::patch("/system/configurations", "AdminAPI\\SystemConfigurationController@update")->name("systemConfigurations.update");
        Route::middleware("admin.autoSPA")->group(function () {
            Route::get("/dashboard", "AdminAPI\\DashboardController@dashboard")->name("admin.dashboard");

            Route::get("/system/configurations", "AdminAPI\\SystemConfigurationController@show")->name("systemConfigurations.show");

            Route::resource("regions", "AdminAPI\\RegionController");

            Route::resource("zones", "AdminAPI\\ZoneController")->except("index");

            Route::get("/zones", "AdminAPI\\ZoneIndexController")->name("zones.index");

            Route::get("/computeNodes", "AdminAPI\\Node\\ComputeNode\\ComputeNodeIndexController")->name("computeNodes.index");
            Route::get("/computeNodes/regions", "AdminAPI\\Node\\ComputeNode\\ComputeNodeIndexController@listRegions")->name("computeNodes.listRegions");
            Route::resource("computeNodes", "AdminAPI\\Node\\ComputeNode\\ComputeNodeController")->except("index");
            Route::get("/computeNodes/{computeNode}/statistics", "AdminAPI\\Node\\ComputeNode\\ComputeNodeController@statistics")->name("computeNodes.statistics");
            Route::post("/computeNodes/{computeNode}/counter/allocated", "AdminAPI\\Node\\ComputeNode\\ComputeNodeController@refreshAllocatedCounter")->name("computeNodes.refreshAllocatedCounter");
            Route::post("/computeNodes/{computeNode}/counter/computeInstanceAndLocalVolume", "AdminAPI\\Node\\ComputeNode\\ComputeNodeController@refreshComputeInstanceAndLocalVolume")->name("computeNodes.refreshComputeInstanceAndLocalVolume");
            Route::get("/computeNodes/{computeNode}/noVNC", "AdminAPI\\Node\\ComputeNode\\ComputeNodeController@showNOVNCBasicSetting")->name("computeNodes.showNOVNCBasicSetting");
            Route::patch("/computeNodes/{computeNode}/noVNC", "AdminAPI\\Node\\ComputeNode\\ComputeNodeController@changeNOVNCBasicSetting")->name("computeNodes.changeNOVNCBasicSetting");

            Route::resource("images", "AdminAPI\\Image\\ImageController")->except("index");
            Route::get("/images", "AdminAPI\\Image\\ImageIndexController")->name("images.index");

            Route::resource("imageCategories", "AdminAPI\ImageCategory\ImageCategoryController")->except("index");

            Route::resource("computeInstancePackageCategories", "AdminAPI\ComputeInstance\Package\PackageCategoryController")->except("index");

            Route::resource("computeInstancePackages", "AdminAPI\\ComputeInstance\\Package\\PackageController");

            // ISO镜像
            Route::resource("publicISOCategories", "AdminAPI\\PublicISOCategory\\PublicISOCategoryController")->except("index");
            Route::get("/publicISOCategories", "AdminAPI\\PublicISOCategory\\PublicISOCategoryIndexController")->name("publicISOCategories.index");
            Route::resource("publicISOs", "AdminAPI\\PublicISO\\PublicISOController")->except("index");
            Route::get("/publicISOs", "AdminAPI\\PublicISO\\PublicISOIndexController")->name("publicISOs.index");
            Route::post("/publicISOs/massDestroy", "AdminAPI\\PublicISO\\PublicISOController@massDestroy")->name("publicISOs.massDestroy");

            // 软盘
            Route::resource("publicFloppyCategories", "AdminAPI\\PublicFloppyCategory\\PublicFloppyCategoryController")->except("index");
            Route::get("/publicFloppyCategories", "AdminAPI\\PublicFloppyCategory\\PublicFloppyCategoryIndexController")->name("publicFloppyCategories.index");
            Route::resource("publicFloppies", "AdminAPI\\PublicFloppy\\PublicFloppyController")->except("index");
            Route::get("/publicFloppies", "AdminAPI\\PublicFloppy\\PublicFloppyIndexController")->name("publicFloppies.index");

            // IP地址池
            Route::resource("ipv4Pools", "IPPool\\IPv4\IPv4Controller")->except("index");
            Route::get("ipv4Pools", "IPPool\\IPv4\IndexController")->name("ipv4Pools.index");
            Route::resource("ipv6Pools", "IPPool\\IPv6\IPv6Controller")->except("index");
            Route::get("ipv6Pools", "IPPool\\IPv6\IndexController")->name("ipv6Pools.index");

            Route::get("/ipv4Pools/{ipv4Pool}/zoneAssignments", "IPPool\\IPv4\\ZoneAssignIndexController")->name("ipv4Pools.zoneAssignments.index");
            Route::get("/ipv4Pools/{ipv4Pool}/nodeAssignments", "IPPool\\IPv4\\NodeAssignIndexController")->name("ipv4Pools.nodeAssignments.index");
            Route::get("/ipv4Pools/{ipv4Pool}/assignments", "IPPool\\IPv4AssignmentIndexController@indexByIPPool")->name("ipv4Pools.assignments.index");
            Route::get("/ipv6Pools/{ipv6Pool}/zoneAssignments", "IPPool\\IPv6\\ZoneAssignIndexController")->name("ipv6Pools.zoneAssignments.index");
            Route::get("/ipv6Pools/{ipv6Pool}/nodeAssignments", "IPPool\\IPv6\\NodeAssignIndexController")->name("ipv6Pools.nodeAssignments.index");
            Route::get("/ipv6Pools/{ipv6Pool}/assignments", "IPPool\\IPv6AssignmentIndexController@indexByIPPool")->name("ipv6Pools.assignments.index");

            // IP分配
            Route::middleware("can:index," . \App\IPPool\IPv4Assignment::class)->group(function () {
                Route::get("/ipv4/assignments", "IPPool\\IPv4AssignmentIndexController@index")->name("ipv4.assignments.index");
                Route::get("/ipv6/assignments", "IPPool\\IPv6AssignmentIndexController@index")->name("ipv6.assignments.index");
            });

            // 付款模块
            Route::get("/availablePaymentModules", "PaymentModule\\PaymentModuleController@availableModules")->name("paymentModules.availableModules");
            Route::patch("/paymentModules/{paymentModule}/setting", "PaymentModule\\PaymentModuleController@updateModuleSetting")->name("paymentModules.updateSetting");
            Route::resource("paymentModules", "PaymentModule\\PaymentModuleController");

            // 货币
            Route::resource("currencies", "CurrencyController");

            // 充值订单
            Route::get("/paymentTrades", "PaymentTrade\\IndexController@indexForAdmin")
                ->middleware("can:index," . \App\PaymentTrade::class)
                ->name("admin.paymentTrades.index")
            ;
            Route::get("/users/{user}/paymentTrades", "PaymentTrade\\IndexController@indexByUser")
                ->middleware(["can:index," . \App\User::class, "can:index," . \App\PaymentTrade::class])
                ->name("users.paymentTrades.index")
            ;
            Route::get("/paymentTrades/{paymentTrade}", "PaymentTrade\\PaymentTradeController@show")
                ->middleware("can:index," . \App\PaymentTrade::class)
                ->name("admin.paymentTrades.show")
            ;
            Route::patch("/paymentTrades/{paymentTrade}/status", "PaymentTrade\\PaymentTradeController@changeStatus")
                ->middleware("can:update," . \App\PaymentTrade::class)
                ->name("admin.paymentTrades.changeStatus")
            ;
            Route::post("/paymentTrades/{paymentTrade}/transaction", "PaymentTrade\\PaymentTradeController@addTransaction")
                ->middleware("can:markSuccess," . \App\PaymentTrade::class)
                ->name("admin.paymentTrades.addTransaction")
            ;
            Route::delete("/paymentTrades/{paymentTrade}/transaction", "PaymentTrade\\PaymentTradeController@removeTransaction")
                ->middleware("can:markCancelled," . \App\PaymentTrade::class)
                ->name("admin.paymentTrades.removeTransaction")
            ;
            Route::post("/paymentTrades/{paymentTrade}/refund", "PaymentTrade\\RefundController@refundForAdmin")
                ->middleware("can:refund," . \App\PaymentTrade::class)
                ->name("admin.paymentTrades.refund")
            ;

            // 退款订单
            Route::get("/paymentTradeRefunds", "RefundTrade\\RefundTradeIndexController@index")
                ->middleware("can:index," . \App\PaymentTrade::class)
                ->name("admin.paymentTradeRefunds.index")
            ;
            Route::patch("/paymentTradeRefunds/{paymentTradeRefund}/commit", "PaymentTrade\\RefundController@commit")
                ->middleware("can:commit," . \App\PaymentTradeRefund::class)
                ->name("admin.paymentTradeRefunds.commit")
            ;
            Route::patch("/paymentTradeRefunds/{paymentTradeRefund}/cancel", "PaymentTrade\\RefundController@cancel")
                ->middleware("can:cancel," . \App\PaymentTradeRefund::class)
                ->name("admin.paymentTradeRefunds.cancel")
            ;
            Route::patch("/paymentTradeRefunds/{paymentTradeRefund}/status", "PaymentTrade\\RefundController@changeStatus")
                ->middleware("can:changeStatus," . \App\PaymentTradeRefund::class)
                ->name("admin.paymentTradeRefunds.changeStatus")
            ;

            // 工单部门
            Route::resource("ticketDepartments", "Ticket\\TicketDepartmentController");

            // 工单
            Route::get("/tickets", "Ticket\\IndexForAdminController@index")
                ->middleware("can:index," . \App\Ticket\Ticket::class)
                ->name("admin.tickets.index")
            ;
            Route::post("/tickets/massDestroy", "Ticket\\AdminTicketController@massDestroy")
                ->middleware("can:delete," . \App\Ticket\Ticket::class)
                ->name("admin.tickets.massDestroy")
            ;
            Route::post("/tickets/massClose", "Ticket\\AdminTicketController@massClose")
                ->middleware("can:update," . \App\Ticket\Ticket::class)
                ->name("admin.tickets.massClose")
            ;
            Route::get("/tickets/{ticket}", "Ticket\\AdminTicketController@show")
                ->middleware("can:index," . \App\Ticket\Ticket::class)
                ->name("admin.tickets.show")
            ;
            Route::patch("/tickets/{ticket}/status", "Ticket\\AdminTicketController@changeStatus")
                ->middleware("can:update," . \App\Ticket\Ticket::class)
                ->name("admin.tickets.changeStatus")
            ;
            Route::patch("/tickets/{ticket}/department", "Ticket\\AdminTicketController@changeDepartment")
                ->middleware("can:update," . \App\Ticket\Ticket::class)
                ->name("admin.tickets.changeDepartment")
            ;
            Route::post("/tickets/{ticket}/reply", "Ticket\\AdminTicketController@makeReply")
                ->middleware("can:makeReply," . \App\Ticket\Ticket::class)
                ->name("admin.tickets.makeReply")
            ;

            // 证书
            Route::resource("certificates", "CertificateController");

            Route::get("/zones/{zone}/packages", "ZoneHasPackageController@index")->name("zones.packages.index");

            // 角色
            Route::get("/admin/roles", "Role\\AdminRoleIndexController@index")->name("admin.roles.index");
            Route::get("/admin/availableRoles", "Admin\\AdminController@availableRoles")->name("admin.availableRoles");
            Route::get("/admin/roles/create", "Role\\AdminRoleIndexController@create")->name("admin.roles.create");
            Route::post("/admin/roles", "Role\\AdminRoleController@store")->name("admin.roles.store");
            Route::get("/admin/roles/{role}", "Role\\AdminRoleController@show")->name("admin.roles.show");
            Route::get("/admin/roles/{role}/edit", "Role\\AdminRoleController@edit")->name("admin.roles.edit");
            Route::get("/admin/roles/{role}/admins", "Admin\\AdminIndexController@indexByRole")->name("admin.roles.admins.index");
            Route::patch("/admin/roles/{role}", "Role\\AdminRoleController@update")->name("admin.roles.update");
            Route::delete("/admin/roles/{role}", "Role\\AdminRoleController@destroy")->name("admin.roles.destroy");

            // 权限
            Route::get("/admin/availablePermissions", "Role\\AdminRoleIndexController@availablePermissions")->name("admin.availablePermissions");

            // 管理员
            Route::get("/admins", "Admin\\AdminIndexController@index")->name("admins.index");
            Route::resource("admins", "Admin\\AdminController")->except("index");

            // 用户配额
            Route::get("/userQuotas", "UserQuota\\UserQuotaIndexController@index")->name("userQuotas.index");
            Route::resource("userQuotas", "UserQuota\\UserQuotaController")->except("index");
            Route::get("/userQuotas/{userQuota}/users", "User\\UserIndexController@indexByUserQuota")->name("userQuotas.users.index");

            // 用户
            Route::post("/users/massDestroy", "User\\UserController@massDestroy")->name("users.massDestroy");
            Route::resource("users", "User\\UserController")->except("index");
            Route::get("/users", "User\\UserIndexController@index")->name("users.index");
            Route::get("/users/{user}/consumption", "User\\UserController@consumption")->name("users.consumption");
            Route::get("/users/{user}/consumption/daily", "User\\UserController@dailyConsumption")->name("users.dailyConsumption");
            Route::get("/users/{user}/consumption/monthly", "User\\UserController@monthlyConsumption")->name("users.monthlyConsumption");
            Route::get("/users/{user}/credit", "UserCreditRecord\\UserCreditRecordIndexController@indexByUser")->name("users.creditRecords.index");
            Route::post("/users/{user}/credit", "UserCreditRecord\\UserCreditRecordIndexController@exportByUser")->name("users.creditRecords.export");

            Route::get("/users/{user}/credit/add", "User\\UserController@addCreditForm");
            Route::get("/users/{user}/credit/remove", "User\\UserController@removeCreditForm");
            Route::post("/users/{user}/credit/add", "User\\UserController@addCredit")->name("users.addCredit");
            Route::post("/users/{user}/credit/remove", "User\\UserController@removeCredit")->name("users.removeCredit");

            Route::get("/users/{user}/computeInstances", "ComputeInstance\\AdminIndexController@indexByUser")->name("users.computeInstances.index");
            Route::get("/users/{user}/localVolumes", "LocalVolume\\LocalVolumeIndexController@indexByUser")->name("users.localVolumes.index");
            Route::get("/users/{user}/ipv4s", "IPPool\\IPv4AssignmentIndexController@indexByUser")->name("users.ipv4s.index");
            Route::get("/users/{user}/ipv6s", "IPPool\\IPv6AssignmentIndexController@indexByUser")->name("users.ipv6s.index");
            Route::get("/users/{user}/tickets", "Ticket\\IndexForAdminController@indexByUser")->name("admin.tickets.indexByUser");

            Route::patch("/users/{user}/suspend", "User\\UserController@suspend")->name("users.suspend");
            Route::patch("/users/{user}/unsuspend", "User\\UserController@unsuspend")->name("users.unsuspend");

            // 流量组
            Route::get("/trafficShareGroups/{trafficShareGroup}/zones", "TrafficShareGroup\\TrafficShareGroupController@zoneIndex")->name("trafficShareGroups.zones.index");
            Route::get("/trafficShareGroups", "TrafficShareGroup\\TrafficShareGroupIndexController")->name("trafficShareGroups.index");
            Route::resource("trafficShareGroups", "TrafficShareGroup\\TrafficShareGroupController")->except("index");
            Route::post("/trafficShareGroups/{trafficShareGroup}/zones/{zone}", "TrafficShareGroup\\TrafficShareGroupController@assignZone")->name("trafficShareGroups.zones.assign");

            Route::middleware("permission:" . \App\Constants\AdminPermissions::SUPER . "|" . \App\Constants\AdminPermissions::R_BILLING_STATISTICS)->group(function () {
                Route::get("/billing/statistics/", "BillingStatisticsController@dashboard")->name("billing.statistics.dashboard");
                Route::get("/billing/statistics/daily/", "BillingStatisticsController@dailyTrade")->name("billing.statistics.daily.trade");
                Route::get("/billing/statistics/monthly/", "BillingStatisticsController@monthlyTrade")->name("billing.statistics.monthly.trade");
                Route::get("/billing/statistics/daily/charge", "BillingStatisticsController@dailyCharge")->name("billing.statistics.daily.charge");
                Route::get("/billing/statistics/monthly/charge", "BillingStatisticsController@monthlyCharge")->name("billing.statistics.monthly.charge");
                Route::get("/billing/statistics/history/", "ChargeHistoryController@byComputeInstancePackages")->name("billing.statistics.charge.history.byComputeInstancePackages");
                Route::get("/billing/statistics/history/computeNodes", "ChargeHistoryController@byComputeNodes")->name("billing.statistics.charge.history.byComputeNodes");
                Route::get("/billing/statistics/history/zones", "ChargeHistoryController@byZone")->name("billing.statistics.charge.history.byZone");
                Route::get("/billing/statistics/history/ipv4Pools", "ChargeHistoryController@byIPv4Pools")->name("billing.statistics.charge.history.byIPv4Pools");
                Route::get("/billing/statistics/history/ipv6Pools", "ChargeHistoryController@byIPv6Pools")->name("billing.statistics.charge.history.byIPv6Pools");
                Route::get("/billing/statistics/history/trafficShareGroups", "ChargeHistoryController@byTrafficShareGroups")->name("billing.statistics.charge.history.byTrafficShareGroups");
            });
        });

        Route::post("/CIDR/parser", "CIDRParserController")->name("CIDR.parser");

        Route::post("ipv4Pools/validate", "IPPool\\IPv4\IPv4Controller@validatePreview")->name("ipv4Pools.validate");
        Route::post("/ipv4Pools/massDestroy", "IPPool\\IPv4\IPv4Controller@massDestroy")->name("ipv4Pools.massDestroy");

        Route::post("/ipv4Pools/{ipv4Pool}/zoneAssignments/massDestroy", "IPPool\\IPv4\\ZoneAssignController@massDestroy")->name("ipv4Pools.zoneAssignments.massDestroy");
        Route::post("/ipv4Pools/{ipv4Pool}/zoneAssignments/{zone}", "IPPool\\IPv4\\ZoneAssignController@assign")->name("ipv4Pools.zoneAssignments.assign");
        Route::delete("/ipv4Pools/{ipv4Pool}/zoneAssignments/{zone}", "IPPool\\IPv4\\ZoneAssignController@destroy")->name("ipv4Pools.zoneAssignments.delete");

        Route::post("/ipv4Pools/{ipv4Pool}/nodeAssignments/massDestroy", "IPPool\\IPv4\\NodeAssignController@massDestroy")->name("ipv4Pools.nodeAssignments.massDestroy");
        Route::post("/ipv4Pools/{ipv4Pool}/nodeAssignments/{computeNode}", "IPPool\\IPv4\\NodeAssignController@assign")->name("ipv4Pools.nodeAssignments.assign");
        Route::delete("/ipv4Pools/{ipv4Pool}/nodeAssignments/{computeNode}", "IPPool\\IPv4\\NodeAssignController@destroy")->name("ipv4Pools.nodeAssignments.delete");


        Route::post("ipv6Pools/validate", "IPPool\\IPv6\IPv6Controller@validatePreview")->name("ipv6Pools.validate");
        Route::post("/ipv6Pools/massDestroy", "IPPool\\IPv6\IPv46ontroller@massDestroy")->name("ipv6Pools.massDestroy");

        Route::post("/ipv6Pools/{ipv6Pool}/zoneAssignments/massDestroy", "IPPool\\IPv6\\ZoneAssignController@massDestroy")->name("ipv6Pools.zoneAssignments.massDestroy");
        Route::post("/ipv6Pools/{ipv6Pool}/zoneAssignments/{zone}", "IPPool\\IPv6\\ZoneAssignController@assign")->name("ipv6Pools.zoneAssignments.assign");
        Route::delete("/ipv6Pools/{ipv6Pool}/zoneAssignments/{zone}", "IPPool\\IPv6\\ZoneAssignController@destroy")->name("ipv6Pools.zoneAssignments.delete");

        Route::post("/ipv6Pools/{ipv6Pool}/nodeAssignments/massDestroy", "IPPool\\IPv6\\NodeAssignController@massDestroy")->name("ipv6Pools.nodeAssignments.massDestroy");
        Route::post("/ipv6Pools/{ipv6Pool}/nodeAssignments/{computeNode}", "IPPool\\IPv6\\NodeAssignController@assign")->name("ipv6Pools.nodeAssignments.assign");
        Route::delete("/ipv6Pools/{ipv6Pool}/nodeAssignments/{computeNode}", "IPPool\\IPv6\\NodeAssignController@destroy")->name("ipv6Pools.nodeAssignments.delete");


        // 批量删除
        Route::post("/computeNodes/massDestroy", "AdminAPI\\Node\\ComputeNode\\ComputeNodeController@massDestroy")->name("computeNodes.massDestroy");

        Route::post("/regions/massDestroy", "AdminAPI\\RegionController@massDestroy")->name("regions.massDestroy");

        Route::post("/zones/massDestroy", "AdminAPI\\ZoneController@massDestroy")->name("zones.massDestroy");

        Route::post("/images/massDestroy", "AdminAPI\\Image\\ImageController@massDestroy")->name("images.massDestroy");

        Route::post("/computeInstancePackages/massDestroy", "AdminAPI\\ComputeInstance\\Package\\PackageController@massDestroy")->name("computeInstancePackages.massDestroy");

        /*
        // Password Reset Routes...
        $this->get('password/reset', $adminAreaSPAController)->name('password.request');
        $this->post('password/email', 'AdminAuth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
        $this->get('password/reset/{token}', $adminAreaSPAController)->name('password.reset');
        $this->post('password/reset', 'AdminAuth\ResetPasswordController@reset');
        */
    });
});


$clientAreaSPAController = "SPA\\ClientAreaSPAController";

Route::get('/', $clientAreaSPAController)->name('welcome');

Route::get('/paymentModules/{paymentModule}/payReturn/{tradeNo?}', "PaymentModule\\NotifyController@payReturn")->name('paymentModules.payReturn');

Auth::routes();

Route::middleware(\App\Http\Middleware\UserAuthenticate::class)->group(function () use ($clientAreaSPAController) {
    Route::get('/computeInstances/{computeInstance}/dashboard', $clientAreaSPAController);
    Route::get('/computeInstances/{computeInstance}/volumes', $clientAreaSPAController);
    Route::get('/computeInstances/{computeInstance}/virtualMedias', $clientAreaSPAController);
    Route::get('/computeInstances/{computeInstance}/network', $clientAreaSPAController);
    Route::get('/computeInstances/{computeInstance}/settings', $clientAreaSPAController);
    Route::get('/computeInstances/{computeInstance}/histories', $clientAreaSPAController);
    Route::get('/computeInstances/{computeInstance}/administrator', $clientAreaSPAController);

    Route::get('/jsapi/currentUser', 'API\\UserController@currentUser')->name('currentUser');

// Compute instances
    Route::get('/computeInstances/createOptions', "ClientAPI\\ComputeInstance\\Controller@createOptions")->name("computeInstances.createOptions");

    Route::get("/zones/{zone}/availablePackages", "ClientAPI\\ComputeInstance\\Controller@packageIndexByZone")->name("zones.availablePackages");
    Route::post("/computeInstances/setupRequests", "ClientAPI\\ComputeInstance\\SetupRequestController@store")->middleware(\App\Http\Middleware\User\CreditCheck::class)->name("computeInstances.setupRequest");

# Route::post("/computeInstances/massDestroy")->name("computeInstances.massDestroy");


    Route::get("/computeInstances/available", "ClientAPI\\ComputeInstance\\IndexController@availableInstances")->name("computeInstances.availableInstances");


    Route::post("/publicIPv4Addresses/massUnbind", "IPPool\\IPv4AssignmentController@massUnbind")->name("publicIPv4Addresses.massUnbind");
    Route::post("/publicIPv4Addresses/massRelease", "IPPool\\IPv4AssignmentController@massRelease")->name("publicIPv4Addresses.massRelease");
    Route::post("/publicIPv6Addresses/massUnbind", "IPPool\\IPv6AssignmentController@massUnbind")->name("publicIPv6Addresses.massUnbind");
    Route::post("/publicIPv6Addresses/massRelease", "IPPool\\IPv6AssignmentController@massRelease")->name("publicIPv6Addresses.massRelease");

    Route::middleware("can:operate,computeInstance")->group(function () {
        Route::get("/volumes/byComputeInstance/{computeInstance}", "ComputeInstance\\Volume\\LocalVolumeIndexController@indexByComputeInstance")->name("volumes.index.by.computeInstance");

        Route::middleware([\App\Http\Middleware\User\CreditCheck::class, \App\Http\Middleware\ComputeInstance\StatusCheck::class])->group(function () {
            Route::post("/publicIPv4Addresses/allocateFor/{computeInstance}", "IPPool\\IPv4AssignmentController@allocatePublicForInstance")->name("publicIPv4Addresses.allocate");
            Route::post("/publicIPv6Addresses/allocateFor/{computeInstance}", "IPPool\\IPv6AssignmentController@allocatePublicForInstance")->name("publicIPv6Addresses.allocate");
        });
    });

    Route::middleware("can:operate,ipv4Assignment")->group(function () {
        Route::post("/publicIPv4Addresses/{ipv4Assignment}/unbind", "IPPool\\IPv4AssignmentController@unbindWithKey")->name("publicIPv4Addresses.unbind");
        Route::post("/publicIPv4Addresses/{ipv4Assignment}/release", "IPPool\\IPv4AssignmentController@releaseWithKey")->name("publicIPv4Addresses.release");
        Route::get("/publicIPv4Addresses/{ipv4Assignment}/bindableInstances", "IPPool\\IPv4AssignmentController@bindableInstances")->name("publicIPv4Addresses.bindableInstances");
        Route::post("/publicIPv4Addresses/{ipv4Assignment}/bindTo/{computeInstance}", "IPPool\\IPv4AssignmentController@bind")->middleware(\App\Http\Middleware\ComputeInstance\StatusCheck::class)->name("publicIPv4Addresses.bind");
    });

    Route::middleware("can:operate,ipv6Assignment")->group(function () {
        Route::post("/publicIPv6Addresses/{ipv6Assignment}/unbind", "IPPool\\IPv6AssignmentController@unbindWithKey")->name("publicIPv6Addresses.unbind");
        Route::post("/publicIPv6Addresses/{ipv6Assignment}/release", "IPPool\\IPv6AssignmentController@releaseWithKey")->name("publicIPv6Addresses.release");
        Route::get("/publicIPv6Addresses/{ipv6Assignment}/bindableInstances", "IPPool\\IPv6AssignmentController@bindableInstances")->name("publicIPv6Addresses.bindableInstances");
        Route::post("/publicIPv6Addresses/{ipv6Assignment}/bindTo/{computeInstance}", "IPPool\\IPv6AssignmentController@bind")->middleware(\App\Http\Middleware\ComputeInstance\StatusCheck::class)->name("publicIPv6Addresses.bind");
    });


// Default
    Route::middleware("user.autoSPA")->group(function () {
        Route::get("/dashboard", "ClientAPI\\ClientAPIController@dashboard")->name("dashboard");

        Route::middleware("can:operate,computeInstance")->group(function () {
            Route::resource("computeInstances", "ClientAPI\\ComputeInstance\\Controller")->except("index");
        });

        Route::get("/computeInstances", "ClientAPI\\ComputeInstance\\IndexController@index")->name("computeInstances.index");

        Route::get('/computeInstances/{computeInstance}/statistics', "ComputeInstance\\ComputeInstanceController@statistics")->name("computeInstances.statistics")->middleware("can:operate,computeInstance");

        Route::get("/volumes", "LocalVolume\\LocalVolumeIndexController@index")->name("volumes.index");

        Route::get("/publicIPv4Addresses", "IPPool\\IPv4IndexController@indexPublicByUser")->name("publicIPv4Addresses.index");
        Route::get("/publicIPv6Addresses", "IPPool\\IPv6IndexController@indexPublicByUser")->name("publicIPv6Addresses.index");

        Route::get("/billing/dashboard", "UserCreditRecord\\UserCreditRecordController@dashboard")->name("billing.dashboard");
        Route::get("/billing/records", "UserCreditRecord\\UserCreditRecordIndexController@index")->name("billing.records.index");
        Route::post("/billing/records", "UserCreditRecord\\UserCreditRecordIndexController@exportIndex")->name("billing.records.exportIndex");
        Route::get("/billing/addCredit", "PaymentModule\\PayController@available")->name("billing.addCredit");
        Route::get("/paymentTrades", "PaymentTrade\\IndexController@index")->name("billing.paymentTrades.index");
        Route::post("/paymentTrades/{paymentTrade}/status", "PaymentTrade\\PaymentTradeController@tradeStatus")->name("billing.paymentTrades.status");

        Route::middleware("can:operate,paymentTrade")->group(function () {
            Route::get("/paymentTrades/{paymentTrade}/refunds", "PaymentTrade\\RefundController@index")->name("paymentTrades.refunds.index");
            Route::post("/paymentTrades/{paymentTrade}/refunds", "PaymentTrade\\RefundController@refund")->name("paymentTrades.refunds.store");
        });
        Route::post("/paymentModules/{paymentModule}/pay", "PaymentModule\\PayController@pay")->name("paymentModules.pay");

        Route::get("/tickets", "Ticket\\TicketController@index")->name("tickets.index");
        Route::get("/tickets/create", "Ticket\\TicketController@show")->name("tickets.create");
        Route::post("/tickets", "Ticket\\TicketController@store")->name("tickets.store");
        Route::middleware("can:operate,ticket")->group(function () {
            Route::patch("/tickets/{ticket}/close", "Ticket\\TicketController@close")->name("tickets.close");
            Route::post("/tickets/{ticket}/reply", "Ticket\\TicketController@makeReply")->name("tickets.makeReply");
            Route::get("/tickets/{ticket}", "Ticket\\TicketController@show")->name("tickets.show");
        });

        Route::get("/account", "AccountController@profile")->name("account.profile");
        Route::get("/account/password", "AccountController@password")->name("account.password");
        Route::patch("/account/profile", "AccountController@changeProfile")->name("account.changeProfile");
        Route::patch("/account/password", "AccountController@changePassword")->name("account.changePassword");
    });

    Route::get("/userServices", "ClientAPI\\ClientAPIController@listUserServices")->name("users.services");
    Route::get("/ticketDepartments", "Ticket\\TicketDepartmentController@index")->name("ticketDepartments.client.index");

    Route::middleware(["can:operate,networkInterface", \App\Http\Middleware\User\CreditCheck::class])->group(function () {
        Route::patch('/networkInterfaces/{networkInterface}/model', "ComputeInstance\\NetworkInterface\\NetworkInterfaceController@changeModel")->name("computeInstances.networkInterfaces.changeModel");
    });

    Route::post('/ipv4Assignments/unbind', "IPPool\\IPv4AssignmentController@unbind")->name("ipv4Assignments.unbind");
    Route::post('/ipv4Assignments/release', "IPPool\\IPv4AssignmentController@release")->name("ipv4Assignments.release");
    Route::post('/ipv6Assignments/unbind', "IPPool\\IPv6AssignmentController@unbind")->name("ipv6Assignments.unbind");
    Route::post('/ipv6Assignments/release', "IPPool\\IPv6AssignmentController@release")->name("ipv6Assignments.release");

    Route::post("/operationRequests/query", "ComputeResourceOperationRequestController@query")->name("operationRequests.query");

    Route::post("/computeInstances/operationRequests/query","ComputeInstance\\OperationRequest\\OperationRequestController@computeInstanceQuery")->name("computeInstanceOperationRequests.query");

    Route::prefix("computeInstances")->group(function () {
        Route::middleware(\App\Http\Middleware\User\CreditCheck::class)->group(function () {
            Route::post('/power/massOn', "ComputeInstance\\PowerController@massOn")->name("computeInstances.power.mass.on");
            Route::post('/power/massReset', "ComputeInstance\\PowerController@massReset")->name("computeInstances.power.mass.reset");
            Route::post('/power/massOff', "ComputeInstance\\PowerController@massOff")->name("computeInstances.power.mass.off");
        });

        Route::post('/massDestroy', "ComputeInstance\\DestroyRequestController@massDestroy")->name("computeInstances.operation.massDestroy");

        Route::prefix("{computeInstance}")->group(function () {
            // ComputeInstance policy
            Route::middleware("can:operate,computeInstance")->group(function () {
                Route::get('/operationRequests', "ComputeResourceOperationRequestIndexController@indexByComputeInstance")->name("computeInstances.operationRequests");
                Route::get('/networkInterfaces', "ComputeInstance\\NetworkInterface\\NetworkInterfaceController@index")->name("computeInstances.networkInterfaces");

                Route::get("/console", "ComputeInstance\\ComputeInstanceController@console")->name("computeInstances.console");

                Route::middleware([\App\Http\Middleware\User\CreditCheck::class, \App\Http\Middleware\ComputeInstance\StatusCheck::class])->group(function () {
                    Route::patch('/basic', "ComputeInstance\\ComputeInstanceController@updateBasic")->name("computeInstances.updateBasic");
                    Route::get('/password', "ClientAPI\\ComputeInstance\\Controller@password")->name("computeInstances.show.password");
                });

                Route::get('/settingAvailableData', "ComputeInstance\\ComputeInstanceController@settingAvailableData")->name("computeInstance.settingAvailableData");

                Route::prefix("operation")->group(function () {

                    // Compute instance operation requests
                    Route::middleware([\App\Http\Middleware\ComputeInstance\StatusCheck::class, \App\Http\Middleware\ComputeInstance\OperationRequestLimit::class])->group(function () {
                        Route::middleware(\App\Http\Middleware\User\CreditCheck::class)->group(function () {
                            Route::post("/newVolume", "LocalVolume\\LocalVolumeController@newVolume")->name("localVolumes.new");

                            Route::post('/power/on', "ComputeInstance\\PowerController@on")->name("computeInstances.power.on");
                            Route::post('/power/off', "ComputeInstance\\PowerController@off")->name("computeInstances.power.off");
                            Route::post('/power/reset', "ComputeInstance\\PowerController@reset")->name("computeInstances.power.reset");

                            // Change virtual media
                            Route::post("/CDROM/changeMedia", "ComputeInstance\\VirtualMediaController@changeCDROMMedia")->name("computeInstances.operation.cdroms.changeMedia");
                            Route::post("/Floppy/changeMedia", "ComputeInstance\\VirtualMediaController@changeFloppyMedia")->name("computeInstances.operation.floppies.changeMedia");

                            // Reconfigure
                            Route::post("/reconfigure", "ComputeInstance\\ComputeInstanceController@reconfigure")->name("computeInstances.operation.reconfigure");

                            Route::patch('/hostname', "ComputeInstance\\ComputeInstanceController@changeHostname")->name("computeInstances.changeHostname");
                            Route::patch('/osPassword', "ComputeInstance\\ComputeInstanceController@resetOSPassword")->name("computeInstances.resetOSPassword");
                            Route::post('/osNetwork', "ComputeInstance\\ComputeInstanceController@reconfigureOSNetwork")->name("computeInstances.reconfigureOSNetwork");
                            Route::patch('/package/{computeInstancePackageId}', "ComputeInstance\\ComputeInstanceController@changePackage")->name("computeInstances.changePackage");

                            // Save advance setting
                            Route::patch("/advanceSettings", "ComputeInstance\\ComputeInstanceController@saveAdvanceSettings")->name("computeInstances.operation.saveAdvanceSettings");

                            // Change public image
                            Route::patch("/publicImage", "ComputeInstance\\ComputeInstanceController@changePublicImage")->name("computeInstances.operation.changePublicImage");
                        });
                    });

                    // Delete compute instance
                    Route::post('/destroy', "ComputeInstance\\DestroyRequestController@destroy")
                        ->middleware([\App\Http\Middleware\ComputeInstance\LockCheck::class, \App\Http\Middleware\ComputeInstance\OperationRequestLimit::class])
                        ->name("computeInstances.operation.destroy")
                    ;
                });
            });
        });
    });

    Route::prefix("localVolumes")->group(function () {
        Route::post("/massRelease", "LocalVolume\\LocalVolumeController@massRelease")->name("localVolumes.operation.massRelease");
        Route::middleware(\App\Http\Middleware\User\CreditCheck::class)->group(function () {
            Route::post("/massDetach", "LocalVolume\\LocalVolumeController@massDetach")->name("localVolumes.operation.massDetach");

            Route::prefix("{localVolume}")->group(function () {
                // LocalVolume policy
                Route::middleware("can:operate,localVolume")->group(function () {
                    Route::patch("/toggleProtectMode", "LocalVolume\\LocalVolumeController@toggleProtectMode")->name("localVolumes.toggleProtectMode");
                    Route::middleware(\App\Http\Middleware\LocalVolume\StatusCheck::class)->group(function () {
                        Route::get("/attachableInstances", "LocalVolume\\LocalVolumeIndexController@attachableInstances")->name("volumes.attachableInstances");

                        Route::patch("/togglePrimaryBootableDisk", "LocalVolume\\LocalVolumeController@togglePrimaryBootableDisk")->name("localVolumes.togglePrimaryBootableDisk");

                        Route::prefix("operation")->group(function () {
                            Route::patch("/changeBus",  "LocalVolume\\LocalVolumeController@changeBus")->name("localVolumes.operation.changeBus");

                            Route::middleware(\App\Http\Middleware\LocalVolume\OperationRequestLimit::class)->group(function () {
                                // resize
                                Route::post("/resize", "LocalVolume\\LocalVolumeController@resize")->name("localVolumes.operation.resize");
                                Route::post("/detach", "LocalVolume\\LocalVolumeController@detach")->name("localVolumes.operation.detach");

                                Route::post("/attachTo/{computeInstance}", "LocalVolume\\LocalVolumeController@attach")
                                    ->middleware(["can:operate,computeInstance", \App\Http\Middleware\ComputeInstance\StatusCheck::class])
                                    ->name("localVolumes.operation.attach")
                                ;
                            });
                        });
                    });

                    Route::middleware(\App\Http\Middleware\LocalVolume\ReleaseCheck::class)->group(function () {
                        Route::prefix("operation")->group(function () {
                            Route::middleware(\App\Http\Middleware\LocalVolume\OperationRequestLimit::class)->group(function () {
                                Route::post("/release", "LocalVolume\\LocalVolumeController@release")->name("localVolumes.operation.release");
                            });
                        });
                    });
                });
            });
        });
    });
});

Route::middleware("auth:web,admin")->group(function () {
    Route::get("/publicISOs", "AdminAPI\\PublicISO\\PublicISOIndexController")->name("client.publicISOs.index");
    Route::get("/publicFloppies", "AdminAPI\\PublicFloppy\\PublicFloppyIndexController")->name("client.publicFloppies.index");
});