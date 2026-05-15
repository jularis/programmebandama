<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Admin;
use App\Models\Section;
use App\Lib\CurlRequest;
use App\Models\Language;
use App\Models\Localite;
use App\Constants\Status;
use App\Models\UserLogin;
use App\Models\Cooperative;
use Illuminate\Http\Request;
use App\Models\LivraisonInfo;
use App\Models\RequestReport;
use App\Rules\FileTypeValidate;
use App\Models\LivraisonPayment;
use App\Models\AdminNotification;
use App\Http\Controllers\Controller;
use App\Models\Parcelle;
use App\Models\Producteur;
use App\Models\SuiviFormation;
use App\Models\SuiviParcelle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{

    public function dashboard()
    {

        $pageTitle = 'Dashboard';
        $cooperativeCount = Cooperative::count();
        $sectionCount = Section::count(); 
        $localiteCount = Localite::count();
        $producteurCount = Producteur::count(); 
        $parcelleCount = Parcelle::count();  
        $formationCount = SuiviFormation::count();  
        $suiviparcelleCount = SuiviParcelle::count();  

        $sectionByCoop = Section::joinRelationship('cooperative')->select('cooperatives.name', DB::RAW('count(sections.id) as total'))->groupby('cooperative_id')->get();
        
        $localiteByCoop = Localite::joinRelationship('section.cooperative')->select('cooperatives.name', DB::RAW('count(localites.id) as total'))->groupby('cooperative_id')->get();
        
        $producteurByCoop = Producteur::joinRelationship('localite.section.cooperative')->select('cooperatives.name', DB::RAW('count(producteurs.id) as total'))->groupby('cooperative_id')->get();

        $formationByCoop = SuiviFormation::joinRelationship('localite.section.cooperative')->select('cooperatives.name', DB::RAW('count(suivi_formations.id) as total'))->groupby('cooperative_id')->get();
         
        $cooperativeGenderChart = DB::table('cooperatives as c')
                                    ->join('sections as s', 'c.id', '=', 's.cooperative_id')
                                    ->join('localites as l', 's.id', '=', 'l.section_id')
                                    ->join('producteurs as p', 'l.id', '=', 'p.localite_id')
                                    ->select('c.name as cooperative_name', 'p.sexe as gender', DB::raw('COUNT(p.id) as number_of_producers'))
                                    ->groupBy('c.name', 'p.sexe')
                                    ->get();
 
        $parcelleGenderChart = Parcelle::joinRelationship('producteur.localite.section.cooperative')
                                    ->select('cooperatives.name as cooperative_name','producteurs.sexe as gender', DB::RAW('count(parcelles.id) as total_parcelle'))
                                    ->groupby('cooperative_id','sexe')
                                    ->get();
      
        return view('admin.dashboard', compact('pageTitle', 'cooperativeCount', 'sectionCount', 'localiteCount', 'producteurCount', 'parcelleCount','sectionByCoop','localiteByCoop','formationCount','suiviparcelleCount','producteurByCoop','formationByCoop','cooperativeGenderChart','parcelleGenderChart'));
    }

    public function profile()
    {
        $pageTitle = 'Profile';
        $admin     = auth('admin')->user();
        return view('admin.profile', compact('pageTitle', 'admin'));
    }

    public function profileUpdate(Request $request)
    {
        $this->validate($request, [
            'name'  => 'required',
            'email' => 'required|email',
            'image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);
        $user = auth('admin')->user();

        if ($request->hasFile('image')) {
            try {
                $old         = $user->image;
                $user->image = fileUploader($request->image, getFilePath('adminProfile'), getFileSize('adminProfile'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Impossible de télécharger votre image'];
                return back()->withNotify($notify);
            }
        }

        $user->name  = $request->name;
        $user->email = $request->email;
        $user->save();
        $notify[] = ['success', 'Le profil a été mis à jour avec succès'];
        return to_route('admin.profile')->withNotify($notify);
    }

    public function password()
    {
        $pageTitle = 'Paramétrage du mot de passe';
        $admin     = auth('admin')->user();
        return view('admin.password', compact('pageTitle', 'admin'));
    }

    public function passwordUpdate(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required',
            'password'     => 'required|min:5|confirmed',
        ]);

        $user = auth('admin')->user();

        if (!Hash::check($request->old_password, $user->password)) {
            $notify[] = ['error', 'Le mot de passe ne correspond pas!!'];
            return back()->withNotify($notify);
        }

        $user->password = bcrypt($request->password);
        $user->save();
        $notify[] = ['success', 'Le mot de passe a été changé avec succès.'];
        return to_route('admin.password')->withNotify($notify);
    }

    public function notifications()
    {
        $notifications = AdminNotification::orderBy('id', 'desc')->with('user')->paginate(getPaginate());
        $pageTitle     = 'Notifications';
        return view('admin.notifications', compact('pageTitle', 'notifications'));
    }

    public function notificationRead($id)
    {
        $notification              = AdminNotification::findOrFail($id);
        $notification->read_status = Status::YES;
        $notification->save();
        $url = $notification->click_url;

        if ($url == '#') {
            $url = url()->previous();
        }

        return redirect($url);
    }

    public function requestReport()
    {
        $pageTitle            = 'Your Listed Report & Request';
        // $arr['app_name']      = systemDetails()['name'];
        // $arr['app_url']       = env('APP_URL');
        // $arr['purchase_code'] = env('PURCHASE_CODE');
        // $url                  = "https://sicadevd.com/issue/get?" . http_build_query($arr);
        // $response             = CurlRequest::curlContent($url);
        // $response             = json_decode($response);

        // if ($response->status == 'error') {
        //     return to_route('admin.dashboard')->withErrors($response->message);
        // }
        // $reports = $response->message[0];

        $reports = RequestReport::get();
        return view('admin.reports', compact('reports', 'pageTitle'));
    }

    public function reportSubmit(Request $request)
    {
        $request->validate([
            'type'    => 'required|in:bug,feature',
            'message' => 'required',
        ]);
        $url = 'https://sicadevd.com/issue/add';

        $arr['app_name']      = systemDetails()['name'];
        $arr['app_url']       = env('APP_URL');
        $arr['purchase_code'] = env('PURCHASE_CODE');
        $arr['req_type']      = $request->type;
        $arr['message']       = $request->message;
        // $response             = CurlRequest::curlPostContent($url, $arr);
        // $response             = json_decode($response);
        $report  = new RequestReport();
        $report->type = $request->type;
        $report->message = $request->message;
        $report->save();   
        
        if ($report->id) {
           $admin = Admin::findOrFail(1);
           
            notify($admin, 'DEVELOPER_SUPPORT', [
                'support_subject' => $request->type,
                'support_message'    => $request->message, 
                'email'    => 'juliuskouame@gmail.com',
            ]);
        }

        $notify[] = ['success', 'Votre message a été bien envoyé.'];
        return back()->withNotify($notify);
    }

    public function readAll()
    {
        AdminNotification::where('read_status', Status::NO)->update([
            'read_status' => Status::YES,
        ]);
        $notify[] = ['success', 'Notifications lues avec succès'];
        return back()->withNotify($notify);
    }

    public function allAdmin()
    {
        $pageTitle = 'Toutes les Admins';
        $admins    = Admin::orderBy('id', 'desc')->paginate(getPaginate());
        $adminId   = auth()->guard('admin')->user()->id;
        return view('admin.all', compact('admins', 'pageTitle', 'adminId'));
    }

    public function adminStore(Request $request)
    {
        $id = $request->id ?? 0;

        $request->validate([
            'email'    => 'required|email|unique:admins,email,' . $id,
            'username' => 'required|unique:admins,username,' . $id,
            'password' => !$id ? 'required|confirmed' : 'nullable',
            "name"     => 'required'
        ]);

        if ($id) {
            if ($id == Status::SUPER_ADMIN_ID) {
                $notify[] = ['error', 'Désolé ! Vous ne pouviez pas mettre à jour l\'administrateur principal.'];
                return back()->withNotify($notify);
            }
            $adminId = auth()->guard('admin')->user()->id;
            if ($adminId != Status::SUPER_ADMIN_ID) {
                $notify[] = ['error', 'Seul l\'administrateur principal met à jour les autres administrateurs'];
                return back()->withNotify($notify);
            }
            $admin = Admin::findOrFail($id);
        } else {
            $admin = new Admin();
            $admin->password = Hash::make($request->password);
        }
        $admin->name     = $request->name;
        $admin->email    = $request->email;
        $admin->username = $request->username;
        $admin->password  = $request->password ? Hash::make($request->password) : $admin->password;
        $admin->save();
        $notify[] = ['success', 'L\'administrateur a été ajouté avec succès'];
        return back()->withNotify($notify);
    }

    public function adminRemove($id)
    {
        if ($id == Status::SUPER_ADMIN_ID) {
            $notify[] = ['error', 'Désolé ! Vous ne pouviez pas mettre à jour l\'administrateur principal.'];
            return back()->withNotify($notify);
        }

        $adminId = auth()->guard('admin')->user()->id;
        if ($adminId != Status::SUPER_ADMIN_ID) {
            $notify[] = ['error', 'Seul l\'administrateur principal met à jour les autres administrateurs'];
            return back()->withNotify($notify);
        }

        $admin = Admin::findOrFail($id);
        $admin->delete();

        $notify[] = ['success', 'L\'administrateur a été supprimé avec succès'];
        return back()->withNotify($notify);
    }

    public function downloadAttachment($fileHash)
    {
        $filePath  = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $general   = gs();
        $title     = slug($general->site_name) . '- attachments.' . $extension;
        $mimetype  = mime_content_type($filePath);
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }

    public function changeLanguage($lang = null)
    {
        $language = Language::where('code', $lang)->first();
        if (!$language) {
            $lang = 'fr';
        }

        session()->put('lang', $lang);
        return back();
    }
}
