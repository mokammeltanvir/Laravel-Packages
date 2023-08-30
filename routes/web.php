<?php

use App\Models\User;
use Illuminate\Support\Str;
use App\DataTables\UsersDataTable;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Permission;
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
// shopping cart
Route::get('shop', [CartController::class, 'shop'])->name('shop');
Route::get('cart', [CartController::class, 'cart'])->name('cart');
Route::get('add-to-cart/{product_id}', [CartController::class, 'addToCart'])->name('add-to-cart');

Route::get('qty-increment/{rowId}', [CartController::class, 'qtyIncrement'])->name('qty-increment');
Route::get('qty-decrement/{rowId}', [CartController::class, 'qtyDecrement'])->name('qty-decrement');
Route::get('remove-product/{rowId}', [CartController::class, 'removeProduct'])->name('remove-product');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Permission Roles
Route::get('create-role', function () {
    // $role = Role::create(['name' => 'publisher']);
    // return $role;
    // $permission = Permission::create(['name' => 'edit articles']);
    // return $permission;

    $user = auth()->user();
    // $user->assignRole('writer');
    $user->givePermissionTo('edit articles');


    return $user;

});

// Socialite login route
Route::get('/auth/redirect', function () {
    return Socialite::driver('github')->redirect();
})->name('login.github');
 
Route::get('/auth/callback', function () {
    $user = Socialite::driver('github')->user();

    $user = User::firstOrCreate([
        'email' => $user->email
    ], [
        'name' => $user->name,
        'password' => bcrypt(Str::random(24))
    ]);

    Auth::login($user, true);
    return redirect()->route('dashboard');
 
    // dd($user);
    // $user->token
});


require __DIR__.'/auth.php';
