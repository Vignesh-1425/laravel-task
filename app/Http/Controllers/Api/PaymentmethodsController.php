<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\Models\Paymentmethod;


class PaymentmethodsController extends Controller
{
    public function lists(Request $request)
	{ 
		if (auth()->user() != null && isset(auth()->user()->company->company)) {
            auth()->user()->company = auth()->user()->company->company;
        }

        if (auth()->user()->role_id == 3) {
            $paymentmethods = Paymentmethod::latest()->where('user_id', auth()->id())->get();
        } elseif (auth()->user()->role_id == 4) {
            if (count(auth()->user()->companies)) {
                foreach (auth()->user()->companies as $company) {
                    $paymentmethods = Paymentmethod::latest()->whereIn('user_id', $company->users->pluck('id'))->get();
                }
            } else {
                $paymentmethods = collect();
            }
        } elseif (auth()->user()->role_id == 6) {
            foreach (auth()->user()->companies as $company) {
                $paymentmethods = Paymentmethod::latest()->whereIn('user_id', $company->users->pluck('id'))->get();
            }
        } else {
            $paymentmethods = Paymentmethod::all();
        }
		
		return response()->json(['success' => $paymentmethods], 200);
	}
	
    public function getData(Request $request)
	{ 
		$requestData = $request->all();
		
		$result = 	Paymentmethod::select('paymentmethods.*',	\DB::raw("CONCAT(B.firstname, ' ', B.lastname) as doctor_name"))
					->leftjoin('users AS B','B.id','=','paymentmethods.user_id')
					->where('paymentmethods.id', $requestData['id'])
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
				$result 					= Paymentmethod::create($requestData);
				$insertid                   = $result->id;
			}else{
				$result 					= Paymentmethod::find($requestData['actionid'])->update($requestData);
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
		Paymentmethod::where('id', $requestData['id'])->delete();
		return response()->json(['success' => []], 200);
	}
}
