<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\CompanyDetail;

class CompanyController extends Controller
{
    public function lists()
	{ 
		//
	}
	
    public function getData(Request $request)
	{ 
		$requestData = $request->all();
		
		$result = CompanyDetail::where('id', $requestData['id'])->first();
		
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
				$requestData['uuid'] 		= Str::uuid()->toString();
				$result 					= CompanyDetail::create($requestData);
				$insertid                   = $result->id;
			}else{
				$requestData['uuid'] 		= Str::uuid()->toString();
				$result 					= CompanyDetail::find($requestData['actionid'])->update($requestData);
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
