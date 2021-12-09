<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\PrivatePublicController;
use App\Http\Controllers\ShareableController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();});

Route::group(['middleware' => "accessAuth"], function()
{
    Route::post('/list_images',[ListingController::class,'listing']);
    Route::post('/search_image',[ListingController::class,'searchImage']);
    Route::post('/make_private',[PrivatePublicController::class,'makePrivate']);
    Route::post('/make_public',[PrivatePublicController::class,'makePublic']);
    Route::post('/make_hidden',[PrivatePublicController::class,'makeHidden']);
    Route::post('/link',[ShareableController::class,'shareLink']);
    Route::post('/show_image',[ShareableController::class,'showLink']);
});
Route::group(['middleware' => "tokenAuth"], function()
{
    Route::post('/add_image',[ImageController::class,'addImage']);
    Route::post('/remove_image',[ImageController::class,'removeImage']);
    Route::post('/add_access',[ImageController::class,'addAccess']);
    Route::post('/remove_access',[ImageController::class,'removeAccess']);
    Route::post('/remove_all_access',[ImageController::class,'removeAllAccess']);
});
Route::any('/storage/images/{filename}',function(Request $request, $filename)
{
    $headers = ["Cache-Control" => "no-store, no-cache, must-revalidate, max-age=0"];
    $path = storage_path("app/images".'/'.$filename);
    if (file_exists($path)) 
    {
        return response()->download($path, null, $headers, null);
    }
    return response()->json(["error"=>"error downloading file"],400);
});
