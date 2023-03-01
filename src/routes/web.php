<?php

use App\Http\Controllers\Api\AutodeskForge;
use Illuminate\Support\Facades\Route;

Route::get('test', function (){
   dd('hello');
});
Route::get('get-token', [AutodeskForge::class, 'getToken'])->middleware('api');

Route::get('get-bucket', [AutodeskForge::class, 'getBucket']);
Route::get('delete-bucket', [AutodeskForge::class, 'deleteBucket']);
