<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\Models\Timing;;
use App\Models\User;;

class TimingController extends Controller
{
    public function lists(Request $request)
	{ 
		//
	}
	
    public function getData(Request $request)
	{
		//
	}
	
    public function action(Request $request)
	{ 
		$user = User::find($request->id);
        if (count($user->timing)) {
            foreach ($user->timing as $item) {
                $services = null;

                switch ($item->day) {
                    case "1":
                        if (isset($request->status_sunday)) {
                            $status = 1;
                        } else {
                            $status = 0;
                        }

                        if (isset($request->sunday_services)) {
                            $services = implode(", ", $request->sunday_services);
                        }

                        $day = 1;
                        $from = $request->sunday_opening ? date('H:i', strtotime($request->sunday_opening)) : null;
                        $to = $request->sunday_closing ? date('H:i', strtotime($request->sunday_closing)) : null;
                        break;
                    case "2":
                        if (isset($request->status_monday)) {
                            $status = 1;
                        } else {
                            $status = 0;
                        }

                        if (isset($request->monday_services)) {
                            $services = implode(", ", $request->monday_services);
                        }

                        $day = 2;
                        $from = $request->monday_opening ? date('H:i', strtotime($request->monday_opening)) : null;
                        $to = $request->monday_closing ? date('H:i', strtotime($request->monday_closing)) : null;
                        break;
                    case "3":
                        if (isset($request->status_tuesday)) {
                            $status = 1;
                        } else {
                            $status = 0;
                        }

                        if (isset($request->tuesday_services)) {
                            $services = implode(", ", $request->tuesday_services);
                        }

                        $day = 3;
                        $from = $request->tuesday_opening ? date('H:i', strtotime($request->tuesday_opening)) : null;
                        $to = $request->tuesday_closing ? date('H:i', strtotime($request->tuesday_closing)) : null;
                        break;
                    case "4":
                        if (isset($request->status_wednesday)) {
                            $status = 1;
                        } else {
                            $status = 0;
                        }

                        if (isset($request->wednesday_services)) {
                            $services = implode(", ", $request->wednesday_services);
                        }

                        $day = 4;
                        $from = $request->wednesday_opening ? date('H:i', strtotime($request->wednesday_opening)) : null;
                        $to = $request->wednesday_closing ? date('H:i', strtotime($request->wednesday_closing)) : null;
                        break;
                    case "5":
                        if (isset($request->status_thursday)) {
                            $status = 1;
                        } else {
                            $status = 0;
                        }

                        if (isset($request->thursday_services)) {
                            $services = implode(", ", $request->thursday_services);
                        }

                        $day = 5;
                        $from = $request->thursday_opening ? date('H:i', strtotime($request->thursday_opening)) : null;
                        $to = $request->thursday_closing ? date('H:i', strtotime($request->thursday_closing)) : null;
                        break;
                    case "6":
                        if (isset($request->status_friday)) {
                            $status = 1;
                        } else {
                            $status = 0;
                        }

                        if (isset($request->friday_services)) {
                            $services = implode(", ", $request->friday_services);
                        }

                        $day = 6;
                        $from = $request->friday_opening ? date('H:i', strtotime($request->friday_opening)) : null;
                        $to = $request->friday_closing ? date('H:i', strtotime($request->friday_closing)) : null;
                        break;
                    default:
                        if (isset($request->status_saturday)) {
                            $status = 1;
                        } else {
                            $status = 0;
                        }

                        if (isset($request->saturday_services)) {
                            $services = implode(", ", $request->saturday_services);
                        }

                        $day = 7;
                        $from = $request->saturday_opening ? date('H:i', strtotime($request->saturday_opening)) : null;
                        $to = $request->saturday_closing ? date('H:i', strtotime($request->saturday_closing)) : null;
                }

                $timing = Timing::find($item->id);
                $timing->from = $from;
                $timing->to = $to;
                $timing->day = $day;
                $timing->status = $status;
                $timing->services = $services ?? null;
                $timing->user_id = $request->id;
                $timing->creator_id = auth()->id();
                $timing->save();

                // Sunday break
                if ($item->day == 1) {
                    if (isset($request->sunday_from)) {
                        foreach ($request->sunday_from as $index => $value) {
                            if ($value != null && $request->sunday_to[$index] != null) {
                                $break = new DayBreak();
                                $break->from = $value;
                                $break->to = $request->sunday_to[$index];
                                $break->timing_id = $timing->id;
                                $break->creator_id = auth()->id();
                                $break->save();
                            }
                        }
                    }

                    if (isset($request->sunday_update_from)) {
                        foreach ($request->sunday_update_from as $index => $value) {
                            if ($value != null && $request->sunday_update_to[$index] != null) {
                                $break = DayBreak::find($request->sunday_update_id[$index]);
                                $break->from = $value;
                                $break->to = $request->sunday_update_to[$index];
                                $break->timing_id = $timing->id;
                                $break->creator_id = auth()->id();
                                $break->save();
                            }
                        }
                    }
                }

                // Monday break
                if ($item->day == 2) {
                    if (isset($request->monday_from)) {
                        foreach ($request->monday_from as $index => $value) {
                            if ($value != null && $request->monday_to[$index] != null) {
                                $break = new DayBreak();
                                $break->from = $value;
                                $break->to = $request->monday_to[$index];
                                $break->timing_id = $timing->id;
                                $break->creator_id = auth()->id();
                                $break->save();
                            }
                        }
                    }

                    if (isset($request->monday_update_from)) {
                        foreach ($request->monday_update_from as $index => $value) {
                            if ($value != null && $request->monday_update_to[$index] != null) {
                                $break = DayBreak::find($request->monday_update_id[$index]);
                                $break->from = $value;
                                $break->to = $request->monday_update_to[$index];
                                $break->timing_id = $timing->id;
                                $break->creator_id = auth()->id();
                                $break->save();
                            }
                        }
                    }
                }

                // Tuesday break
                if ($item->day == 3) {
                    if (isset($request->tuesday_from)) {
                        foreach ($request->tuesday_from as $index => $value) {
                            if ($value != null && $request->tuesday_to[$index] != null) {
                                $break = new DayBreak();
                                $break->from = $value;
                                $break->to = $request->tuesday_to[$index];
                                $break->timing_id = $timing->id;
                                $break->creator_id = auth()->id();
                                $break->save();
                            }
                        }
                    }

                    if (isset($request->tuesday_update_from)) {
                        foreach ($request->tuesday_update_from as $index => $value) {
                            if ($value != null && $request->tuesday_update_to[$index] != null) {
                                $break = DayBreak::find($request->tuesday_update_id[$index]);
                                $break->from = $value;
                                $break->to = $request->tuesday_update_to[$index];
                                $break->timing_id = $timing->id;
                                $break->creator_id = auth()->id();
                                $break->save();
                            }
                        }
                    }
                }

                // Wednesday break
                if ($item->day == 4) {
                    if (isset($request->wednesday_from)) {
                        foreach ($request->wednesday_from as $index => $value) {
                            if ($value != null && $request->wednesday_to[$index] != null) {
                                $break = new DayBreak();
                                $break->from = $value;
                                $break->to = $request->wednesday_to[$index];
                                $break->timing_id = $timing->id;
                                $break->creator_id = auth()->id();
                                $break->save();
                            }
                        }
                    }

                    if (isset($request->wednesday_update_from)) {
                        foreach ($request->wednesday_update_from as $index => $value) {
                            if ($value != null && $request->wednesday_update_to[$index] != null) {
                                $break = DayBreak::find($request->wednesday_update_id[$index]);
                                $break->from = $value;
                                $break->to = $request->wednesday_update_to[$index];
                                $break->timing_id = $timing->id;
                                $break->creator_id = auth()->id();
                                $break->save();
                            }
                        }
                    }
                }

                // Thursday break
                if ($item->day == 5) {
                    if (isset($request->thursday_from)) {
                        foreach ($request->thursday_from as $index => $value) {
                            if ($value != null && $request->thursday_to[$index] != null) {
                                $break = new DayBreak();
                                $break->from = $value;
                                $break->to = $request->thursday_to[$index];
                                $break->timing_id = $timing->id;
                                $break->creator_id = auth()->id();
                                $break->save();
                            }
                        }
                    }

                    if (isset($request->thursday_update_from)) {
                        foreach ($request->thursday_update_from as $index => $value) {
                            if ($value != null && $request->thursday_update_to[$index] != null) {
                                $break = DayBreak::find($request->thursday_update_id[$index]);
                                $break->from = $value;
                                $break->to = $request->thursday_update_to[$index];
                                $break->timing_id = $timing->id;
                                $break->creator_id = auth()->id();
                                $break->save();
                            }
                        }
                    }
                }

                // Friday break
                if ($item->day == 6) {
                    if (isset($request->friday_from)) {
                        foreach ($request->friday_from as $index => $value) {
                            if ($value != null && $request->friday_to[$index] != null) {
                                $break = new DayBreak();
                                $break->from = $value;
                                $break->to = $request->friday_to[$index];
                                $break->timing_id = $timing->id;
                                $break->creator_id = auth()->id();
                                $break->save();
                            }
                        }
                    }

                    if (isset($request->friday_update_from)) {
                        foreach ($request->friday_update_from as $index => $value) {
                            if ($value != null && $request->friday_update_to[$index] != null) {
                                $break = DayBreak::find($request->friday_update_id[$index]);
                                $break->from = $value;
                                $break->to = $request->friday_update_to[$index];
                                $break->timing_id = $timing->id;
                                $break->creator_id = auth()->id();
                                $break->save();
                            }
                        }
                    }
                }

                // Saturday break
                if ($item->day == 7) {
                    if (isset($request->saturday_from)) {
                        foreach ($request->saturday_from as $index => $value) {
                            if ($value != null && $request->saturday_to[$index] != null) {
                                $break = new DayBreak();
                                $break->from = $value;
                                $break->to = $request->saturday_to[$index];
                                $break->timing_id = $timing->id;
                                $break->creator_id = auth()->id();
                                $break->save();
                            }
                        }
                    }

                    if (isset($request->saturday_update_from)) {
                        foreach ($request->saturday_update_from as $index => $value) {
                            if ($value != null && $request->saturday_update_to[$index] != null) {
                                $break = DayBreak::find($request->saturday_update_id[$index]);
                                $break->from = $value;
                                $break->to = $request->saturday_update_to[$index];
                                $break->timing_id = $timing->id;
                                $break->creator_id = auth()->id();
                                $break->save();
                            }
                        }
                    }
                }
            }
        } else {
            for ($i = 1; $i < 8; $i++) {
                $services = null;

                switch ($i) {
                    case "1":
                        if (isset($request->status_sunday)) {
                            $status = 1;
                        } else {
                            $status = 0;
                        }

                        if (isset($request->sunday_services)) {
                            $services = implode(", ", $request->sunday_services);
                        }

                        $day = 1;
                        $from = $request->sunday_opening ? date('H:i', strtotime($request->sunday_opening)) : null;
                        $to = $request->sunday_closing ? date('H:i', strtotime($request->sunday_closing)) : null;
                        break;
                    case "2":
                        if (isset($request->status_monday)) {
                            $status = 1;
                        } else {
                            $status = 0;
                        }

                        if (isset($request->monday_services)) {
                            $services = implode(", ", $request->monday_services);
                        }

                        $day = 2;
                        $from = $request->monday_opening ? date('H:i', strtotime($request->monday_opening)) : null;
                        $to = $request->monday_closing ? date('H:i', strtotime($request->monday_closing)) : null;
                        break;
                    case "3":
                        if (isset($request->status_tuesday)) {
                            $status = 1;
                        } else {
                            $status = 0;
                        }

                        if (isset($request->tuesday_services)) {
                            $services = implode(", ", $request->tuesday_services);
                        }

                        $day = 3;
                        $from = $request->tuesday_opening ? date('H:i', strtotime($request->tuesday_opening)) : null;
                        $to = $request->tuesday_closing ? date('H:i', strtotime($request->tuesday_closing)) : null;
                        break;
                    case "4":
                        if (isset($request->status_wednesday)) {
                            $status = 1;
                        } else {
                            $status = 0;
                        }

                        if (isset($request->wednesday_services)) {
                            $services = implode(", ", $request->wednesday_services);
                        }

                        $day = 4;
                        $from = $request->wednesday_opening ? date('H:i', strtotime($request->wednesday_opening)) : null;
                        $to = $request->wednesday_closing ? date('H:i', strtotime($request->wednesday_closing)) : null;
                        break;
                    case "5":
                        if (isset($request->status_thursday)) {
                            $status = 1;
                        } else {
                            $status = 0;
                        }

                        if (isset($request->thursday_services)) {
                            $services = implode(", ", $request->thursday_services);
                        }

                        $day = 5;
                        $from = $request->thursday_opening ? date('H:i', strtotime($request->thursday_opening)) : null;
                        $to = $request->thursday_closing ? date('H:i', strtotime($request->thursday_closing)) : null;
                        break;
                    case "6":
                        if (isset($request->status_friday)) {
                            $status = 1;
                        } else {
                            $status = 0;
                        }

                        if (isset($request->friday_services)) {
                            $services = implode(", ", $request->friday_services);
                        }

                        $day = 6;
                        $from = $request->friday_opening ? date('H:i', strtotime($request->friday_opening)) : null;
                        $to = $request->friday_closing ? date('H:i', strtotime($request->friday_closing)) : null;
                        break;
                    default:
                        if (isset($request->status_saturday)) {
                            $status = 1;
                        } else {
                            $status = 0;
                        }

                        if (isset($request->saturday_services)) {
                            $services = implode(", ", $request->saturday_services);
                        }

                        $day = 7;
                        $from = $request->saturday_opening ? date('H:i', strtotime($request->saturday_opening)) : null;
                        $to = $request->saturday_closing ? date('H:i', strtotime($request->saturday_closing)) : null;
                }

                $timing = new Timing();
                $timing->from = $from;
                $timing->to = $to;
                $timing->day = $day;
                $timing->status = $status;
                $timing->services = $services ?? null;
                $timing->user_id = $request->id;
                $timing->creator_id = auth()->id();
                $timing->save();

                // Sunday break
                if ($i == 1 && isset($request->sunday_from)) {
                    foreach ($request->sunday_from as $index => $value) {
                        if ($value != null && $request->sunday_to[$index] != null) {
                            $break = new DayBreak();
                            $break->from = $value;
                            $break->to = $request->sunday_to[$index];
                            $break->timing_id = $timing->id;
                            $break->creator_id = auth()->id();
                            $break->save();
                        }
                    }
                }

                // Monday break
                if ($i == 2 && isset($request->monday_from)) {
                    foreach ($request->monday_from as $index => $value) {
                        if ($value != null && $request->monday_to[$index] != null) {
                            $break = new DayBreak();
                            $break->from = $value;
                            $break->to = $request->monday_to[$index];
                            $break->timing_id = $timing->id;
                            $break->creator_id = auth()->id();
                            $break->save();
                        }
                    }
                }

                // Tuesday break
                if ($i == 3 && isset($request->tuesday_from)) {
                    foreach ($request->tuesday_from as $index => $value) {
                        if ($value != null && $request->tuesday_to[$index] != null) {
                            $break = new DayBreak();
                            $break->from = $value;
                            $break->to = $request->tuesday_to[$index];
                            $break->timing_id = $timing->id;
                            $break->creator_id = auth()->id();
                            $break->save();
                        }
                    }
                }

                // Wednesday break
                if ($i == 4 && isset($request->wednesday_from)) {
                    foreach ($request->wednesday_from as $index => $value) {
                        if ($value != null && $request->wednesday_to[$index] != null) {
                            $break = new DayBreak();
                            $break->from = $value;
                            $break->to = $request->wednesday_to[$index];
                            $break->timing_id = $timing->id;
                            $break->creator_id = auth()->id();
                            $break->save();
                        }
                    }
                }

                // Thursday break
                if ($i == 5 && isset($request->thursday_from)) {
                    foreach ($request->thursday_from as $index => $value) {
                        if ($value != null && $request->thursday_to[$index] != null) {
                            $break = new DayBreak();
                            $break->from = $value;
                            $break->to = $request->thursday_to[$index];
                            $break->timing_id = $timing->id;
                            $break->creator_id = auth()->id();
                            $break->save();
                        }
                    }
                }

                // Friday break
                if ($i == 6 && isset($request->friday_from)) {
                    foreach ($request->friday_from as $index => $value) {
                        if ($value != null && $request->friday_to[$index] != null) {
                            $break = new DayBreak();
                            $break->from = $value;
                            $break->to = $request->friday_to[$index];
                            $break->timing_id = $timing->id;
                            $break->creator_id = auth()->id();
                            $break->save();
                        }
                    }
                }

                // Saturday break
                if ($i == 7 && isset($request->saturday_from)) {
                    foreach ($request->saturday_from as $index => $value) {
                        if ($value != null && $request->saturday_to[$index] != null) {
                            $break = new DayBreak();
                            $break->from = $value;
                            $break->to = $request->saturday_to[$index];
                            $break->timing_id = $timing->id;
                            $break->creator_id = auth()->id();
                            $break->save();
                        }
                    }
                }
            }
        }

	}
	
	public function delete(Request $request)
	{ 
		$requestData = $request->all();
		Charge::where('id', $requestData['id'])->delete();
		return response()->json(['success' => []], 200);
	}
}
