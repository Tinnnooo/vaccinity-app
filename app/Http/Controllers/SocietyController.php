<?php

namespace App\Http\Controllers;

use App\Models\Society;
use Illuminate\Http\Request;

class SocietyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $request->validate([
            'id_card_number' => 'required',
            'password' => 'required|string',
        ]);
    
        $society = Society::where('id_card_number', $request->id_card_number)->first();
        if (!$society) {
            return response()->json(['message' => 'Invalid id_card_number or password'], 401);
        }

        $regional = $society->regional;
        
    
        if ($request->password === $society->password) {
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
                'id' => $regional->id,
                'province' => $regional->province,
                'district' => $regional->district
            ]
            ], 200);
        } else {
            return response()->json(['message' => 'Invalid id_card_number or password'], 401);
        }
    }
    
    public function logout(Request $request)
    {
    $user = $request->user();
    if ($request->token == md5($user->id_card_number)) {
        $user->tokens()->delete();
        return response()->json(['message' => 'Successfully logged out']);
    } else {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
}

}
