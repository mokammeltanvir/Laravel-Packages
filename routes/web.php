<?php

use App\Models\User;
use App\DataTables\UsersDataTable;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use Intervention\Image\ImageManagerStatic as Image;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('user/{id}/edit', function ($id) {
    $user = User::findOrFail($id);
    return view('user.edit', compact('user'));
})->name('user.edit');
Route::get('user/{id}/destroy', function ($id) {
    $user = User::findOrFail($id);
    $user->delete();
    return redirect()->route('dashboard');
})->name('user.destroy');

Route::get('/dashboard', function (UsersDataTable $dataTable) {
    return $dataTable->render('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('image', function () {
    $img = Image::make('https://picsum.photos/200/300');
    $img->filter(new \App\Helpers\ImageFilter(80));

    return $img->response('jpg');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
