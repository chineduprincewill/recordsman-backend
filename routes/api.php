<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\SubgroupController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\RedemptionController;
use App\Http\Controllers\NewPasswordController;

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
    return $request->user();
});

// AUTHENTICATION ROUTES
Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'authenticate']);
Route::post('forgot-password', [NewPasswordController::class, 'forgotPassword']);
Route::post('reset-password', [NewPasswordController::class, 'resetPassword']);


Route::get('all-events', [EventController::class, 'getAllEvents']);
Route::get('list-branches', [GroupController::class, 'listBranches']);
Route::get('list-groups', [GroupController::class, 'listGroups']);

Route::group(['middleware' => ['jwt.verify']], function() {
    // USER ROUTES
    Route::get('user', [UserController::class, 'getAuthenticatedUser']);
    Route::post('create-user', [UserController::class, 'createUser']);
    Route::get('users', [UserController::class, 'allUsers']);
    Route::post('delete-user', [UserController::class, 'deleteUser']);
    Route::post('activate', [UserController::class, 'activate']);
    Route::post('update-user', [UserController::class, 'updateUser']);
    Route::post('update-password', [UserController::class, 'updatePassword']);

    // GROUP ROUTES
    Route::get('groups', [GroupController::class, 'allGroups']);
    Route::post('create-group', [GroupController::class, 'createGroup']);
    Route::post('update-group', [GroupController::class, 'updateGroup']);
    Route::post('delete-group', [GroupController::class, 'deleteGroup']);

    // MEMBER ROUTES
    Route::get('members', [MemberController::class, 'allMembers']);
    Route::get('get-members', [MemberController::class, 'getMembers']);
    Route::post('create-member', [MemberController::class, 'createMember']);
    Route::post('update-member', [MemberController::class, 'updateMember']);
    Route::post('delete-member', [MemberController::class, 'deleteMember']);

    // EVENT ROUTE
    Route::get('events', [EventController::class, 'allEvents']);
    Route::post('create-event', [EventController::class, 'createEvent']);
    Route::post('update-event', [EventController::class, 'updateEvent']);
    Route::post('delete-event', [EventController::class, 'deleteEvent']);

    // SUBGROUP ROUTES
    Route::get('group-subgroups', [SubgroupController::class, 'groupSubgroups']);
    Route::post('create-subgroup', [SubgroupController::class, 'createSubgroup']);

    // DONATION ROUTES
    Route::get('group-donations', [DonationController::class, 'groupDonations']);
    Route::post('create-donation', [DonationController::class, 'createDonation']);
    Route::post('filter-donations', [DonationController::class, 'filterDonations']);

    // REDEMPTION ROUTES
    Route::post('donation-redemptions', [RedemptionController::class, 'donationRedemptions']);
    Route::post('redeem-donation', [RedemptionController::class, 'redeem']);
});

