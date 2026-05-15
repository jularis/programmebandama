<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Cooperative;
use App\Models\User;
use Illuminate\Http\Request;

class ApisectionController extends Controller
{
    public function getsections(Request $request)
    {
        $userid = $request->userid;
        $manager = User::where('id',$userid)->first();
        $cooperative_id = $manager->cooperative_id;
        $sections = Section::where('cooperative_id',$cooperative_id)->with('cooperative')->get();
        return response()->json($sections,201);
    }
}
