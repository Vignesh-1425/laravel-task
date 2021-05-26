<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Medication;

class MedicationsController extends Controller
{
	public function lists(Request $request)
	{ 
		$requestData = $request->all();
		
		$data 		= 	$this->medications('all', $requestData);
		$datacount 	= 	$this->medications('count', $requestData);
		return response()->json(["data" => $data,"recordsTotal" => $datacount,"recordsFiltered" => $datacount], 200);
	}
	
	public function medications($type, $input)
	{ 
		$sql = 	Medication::select('medications.*',	\DB::raw("CONCAT(B.firstname, ' ', B.lastname) as doctor_name"))
				->leftjoin('users AS B','B.id','=','medications.user_id');
		
		if(isset($input['search']['value']) && $input['search']['value']!=''){
			$searchvalue = $input['search']['value'];
			
			$sql = $sql->where(function($q) use($searchvalue){
				$q->where('medications.name', 'like', '%'.$searchvalue.'%');
				$q->orWhere('medications.description', 'like', '%'.$searchvalue.'%');
			});			
		}
		
		if($type!=='count' && isset($input['start']) && isset($input['length'])){
			$sql = $sql->offset($input['start'])->limit($input['length']);
		}
		if(isset($input['order']['0']['column']) && isset($input['order']['0']['dir'])){
			$column = ['medications.id', 'medications.name', 'medications.created_at', 'B.firstname'];
			$sql = $sql->orderBy($column[$input['order']['0']['column']], $input['order']['0']['dir']);
		}
		
		if($type=='count'){
			$result = $sql->count();
		}else{
			if($type=='all') 		$result = $sql->get();
			elseif($type=='row') 	$result = $sql->first();
		}
		
		return $result;
	}
   
	
    public function getData(Request $request)
	{ 
		$requestData = $request->all();
		
		$result = 	Medication::select('medications.*',	\DB::raw("CONCAT(B.firstname, ' ', B.lastname) as doctor_name"))
					->leftjoin('users AS B','B.id','=','medications.user_id')
					->where('medications.id', $requestData['id'])
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
				$result 					= Medication::create($requestData);
				$insertid                   = $result->id;	
			}else{
				$result 					= Medication::find($requestData['actionid'])->update($requestData);
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
		Medication::where('id', $requestData['id'])->delete();
		return response()->json(['success' => []], 200);
	}
	
	public function fileupload(Request $request)
	{ 
		$file = $this->filesupload($request, 'file', '/assets/storage/excel/');
		return response()->json(['success' => $file], 200);
	}
}
