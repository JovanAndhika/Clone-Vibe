<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\User\PlaylistController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

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

// Before Login
Route::group(['middleware' => 'guest'], function () {
    // home
    Route::get('/', function () {
        return view('opening', [
            'title' => 'opening',
        ]);
    })->name('home');

    // register
    Route::get('/register', [RegisterController::class, 'index'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

    // login
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.store');
});

// After Login
Route::group(['middleware' => 'auth'], function () {
    // role user
    Route::group(['middleware' => 'user', 'prefix' => 'user', 'as' => 'user.'], function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/discover', [UserController::class, 'search'])->name('search');
        Route::get('/nowPlaying', [UserController::class, 'nowPlaying'])->name('nowPlaying');

        // History
        Route::get('/history', [UserController::class, 'history'])->name('history');
        // Route hapus satu item history
        Route::delete('/history/{history}', [UserController::class, 'destroyHistory'])->name('destroyHistory');
        // Route hapus semua history
        Route::delete('/history-clear', [UserController::class, 'clearHistory'])->name('clearHistory');

        // Search
        // JAZZ
        Route::post('/search/jazz', [UserController::class, 'jazz'])->name('jazz');
        // POP
        Route::post('/search/pop', [UserController::class, 'pop'])->name('pop');
        // DANGDUT
        Route::post('/search/dangdut', [UserController::class, 'dangdut'])->name('dangdut');
        // KPOP
        Route::post('/search/kpop', [UserController::class, 'kpop'])->name('kpop');
        // ROCK
        Route::post('/search/rock', [UserController::class, 'rock'])->name('rock');
        // CLASSICAL
        Route::post('/search/classical', [UserController::class, 'classical'])->name('classical');
        // DANCE
        Route::post('/search/dance', [UserController::class, 'dance'])->name('dance');
        // PONK
        Route::post('/search/ponk', [UserController::class, 'ponk'])->name('ponk');
        // NEW GENRE
        Route::post('/search/{newgenre}', [UserController::class, 'newgenre'])->name('newgenre');

        // Liked Songs
        Route::get('/likedsongs', [UserController::class, 'like'])->name('like');
        Route::post('/addlike/{music}', [UserController::class, 'addlike'])->name('addlike');
        Route::delete('deletelike/{music_playlist}', [UserController::class, 'deletelike'])->name('deletelike');

        // History
        // simpan history, jika perlu akses, harus menggunakan ajax
        Route::post('/history/{music_id}', [UserController::class, 'storeHistory'])->name('history.store');

        // Playlist
        Route::get('/musics', [PlaylistController::class, 'getAllMusics'])->name('musics');
        Route::get('/bookmark', [PlaylistController::class, 'getAllPlaylist'])->name('playlists');
        Route::post('/bookmark', [PlaylistController::class, 'updateMusic'])->name('bookmark.store');
        Route::resource('playlists', PlaylistController::class);
    });

    // role admin
    Route::group(['middleware' => 'admin', 'prefix' => 'admin', 'as' => 'admin.'], function () {
        // CRUD
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::get('/addsong', [AdminController::class, 'add_song'])->name('add');
        Route::post('/store', [AdminController::class, 'store_song'])->name('store');
        Route::get('/editsong/{music}', [AdminController::class, 'edit_song'])->name('edit');
        Route::put('/update/{music}', [AdminController::class, 'update_song'])->name('update');
        Route::delete('/destroy/{music}', [AdminController::class, 'destroy_song'])->name('destroy');
        // VIEW USER
        Route::get('/viewuser', [AdminController::class, 'view_user'])->name('viewuser');
        Route::get('/viewadmin', [AdminController::class, 'view_admin'])->name('viewadmin');
        // DEACTIVATE
        Route::post('/deactivateuser/{user}', [AdminController::class, 'deactivate_user'])->name('deactivateuser');
        Route::post('/deactivateadmin/{user}', [AdminController::class, 'deactivate_admin'])->name('deactivateadmin');
        // REACTIVATE
        Route::post('/reactivateuser/{user}', [AdminController::class, 'reactivate_user'])->name('reactivateuser');
        Route::post('/reactivateadmin/{user}', [AdminController::class, 'reactivate_admin'])->name('reactivateadmin');
        // DISCOVER LAGU
        Route::get('/discover', [AdminController::class, 'discover'])->name('discover');
        Route::get('/editdiscover/{music}', [AdminController::class, 'edit_discover'])->name('editdiscover');
        Route::put('/updatediscover/{music}', [AdminController::class, 'update_discover'])->name('updatediscover');

        // ADD DISCOVERY
        Route::get('/adddiscovery', [AdminController::class, 'adddiscovery'])->name('adddiscovery');
        Route::post('/store_adddiscovery', [AdminController::class, 'store_adddiscovery'])->name('store_adddiscovery');
        Route::get('/edit_adddiscovery/{discovery}', [AdminController::class, 'edit_adddiscovery'])->name('edit_adddiscovery');
        Route::put('/update_adddiscovery/{discovery}', [AdminController::class, 'update_adddiscovery'])->name('update_adddiscovery');
        Route::delete('/destroy_adddiscovery/{discovery}', [AdminController::class, 'destroy_adddiscovery'])->name('destroy_adddiscovery');

        // ADD GENRE
        Route::post('/store_newgenre', [AdminController::class, 'store_newgenre'])->name('store_newgenre');
        Route::get('/edit_newgenre/{newgenre}', [AdminController::class, 'edit_newgenre'])->name('edit_newgenre');
        Route::put('/update_newgenre/{newgenre}', [AdminController::class, 'update_newgenre'])->name('update_newgenre');
        Route::delete('/destroy_newgenre/{newgenre}', [AdminController::class, 'destroy_newgenre'])->name('destroy_newgenre');
    });

    // logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
