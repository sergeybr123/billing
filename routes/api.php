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
    Route::post('subscribe/rewrite/{user_id}/{plan_id}/{period}', 'SubscribeController@rewrite');                        // Переподписка пользователя
    Route::post('subscribe/additional-rewrite/{user_id}/{plan_id}/{month}', 'SubscribeController@additional_rewrite');   // Переподписка дополнительных услуг
    Route::match(['get', 'post'], 'subscribe/free/{user_id}', 'SubscribeController@free');                               // Подписка пользователей на тариф FREE
    Route::match(['get', 'post'], 'subscribe/unlimited/{user_id}', 'SubscribeController@unlimited');                     // Подписка пользователей на тариф Unlimited
    Route::resource('invoice', 'InvoicesController')->except(['create', 'edit']);                                   // Платежи
    Route::match(['get', 'post'], 'invoices/{id}/paid', 'InvoicesController@paid');
    Route::resource('type-invoice', 'TypeInvoicesController')->except(['create', 'edit']);                          // Список доступных видов платежей
    Route::resource('services', 'ServicesController')->except(['create', 'edit']);                                  // Список доступных услуг
    Route::match(['get', 'post'], 'service/{id}/plan', 'ServicesController@byIdPlan');                                  // Получаем услуги по ИД плана
    Route::match(['get', 'post'], 'service/plan-not-null', 'ServicesController@planNotNull');                           // Получаем услуги по ИД плана

    Route::get('user-invoice/{id}', 'InvoicesController@userInvoice');                                                  // Получаем все счета по ИД пользователя

    Route::get('ext-subscribe/{id}', 'SubscribeController@extSubscribe');                                               // Продление подписки пользователя по его ИД

    Route::get('invoice-count', 'InvoicesController@countInvoice');                                                     // Возврат integer числа счетов

    Route::resource('pays', 'PaymentController');                                                                   // Обработка ответа от CloudPayment

    Route::post('activate', 'ActivateController@activate');                                                             // Ставить оплачено и активировать подписку по данному чеку

    Route::match(['get', 'post'], 'pay', 'PaymentController@pays');                                                     // Обработка ответа от CloudPayment
    Route::match(['get', 'post'], 'pay-with-day', 'PaymentController@payWithDay');                                      // Подтверждение платежа от менеджера

    Route::get('set-not-active', 'ActivateController@setStatus');                                                       // Меняем статус у неактивных подписок
    Route::get('end-active/{day}', 'ActivateController@getSubscribeEndOfDay');                                          // Получаем список подписок которые завершатся через *n дней
    Route::get('set-not-active', 'ActivateController@set_not_active');
});




