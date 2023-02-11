<?php

namespace App\Http\Controllers;

use App\Models\Society;
use App\Models\Spot;
use App\Models\Spot_vaccine;
use App\Models\Vaccination;
use App\Models\Vaccine;
use Illuminate\Http\Request;

class SpotController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $token = $request->input('token');

        if(!$token){
            return response()->json([
                "message" => "Unauthorized user"
            ], 401);
        }

        $society = Society::where("login_tokens", $token)->first();

        if($society){
            $region = $society->regional_id;
            $spots = Spot::where("regional_id", $region)->get();

            $spots_list = [];
            foreach($spots as $spot){
                $spotVaccines = Spot_vaccine::where("spot_id", $spot->id)->get();

                $available_vaccines = [];
                foreach($spotVaccines as $spotVaccine){
                    $vaccine = Vaccine::find($spotVaccine->vaccine_id);
                    $available_vaccines[$vaccine->name] = true;
                }

                $vaccines = Vaccine::all();
                $unavailableVaccines = [];
                foreach($vaccines as $vaccine){
                    if(!isset($available_vaccines[$vaccine->name])){
                        $unavailableVaccines[$vaccine->name] = false;
                    }
                }

                $spots_list[] = [
                    "id" => $spot->id,
                    "name" => $spot->name,
                    "address" => $spot->address,
                    "serve" => $spot->serve,
                    "capacity" => $spot->capacity,
                    "available_vaccines" => $available_vaccines + $unavailableVaccines
                ];
            }

            return response()->json([
                "spots" => $spots_list
            ], 200);
        }

        return response()->json([
            "message" => "Unauthorized user"
        ], 401);
    }

    public function showSpotDetail(Request $request, $spot_id){
        $token = $request->input('token');

        if($token){

            $society = Society::where("login_tokens", $token)->first();

            if($society){
                $spot = Spot::find($spot_id);
                if($spot){
                    $date = $request->input('date') ?? date('Y-m-d');
                    $vaccinations_count = Vaccination::where([
                        ['spot_id',$spot_id],
                        ['date', $date]
                    ])->count();

                    return response()->json([
                        "date" => $date,
                        "spot" => [
                            "id" => $spot->id,
                            "name" => $spot->name,
                            "address" => $spot->address,
                            "serve" => $spot->serve,
                            "capacity" => $spot->capacity,
                        ],
                        "vaccinations_count" => $vaccinations_count
                    ], 200);
                }
            }
        }

        return response()->json([
            "message" => "Unauthorized user"
        ], 401);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
