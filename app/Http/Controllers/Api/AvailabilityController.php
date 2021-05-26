<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Service;
use App\Models\Role;
use App\Models\Room;
use App\Models\Charge;
use App\Models\Tax;
use App\Models\Product;
use App\Models\Currency;
use App\Models\Timing;
use App\Models\CompanyDetail;
use App\Models\CompanyDetailUser;
use Illuminate\Support\Facades\Auth;
use App\Models\Availability;

class AvailabilityController extends Controller
{
    public function lists()
	{ 
		
	}
	
    public function action(Request $request)
	{
		
        $availability = new Availability();

        if ($request->type) {
            $availability->from_time = date('H:i', strtotime($request->from_time));
            $availability->to_time = date('H:i', strtotime($request->to_time));
        } else {
            $availability->to = $request->to;
            $availability->from_time = date('H:i', strtotime($request->from_time));
            $availability->to_time = date('H:i', strtotime($request->to_time));
        }

        $availability->from = $request->from;
        $availability->type = $request->type;
        $availability->user_id = auth()->user()->id;
        $availability->creator_id = auth()->id();
        $availability->save();
	}
	public function getData(Request $request)
	{ 
		$requestData 				= $request->all();
		
		$users = 	Availability::where('id', $requestData['id'])->first();

		return response()->json(['success' => $users], 200);
	}
	
	public function delete(Request $request)
	{ 
		$requestData = $request->all();
		Availability::where('id', $requestData['id'])->delete();
		return response()->json(['success' => []], 200);
	}
}
