<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/

/**
 * Companies
 */

Route::group(['namespace' => 'Companies', 'prefix' => 'companies'], function () {

    //\Auth

    Route::group(['namespace' => 'Auth'], function () {
        Route::group(['prefix' => 'auth'], function () {
            // Authentication routes...
            Route::post('login', ['as' => 'companies.auth.login', 'uses' => 'AuthController@postLogin']);

            // Registration routes...
            Route::post('register', ['as' => 'companies.auth.register', 'uses' => 'AuthController@postRegister']);

        });

        Route::group(['prefix' => 'password'], function () {
            // Password reset link request routes...
            Route::post('email', ['as' => 'companies.auth.email', 'uses' => 'PasswordController@postEmail']);

            // Password reset routes...
            Route::post('reset', ['as' => 'companies.auth.reset', 'uses' => 'PasswordController@postReset']);
        });
    });

    Route::group(['namespace' => 'Employees'], function () {
        Route::resource('employees', 'EmployeesController', ['except' => ['create', 'edit']]);
    });

    Route::group(['namespace' => 'Groups'], function () {
        Route::resource('groups', 'GroupsController', ['except' => ['create', 'edit']]);
        Route::resource('groups.employees', 'EmployeesController', ['only' => ['store', 'index', 'destroy']]);
    });

    Route::group(['namespace' => 'Company', 'prefix' => 'company'], function () {
        Route::get('/', ['as' => 'companies.company.index', 'uses' => 'CompanyController@index']);
        Route::put('/', ['as' => 'companies.company.update', 'uses' => 'CompanyController@update']);
    });
});


/**
 * Employees
 */
Route::group(['namespace' => 'Employees', 'prefix' => 'employees'], function () {

    //\Auth
    Route::group(['namespace' => 'Auth'], function () {
        Route::group(['prefix' => 'auth'], function () {
            // Authentication routes...
            Route::post('login', ['as' => 'companies.auth.login', 'uses' => 'AuthController@postLogin']);

            // Registration routes...
            //Route::post('register', ['as' => 'companies.auth.register', 'uses'=>'AuthController@postRegister']); //the registration is managed by the company

        });

        Route::group(['prefix' => 'password'], function () {
            // Password reset link request routes...
            Route::post('email', ['as' => 'companies.auth.email', 'uses' => 'PasswordController@postEmail']);

            // Password reset routes...
            Route::post('reset', ['as' => 'companies.auth.reset', 'uses' => 'PasswordController@postReset']);
        });
    });

    Route::group(['namespace' => 'Calendars'], function () {
        Route::resource('calendars', 'CalendarsController', ['except' => ['create', 'edit']]);
        Route::post('calendars/caldav', ['as' => 'employees.calendars.caldav', 'uses' => 'CalendarsController@storeCaldav']);
        Route::post('calendars/calendars', ['as' => 'employees.calendars.calendars', 'uses' => 'CalendarsController@getCalendars']);
        Route::resource('calendars.timeslots', 'TimeslotsController', ['except' => ['create', 'edit']]);
    });

    Route::group(['namespace' => 'Planners', 'prefix' => 'planners'], function () {
        Route::resource('groups', 'GroupsController', ['only' => ['index', 'show']]);
        Route::post('groups/{groups}/meetings/{meetings}/image', ['as' => 'employees.planners.groups.meetings.storeImage', 'uses' => 'MeetingsController@storeImage']);
        Route::get('groups/{groups}/meetings/{meetings}/image', ['as' => 'employees.planners.groups.meetings.showImage', 'uses' => 'MeetingsController@showImage']);
        Route::resource('groups.meetings', 'MeetingsController', ['except' => ['create', 'edit']]);
        Route::resource('groups.meetings.timeslots', 'MeetingTimeslotsController', ['except' => ['create', 'edit']]);
    });

    Route::group(['namespace' => 'Groups'], function () {
        Route::resource('groups', 'GroupsController', ['only' => ['index', 'show']]);
    });


    Route::group(['namespace' => 'Meetings'], function () {
        Route::get('meetings/{meetings}/image', ['as' => 'employees.meetings.showImage', 'uses' => 'MeetingsController@showImage']);
        Route::resource('meetings', 'MeetingsController', ['only' => ['index', 'show']]);
    });

    Route::group(['namespace' => 'Employee', 'prefix' => 'employee'], function () {
        Route::get('/', ['as' => 'employees.employee.index', 'uses' => 'EmployeeController@index']);
        Route::put('/', ['as' => 'employees.employee.update', 'uses' => 'EmployeeController@update']);
    });
});

Route::get('noauth/meetings/{meetings}/image', ['as' => 'noAuth.meetings.showImage', 'uses' => 'NoAuthController@showImage']);