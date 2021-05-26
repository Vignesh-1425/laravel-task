<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\Models\User;
use App\Models\Template;

class TemplateController extends Controller
{
    public function lists(Request $request)
	{ 
		if (auth()->user() != null && isset(auth()->user()->company->company)) {
            auth()->user()->company = auth()->user()->company->company;
        }
        $root_user_ids = User::whereIn('role_id', [1, 2])->pluck('id');
        // check user role
        if (auth()->user()->role_id == 5) {
            if (auth()->user()->doctor) {
                if (count(auth()->user()->doctor->companies)) {
                    $templates = Template::orderBy('id')
                        ->whereIn('user_id', auth()->user()->doctor->companies->first()->users->pluck('id'))
                        ->orWhereIn('user_id', $root_user_ids)->with('sections')->with('questions')
                        ->get();
                } else {
                    $templates = Template::orderBy('id') ->orWhereIn('user_id', $root_user_ids)->get();
                }
            } else {
                $templates = Template::orderBy('id') ->orWhereIn('user_id', $root_user_ids)->get();
            }
        } elseif (auth()->user()->role_id == 3) {
            if (count(auth()->user()->companies)) {
                $templates = Template::orderBy('id')->with('sections')->with('questions')
                    ->whereIn('user_id', auth()->user()->companies->first()->users->pluck('id'))
//                    ->where(function ($q) {
//                        if (auth()->user()->role_type != 3) {
//                            $q->whereIn('user_id', auth()->user()->companies->first()->users->pluck('id'));
//                        } else {
//                            $q->where('user_id', auth()->user()->id);
//                        }
//                    })
                    ->orWhereIn('user_id', $root_user_ids)->get();
            } else {
                $templates = Template::orderBy('id')->where('user_id', auth()->user()->id)->orWhereIn('user_id', $root_user_ids)->get();
            }
        } elseif (auth()->user()->role_id == 4) {
            if (count(auth()->user()->companies)) {
                $templates = Template::orderBy('id')
                    ->whereIn('user_id', auth()->user()->companies->first()->users->pluck('id'))
                    ->orWhereIn('user_id', $root_user_ids)->with('sections')->with('questions')
                    ->get();
            } else {
                $templates = collect();
            }
        } elseif (auth()->user()->role_id == 6) {
            $templates = Template::orderBy('id')
                ->whereIn('user_id', auth()->user()->companies->first()->users->pluck('id'))
                ->orWhereIn('user_id', $root_user_ids)->with('sections')->with('questions')
                ->get();
        } else {
            $templates = Template::orderBy('id')->with('sections')->with('questions')->get();
        }
		
		return response()->json(['success' => $templates], 200);
	}
	
    public function getData(Request $request)
	{ 
		$requestData = $request->all();
		
		$result = Template::with('sections.questions.answers')->find($request->id);
		
		if($result){
			return response()->json(['success' => $result], 200);
		}else{
			return response()->json(['error' => []], 200);
		}
	}
	
    public function action(Request $request)
	{ 
		if($request->isMethod('post')){
				

			// Create a new Template model instance assign form values & save to DB
			$template = new Template();
			$template->title = $request->title;
			$template->print_title = (isset($request->print_title)) ? $request->print_title : $request->title;
			$template->is_show_patients_address = (isset($request->is_show_patients_address)) ? true : false;
			$template->is_show_patients_dob = (isset($request->is_show_patients_dob)) ? true : false;
			$template->is_show_patients_nhs_number = (isset($request->is_show_patients_nhs_number)) ? true : false;
			$template->is_show_patients_referral_source = (isset($request->is_show_patients_referral_source)) ? true : false;
			$template->is_show_patients_occupation = (isset($request->is_show_patients_occupation)) ? true : false;
			$template->user_id = auth()->id();
			$template->save();

			// Check if there is any section
			if (isset($request->sections_title) && !empty($request->sections_title)) {
				// Loop over each section
				foreach ($request->sections_title as $index => $sectionsTitle) {
					// Create a new Section model instance assign form values & save to DB
					$section = new Section();
					$section->title = $sectionsTitle;
					$section->template_id = $template->id;
					$section->save();

					// Check if section has question
					if (isset($request->question_title[$index]) && !empty($request->question_title[$index])) {
						// Loop over each question under this section
						foreach ($request->question_title[$index] as $i => $questionTitle) {
							// Create a new Question model instance assign form values & save to DB
							$question = new Question();
							$question->title = $questionTitle;
							$question->type = $request->type[$index][$i];
							$question->section_id = $section->id;
							$question->template_id = $template->id;
							$question->save();

							// Check if question has answer
							if (isset($request->answer[$index][$i]) && !empty($request->answer[$index][$i])) {
								// Loop over each answer under this question
								foreach ($request->answer[$index][$i] as $key => $ans) {
									if ($ans) {
										// Create a new Answer model instance assign form values & save to DB
										$answer = new Answer();
										$answer->answer = $ans;
										$answer->question_id = $question->id;
										$answer->section_id = $section->id;
										$answer->template_id = $template->id;
										$answer->save();
									}
								}
							}
						}
					}
				}
			}
			return response()->json(['success' => ['id' => $template->id]], 200);
		}
	}
	
	public function delete(Request $request)
	{ 
		$requestData = $request->all();
		Template::where('id', $requestData['id'])->delete();
		return response()->json(['success' => []], 200);
	}
}
