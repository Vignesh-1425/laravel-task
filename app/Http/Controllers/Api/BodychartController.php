<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\Models\Bodychart;

class BodychartController extends Controller
{
    public function lists()
	{ 
		$bodycharts = Bodychart::all();
		return response()->json(['success' => $bodycharts], 200);
	}
	
    public function action(Request $request)
	{ 
		if($request->isMethod('post')){
			DB::beginTransaction();
			
			$requestData 				= $request->all();
			
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
				$result 					= Bodychart::create($requestData);
				$insertid                   = $result->id;	
			}else{
				$result 					= Bodychart::find($requestData['actionid'])->update($requestData);
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
	
	public function getData(Request $request)
	{ 
		$requestData = $request->all();
		
		$data = Bodychart::where('id', $requestData['id'])->first();
		return response()->json(['success' => $data], 200);
	}
	
	public function delete(Request $request)
	{ 
		$requestData = $request->all();
		Bodychart::where('id', $requestData['id'])->delete();
		return response()->json(['success' => []], 200);
	}
	
	public function fileupload(Request $request)
	{ 
		$file = $this->filesupload($request, 'link', 'assets/bodycharts/');
		return response()->json(['success' => $file], 200);
	}
}
