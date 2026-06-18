<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\FrameController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrameController::class, 'index'])->name('frame.index');
Route::post('/generate', [FrameController::class, 'generate'])->name('frame.generate');
Route::get('/download/{filename}', [FrameController::class, 'download'])->name('frame.download');

Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
Route::post('/admin/save', [AdminController::class, 'save'])->name('admin.save');
