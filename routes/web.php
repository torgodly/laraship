<?php

use App\Services\DatabaseServices\CreateDatabaseService;
use App\Services\ShellScriptService;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

//create databse
Route::get('/create-database', function () {
    return (new CreateDatabaseService())
        ->execute('new_database');
});

//create database with custom user
Route::get('/create-database-with-user', function () {
    return (new CreateDatabaseService())
        ->execute('new_new_database', 'custom_user', 'gEW8^%WBCRgk!nyn');
});


//whoami
Route::get('/whoami', function () {
    return (new ShellScriptService())
        ->runCommand('whoami');
});


//sudo test
Route::get('/sudo-test', function () {
    return (new ShellScriptService())
        ->runCommand('sudo -u laraship whoami');
});

//fail test
Route::get('/fail-test', function () {
    return (new ShellScriptService())
        ->runCommand('sudo -u laraship whoami2');
});
