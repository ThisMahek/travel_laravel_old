<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('signup', [ApiController::class, 'user_signup']);
Route::post('login', [ApiController::class, 'user_signin']);
Route::post('get_otp', [ApiController::class, 'send_otp']);
Route::post('verify_otp', [ApiController::class, 'verify_otp']);
Route::post('change_password', [ApiController::class, 'change_password']);
Route::post('create_lead', [ApiController::class, 'create_lead']);
Route::get('get_requrement', [ApiController::class, 'get_requrement']);
Route::post('add_call_follow_up', [ApiController::class, 'add_call_follow_up']);
Route::post('fix_metting', [ApiController::class, 'fix_metting']);
Route::post('add_notes', [ApiController::class, 'add_notes']);
Route::post('send_quotation', [ApiController::class, 'send_quotation']);
Route::post('get_lead_by_id', [ApiController::class, 'get_lead_by_id']);
Route::get('get_enquiry_type', [ApiController::class, 'get_enquiry_type']);
Route::get('get_lead_source', [ApiController::class, 'get_lead_source']);
Route::post('get_quotation_by_id', [ApiController::class, 'get_quotation_by_id']);
Route::post('get_all_leads', [ApiController::class, 'get_all_leads']);
Route::post('delete_lead', [ApiController::class, 'delete_lead']);
Route::post('update_lead', [ApiController::class, 'update_lead']);
Route::get('get_dashboard_data_count', [ApiController::class, 'dashboard_data']);
Route::post('add_pax_details', [ApiController::class, 'add_pax']);
Route::post('assign_lead', [ApiController::class, 'assign_lead']);

