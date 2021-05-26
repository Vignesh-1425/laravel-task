<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Service;
use App\Models\CompanyDetail;
use App\Models\Branch;
use App\Models\Room;
use App\Models\User;

class BranchController extends Controller
{
    public function lists(Request $request)
	{ 
		if (auth()->user() != null && isset(auth()->user()->company->company)) {
            auth()->user()->company = auth()->user()->company->company;
        }

        // check user role
        if (auth()->user()->role_id == 5) {
            if (auth()->user()->doctor) {
                if (count(auth()->user()->doctor->companies)) {
                    $rooms = Branch::with(['rooms'])->orderBy('id')->with('companyDetail')
                        ->whereIn('user_id', auth()->user()->doctor->companies->first()->users->pluck('id'))
                        ->get();
                } else {
                    $rooms = Branch::with(['rooms'])->orderBy('id')->with('companyDetail')->get();
                }
            } else {
                $rooms = Branch::with(['rooms'])->with('companyDetail')->orderBy('id')->get();
            }
        } elseif (auth()->user()->role_id == 3) {

            if (count(auth()->user()->companies)) {
                $rooms = Branch::with(['rooms'])->orderBy('id')
                    ->where(function ($q) {
                        if (auth()->user()->role_type != 3) {
                            $q->whereIn('user_id', auth()->user()->companies->first()->users->pluck('id'));
                        } else {
                            $q->where('user_id', auth()->user()->id);
                        }
                    })->with('companyDetail')
                    ->get();
            } else {
                $rooms = Branch::with(['rooms'])->orderBy('id')->where('user_id', auth()->user()->id)->get();
            }
        } elseif (auth()->user()->role_id == 4) {
            if (count(auth()->user()->companies)) {
                $rooms = Branch::with(['rooms'])->orderBy('id')
				->with('companyDetail')
                    ->whereIn('user_id', auth()->user()->companies->first()->users->pluck('id'))
                    ->get();
            } else {
                $rooms = collect();
            }
        } elseif (auth()->user()->role_id == 6) {
            $rooms = Branch::with(['rooms'])->orderBy('id')->with('companyDetail')
                ->whereIn('user_id', auth()->user()->companies->first()->users->pluck('id'))
                ->get();
        } else {
            $rooms = Branch::with(['rooms'])->orderBy('id')->with('companyDetail')->get();
        }

		return response()->json(['success' => $rooms], 200);
	}
	public function create(Request $request)
	{
		if (auth()->user() != null && isset(auth()->user()->company->company)) {
            auth()->user()->company = auth()->user()->company->company;
        }

        if (auth()->user()->role_id == 3) {
            $companies = collect();
        } elseif (auth()->user()->role_id == 4) {
            $companies = collect();
        } elseif (auth()->user()->role_id == 6) {
            $companies = collect();
        } else {
            $companies = CompanyDetail::latest()->get();
        }

		return response()->json(['success' => $companies,], 200);
	}
	
    public function action(Request $request)
	{ 
		if($request->isMethod('post')){
			DB::beginTransaction();
			
			$requestData 				= $request->all();
			$requestData['user_id'] 	= auth()->user()->id;
			
			/*
			$validator = Validator::make($request->all(), [
                'name' => 'required',
				'code_serial_number' => 'required',
				'stock' => 'required',
				'buying_price' => 'required',
				'selling_price' => 'required',
            ]);
			
			if($validator->fails()){
                return response()->json(['error' => [$validator->errors()->toJson()]], 500);
            }
			*/
			
			if($requestData['actionid']==''){
				$result 					= Branch::create($requestData);
				$insertid                   = $result->id;
			}else{
				$result 					= Branch::find($requestData['actionid'])->update($requestData);
				$insertid                   = $requestData['actionid'];
			}
			
			if($result){
				DB::commit();
				return response()->json(['success' => ['id' => $insertid]], 200);
			}else{
				DB::rollBack();
				return response()->json(['error' => []], 500);
			}
		}
	}
	
	public function delete(Request $request)
	{ 
		$requestData = $request->all();
		Branch::where('id', $requestData['id'])->delete();
		return response()->json(['success' => []], 200);
	}
}
