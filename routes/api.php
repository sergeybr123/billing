<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('login', 'API\UserController@login');
//Route::post('register', 'API\UserController@register');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth.basic')->group(function () {
    Route::resource('plans', 'PlanController')->except(['create', 'edit']);                                         // Тарифные планы
    Route::get('plan/all', 'PlanController@all');                                                                        // Все тарифные планы
    Route::resource('features', 'FeatureController')->except(['create', 'edit']);                                   // Фичи для подписки
    Route::resource('subscribe', 'SubscribeController')->except(['create', 'edit']);                                // Подписка по ИД подписчика
    Route::resource('additional', 'AdditionalSubscribeController')->except(['create', 'edit']);
    Route::get('additional-type/all', 'AdditionalTypeController@all');                                                   // Все виды дополнений к подписке
    Route::post('subscribe/rewrite/{user_id}/{plan_id}/{period}', 'SubscribeController@rewrite');                        // Переподписка пользователя
    Route::post('subscribe/additional-rewrite/{user_id}/{plan_id}/{month}', 'SubscribeController@additional_rewrite');   // Переподписка дополнительных услуг
    Route::match(['get', 'post'], 'subscribe/free/{user_id}', 'SubscribeController@free');                               // Подписка пользователей на тариф FREE
    Route::match(['get', 'post'], 'subscribe/unlimited/{user_id}', 'SubscribeController@unlimited');                     // Подписка пользователей на тариф Unlimited
    Route::resource('invoice', 'InvoicesController');                                                               // Платежи
    Route::match(['get', 'post'], 'invoices/{id}/paid', 'InvoicesController@paid');
    Route::resource('type-invoice', 'TypeInvoicesController')->except(['create', 'edit']);                          // Список доступных видов платежей
    Route::resource('services', 'ServicesController')->except(['create', 'edit']);                                  // Список доступных услуг
    Route::match(['get', 'post'], 'service/{id}/plan', 'ServicesController@byIdPlan');                                  // Получаем услуги по ИД плана
    Route::match(['get', 'post'], 'service/plan-not-null', 'ServicesController@planNotNull');                           // Получаем услуги по ИД плана

    Route::get('user-invoice/{id}', 'InvoicesController@userInvoice');                                                  // Получаем все счета по ИД пользователя

    Route::get('ext-subscribe/{id}', 'SubscribeController@extSubscribe');                                               // Продление подписки пользователя по его ИД
    Route::get('set-free/{id}', 'SubscribeController@setFreeSubscribe');                                                // Даем бесплатную подписку
    Route::get('set-trial/{id}', 'SubscribeController@setTrialSubscribe');                                                       // Даем Trial подписку

    Route::get('invoice-count', 'InvoicesController@countInvoice');                                                     // Возврат integer числа счетов

    //Route::resource('pays', 'PaymentController');                                                                   // Обработка ответа от CloudPayment

    Route::post('activate', 'ActivateController@activate');                                                             // Ставить оплачено и активировать подписку по данному чеку

    Route::match(['get', 'post'], 'pay', 'PaymentController@pays');                                                     // Обработка ответа от CloudPayment
    Route::match(['get', 'post'], 'pay-with-day', 'PaymentController@payWithDay');                                      // Подтверждение платежа от менеджера

    Route::get('set-not-active', 'ActivateController@set_not_active');                                                  // Меняем статус у неактивных подписок
    Route::get('set-free-not-active', 'ActivateController@set_free_not_active');                                        // Меняем подписку у неактивных подписок
    Route::get('end-active/{day}', 'ActivateController@getSubscribeEndOfDay');                                          // Получаем список подписок которые завершатся через *n дней

    /*RefInvoice*/
    Route::prefix('ref')->group(function() {
        Route::post('create-ref', 'RefController@create_ref_invoice');    // Создаем RefInvoice
        Route::post('create-ref-invoice-details', 'RefController@create_ref_invoice_detail');   // Добавляем к RefInvoice RefInvoiceDetail
        Route::post('get-test-ref/{ref_invoice_id}', 'RefController@ref');

    });
    Route::prefix('db')->group(function() {
        Route::get('insert-tables', 'DbController@insert_tables')->name('insert_tables');
        Route::get('update-created', 'DbController@update_invoice_created')->name('update_invoice_created');
    });
    /*--------Для других нужд--------*/
    Route::prefix('other')->group(function() {
        Route::get('rename-test-trial', 'OtherController@rename_test_trial'); // Переименование пробного в триальный тарифный план
        /*--------Сделать счета не активными--------*/
        Route::get('set-not-active', 'OtherController@setNotActive');
        Route::get('set-completed/{id}', 'InvoicesController@completed'); // ставим статус completed
        /*--------Изменить стоимость пакетов--------*/
        Route::get('change-price', 'OtherController@changePrice');
        // Заполняем таблицу с деталями счета
        Route::get('fillInvoiceOrders', 'OtherController@fillInvoiceOrders');
        // В подписке заполнить поле с количеством ботов
        Route::get('fillBotCount', 'OtherController@fillBotCount');
        Route::get('create-ref-invoice', 'OtherController@create_ref_inv');
    });
});
