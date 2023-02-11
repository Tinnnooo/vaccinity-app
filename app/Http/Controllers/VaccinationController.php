<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Medical;
use App\Models\Society;
use App\Models\Spot;
use App\Models\Spot_vaccine;
use App\Models\Vaccination;
use App\Models\Vaccine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VaccinationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $token = $request->input('token');

        if($token){
            $society = Society::where("login_tokens", $token)->first();

            if($society){
                $firstVaccination = Vaccination::where('society_id', $society->id)->where('dose', 1)->first();
                $secondVaccination = Vaccination::where('society_id', $society->id)->where('dose', 2)->first();

                if(!$firstVaccination){
                    return response()->json([
                        'vaccinations' => [
                            'first' => null,
                            'second' => null
                        ]
                        ], 200);
                }
                $vaccine = [];
                $doctor = [];

                if(!$firstVaccination->vaccine_id && !$firstVaccination->doctor_id){
                    $vaccine['id'] = null;
                    $vaccine['name'] = null;
                    $doctor['id']= null;
                    $doctor['role']= null;
                    $doctor['name']= null;
                } else {
                    $vaccine = Vaccine::find($firstVaccination->vaccine_id);
                    $doctor = Medical::find($firstVaccination->doctor_id);
                }

                $firstSpot = Spot::find($firstVaccination->spot_id);

                $second = [];

                $secondVaccine = [];
                $secondDoctor = [];

                if($secondVaccination){
                    $secondSpot = Spot::find($secondVaccination->spot_id);

                    if(!$secondVaccination->vaccine_id && !$secondVaccination->doctor_id){
                        $secondVaccine['id'] = null;
                        $secondVaccine['name'] = null;
                        $secondDoctor['id']= null;
                        $secondDoctor['role']= null;
                        $secondDoctor['name']= null;
                    } else {
                        $secondVaccine = Vaccine::find($secondVaccination->vaccine_id);
                        $secondDoctor = Medical::find($secondVaccination->doctor_id);
                    }

                    $second = [
                        'queue' => $secondVaccination->queue,
                            'dose' => $secondVaccination->dose,
                            'vaccination_date' => $secondVaccination->date,
                            'spot' => [
                                'id' => $secondSpot->id,
                                'name' => $secondSpot->name,
                                'address' => $secondSpot->address,
                                'serve' => $secondSpot->serve,
                                'capacity' => $secondSpot->capacity,
                                'regional' => [
                                    'id' => $secondSpot->regional_id,
                                    'province' => $secondSpot->regional->province,
                                    'district' => $secondSpot->regional->district
                                ]
                                ],
                                'status' => "done",
                                'vaccine' => [
                                    'id' => $secondVaccine['id'],
                                    'name' => $secondVaccine['name']
                                ],
                                'vaccinator' => [
                                    'id' => $secondDoctor['id'],
                                    'role' => $secondDoctor['role'],
                                    'name' => $secondDoctor['name']
                                ]
                                ];
                }

                return response()->json([
                    'vaccinations' => [
                        'first' => [
                            'queue' => $firstVaccination->queue,
                            'dose' => $firstVaccination->dose,
                            'vaccination_date' => $firstVaccination->date,
                            'spot' => [
                                'id' => $firstSpot->id,
                                'name' => $firstSpot->name,
                                'address' => $firstSpot->address,
                                'serve' => $firstSpot->serve,
                                'capacity' => $firstSpot->capacity,
                                'regional' => [
                                    'id' => $firstSpot->regional_id,
                                    'province' => $firstSpot->regional->province,
                                    'district' => $firstSpot->regional->district
                                ]
                                ],
                                'status' => "done",
                                'vaccine' => [
                                    'id' => $vaccine['id'],
                                    'name' => $vaccine['name']
                                ],
                                'vaccinator' => [
                                    'id' => $doctor['id'],
                                    'role' => $doctor['role'],
                                    'name' => $doctor['name']
                                ]
                                ],
                        'second' => $second
                    ]
                            ], 200);
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
        $token = $request->input('token');

        if($token){
            $society = Society::where("login_tokens", $token)->first();

            if($society){



                // Validate request data
                $validator = Validator::make($request->all(), [
                    'spot_id' => 'required',
                    'date' => 'required|date_format:Y-m-d'
                ]);

                if($validator->fails()){
                    return response()->json([
                        "message" => "Invalid field",
                        "errors" => $validator->errors()
                    ], 401);
                }

                // Check if society consultation has been accepted by doctor
                $consultation = Consultation::where("society_id", $society->id)->first();
                if(!$consultation || $consultation->status != 'accepted'){
                    return response()->json([
                        "message" => "Your consultation must be accepted by doctor before"
                    ], 401);
                }


                // Check if it's first or second vaccine
                $vaccineCount = Vaccination::where('society_id', $society->id)->count();
                $vaccineType = $vaccineCount == 0 ? 'First' : 'Second';
                $vaccineDose = $vaccineCount == 0 ? 1 : 2;

                // Check if 30 days have passed from first vaccine
                if($vaccineCount == 1){
                    $firstVaccine = Vaccination::where('society_id', $society->id)->first();
                    $date1 = new \DateTime($firstVaccine->date);
                    $date2 = new \DateTime($request->input('date'));
                    $interval = $date1->diff($date2);
                    if($interval->days < 30){
                        return response()->json([
                            'message' => 'Wait at least +30 days from first vaccination'
                        ], 401);
                    }
                }

                // Check if society has been 2x vaccinated
                if($vaccineCount >= 2){
                    return response()->json([
                        'message' => 'Society has been 2x vaccinated'
                    ], 401);
                }

                $vaccine = Spot_vaccine::find($request->input('spot_id'));
                $doctor = Medical::where("spot_id",$request->input('spot_id'))->first();

                // Register vaccine
                $vaccination = new Vaccination;
                $vaccination->society_id = $society->id;
                $vaccination->spot_id = $request->input('spot_id');
                $vaccination->date = $request->input('date');
                $vaccination->vaccine_id = $vaccine->vaccine_id;
                $vaccination->doctor_id = $doctor->id;
                $vaccination->dose = $vaccineDose;
                $vaccination->save();

                return response()->json([
                    'message' => $vaccineType . ' vaccination registered successful'
                ], 200);

            }
        }

        return response()->json([
            "message" => "Unauthorized user"
        ], 401);
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
