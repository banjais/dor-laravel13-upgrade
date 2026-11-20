<?php

use Illuminate\Support\Facades\Route;

//admin-user
Route::get('admin/login', array('as' => 'admin.login', 'uses' => 'Admin\LoginController@getLogin'));
Route::post('admin/login', array('uses' => 'Admin\LoginController@postLogin'))->middleware('throttle:5,1');

// Public email OTP password reset for Super Admin, Office Admin and Circular Admin
Route::get('admin/forgot-password', array('as' => 'admin.password.request', 'uses' => 'Admin\PasswordResetOtpController@showForgotPassword'));
Route::post('admin/forgot-password', array('as' => 'admin.password.email', 'uses' => 'Admin\PasswordResetOtpController@sendOtp'))->middleware('throttle:5,1');
Route::get('admin/reset-password', array('as' => 'admin.password.reset', 'uses' => 'Admin\PasswordResetOtpController@showResetForm'));
Route::post('admin/reset-password', array('as' => 'admin.password.verify', 'uses' => 'Admin\PasswordResetOtpController@resetPassword'))->middleware('throttle:10,1');

Route::group(array('middleware' => ['auth', 'restrict.admin.roles'], 'prefix' => 'admin'), function () {

    // Route::get('/logout', array('as' => 'admin.logout', 'uses' => 'Admin\LoginController@logoutUser'))
    //     ->after('invalidate-browser-cache');

    Route::get('/logout', array('as' => 'admin.logout', 'uses' => 'Admin\LoginController@logoutUser'));


    Route::get('/home', array('as' => 'dashboard', 'uses' => 'Admin\LoginController@getHome'));
    // Global website appearance settings (Super Admin only)
    Route::get('website-theme', array(
        'as' => 'admin.website-theme.index',
        'uses' => 'Admin\WebsiteThemeSettingController@index'
    ));
    Route::post('website-theme', array(
        'as' => 'admin.website-theme.update',
        'uses' => 'Admin\WebsiteThemeSettingController@update'
    ));
    Route::post('website-theme/reset', array(
        'as' => 'admin.website-theme.reset',
        'uses' => 'Admin\WebsiteThemeSettingController@reset'
    ));

    // Flash Box animation settings (Super Admin only)
    Route::get('flash-box-settings', [
        'as' => 'admin.flash-box-settings.index',
        'uses' => 'Admin\\FlashBoxSettingController@index'
    ]);
    Route::post('flash-box-settings', [
        'as' => 'admin.flash-box-settings.update',
        'uses' => 'Admin\\FlashBoxSettingController@update'
    ]);

    // Flash Box publish/unpublish
    Route::post('flash-box/change-status', [
        'as' => 'admin.flash-box.change-status',
        'uses' => 'Admin\\FlashBoxSettingController@changeStatus'
    ]);

    // GRMS settings (Super Admin only)
    Route::get('grms', [
        'as' => 'admin.contact-bcc-settings.index',
        'uses' => 'Admin\\ContactBccSettingController@index'
    ]);
    Route::post('grms', [
        'as' => 'admin.contact-bcc-settings.update',
        'uses' => 'Admin\\ContactBccSettingController@update'
    ]);

    // Chatbot global settings (Super Admin only)

        Route::get('external-api-settings', [
            'as' => 'admin.external-api-settings.index',
            'uses' => 'Admin\ExternalApiSettingController@index'
        ]);
        Route::post('external-api-settings/confirm-password', [
            'as' => 'admin.external-api-settings.confirm-password',
            'uses' => 'Admin\ExternalApiSettingController@confirmPassword'
        ])->middleware('throttle:5,5');
        Route::post('external-api-settings/verify-otp', [
            'as' => 'admin.external-api-settings.verify-otp',
            'uses' => 'Admin\ExternalApiSettingController@verifyOtp'
        ])->middleware('throttle:10,5');
        Route::post('external-api-settings', [
            'as' => 'admin.external-api-settings.update',
            'uses' => 'Admin\ExternalApiSettingController@update'
        ]);
        Route::post('external-api-settings/integration-test', [
            'as' => 'admin.external-api-settings.integration-test',
            'uses' => 'Admin\ExternalApiSettingController@testIntegration'
        ]);

    Route::get('chatbot-settings', [
        'as' => 'admin.chatbot-settings.index',
        'uses' => 'Admin\\ChatbotSettingController@index'
    ]);
    Route::post('chatbot-settings', [
        'as' => 'admin.chatbot-settings.update',
        'uses' => 'Admin\\ChatbotSettingController@update'
    ]);

    // Chatbot Knowledge Sources (Super Admin only)
    Route::get('chatbot-knowledge', [
        'as' => 'admin.chatbot-knowledge.index',
        'uses' => 'Admin\\ChatbotKnowledgeController@index'
    ]);

    // Chatbot Knowledge Sources (Super Admin only)
    Route::get('chatbot-knowledge', [
        'as' => 'admin.chatbot-knowledge.index',
        'uses' => 'Admin\\ChatbotKnowledgeController@index'
    ]);
    Route::post('chatbot-knowledge/upload', [
        'as' => 'admin.chatbot-knowledge.upload',
        'uses' => 'Admin\\ChatbotKnowledgeController@upload'
    ]);
    Route::post('chatbot-knowledge/url', [
        'as' => 'admin.chatbot-knowledge.url',
        'uses' => 'Admin\\ChatbotKnowledgeController@registerUrl'
    ]);
    Route::post('chatbot-knowledge/{source}/reindex', [
        'as' => 'admin.chatbot-knowledge.reindex',
        'uses' => 'Admin\\ChatbotKnowledgeController@reindex'
    ]);
    Route::post('chatbot-knowledge/{source}/toggle', [
        'as' => 'admin.chatbot-knowledge.toggle',
        'uses' => 'Admin\\ChatbotKnowledgeController@toggle'
    ]);
    Route::post('chatbot-knowledge/{source}/delete', [
        'as' => 'admin.chatbot-knowledge.delete',
        'uses' => 'Admin\\ChatbotKnowledgeController@destroy'
    ]);

    // Internal AI Provider Management (Super Admin only)
    Route::get('chatbot-internal-providers', [
        'as' => 'admin.chatbot-internal-providers.index',
        'uses' => 'Admin\\ChatbotInternalProviderController@index'
    ]);
    Route::post('chatbot-internal-providers', [
        'as' => 'admin.chatbot-internal-providers.store',
        'uses' => 'Admin\\ChatbotInternalProviderController@store'
    ]);
    Route::post('chatbot-internal-providers/{provider}/update', [
        'as' => 'admin.chatbot-internal-providers.update',
        'uses' => 'Admin\\ChatbotInternalProviderController@update'
    ]);
    Route::post('chatbot-internal-providers/{provider}/delete', [
        'as' => 'admin.chatbot-internal-providers.destroy',
        'uses' => 'Admin\\ChatbotInternalProviderController@destroy'
    ]);
    Route::post('chatbot-settings/integration-test', [
        'as' => 'admin.chatbot-settings.integration-test',
        'uses' => 'Admin\\ChatbotSettingController@testIntegration'
    ]);


    // Government identity settings (Super Admin only)
    Route::post('website-identity', array(
        'as' => 'admin.website-identity.update',
        'uses' => 'Admin\WebsiteIdentitySettingController@update'
    ));

    Route::get('/', array('uses' => 'Admin\LoginController@getHome'));

    Route::get('users',  array('as' => 'admin.users', 'uses' => 'Admin\UserController@getIndex'));
    Route::get('user/add',  array('as' => 'admin.users.add', 'uses' => 'Admin\UserController@getAddUser'));
    Route::post('user/add',  array('as' => 'admin.users.store', 'uses' => 'Admin\UserController@addUser'));
    Route::get('user/edit/{id}',  array('as' => 'admin.users.edit', 'uses' => 'Admin\UserController@getEditUser'));
    Route::post('user/edit/{id}',  array('as' => 'admin.users.update', 'uses' => 'Admin\UserController@editUser'));
    Route::post('user/delete', array('as' => 'admin.user.delete', 'uses' => 'Admin\UserController@deleteUser'));
    Route::post('user/change-status', array('as' => 'admin.user.change.status', 'uses' => 'Admin\UserController@changeStatus'));

    Route::get('own/change-password', array('as' => 'admin.change.password', 'uses' => 'Admin\UserController@getChangePassword'));
    Route::post('own/change-password', array('as' => 'admin.change.password.post', 'uses' => 'Admin\UserController@changeOwnPassword'));
    Route::get('user/change-password/{id}', array('as' => 'admin.user.change.password', 'as' => 'admin.users.change.password', 'uses' => 'Admin\UserController@getChangePasswordByAdmin'));
    Route::post('user/change-password/{id}', array('as' => 'admin.user.change.password.update', 'uses' => 'Admin\UserController@changePassword'));

    // Route::post('/user/get-info-dynamically',array('uses'=>'Admin\UserController@getUserDetails'));
    // Route::get('/user/get-info-dynamically',array('uses'=>'Admin\UserController@getUserDetails'));

    // Circular Admin account management
    Route::get('circular-admins', ['as' => 'admin.circular-admin.index', 'uses' => 'Admin\\CircularAdminController@index']);
    Route::get('circular-admins/create', ['as' => 'admin.circular-admin.create', 'uses' => 'Admin\\CircularAdminController@create']);
    Route::post('circular-admins', ['as' => 'admin.circular-admin.store', 'uses' => 'Admin\\CircularAdminController@store']);
    Route::get('circular-admins/{id}/edit', ['as' => 'admin.circular-admin.edit', 'uses' => 'Admin\\CircularAdminController@edit']);
    Route::post('circular-admins/{id}', ['as' => 'admin.circular-admin.update', 'uses' => 'Admin\\CircularAdminController@update']);
    Route::post('circular-admins/{id}/status', ['as' => 'admin.circular-admin.status', 'uses' => 'Admin\\CircularAdminController@toggleStatus']);

    //==================== User types  routes===========================================

    Route::get('usertypes', array('as' => 'admin.usertypes', 'uses' => 'Admin\UserTypeController@getIndex'));
    Route::get('usertype/add',  array('as' => 'admin.usertype.add', 'uses' => 'Admin\UserTypeController@getAddUserType'));
    Route::post('usertype/add',  array('as' => 'admin.usertype.store', 'uses' => 'Admin\UserTypeController@addUserType'));
    Route::post('usertype/edit',  array('as' => 'admin.usertype.update', 'uses' => 'Admin\UserTypeController@editUserType'));
    Route::post('usertype/change-status', array('as' => 'admin.usertype.change.status', 'uses' => 'Admin\UserTypeController@changeStatus'));
    Route::post('usertype/delete', array('as' => 'admin.usertype.delete', 'uses' => 'Admin\UserTypeController@deleteUserType'));
    Route::post('/usertype/get-info-dynamically', array('uses' => 'Admin\UserTypeController@getUserTypeDynamically'));

    //==================== office routes	===========================================

    Route::get('offices',  array('as' => 'admin.offices', 'uses' => 'Admin\OfficeController@getIndex'));
    Route::get('office/add',  array('as' => 'admin.office.add', 'uses' => 'Admin\OfficeController@getAddOffice'));
    Route::post('office/add',  array('as' => 'admin.office.store', 'uses' => 'Admin\OfficeController@addOffice'));
    Route::get('office/edit/{id}',  array('as' => 'admin.office.edit', 'uses' => 'Admin\OfficeController@getEditOffice'));
    Route::post('office/edit/{id}',  array('as' => 'admin.office.update', 'uses' => 'Admin\OfficeController@editOffice'));
    Route::post('office/delete', array('as' => 'admin.office.delete', 'uses' => 'Admin\OfficeController@deleteOffice'));
    Route::post('office/change-status', array('as' => 'admin.office.change.status', 'uses' => 'Admin\OfficeController@changeStatus'));
    Route::get('office/sort_offices',  array('as' => 'admin.office.sortorder', 'as' => 'office.sortorder', 'uses' => 'Admin\OfficeController@getSortOrder'));
    Route::post('office/sort_offices',  array('as' => 'admin.office.sortorder.update', 'as' => 'office.sortorder.post', 'uses' => 'Admin\OfficeController@sortOrder'));

    // Route::post('/office/get-info-dynamically',array('uses'=>'Admin\OfficeController@getOfficeDetails'));
    // Route::get('/office/get-info-dynamically',array('uses'=>'Admin\OfficeController@getOfficeDetails'));

    //==================== access control routes	===========================================
    Route::get('/access-control', array('as' => 'admin.access.control', 'uses' => 'Admin\AccessListController@getIndex'));
    Route::get('/access-control/edit/{id?}', array('uses' => 'Admin\AccessListController@getEditAccessList'));
    Route::get('/acess-control/change-access-status-dynamically', array('uses' => 'Admin\AccessListController@changeStatus'));
    Route::post('/acess-control/change-access-status-dynamically', array('uses' => 'Admin\AccessListController@changeStatus'));

    Route::get('/access-control-user', array('uses' => 'Admin\AccessListController@getIndexUser'));
    Route::get('/access-control-user/edit/{id?}', array('uses' => 'Admin\AccessListController@getEditAccessListUser'));
    Route::get('/acess-control-user/change-access-status-dynamically', array('uses' => 'Admin\AccessListController@changeStatusUser'));
    Route::post('/acess-control-user/change-access-status-dynamically', array('uses' => 'Admin\AccessListController@changeStatusUser'));
    //==================== modules routes	===========================================
    Route::post('/module/add', array('as' => 'admin.module.add', 'uses' => 'Admin\AccessListController@addModule'));
});

Route::group(array('middleware' => 'auth', 'prefix' => 'project'), function () {
    Route::get('own/change-password', array('as' => 'project.admin.change.password', 'uses' => 'Admin\Project\UserController@changePassword'));
    Route::post('own/change-password', array('as' => 'project.admin.update.password', 'uses' => 'Admin\Project\UserController@updatePassword'));
});

//admin - homes
Route::group(array('middleware' => ['auth', 'restrict.admin.roles'], 'prefix' => 'admin'), function () {
    Route::get('homes', ['as' => 'admin.homes', 'uses' => 'Admin\HomeController@getIndex']);
    Route::get('home/add', ['as' => 'admin.home.add', 'uses' => 'Admin\HomeController@getAddHome']);
    Route::get('home/add-live', ['as' => 'admin.home.add.live', 'uses' => 'Admin\HomeController@getAddLiveHome']);
    Route::post('home/add', ['as' => 'admin.home.store', 'uses' => 'Admin\HomeController@addHome']);
    Route::post('home/add-live', ['as' => 'admin.home.store.live', 'uses' => 'Admin\HomeController@addHome']);
    Route::get('home/edit/{id}', ['as' => 'admin.home.edit', 'uses' => 'Admin\HomeController@getEditHome']);
    Route::post('home/edit/{id}', ['as' => 'admin.home.update', 'uses' => 'Admin\HomeController@editHome']);
    // Route::post('home/delete', ['as'=>'admin.home.delete','uses'=>'Admin\HomeController@deleteHome']);
    Route::post('home/change-status', ['as' => 'admin.home.change.status', 'uses' => 'Admin\HomeController@changeStatus']);

    Route::get('home/addspoke', 'Admin\HomeController@getAddHomespoke');
    Route::get('home/addworkarea', 'Admin\HomeController@getAddHomework');
    Route::get('home/addgeninfo', 'Admin\HomeController@getAddHomegeneralinfo');
    Route::get('home/addaboutus', 'Admin\HomeController@getAddHomeaboutus');
    Route::get('home/addgrievance', 'Admin\HomeController@getAddHomeGrievanceOfficer');
    Route::get('home/addadditionalofficer', 'Admin\HomeController@getAddHomeAdditionalOfficer');

    Route::get('news-ticker', 'Admin\HomeController@getNewsTicker');
    Route::get('news-ticker/add', 'Admin\HomeController@getAddNewsTicker');
    Route::get('news-ticker/edit/{id}', 'Admin\HomeController@getEditNewsTicker');
    Route::post('news-ticker/add', 'Admin\HomeController@addNewsTicker');
    Route::post('news-ticker/edit/{id}', 'Admin\HomeController@editNewsTicker');
    Route::post('news-ticker/change', 'Admin\HomeController@changeNewsTickerStatus');
    Route::post('news-ticker/delete', 'Admin\HomeController@deleteNewsTicker');
});

//admin - contents
Route::group(array('middleware' => ['auth', 'restrict.admin.roles'], 'prefix' => 'admin'), function () {
    Route::get('/contents', ['as' => 'admin.contents', 'uses' => 'Admin\ContentController@getIndex']);
    Route::get('/content/add', ['as' => 'admin.content.add', 'uses' => 'Admin\ContentController@getAddContent']);
    Route::post('/content/add', ['as' => 'admin.content.store', 'uses' => 'Admin\ContentController@addContent']);
    Route::get('/content/edit/{id}', ['as' => 'admin.content.edit', 'uses' => 'Admin\ContentController@getEditContent']);
    Route::post('/content/edit/{id}', ['as' => 'admin.content.update', 'uses' => 'Admin\ContentController@editContent']);
    Route::post('/content/delete', ['as' => 'admin.content.delete', 'uses' => 'Admin\ContentController@deleteContent']);
    Route::post('/content/change-status', ['as' => 'admin.content.change.status', 'uses' => 'Admin\ContentController@changeStatus']);
    Route::post('/content_document/delete', ['as' => 'admin.content.delete.document', 'uses' => 'Admin\ContentController@deleteContentDoc']);
});

//admin - projects
Route::group(array('middleware' => ['auth', 'restrict.admin.roles'], 'prefix' => 'admin'), function () {
    Route::get('projects', ['as' => 'admin.projects', 'uses' => 'Admin\ProjectController@getIndex']);
    Route::get('project/add', ['as' => 'admin.project.add', 'uses' => 'Admin\ProjectController@getAddProject']);
    Route::post('project/add', ['as' => 'admin.project.store', 'uses' => 'Admin\ProjectController@addProject']);
    Route::get('project/edit/{id}', ['as' => 'admin.project.edit', 'uses' => 'Admin\ProjectController@getEditProject']);
    Route::post('project/edit/{id}', ['as' => 'admin.project.update', 'uses' => 'Admin\ProjectController@editProject']);
    Route::post('project/delete', ['as' => 'admin.project.delete', 'uses' => 'Admin\ProjectController@deleteProject']);
    Route::post('project/change-status', ['as' => 'admin.project.change.status', 'uses' => 'Admin\ProjectController@changeStatus']);
});

Route::group(array('middleware' => 'auth', 'prefix' => 'project'), function () {
    Route::get('dashboard', ['as' => 'project.admin.dashboard', 'uses' => 'Project\ProjectController@getIndex']);
    Route::get('edit/{id}', ['as' => 'project.admin.edit', 'uses' => 'Project\ProjectController@getEditProject']);
    Route::post('edit/{id}', ['as' => 'project.admin.update', 'uses' => 'Project\ProjectController@updateProject']);
});

//admin - news
Route::group(array('middleware' => ['auth', 'restrict.admin.roles'], 'prefix' => 'admin'), function () {
    Route::get('news',  array('as' => 'admin.news', 'uses' => 'Admin\NewsController@getIndex'));
    Route::get('news/add',  array('as' => 'admin.news.add', 'uses' => 'Admin\NewsController@getAddNews'));
    Route::post('news/add',  array('as' => 'admin.news.store', 'uses' => 'Admin\NewsController@addNews'));
    Route::get('news/edit/{id}',  array('as' => 'admin.news.edit', 'uses' => 'Admin\NewsController@getEditNews'));
    Route::post('news/edit/{id}',  array('as' => 'admin.news.update', 'uses' => 'Admin\NewsController@editNews'));
    Route::post('news/delete', array('as' => 'admin.news.delete', 'uses' => 'Admin\NewsController@deleteNews'));
    Route::post('news/change-status', array('as' => 'admin.news.change.status', 'uses' => 'Admin\NewsController@changeStatus'));
});

//admin - notices
Route::group(array('middleware' => ['auth', 'restrict.admin.roles'], 'prefix' => 'admin'), function () {
    Route::get('notices',  array('as' => 'admin.notices', 'uses' => 'Admin\NoticeController@getIndex'));
    Route::get('notice/add',  array('as' => 'admin.notice.add', 'uses' => 'Admin\NoticeController@getAddNotice'));
    Route::post('notice/add',  array('as' => 'admin.notice.store', 'uses' => 'Admin\NoticeController@addNotice'));
    Route::get('notice/edit/{id}',  array('as' => 'admin.notice.edit', 'uses' => 'Admin\NoticeController@getEditNotice'));
    Route::post('notice/edit/{id}',  array('as' => 'admin.notice.update', 'uses' => 'Admin\NoticeController@editNotice'));
    Route::post('notice/delete', array('as' => 'admin.notice.delete', 'uses' => 'Admin\NoticeController@deleteNotice'));
    Route::post('notice/change-status', array('as' => 'admin.notice.change.status', 'uses' => 'Admin\NoticeController@changeStatus'));

    Route::get('newsletters',  array('as' => 'admin.newsletters', 'uses' => 'Admin\NewsletterController@getNewsletterIndex'));
    Route::get('newsletter/add',  array('as' => 'admin.newsletter.add', 'uses' => 'Admin\NewsletterController@getaddNewsletter'));
    Route::post('newsletter/add',  array('as' => 'admin.newsletter.store', 'uses' => 'Admin\NewsletterController@addNewsletter'));
    Route::get('newsletter/view/{id}',  array('as' => 'admin.newsletter.view', 'uses' => 'Admin\NewsletterController@getviewNewsletter'));
});

//admin - guidelines
Route::group(array('middleware' => ['auth', 'restrict.admin.roles'], 'prefix' => 'admin'), function () {
    Route::get('guidelines',  array('as' => 'admin.guidelines', 'uses' => 'Admin\GuidelineController@getIndex'));
    Route::get('guideline/add',  array('as' => 'admin.guideline.add', 'uses' => 'Admin\GuidelineController@getAddguideline'));
    Route::post('guideline/add',  array('as' => 'admin.guideline.store', 'uses' => 'Admin\GuidelineController@addguideline'));
    Route::get('guideline/edit/{id}',  array('as' => 'admin.guideline.edit', 'uses' => 'Admin\GuidelineController@getEditguideline'));
    Route::post('guideline/edit/{id}',  array('as' => 'admin.guideline.update', 'uses' => 'Admin\GuidelineController@editguideline'));
    Route::post('guideline/delete', array('as' => 'admin.guideline.delete', 'uses' => 'Admin\GuidelineController@deleteguideline'));
    Route::post('guideline/change-status', array('as' => 'admin.guideline.change.status', 'uses' => 'Admin\GuidelineController@changeStatus'));

    Route::get('newsletters',  array('as' => 'admin.newsletters', 'uses' => 'Admin\NewsletterController@getNewsletterIndex'));
    Route::get('newsletter/add',  array('as' => 'admin.newsletter.add', 'uses' => 'Admin\NewsletterController@getaddNewsletter'));
    Route::post('newsletter/add',  array('as' => 'admin.newsletter.store', 'uses' => 'Admin\NewsletterController@addNewsletter'));
    Route::get('newsletter/view/{id}',  array('as' => 'admin.newsletter.view', 'uses' => 'Admin\NewsletterController@getviewNewsletter'));
});

//admin - tenders
Route::group(array('middleware' => ['auth', 'restrict.admin.roles'], 'prefix' => 'admin'), function () {
    Route::get('tenders',  array('as' => 'admin.tenders', 'uses' => 'Admin\TenderController@getIndex'));
    Route::get('tender/add',  array('as' => 'admin.tender.add', 'uses' => 'Admin\TenderController@getAddTender'));
    Route::post('tender/add',  array('as' => 'admin.tender.store', 'uses' => 'Admin\TenderController@addTender'));
    Route::get('tender/edit/{id}',  array('as' => 'admin.tender.edit', 'uses' => 'Admin\TenderController@getEditTender'));
    Route::post('tender/edit/{id}',  array('as' => 'admin.tender.store.post', 'uses' => 'Admin\TenderController@editTender'));
    Route::post('tender/delete', array('as' => 'admin.tender.delete', 'uses' => 'Admin\TenderController@deleteTender'));
    Route::post('tender/change-status', array('as' => 'admin.tender.change.status', 'uses' => 'Admin\TenderController@changeStatus'));
});

//admin - presses
Route::group(array('middleware' => ['auth', 'restrict.admin.roles'], 'prefix' => 'admin'), function () {
    Route::get('presses',  array('as' => 'admin.presses', 'uses' => 'Admin\PressController@getIndex'));
    Route::get('press/add',  array('as' => 'admin.press.add', 'uses' => 'Admin\PressController@getAddPress'));
    Route::post('press/add',  array('as' => 'admin.press.store', 'uses' => 'Admin\PressController@addPress'));
    Route::get('press/edit/{id}',  array('as' => 'admin.press.edit', 'uses' => 'Admin\PressController@getEditPress'));
    Route::post('press/edit/{id}',  array('as' => 'admin.press.update', 'uses' => 'Admin\PressController@editPress'));
    Route::post('press/delete', array('as' => 'admin.press.delete', 'uses' => 'Admin\PressController@deletePress'));
    Route::post('press/change-status', array('as' => 'admin.press.change.status', 'uses' => 'Admin\PressController@changeStatus'));
});

//admin - downloads
Route::group(array('middleware' => ['auth', 'restrict.admin.roles'], 'prefix' => 'admin'), function () {
    Route::get('downloads',  array('as' => 'admin.downloads', 'uses' => 'Admin\DownloadController@getIndex'));
    Route::get('download/add',  array('as' => 'admin.download.add', 'uses' => 'Admin\DownloadController@getAddDownload'));
    Route::post('download/add',  array('as' => 'admin.download.store', 'uses' => 'Admin\DownloadController@addDownload'));
    Route::get('download/edit/{id}',  array('as' => 'admin.download.edit', 'uses' => 'Admin\DownloadController@getEditDownload'));
    Route::post('download/edit/{id}',  array('as' => 'admin.download.update', 'uses' => 'Admin\DownloadController@editDownload'));
    Route::post('download/delete', array('as' => 'admin.download.delete', 'uses' => 'Admin\DownloadController@deleteDownload'));
    Route::post('download/change-status', array('as' => 'admin.download.change.status', 'uses' => 'Admin\DownloadController@changeStatus'));
});


//admin - vehicles
Route::group(array('middleware' => ['auth', 'restrict.admin.roles'], 'prefix' => 'admin'), function () {
    Route::get('vehicles',  array('as' => 'admin.vehicles', 'uses' => 'Admin\VehicleController@getIndex'));
    Route::get('vehicle/add',  array('as' => 'admin.vehicle.add', 'uses' => 'Admin\VehicleController@getAddVehicle'));
    Route::post('vehicle/add',  array('as' => 'admin.vehicle.store', 'uses' => 'Admin\VehicleController@addVehicle'));
    Route::get('vehicle/edit/{id}',  array('as' => 'admin.vehicle.edit', 'uses' => 'Admin\VehicleController@getEditVehicle'));
    Route::post('vehicle/edit/{id}',  array('as' => 'admin.vehicle.update', 'uses' => 'Admin\VehicleController@editVehicle'));
    Route::post('vehicle/delete', array('as' => 'admin.vehicle.delete', 'uses' => 'Admin\VehicleController@deleteVehicle'));
    Route::post('vehicle/change-status', array('as' => 'admin.vehicle.change.status', 'uses' => 'Admin\VehicleController@changeStatus'));
});

//admin - categories
Route::group(array('middleware' => ['auth', 'restrict.admin.roles'], 'prefix' => 'admin'), function () {
    Route::get('categories',  array('as' => 'admin.categories', 'uses' => 'Admin\CategoryController@getIndex'));
    Route::get('category/add',  array('as' => 'admin.category.add', 'uses' => 'Admin\CategoryController@getAddCategory'));
    Route::post('category/add',  array('as' => 'admin.category.store', 'uses' => 'Admin\CategoryController@addCategory'));
    Route::get('category/edit/{id}',  array('as' => 'admin.category.edit', 'uses' => 'Admin\CategoryController@getEditCategory'));
    Route::post('category/edit/{id}',  array('as' => 'admin.category.update', 'uses' => 'Admin\CategoryController@editCategory'));
    Route::post('category/delete', array('as' => 'admin.category.delete', 'uses' => 'Admin\CategoryController@deleteCategory'));
    Route::post('category/change-status', array('as' => 'admin.category.change.status', 'uses' => 'Admin\CategoryController@changeStatus'));

    Route::get('publications',  array('as' => 'admin.publications', 'uses' => 'Admin\PublicationController@getIndex'));
    Route::get('publication/add',  array('as' => 'admin.publication.add', 'uses' => 'Admin\PublicationController@getAddPublication'));
    Route::post('publication/add',  array('as' => 'admin.publication.store', 'uses' => 'Admin\PublicationController@addPublication'));
    Route::get('publication/edit/{id}',  array('as' => 'admin.publication.edit', 'uses' => 'Admin\PublicationController@getEditPublication'));
    Route::post('publication/edit/{id}',  array('as' => 'admin.publication.update', 'uses' => 'Admin\PublicationController@editPublication'));
    Route::post('publication/delete', array('as' => 'admin.publication.delete', 'uses' => 'Admin\PublicationController@deletePublication'));
    Route::post('publication/change-status', array('as' => 'admin.publication.change.status', 'uses' => 'Admin\PublicationController@changeStatus'));
});

//admin - banners
Route::group(array('middleware' => ['auth', 'restrict.admin.roles'], 'prefix' => 'admin'), function () {
    Route::get('banners',  array('as' => 'admin.banners', 'uses' => 'Admin\BannerController@getIndex'));
    Route::get('banner/add',  array('as' => 'admin.banner.add', 'uses' => 'Admin\BannerController@getAddBanner'));
    Route::post('banner/add',  array('as' => 'admin.banner.store', 'uses' => 'Admin\BannerController@addBanner'));
    Route::get('banner/edit/{url}',  array('as' => 'admin.banner.edit', 'uses' => 'Admin\BannerController@getEditBanner'));
    Route::post('banner/edit/{url}',  array('as' => 'admin.banner.update', 'uses' => 'Admin\BannerController@editBanner'));
    Route::post('banner/delete', array('as' => 'admin.banner.delete', 'uses' => 'Admin\BannerController@deleteBanner'));
    Route::post('banner/change-status', array('as' => 'admin.banner.change.status', 'uses' => 'Admin\BannerController@changeStatus'));
});

//admin - albums
Route::group(array('middleware' => ['auth', 'restrict.admin.roles'], 'prefix' => 'admin'), function () {
    Route::get('albums',  array('as' => 'admin.albums', 'uses' => 'Admin\AlbumController@getIndex'));
    Route::get('album/add',  array('as' => 'admin.album.add', 'uses' => 'Admin\AlbumController@getAddAlbum'));
    Route::post('album/add',  array('as' => 'admin.album.store', 'uses' => 'Admin\AlbumController@addAlbum'));
    Route::get('album/edit/{id}',  array('as' => 'admin.album.edit', 'uses' => 'Admin\AlbumController@getEditAlbum'));
    Route::post('album/edit/{id}',  array('as' => 'admin.album.update', 'uses' => 'Admin\AlbumController@editAlbum'));
    Route::post('album/delete', array('as' => 'admin.album.delete', 'uses' => 'Admin\AlbumController@deleteAlbum'));
    Route::post('album/change-status', array('as' => 'admin.album.change.status', 'uses' => 'Admin\AlbumController@changeStatus'));

    Route::get('album/viewgallery/{id}', array('as' => 'admin.album.view.gallery', 'uses' => 'Admin\GalleryController@getIndex'));
    Route::get('album/addgallery/{id}', array('as' => 'admin.album.add.gallery', 'uses' => 'Admin\GalleryController@getAddGallery'));
    Route::post('album/addgallery/{id}', array('as' => 'admin.album.store.gallery', 'uses' => 'Admin\GalleryController@addGallery'));
    Route::get('gallery/edit/{id}', array('as' => 'admin.gallery.edit', 'uses' => 'Admin\GalleryController@getEditGallery'));
    Route::post('gallery/edit/{id}', array('as' => 'admin.gallery.update', 'uses' => 'Admin\GalleryController@editGallery'));
    Route::post('gallery/change-status', array('as' => 'admin.gallery.change.status', 'uses' => 'Admin\GalleryController@changeStatus'));
    Route::post('gallery/delete', array('as' => 'admin.gallery.delete', 'uses' => 'Admin\GalleryController@deleteGallery'));
});

//admin - staffs
Route::group(array('middleware' => ['auth', 'restrict.admin.roles'], 'prefix' => 'admin'), function () {
    Route::get('staffs',  array('as' => 'admin.staffs', 'uses' => 'Admin\StaffController@getIndex'));
    Route::get('staff/add',  array('as' => 'admin.staff.add', 'uses' => 'Admin\StaffController@getAddStaff'));
    Route::post('staff/add',  array('as' => 'admin.staff.store', 'uses' => 'Admin\StaffController@addStaff'));
    Route::get('staff/sort_staffs',  array('as' => 'admin.staffs.sortorder', 'uses' => 'Admin\StaffController@getsortStaff'));
    Route::post('staff/sort_staffs',  array('as' => 'admin.staffs.sortorder.update', 'uses' => 'Admin\StaffController@sortStaff'));
    Route::get('staff/edit/{id}',  array('as' => 'admin.staff.edit', 'uses' => 'Admin\StaffController@getEditStaff'));
    Route::post('staff/edit/{id}',  array('as' => 'admin.staff.update', 'uses' => 'Admin\StaffController@editStaff'));
    Route::post('staff/delete', array('as' => 'admin.staff.delete', 'uses' => 'Admin\StaffController@deleteStaff'));
    Route::post('staff/change-status', array('as' => 'admin.staff.change.status', 'uses' => 'Admin\StaffController@changeStatus'));

    Route::get('email/request',  array('as' => 'admin.email.request', 'uses' => 'Admin\EmailRequestController@getEmailRequestForm'));
    Route::post('email/request',  array('as' => 'admin.email.request.store', 'uses' => 'Admin\EmailRequestController@submitEmailRequestForm'));
    Route::get('email/request/forms',  array('as' => 'admin.email.request.forms', 'uses' => 'Admin\EmailRequestController@emailRequestForms'));
    Route::post('email/request/change-status',  array('as' => 'admin.email.request.change.status', 'uses' => 'Admin\EmailRequestController@changeStatus'));
    Route::get('email/request/force/{id}', ['as' => 'admin.email.request.download', 'uses' => 'Admin\EmailRequestController@getForceDownload']);

    Route::get('login-credentials',  array('as' => 'admin.login-credentials.request', 'uses' => 'Admin\LoginRequestController@getAll'));
    Route::post('login-credentials/change-status',  array('as' => 'admin.login-credentials.request.change.status', 'uses' => 'Admin\LoginRequestController@changeStatus'));

    Route::get('office_chiefs',  array('as' => 'admin.office_chiefs', 'uses' => 'Admin\OfficeChiefController@getIndex'));
    Route::get('office_chiefs/add',  array('as' => 'admin.office_chiefs.add', 'uses' => 'Admin\OfficeChiefController@getAddStaff'));
    Route::post('office_chiefs/add',  array('as' => 'admin.office_chiefs.store', 'uses' => 'Admin\OfficeChiefController@addStaff'));
    // Internal AI Photo Studio (Staff and Office Chief)
    Route::post('photo-studio/{type}/preview', [
        'as' => 'admin.photo-studio.create',
        'uses' => 'Admin\PhotoStudioController@createPreview'
    ]);
    Route::get('photo-studio/{type}/preview/{token}', [
        'as' => 'admin.photo-studio.preview',
        'uses' => 'Admin\PhotoStudioController@showPreview'
    ]);
    Route::get('photo-studio/{type}/download/{token}', [
        'as' => 'admin.photo-studio.download',
        'uses' => 'Admin\PhotoStudioController@downloadPreview'
    ]);
    Route::post('photo-studio/{type}/reject', [
        'as' => 'admin.photo-studio.reject',
        'uses' => 'Admin\PhotoStudioController@rejectPreview'
    ]);
    Route::get('office_chiefs/sort_office_chiefs',  array('as' => 'admin.office_chiefs.sortorder', 'uses' => 'Admin\OfficeChiefController@getsortStaff'));
    Route::post('office_chiefs/sort_office_chiefs',  array('as' => 'admin.office_chiefs.sortorder.update', 'uses' => 'Admin\OfficeChiefController@sortStaff'));
    Route::get('office_chiefs/edit/{id}',  array('as' => 'admin.office_chiefs.edit', 'uses' => 'Admin\OfficeChiefController@getEditStaff'));
    Route::post('office_chiefs/edit/{id}',  array('as' => 'admin.office_chiefs.update', 'uses' => 'Admin\OfficeChiefController@editStaff'));
    Route::post('office_chiefs/delete', array('as' => 'admin.office_chiefs.delete', 'uses' => 'Admin\OfficeChiefController@deleteStaff'));
    Route::post('office_chiefs/change-status', array('as' => 'admin.office_chiefs.change.status', 'uses' => 'Admin\OfficeChiefController@changeStatus'));
});

//admin - laws
Route::group(array('middleware' => ['auth', 'restrict.admin.roles'], 'prefix' => 'admin'), function () {
    Route::get('laws',  array('as' => 'admin.laws', 'uses' => 'Admin\LawController@getIndex'));
    Route::get('law/add',  array('as' => 'admin.law.add', 'uses' => 'Admin\LawController@getAddLaw'));
    Route::post('law/add',  array('as' => 'admin.law.store', 'uses' => 'Admin\LawController@addLaw'));
    Route::get('law/edit/{id}',  array('as' => 'admin.law.edit', 'uses' => 'Admin\LawController@getEditLaw'));
    Route::post('law/edit/{id}',  array('as' => 'admin.law.update', 'uses' => 'Admin\LawController@editLaw'));
    Route::post('law/delete', array('as' => 'admin.law.delete', 'uses' => 'Admin\LawController@deleteLaw'));
    Route::post('law/change-status', array('as' => 'admin.law.change.status', 'uses' => 'Admin\LawController@changeStatus'));
});

//admin - trainings
Route::group(array('middleware' => ['auth', 'restrict.admin.roles'], 'prefix' => 'admin'), function () {
    Route::get('trainings',  array('as' => 'admin.trainings', 'uses' => 'Admin\TrainingController@getIndex'));
    Route::get('training/add',  array('as' => 'admin.training.add', 'uses' => 'Admin\TrainingController@getAddTraining'));
    Route::post('training/add',  array('as' => 'admin.training.store', 'uses' => 'Admin\TrainingController@addTraining'));
    Route::get('training/edit/{id}',  array('as' => 'admin.training.edit', 'uses' => 'Admin\TrainingController@getEditTraining'));
    Route::post('training/edit/{id}',  array('as' => 'admin.training.update', 'uses' => 'Admin\TrainingController@editTraining'));
    Route::post('training/delete', array('as' => 'admin.training.delete', 'uses' => 'Admin\TrainingController@deleteTraining'));
    Route::post('training/change-status', array('as' => 'admin.training.change.status', 'uses' => 'Admin\TrainingController@changeStatus'));
});

//admin - circulars
Route::group(array('middleware' => ['auth', 'restrict.admin.roles'], 'prefix' => 'admin'), function () {
    Route::get('circulars',  array('as' => 'admin.circulars', 'uses' => 'Admin\CircularController@getIndex'));
    Route::get('circular/add',  array('as' => 'admin.circular.add', 'uses' => 'Admin\CircularController@getAddCircular'));
    Route::post('circular/add',  array('as' => 'admin.circular.store', 'uses' => 'Admin\CircularController@addCircular'));
    Route::get('circular/edit/{id}',  array('as' => 'admin.circular.edit', 'uses' => 'Admin\CircularController@getEditCircular'));
    Route::post('circular/edit/{id}',  array('as' => 'admin.circular.update', 'uses' => 'Admin\CircularController@editCircular'));
    Route::post('circular/delete', array('as' => 'admin.circular.delete', 'uses' => 'Admin\CircularController@deleteCircular'));
    Route::post('circular/change-status', array('as' => 'admin.circular.change.status', 'uses' => 'Admin\CircularController@changeStatus'));
    Route::get('circular/view/{id}',  array('as' => 'admin.circular.view', 'uses' => 'Admin\CircularController@getViewCircular'));
});

//admin - faqs
Route::group(array('middleware' => ['auth', 'restrict.admin.roles'], 'prefix' => 'admin'), function () {
    Route::get('faqs',  array('as' => 'admin.faqs', 'uses' => 'Admin\FaqController@getIndex'));
    Route::get('faq/add',  array('as' => 'admin.faq.add', 'uses' => 'Admin\FaqController@getAddFaq'));
    Route::post('faq/add',  array('as' => 'admin.faq.store', 'uses' => 'Admin\FaqController@addFaq'));
    Route::get('faq/edit/{url}',  array('as' => 'admin.faq.edit', 'uses' => 'Admin\FaqController@getEditFaq'));
    Route::post('faq/edit/{url}',  array('as' => 'admin.faq.update', 'uses' => 'Admin\FaqController@editFaq'));
    Route::post('faq/delete', array('as' => 'admin.faq.delete', 'uses' => 'Admin\FaqController@deleteFaq'));
    Route::post('faq/change-status', array('as' => 'admin.faq.change.status', 'uses' => 'Admin\FaqController@changeStatus'));
});

//admin - seo
Route::group(array('middleware' => ['auth', 'restrict.admin.roles'], 'prefix' => 'admin'), function () {
    Route::get('seos',  array('as' => 'admin.seos', 'uses' => 'Admin\SeoController@getIndex'));
    Route::get('seo/add',  array('as' => 'admin.seo.add', 'uses' => 'Admin\SeoController@getAddSeo'));
    Route::post('seo/add',  array('as' => 'admin.seo.store', 'uses' => 'Admin\SeoController@addSeo'));
    Route::get('seo/edit/{id}',  array('as' => 'admin.seo.edit', 'uses' => 'Admin\SeoController@getEditSeo'));
    Route::post('seo/edit/{id}',  array('as' => 'admin.seo.update', 'uses' => 'Admin\SeoController@editSeo'));
    Route::post('seo/delete', array('as' => 'admin.seo.delete', 'uses' => 'Admin\SeoController@deleteSeo'));
    Route::post('seo/change-status', array('as' => 'admin.seo.change.status', 'uses' => 'Admin\SeoController@changeStatus'));
});

//admin - equipments
Route::group(array('middleware' => ['auth', 'restrict.admin.roles'], 'prefix' => 'admin'), function () {
    Route::get('equipments',  array('as' => 'admin.equipments', 'uses' => 'Admin\EquipmentController@getIndex'));
    Route::get('equipment/add',  array('as' => 'admin.equipment.add', 'uses' => 'Admin\EquipmentController@getAddEquipment'));
    Route::post('equipment/add',  array('as' => 'admin.equipment.store', 'uses' => 'Admin\EquipmentController@addEquipment'));
    Route::get('equipment/edit/{id}',  array('as' => 'admin.equipment.edit', 'uses' => 'Admin\EquipmentController@getEditEquipment'));
    Route::post('equipment/edit/{id}',  array('as' => 'admin.equipment.update', 'uses' => 'Admin\EquipmentController@editEquipment'));
    Route::post('equipment/delete', array('as' => 'admin.equipment.delete', 'uses' => 'Admin\EquipmentController@deleteEquipment'));
    Route::post('equipment/change-status', array('as' => 'admin.equipment.change.status', 'uses' => 'Admin\EquipmentController@changeStatus'));
    Route::post('equipment/add-log', array('as' => 'admin.equipment.add.log', 'uses' => 'Admin\EquipmentController@addEquipmentLog'));
    Route::post('equipment/find-log', array('as' => 'admin.equipment.find.log', 'uses' => 'Admin\EquipmentController@findEquipmentLog'));
    Route::get('equipment/view-detail/{id}',  array('as' => 'admin.equipment.view.detail', 'uses' => 'Admin\EquipmentController@viewEquipmentDetail'));
});

//admin - logs
Route::group(array('middleware' => ['auth', 'restrict.admin.roles'], 'prefix' => 'admin'), function () {
    Route::get('logs',  array('as' => 'admin.logs', 'uses' => 'Admin\LogController@getIndex'));
});

//admin - tools
Route::group(array('middleware' => ['auth', 'restrict.admin.roles'], 'prefix' => 'admin'), function () {
    Route::get('tools',  array('as' => 'admin.tools', 'uses' => 'Admin\ExternalToolController@getIndex'));
    Route::get('tools/add',  array('as' => 'admin.tools.add', 'uses' => 'Admin\ExternalToolController@getAddTool'));
    Route::post('tools/add',  array('as' => 'admin.tools.store', 'uses' => 'Admin\ExternalToolController@addTool'));
    Route::get('tools/edit/{id}',  array('as' => 'admin.tools.edit', 'uses' => 'Admin\ExternalToolController@getEditTool'));
    Route::post('tools/edit/{id}',  array('as' => 'admin.tools.update', 'uses' => 'Admin\ExternalToolController@editTool'));
    Route::post('tools/delete', array('as' => 'admin.tools.delete', 'uses' => 'Admin\ExternalToolController@deleteTool'));
    Route::post('tools/change-status', array('as' => 'admin.tools.change.status', 'uses' => 'Admin\ExternalToolController@changeStatus'));
});

//public routes

Route::view('/install', 'pwa.install')->name('pwa.install');
Route::get('/', ['as' => 'root', 'uses' => 'HomeController@getHome']);
Route::get('{subdomain}/banner/{id}', 'HomeController@getBannerDetail')->whereNumber('id');
Route::get('{subdomain}/calendar/{type}', 'CalendarController@index')->where('type', 'bs|ad|ns')->name('frontend.calendar');
Route::post('chatbot/{subdomain}/message', [
    \App\Http\Controllers\PublicChatbotController::class,
    'message'
])->middleware('throttle:12,1')->name('public.chatbot.message');

Route::post('chatbot/{subdomain}/external-consent', [
    \App\Http\Controllers\PublicChatbotController::class,
    'externalConsent'
])->middleware('throttle:5,1')->name('public.chatbot.external-consent');

Route::get('{subdomain}', ['as' => 'frontend.homepage', 'uses' => 'HomeController@getIndex']);

Route::post('change_language', 'ConfigurationController@changeLangugage');

Route::post('reload_captcha', 'ConfigurationController@changeCaptcha');

Route::post('get_zones_data_for_map', 'ConfigurationController@getMapdata');
//Route::get('get_zones_data_for_map', 'ConfigurationController@getMapdata');

Route::get('{subdomain}/changedate', 'ConfigurationController@changeDateFromEngToNep');

Route::get('{subdomain}/search', 'HomeController@getSearch');
Route::get('{subdomain}/feedback', 'HomeController@getFeedback');
Route::post('{subdomain}/feedback', 'HomeController@postFeedback')->name('public.feedback.post');
Route::get('{subdomain}/contact', 'HomeController@getContact');
Route::get('{subdomain}/sitemap', 'HomeController@siteMap');
Route::get('{subdomain}/zone/{url}', 'HomeController@getZone');

Route::get('{subdomain}/home/{url}', 'HomeController@getHomeDetail');
Route::post('{subdomain}/distancecalc', 'HomeController@calcDistance');

Route::get('{subdomain}/getdistancecalc', 'HomeController@getDistanceIndex');
Route::post('{subdomain}/getdistancecalc', 'HomeController@getDistanceIndex');

Route::get('{subdomain}/office/{url}', 'HomeController@getOffice');
Route::get('{subdomain}/DRO', 'HomeController@getHomeOffice');

//content
Route::get('{subdomain}/page/{url}',  'ContentController@getContentDetail')->name('subdomain.page.url');
Route::get('{subdomain}/page/force/{url}', 'ContentController@getForceContent')->name('subdomain.page.force.url');
Route::get('{subdomain}/page/mainforce/{url}/{url1}', 'ContentController@getMainForceContent')->name('subdomain.page.mainforce.url');

//home
Route::get('home/latest-news/{id}', 'HomeController@getNewsTickerDetail');

//login credentials
Route::get('home/login-credentials-request', 'HomeController@getLoginCredentialPage');
Route::post('home/login-credentials-request/post', 'HomeController@postLoginCredentialPage');

//notice
Route::get('{subdomain}/notices', 'NoticeController@getActiveNotices');
Route::post('{subdomain}/notices', 'NoticeController@getActiveOfficeNotices');
Route::get('{subdomain}/notice/{url}', ['as' => 'notice.detail.slug', 'uses' => 'NoticeController@getNoticeDetail']);
// Route::get('{subdomain}/noticeid/{id}', 'NoticeController@getNoticeDetailById');
Route::get('{subdomain}/notice/force/{url}', 'NoticeController@getForceDownload');
Route::get('{subdomain}/searchnotice', 'NoticeController@getNoticeSearch');
Route::get('{subdomain}/searchoffice', 'NoticeController@getOfficesSearch');
Route::get('{subdomain}/searchofficeNotice', 'NoticeController@getOfficesNoticeSearch');

//guideline
Route::get('{subdomain}/guidelines', 'GuidelineController@getActiveGuidelines');
Route::get('{subdomain}/guideline/{url}', 'GuidelineController@getGuidelineDetail')->name('guideline.detail.slug');
Route::get('{subdomain}/guidelineid/{id}', 'GuidelineController@getGuidelineDetailById');
Route::get('{subdomain}/guideline/force/{url}', 'GuidelineController@getForceDownload');

//news
Route::get('{subdomain}/news', 'NewsController@getActiveNews');
Route::get('{subdomain}/news/{url}', 'NewsController@getNewsDetail');
Route::get('{subdomain}/newsid/{id}', 'NewsController@getNewsDetailById');
Route::get('{subdomain}/news/force/{url}', 'NewsController@getForceDownload');

//publication
Route::get('{subdomain}/searchpublication', 'PublicationController@getPublicationSearch');
Route::get('{subdomain}/publications', 'PublicationController@getActiveCategories');
Route::get('{subdomain}/publication/{url}', 'PublicationController@getActivePublications');
Route::get('{subdomain}/publication/{category_url}/{url}', 'PublicationController@getPublicationDetail');
Route::get('{subdomain}/publication/{category_url}/force/{url}', 'PublicationController@getForceDownload');

//circular
Route::get('{subdomain}/circulars', 'CircularController@getActiveCirculars');
Route::post('{subdomain}/circulars', 'CircularController@getActiveOfficeCirculars');
Route::get('{subdomain}/circular/{url}', 'CircularController@getCircularDetail');
Route::get('{subdomain}/circularid/{id}', 'CircularController@getCircularDetailById');
Route::get('{subdomain}/circular/force/{url}', 'CircularController@getForceDownload');
Route::get('{subdomain}/searchoffice', 'CircularController@getOfficesSearch');
Route::get('{subdomain}/searchofficeCircular', 'CircularController@getOfficesCircularSearch');

//laws
Route::get('{subdomain}/laws', 'LawController@getActiveLaws');
Route::get('{subdomain}/law/{url}', 'LawController@getLawDetail');
Route::get('{subdomain}/law/force/{url}', 'LawController@getForceDownload');

//search
Route::get('{subdomain}/search',  'SearchController@getSearchDetail');

//staff
Route::get('{subdomain}/staffs',  'StaffController@getStaffLists');
Route::get('{subdomain}/staffsOld',  'StaffController@getStaffListsOld');
Route::get('{subdomain}/staff/{id}',  'StaffController@getStaffDetail');

//download
Route::get('{subdomain}/downloads', 'DownloadController@getActiveDownloads');
Route::get('{subdomain}/download/{url}', 'DownloadController@getDownloadDetail');
Route::get('{subdomain}/download/force/{url}', 'DownloadController@getForceDownload');

//project
Route::get('{subdomain}/projects',  'ProjectController@getActiveProjects');
Route::get('{subdomain}/project/{url}',  ['as' => 'project.detail.slug', 'uses' => 'ProjectController@getProjectDetail']);

//vehicle
Route::get('{subdomain}/vehicles', 'VehicleController@getActiveVehicles');

//album
Route::get('{subdomain}/gallery', 'AlbumController@getActiveAlbums');
Route::get('{subdomain}/photogallery/{url}', 'GalleryController@getActiveGalleries');

//faq
Route::get('{subdomain}/faqs', 'FaqController@getActiveFaqs');

//equipment
Route::get('{subdomain}/equipments', 'EquipmentController@getActiveEquipments');

//press
Route::get('{subdomain}/presses', 'PressController@getActivePresses');
Route::get('{subdomain}/press/{url}', 'PressController@getPressDetail');
Route::get('{subdomain}/pressid/{id}', 'PressController@getPressDetailById');
Route::get('{subdomain}/press/force/{url}', 'PressController@getForceDownload');

//tenders
Route::get('{subdomain}/tenders', 'TenderController@getActiveTenders');
Route::post('{subdomain}/tenders', 'TenderController@searchActiveTenders');
Route::get('{subdomain}/tender/{url}', 'TenderController@getTenderDetail');
Route::get('{subdomain}/tenderid/{id}', 'TenderController@getTenderDetailById');
Route::get('{subdomain}/tender/force/{url}', 'TenderController@getForceDownload');

//trainings
Route::get('{subdomain}/trainings', 'TrainingController@getActiveTrainings');
Route::get('{subdomain}/training/{url}', 'TrainingController@getTrainingDetail');

//font size and bandwidth changer
Route::post('change_font_size', 'ConfigurationController@changeFontSize');
Route::post('change_bandwidth', 'ConfigurationController@changeBandwidth');

// Sitemap route is registered with the office-aware public routes.
