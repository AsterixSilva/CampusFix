<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::livewire('/issues/create', 'issues.create-issue')
    ->name('issues.create');

Route::livewire('/issues/{issue}', 'issues.show-issue')
    ->name('issues.show');