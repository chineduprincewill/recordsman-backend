<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Subgroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;


class SubgroupController extends Controller
{
    //
    public function groupSubgroups(Request $request)
    {
        try {

            if(! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

            $subgroups = Subgroup::where('groupid', $user->groupid)->where('status', 1)->get();

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidExceptions $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        return response()->json(compact('subgroups'), 201);
    }


    public function createSubgroup (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        try {

            if(! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

            $usertype = $user->role;
            $email = $user->email;

            if($usertype !== 'admin'){
                return response()->json([
                    "error" => "You do not have the permission to access this resource!"
                ], 401);
            }

            // check if subgroup already exists
            $check = Subgroup::where('title', $request->title)->where('groupid', $user->groupid)->first();

            if($check){
                return response()->json([
                    "error" => "Subgroup already exists!"
                ], 401);
            }

            $event = Subgroup::create([
                'groupid' => $user->groupid,
                'title' => $request->get('title'),
                'created_by' => $email,
                'status' => 1,
            ]);

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidExceptions $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        //$users = User::all();
        $success = 'Subgroup Created Successfully!';

        return response()->json(compact('success'), 201);
    }
}
