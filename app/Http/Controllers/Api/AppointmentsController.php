<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\Models\Task;
use App\Models\User;
use App\Models\CompanyDetail;
use App\Models\Appointment;

class AppointmentsController extends Controller
{
    public function lists(Request $request)
	{ 
		$patient_id = $request->patient_id ?? null;

        if (auth()->user() != null && isset(auth()->user()->company->company)) {
            auth()->user()->company = auth()->user()->company->company;
        }

        if (auth()->user()->role_id == 5) {
            $appointment = auth()->user()->appointment;
        } elseif (auth()->user()->role_id == 3) {
            $appointment = Task::where('doctor_id', auth()->id())->get();
        } elseif (auth()->user()->role_id == 4) {
            if (count(auth()->user()->companies)) {
                foreach (auth()->user()->companies as $company) {
                    $appointment = Appointment::where('doctor_id', $company->users->pluck('id'))->get();
                }
            } else {
                $appointment = collect();
            }
        } elseif (auth()->user()->role_id == 6) {
            foreach (auth()->user()->companies as $company) {
                $appointment = Appointment::where('doctor_id', $company->users->pluck('id'))->get();
            }
        } else {
            $appointment = User::with('appointment')->get()->map->appointment->collapse();
        }
		
		foreach($appointment as $key => $task){
			$appointment[$key]['patientassigned'] = User::find($task->user_id)->firstname.' '.User::find($task->user_id)->lastname;
			$appointment[$key]['staffassigned'] 	= User::find($task->doctor_id)->firstname;
		}
		
		return response()->json(['data' => $appointment], 200);
	}
	
}
