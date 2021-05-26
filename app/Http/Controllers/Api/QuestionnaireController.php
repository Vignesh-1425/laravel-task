<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\Models\Questionnaire;
use App\Models\QuestionnaireAnswer;
use App\Models\QuestionnaireQuestion;

class QuestionnaireController extends Controller
{
    public function lists(Request $request)
	{ 
		$requestData = $request->all();
		
		$questionnaires = Questionnaire::all();
		
		return response()->json(['success' => $questionnaires], 200);
	}
	
    public function getData(Request $request)
	{ 
		$requestData = $request->all();
		
		$result = Questionnaire::with('questions')->find($request->id);
		
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
				$result 					= Questionnaire::create($requestData);
				$insertid                   = $result->id;
			}else{
				$result 					= Questionnaire::find($requestData['actionid'])->update($requestData);
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
		Questionnaire::where('id', $requestData['id'])->delete();
		return response()->json(['success' => []], 200);
	}
}
