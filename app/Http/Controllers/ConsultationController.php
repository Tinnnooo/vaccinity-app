<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Medical;
use App\Models\Society;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $token = $request->input('token');

        if($token === null){
            return response()->json([
                "message" => "Unathorized user",
            ], 401);
        }

        $society = Society::where('login_tokens', $token)->first();

        $data = [
            'disease_history' => $request->input('disease_history'),
            'current_symptoms' => $request->input('current_symptoms')
        ];

        if($society){

            $consultation = new Consultation;
            $consultation->society_id = $society->id;
            $consultation->disease_history = $data['disease_history'];
            $consultation->current_symptoms = $data['current_symptoms'];
            $consultation->save();

            return response()->json([
                "message" => "Request consultation sent successful",
            ], 200);
        } else {
            return response()->json([
                "message" => "Unathorized user",
            ], 401);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $token = $request->input('token');
        if(!$token){
            return response()->json([
                "message" => "Unauthorized user"
            ], 401);
        }

        $society = Society::where("login_tokens", $token)->first();

        if($society){
            $societyId = $society->id;
            $consultations = Consultation::where("society_id", $societyId)->get();

            if($consultations){
                $data = [];
                foreach($consultations as $consultation){
                    $doctor = Medical::find($consultation->doctor_id);

                    if($doctor){
                        $data[] = [
                            "id" => $consultation->id,
                            "status" => $consultation->status,
                            "disease_history" => $consultation->disease_history,
                            "current_symptoms" => $consultation->current_symptoms,
                            "doctor_notes" => $consultation->doctor_notes,
                            "doctor" => [
                                "name" => $doctor->name,
                            ]
                            ];
                    } else {
                        $data[] = [
                            "id" => $consultation->id,
                            "status" => $consultation->status,
                            "disease_history" => $consultation->disease_history,
                            "current_symptoms" => $consultation->current_symptoms,
                            "doctor_notes" => $consultation->doctor_notes,
                            "doctor" => [
                                "name" => null,
                            ]
                            ];
                    }
                }
                return response()->json([
                    "data" => $data
                ], 200);
            } else {
                return response()->json([
                    "message" => "No consultation record found for this society",
                ], 404);
            }


        };

        return response()->json([
            "message" => "Unauthorized user"
        ], 401);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
