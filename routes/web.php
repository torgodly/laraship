<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Process;

Route::get('/', function () {

    $result = Process::run('ls');

    dd($result->output());
});
