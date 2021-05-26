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

class UsersController extends Controller
{
    public function lists()
	{ 
		$user = Auth::user();
        // check user role
        if ($user != null && isset($user->company->company)) {
            $user->company = $user->company->company;
        }

        if ($user->role_id == 3) {
            $users = User::query()->where('id', $user->id)->with('companies')->get();
        } elseif ($user->role_id == 4) {
            if (count($user->companies)) {
                foreach ($user->companies as $company) {
                    $users = User::query()->whereIn('id', $company->users->pluck('id'))
                        ->where('role_id', '!=', 6)->with('companies')
                        ->get();
                }
            } else {
                $users = collect();
            }
        } elseif ($user->role_id == 6) {
            $company_ids = $user->companies()->pluck('id')->toArray();
            $user_ids = DB::table('company_detail_user')
                ->whereIn('company_detail_id', $company_ids)
                ->pluck('user_id')
                ->toArray();
            $users = User::query()->whereIn('id', $user_ids)->with('companies')
                ->where('role_id', '!=', 6)
                ->get();
        } else {
            $users = User::query()->whereNotIn('role_id', [1, 2, 5])->with('companies')
                ->get();
        }
		return response()->json(['success' => $users], 200);
	}
	
    public function getData(Request $request)
	{ 
		$requestData 				= $request->all();
		
		$users = 	User::with('creator')->where('id', $requestData['id'])->with('company')->first();

		return response()->json(['success' => $users], 200);
	}
	
	public function availability(Request $request)
	{ 
		$user = auth()->user();
		$availabilityuser = Timing::where('user_id',$user['id'])->get();
		$availability = Availability::where('user_id',$user['id'])->get();
		
        if ($user->profile_photo) {

            $profile_photo = asset('storage/' . $user->profile_photo);

        } else {
            $profile_photo = asset('img/user.png');
        }


        if (auth()->user()->role_id == 3) {
            if (count(auth()->user()->companies)) {
                foreach (auth()->user()->companies as $company) {
                    $services = Service::whereIn('user_id', $company->users->pluck('id'))
                        ->get();
                }
            } else {
                $services = collect();
            }
        } elseif (auth()->user()->role_id == 4) {
            if (count(auth()->user()->companies)) {
                foreach (auth()->user()->companies as $company) {
                    $services = Service::whereIn('user_id', $company->users->pluck('id'))
                        ->get();
                }
            } else {
                $services = collect();
            }
        } elseif (auth()->user()->role_id == 6) {
            foreach (auth()->user()->companies as $company) {
                $services = Service::whereIn('user_id', $company->users->pluck('id'))
                    ->get();
            }
        } else {
            $services = Service::all();
        }
		
		return response()->json(['data' => $availabilityuser,'services' => $services,'availability' => $availability], 200);

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
			
			if(isset($requestData['password'])){
				$password = $requestData['password'];
			}
			if($requestData['actionid']==''){
				$requestData['uuid'] 		= Str::uuid()->toString();
				$requestData['password'] 	= $password;
				$result 					= User::create($requestData);
				$company = CompanyDetailUser::create([
					'company_detail_id' => $requestData['company_id'],
					'user_id' => $requestData['user_id'],
				]);
				$insertid                   = $result->id;
			}else{
				$requestData['uuid'] 		= Str::uuid()->toString();
				$result 					= User::find($requestData['actionid'])->update($requestData);
				$company = CompanyDetailUser::where('user_id',$requestData['actionid'])->update([
					'company_id' => $requestData['company_id'],
					'user_id' => $requestData['user_id'],
				]);
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
	public function slotaction(Request $request)
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
			
			if($requestData['actionid']!=''){
				$requestData['uuid'] 		= Str::uuid()->toString();
				$result 					= User::find($requestData['actionid'])->update($requestData);
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
	public function getStaffActionData(Request $request)
	{ 
		if (auth()->user() != null && isset(auth()->user()->company->company)) {
            auth()->user()->company = auth()->user()->company->company;
        }

        if (auth()->user()->role_id == 3) {
            foreach (auth()->user()->companies as $company) {
                $doctors = User::where('role_id', '!=', 5)
                    ->where('role_id', '!=', 1)
                    ->where('role_id', '!=', 2)
                    ->where('role_id', '!=', 6)
                    ->whereIn('id', $company->users->pluck('id'))
                    ->get();
                $users = User::where(['role_id' => 5])->whereIn('user_id', $company->users->pluck('id'))->get();
                $companydetails = CompanyDetail::latest()->whereIn('user_id', $company->users->pluck('id'))->get();
				$roles = Role::where('id', 5)->get();
            }
        } elseif (auth()->user()->role_id == 4) {
            if (count(auth()->user()->companies)) {
                foreach (auth()->user()->companies as $company) {
                    $doctors = User::where('role_id', '!=', 5)
                        ->where('role_id', '!=', 1)
                        ->where('role_id', '!=', 2)
                        ->where('role_id', '!=', 6)
                        ->whereIn('id', $company->users->pluck('id'))
                        ->get();
                    $users = User::where(['role_id' => 5])->whereIn('user_id', $company->users->pluck('id'))->get();
					$companydetails = CompanyDetail::latest()->whereIn('user_id', $company->users->pluck('id'))->get();
					$roles = Role::whereBetween('id', [3, 5])->get();
                }
            } else {
                $users = collect();
                $doctors = collect();
                $charges = collect();
                $taxes = collect();
                $services = collect();
                $products = collect();
                $currencies = collect();
            }
        } elseif (auth()->user()->role_id == 6) {
            foreach (auth()->user()->companies as $company) {
                $doctors = User::where('role_id', '!=', 5)
                    ->where('role_id', '!=', 1)
                    ->where('role_id', '!=', 2)
                    ->where('role_id', '!=', 6)
                    ->whereIn('id', $company->users->pluck('id'))
                    ->get();
                $users = User::where(['role_id' => 5])->whereIn('user_id', $company->users->pluck('id'))->get();
                $companydetails = CompanyDetail::latest()->whereIn('user_id', $company->users->pluck('id'))->get();
				$roles = Role::query()->whereBetween('id', [3, 10])->where('id', '!=', 6)->get();
            } 
        } else {
            $users = User::latest()->where('role_id', 5)->get();
            $companydetails = CompanyDetail::all();
			$roles = Role::all();
        }
		
		return response()->json(['success' => [
			'users' => $users,
			'roles'  => $roles,
			'companydetails'  => $companydetails
		]], 200);
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
