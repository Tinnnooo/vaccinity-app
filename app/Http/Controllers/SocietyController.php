<?php

namespace App\Http\Controllers;

use App\Models\Society;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SocietyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {

        $credentials = $request->validate([
            'id_card_number' => 'required',
            'password' => 'required',
        ]);


        if (Auth::guard('society')->attempt($credentials)) {
            // Authentication passed...
            $society = Auth::guard('society')->user();

            $token = md5($society->id_card_number);

            $society->login_tokens = $token;
            $society->save();

            return response()->json([
                'name' => $society->name,
                'born_date' => $society->born_date,
                'gender' => $society->gender,
                'address' => $society->address,
                'token' => $token,
                'regional' => [
                    'id' => $society->regional->id,
                    'province' => $society->regional->province,
                    'district' => $society->regional->district,
                ],
            ]);
        }

        return response()->json(['message' => 'ID Card Number or Password incorrect'], 401);
    }

    public function logout(Request $request)
{
    $token = $request->input('token');

    if(!$token){
        return response()->json(['message' => 'Unauthorized user'], 401);
    }

    $society = Society::where('login_tokens', $token)->first();

    if ($society) {
        $society->login_tokens = null;
        $society->save();

        return response()->json(['message' => 'Successfully logged out']);
    }

    return response()->json(['message' => 'Unauthorized user'], 401);
}

}
