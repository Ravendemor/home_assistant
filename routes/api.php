<?php

use App\Http\Controllers\NoteController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json(['response' => ['user' => $request->user()], 'success' => true]);
});

Route::prefix('user')->group(function() {
    Route::post('/auth', [UserController::class, 'auth']);
    Route::post('/auth_with_google', [UserController::class, 'authWithGoogle']);

    Route::middleware('auth:sanctum')->group(function() {
        Route::patch('/edit_profile', [UserController::class, 'editProfile']);
        Route::delete('/logout', [UserController::class, 'logout']);
    });
});

Route::prefix('note')->middleware('auth:sanctum')->group(function() {
    Route::post('/add', [NoteController::class, 'addNote']);
    Route::patch('/update', [NoteController::class, 'updateNote']);
    Route::get('/get_note', [NoteController::class, 'getNote']);
    Route::get('/get_notes', [NoteController::class, 'getNotes']);
    Route::get('/get_random', [NoteController::class, 'getRandomNote']);
    Route::patch('/read', [NoteController::class, 'readNote']);
    Route::patch('/finish', [NoteController::class, 'finishNote']);
    Route::patch('/return_to_not_read', [NoteController::class, 'returnNoteToNotRead']);
});
