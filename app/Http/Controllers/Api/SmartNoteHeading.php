<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\Models\Service;
use App\Models\CompanyDetail;

class BranchController extends Controller
{
    public function lists(Request $request)
	{ 
		$requestData = $request->all();
		
        f (auth()->user() != null && isset(auth()->user()->company->company)) {
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

		return response()->json(['success' => $companies], 200);
	}
	
    public function getData(Request $request)
	{ 
		$requestData = $request->all();
		
		$result = 	Service::select('services.*',	\DB::raw("CONCAT(B.firstname, ' ', B.lastname) as doctor_name"))
					->leftjoin('users AS B','B.id','=','services.user_id')
					->where('services.id', $requestData['id'])
					->first();
		
		if($result){
			return response()->json(['success' => $result], 200);
		}else{
			return response()->json(['error' => []], 200);
		}
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
				$result 					= Service::create($requestData);
				$insertid                   = $result->id;
			}else{
				$result 					= Service::find($requestData['actionid'])->update($requestData);
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
		Service::where('id', $requestData['id'])->delete();
		return response()->json(['success' => []], 200);
	}
}
