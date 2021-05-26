<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\Models\Register;

class RegisterController extends Controller
{
	public function lists()
	{ 		
		$result = 	Register::all();
		
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
			
			
			$validator = Validator::make($request->all(), [
                'name' => 'regex:/[a-zA-Z0-9\s]+/',
				'email' => 'required|regex:/(.+)@(.+)\.(.+)/i',
				'phone' => 'required|min:11|numeric',
				'password' =>'required|min:6|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[! $#%]).*$/|confirmed',
            ]);
			
			if($validator->fails()){
                return response()->json(['error' => [$validator->errors()->toJson()]], 500);
            }

			
			if($requestData['actionid']==''){
				$result 					= Register::create($requestData);
				$insertid                   = $result->id;
			}else{
				$result 					= Register::find($requestData['actionid'])->update($requestData);
				$insertid                   = $requestData['actionid'];
			}
			
			if($result){
				DB::commit();
				return response()->json(['message' => 'Record Fetch Successfully','success' => ['data' => $result]], 200);
			}else{
				DB::rollBack();
				return response()->json(['error' => []], 500);
			}
		}
	}
	
	public function delete(Request $request)
	{ 
		$requestData = $request->all();
		Product::where('id', $requestData['id'])->delete();
		return response()->json(['success' => []], 200);
	}
	
	public function fileupload(Request $request)
	{ 
		$fileArray = array('image' => $request);
		
		$validator = Validator::make($request->all(), [
			'image' => 'mimes:jpeg,jpg,png,gif|required|max:10000' // max 10000kb
        ]);
			
		$file = $this->filesupload($request, 'product_image', 'assets/product_image/');
		return response()->json(['success' => $file], 200);
	}
}
