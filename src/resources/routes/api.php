<?php

Route::prefix('api/v1')->middleware('api')->group(function () {
    Route::post('auth/login', 'Auth\PassportController@login');
    Route::post('auth/register', 'Auth\PassportController@register');
    Route::post('auth/logout', 'Auth\PassportController@logout');
    Route::post('auth/logout/allDevices', 'Auth\PassportController@logoutAllDevices');
    Route::group(['middleware' => ['role:Client']], function () {
        Route::get('users/{userId}', 'ClientApi\UserController@show');
        Route::put('users/{userId}', 'ClientApi\UserController@update');

        Route::get('notifications', 'NotificationController@index');
        Route::get('notifications/unRead', 'NotificationController@unRead');
        Route::post('notifications/readAll', 'NotificationController@markAll');
        Route::post('notifications/readSingle/{notification}', 'NotificationController@markSingle');
        Route::delete('notifications/{notification}', 'NotificationController@destroy');

        Route::post('uploads/avatar', 'UploadController@uploadAvatar');
        Route::get('uploads/avatar/{user}', 'UploadController@getAvatar');
        Route::post('uploads/file', 'UploadController@uploadFile');
        Route::get('uploads/files/{user}', 'UploadController@getUserFiles');

        Route::get('agent/attributes', 'ClientApi\AgentController@getAgentAttributes');
        Route::post('agent/register', 'ClientApi\AgentController@register');
    });

});

Route::prefix('api/v1/admin')->middleware('api')->group(function () {
    Route::post('auth/login', 'Auth\PassportController@login');
    Route::post('auth/logout', 'Auth\PassportController@logout');
    Route::post('auth/logout/allDevices', 'Auth\PassportController@logoutAllDevices');

    Route::get('users', 'AdminApi\UserController@index');
    Route::get('users/search', 'AdminApi\UserController@search');
    Route::get('users/{userId}', 'AdminApi\UserController@show');
    Route::post('users', 'AdminApi\UserController@store');
    Route::put('users/{userId}', 'AdminApi\UserController@update');
    Route::delete('users/{userId}', 'AdminApi\UserController@destroy');

    Route::get('agent', 'AdminApi\AgentController@index');
    Route::get('agent/pending', 'AdminApi\AgentController@pending');
    Route::post('agent/approve/{agentCode}', 'AdminApi\AgentController@approve');
    Route::post('agent/decline/{agentCode}', 'AdminApi\AgentController@decline');

    Route::get('notifications', 'NotificationController@index');
    Route::get('notifications/unRead', 'NotificationController@unRead');
    Route::post('notifications/readAll', 'NotificationController@markAll');
    Route::post('notifications/readSingle/{notification}', 'NotificationController@markSingle');
    Route::delete('notifications/{notification}', 'NotificationController@destroy');

    Route::get('roles', 'AdminApi\RoleController@index');
    Route::get('roles/{roleId}', 'AdminApi\RoleController@show');
    Route::post('roles', 'AdminApi\RoleController@store');
    Route::put('roles/{roleId}', 'AdminApi\RoleController@update');
    Route::delete('roles/{roleId}', 'AdminApi\RoleController@destroy');

    Route::get('permissions', 'AdminApi\PermissionController@index');

    Route::post('uploads/avatar', 'UploadController@uploadAvatar');
    Route::get('uploads/avatar/{user}', 'UploadController@getAvatar');
    Route::post('uploads/file', 'UploadController@uploadFile');
    Route::get('uploads/files/{user}', 'UploadController@getUserFiles');
});

//Handling not existing routes
Route::fallback(function(){
    return response()->json(['message' => 'Route doesn\'t exist, please read the docs for our available routes'], 404);
});
