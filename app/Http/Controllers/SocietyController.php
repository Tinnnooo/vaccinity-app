<?php

namespace App\Http\Controllers;

use App\Society;
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
        // $request['password'] = bcrypt($request['password']);

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

        // $request->validate([
        //     'id_card_number' => 'required',
        //     'password' => 'required|string',
        // ]);

        // $society = Society::where('id_card_number', $request->id_card_number)->first();
        // if (!$society) {
        //     return response()->json(['message' => 'Invalid id_card_number or password'], 401);
        // }

        // $regional = $society->regional;


        // if ($request->password === $society->password) {
        //     $token = md5($society->id_card_number);
        //     $society->login_tokens = $token;
        //     $society->save();


        //     return response()->json([
        //         'name' => $society->name,
        //         'born_date' => $society->born_date,
        //         'gender' => $society->gender,
        //         'address' => $society->address,
        //         'token' => $token,
        //         'regional' => [
        //             'id' => $regional->id,
        //             'province' => $regional->province,
        //             'district' => $regional->district
        //         ]
        //         ], 200);
        //     } else {
        //         return response()->json(['message' => 'ID Card Number or Password incorrect'], 401);
        //     }
    }

    public function logout(Request $request)
{
    // $token = $request->input('token');
    // $society = Society::where('login_token', $token)->first();

    // if (!$society) {
    //     return response()->json(['message' => 'Token not found'], 401);
    // }

    // $society->update(['login_token' => null]);

    // return response()->json(['message' => 'Logout success']);

    $society = Auth::guard('society')->user();
    $token = $request->input('token');

    if ($society && $society->login_tokens->contains('token', $token)) {
        $society->login_tokens->where('token', $token)->delete();
        return response()->json(['message' => 'Successfully logged out']);
    }

    return response()->json(['message' => 'Unauthorized'], 401);
}

}
