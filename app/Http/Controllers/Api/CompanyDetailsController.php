<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\CompanyDetail;

class CompanyDetailsController extends Controller
{
    public function lists()
	{ 
		//
	}
	
    public function getData(Request $request)
	{ 
		$requestData = $request->all();
		
		$result = CompanyDetail::with('items')->where('id', $requestData['id'])->first();
		
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
			$result = new CompanyDetail;
			$result->name = $requestData['name'];
			$result->uuid = Str::uuid()->toString();
			$result->address = $requestData['address'];
			$result->email = $requestData['email'];
			$result->phone = $requestData['phone'];
			$result->industry = $requestData['industry'];
			$result->status = $requestData['status'];
			$result->from = $requestData['from'];
			$result->to = $requestData['to'];
			$result->more_info = $requestData['more_info'];
			$result->privacy_policy = $requestData['privacy_policy'];
			$result->user_id = $requestData['user_id'];
			$result->logo = $requestData['logo'];
			$result->footer_logo = $requestData['footer_logo'];
			$result->save();
			$insertid                   = $result->id;
		
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
		CompanyDetail::where('id', $requestData['id'])->delete();
		return response()->json(['success' => []], 200);
	}
	
	public function fileupload(Request $request)
	{ 
		$file = $this->filesupload($request, 'logo', 'assets/company_logo/');
		$file = $this->filesupload($request, 'footer_logo', 'assets/footer_logo/');
		return response()->json(['success' => $file], 200);
	}
}
