<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Redemption;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class RedemptionController extends Controller
{
    //
    public function donationRedemptions(Request $request)
    {
        try {

            if(! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

            $redemptions = Redemption::where('donation_id', $request->donation_id)->get();

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidExceptions $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        return response()->json(compact('redemptions'), 201);
    }


    public function redeem (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'donation_id' => 'required|integer',
            'amount' => 'required|numeric|min:0|digits_between:1,12',
            'channel' => 'required|string|max:255',
            'received_by' => 'required|string|max:255',
            'received_on' => 'required|date'
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

            $updateRedeemed = Donation::find($request->donation_id);

            $redeemed = $updateRedeemed->redeemed += $request->amount;
            if($updateRedeemed->donation < $redeemed){
                return response()->json([
                    "error" => "Redemption exceeds the total donation. You can add the excess as a new donation!"
                ], 401);
            }

            $redemption = Redemption::create([
                'donation_id' => $request->get('donation_id'),
                'amount' => $request->get('amount'),
                'channel' => $request->get('channel'),
                'received_by' => $request->get('received_by') == 'self' ? $user->username : $request->get('received_by'),
                'received_on' => $request->get('received_on'),
                'created_by' => $email
            ]);

            if($redemption){             
                $updateRedeemed->redeemed = $redeemed;
                $updateRedeemed->save();
            }
            

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidExceptions $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        //$users = User::all();
        $success = 'Redemption recorded Successfully!';

        return response()->json(compact('success'), 201);
    }

}
