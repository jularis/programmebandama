<?php

namespace App\Http\Controllers\Manager;

use App\Models\Instance;
use App\Models\LeaveType;
use App\Http\Helpers\Reply;
use App\Models\Cooperative;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\EmployeeShift;
use App\Models\CustomFieldGroup;
use App\Models\AttendanceSetting;
use App\Models\CooperativeDocument;
use App\Models\CooperativeInstance;
use App\Http\Controllers\Controller;
use App\Models\GoogleCalendarModule;
use App\Models\DocumentAdministratif;

class CooperativeSettingController extends Controller
{


    public function index()
    {

        $pageTitle = "Paramètre de la coopérative";
        $cooperative  = Cooperative::where('id', auth()->user()->cooperative_id)->first();
        // $documents = DocumentAdministratif::get();
        // $instances = Instance::get();
        $dataDocument = $dataInstance = array();
        $documentListe = CooperativeDocument::where('cooperative_id', auth()->user()->cooperative_id)->get();
        if ($documentListe->count()) {
            foreach ($documentListe as $data) {
                $dataDocument[] = $data->document_administratif_id;
            }
        }
        $instanceListe = CooperativeInstance::where('cooperative_id', auth()->user()->cooperative_id)->get();
        if ($instanceListe->count()) {
            foreach ($instanceListe as $data) {
                $dataInstance[] = $data->instance_id;
            }
        }
        $activeSettingMenu = 'cooperative_settings';

        return view('manager.cooperative-settings.index', compact('pageTitle', 'cooperative', 'activeSettingMenu', 'dataDocument', 'dataInstance'));
    }

    public function update(Request $request)
    {

        $request->validate([
            'name'    => 'required|max:40',
            'email'   => 'required|email|max:40',
            'phone'   => 'required|max:40',
            'address' => 'required|max:255',
            'web' => 'required|max:255',
            'mobile' => 'required|max:255',
        ]);

        if ($request->id) {
            $cooperative  = Cooperative::find($request->id);
            $message = "La coopérative a été mise à jour avec succès";

            $cooperative->codeCoop    = $request->codeCoop;
            $cooperative->name    = $request->name;
            $cooperative->email   = $request->email;
            $cooperative->phone   = $request->phone;
            $cooperative->address = $request->address;
            $cooperative->web = $request->web;
            $cooperative->mobile = $request->mobile;
            $cooperative->statut_juridique = isset($request->statut_juridique) ? $request->statut_juridique : '';
            $cooperative->annee_creation = isset($request->annee_creation) ? $request->annee_creation : '';
            $cooperative->code_ccc = isset($request->code_ccc) ? $request->code_ccc : '';
            $cooperative->nb_membres_creation = isset($request->nb_membres_creation) ? $request->nb_membres_creation : '';
            $cooperative->nb_sections_creation = isset($request->nb_sections_creation) ? $request->nb_sections_creation : '';
            $cooperative->nb_membres_actuel = isset($request->nb_membres_actuel) ? $request->nb_membres_actuel : '';
            $cooperative->nb_sections_actuel = isset($request->nb_sections_actuel) ? $request->nb_sections_actuel : '';
            $cooperative->nb_pca_creation = isset($request->nb_pca_creation) ? $request->nb_pca_creation : '';
            $cooperative->codeApp   = isset($request->codeApp) ? $request->codeApp : $this->generecodeapp($request->name);
            $cooperative->region  = isset($request->region) ? $request->region : '';
            $cooperative->departement  = isset($request->departement) ? $request->departement : '';
            $cooperative->ville  = isset($request->ville) ? $request->ville : '';
            $cooperative->dateOHADA  = isset($request->dateOHADA) ? $request->dateOHADA : '';
            $cooperative->postal = isset($request->postal) ? $request->postal : '';
            $cooperative->numCompteContribuable = isset($request->numCompteContribuable) ? $request->numCompteContribuable : '';
            $cooperative->numRSC = isset($request->numRSC) ? $request->numRSC : '';
            $cooperative->secteurActivite = isset($request->secteurActivite) ? $request->secteurActivite : '';
            $cooperative->historique = isset($request->historique) ? $request->historique : '';
            $cooperative->mission = isset($request->mission) ? $request->mission : '';
            $cooperative->vision = isset($request->vision) ? $request->vision : '';
            $cooperative->save();
            // if ($cooperative != null) {
            //     $id = $cooperative->id;
            //     if (($request->document != null)) {
            //         CooperativeDocument::where('cooperative_id', $id)->delete();
            //         $i = 0;
            //         foreach ($request->document as $data) {
            //             if ($data != null) {
            //                 $datas[] = [
            //                     'cooperative_id' => $id,
            //                     'document_administratif_id' => $data,
            //                 ];
            //             }
            //             $i++;
            //         }
            //         CooperativeDocument::insert($datas);
            //     }
            //     if (($request->instance != null)) {
            //         CooperativeInstance::where('cooperative_id', $id)->delete();
            //         $i = 0;
            //         foreach ($request->instance as $data) {
            //             if ($data != null) {
            //                 $datas2[] = [
            //                     'cooperative_id' => $id,
            //                     'instance_id' => $data,
            //                 ];
            //             }
            //             $i++;
            //         }
            //         CooperativeInstance::insert($datas2);
            //     }
            // }
            return Reply::success(__('messages.updateSuccess'));
        } else {
            return Reply::success(__('messages.updateError'));
        }
    }

    private function generecodeapp($name)
    {

        $data = Cooperative::select('codeApp')->orderby('id', 'desc')->limit(1)->get();

        if (count($data) > 0) {
            $code = $data[0]->codeApp;

            $chaine_number = Str::afterLast($code, '-');

            if ($chaine_number < 10) {
                $zero = "00";
            } else if ($chaine_number < 100) {
                $zero = "0";
            } else {
                $zero = "";
            }
        } else {
            $zero = "00";
            $chaine_number = 0;
        }


        $abrege = Str::upper(Str::substr($name, 0, 3));
        $sub = $abrege . '-';
        $lastCode = $chaine_number + 1;
        $codeP = $sub . $zero . $lastCode;

        return $codeP;
    }

    public function status($id)
    {
        return Cooperative::changeStatus($id);
    }
    public function customFieldGroup($cooperative)
    {

        $fields = CustomFieldGroup::ALL_FIELDS;

        array_walk($fields, function (&$a) use ($cooperative) {
            $a['cooperative_id'] = $cooperative->id;
        });

        CustomFieldGroup::insert($fields);
    }
    public function cooperativeAddress($cooperative)
    {
        $cooperative->cooperativeAddress()->create([
            'address' => $cooperative->address ?? $cooperative->name,
            'location' => $cooperative->name ?? 'CCB',
            'is_default' => 1,
            'cooperative_id' => $cooperative->id,
        ]);
    }
    public function googleCalendar($cooperative): void
    {
        $module = new GoogleCalendarModule();
        $module->cooperative_id = $cooperative->id;
        $module->lead_status = 0;
        $module->leave_status = 0;
        $module->invoice_status = 0;
        $module->contract_status = 0;
        $module->task_status = 0;
        $module->event_status = 0;
        $module->holiday_status = 0;
        $module->saveQuietly();
    }
    public function employeeShift($cooperative)
    {

        $employeeShift = new EmployeeShift();
        $employeeShift->shift_name = 'Day Off';
        $employeeShift->cooperative_id = $cooperative->id;
        $employeeShift->shift_short_code = 'DO';
        $employeeShift->color = '#E8EEF3';
        $employeeShift->late_mark_duration = 0;
        $employeeShift->clockin_in_day = 0;
        $employeeShift->office_open_days = '';
        $employeeShift->saveQuietly();

        $employeeShift = new EmployeeShift();
        $employeeShift->shift_name = 'General Shift';
        $employeeShift->cooperative_id = $cooperative->id;
        $employeeShift->shift_short_code = 'GS';
        $employeeShift->color = '#99C7F1';
        $employeeShift->office_start_time = '08:00:00';
        $employeeShift->office_end_time = '18:00:00';
        $employeeShift->late_mark_duration = 20;
        $employeeShift->clockin_in_day = 2;
        $employeeShift->office_open_days = '["1","2","3","4","5"]';
        $employeeShift->saveQuietly();
    }

    public function attendanceSetting($cooperative)
    {
        $setting = new AttendanceSetting();
        $setting->cooperative_id = $cooperative->id;
        $setting->office_start_time = '09:00:00';
        $setting->office_end_time = '18:00:00';
        $setting->late_mark_duration = 20;
        $setting->default_employee_shift = EmployeeShift::where('cooperative_id', $cooperative->id)->where('shift_name', '<>', 'Day Off')->first()->id;
        $setting->alert_after_status = 0;
        $setting->saveQuietly();
    }

    public function leaveType($cooperative)
    {
        $gender = ['Homme', 'Femme'];
        $maritalstatus = ['marie', 'celibataire'];

        $status = [
            ['type_name' => 'Payes', 'color' => '#16813D', 'cooperative_id' => $cooperative->id, 'gender' => json_encode($gender), 'marital_status' => json_encode($maritalstatus), 'role' => ''],
            ['type_name' => 'Maladie', 'color' => '#DB1313', 'cooperative_id' => $cooperative->id, 'gender' => json_encode($gender), 'marital_status' => json_encode($maritalstatus), 'role' => ''],
            ['type_name' => 'Deuil', 'color' => '#B078C6', 'cooperative_id' => $cooperative->id, 'gender' => json_encode($gender), 'marital_status' => json_encode($maritalstatus), 'role' => ''],
            ['type_name' => 'Maternite', 'color' => '#B078C6', 'cooperative_id' => $cooperative->id, 'gender' => json_encode($gender), 'marital_status' => json_encode($maritalstatus), 'role' => ''],
            ['type_name' => 'R & R / Home leave', 'color' => '#B078C6', 'cooperative_id' => $cooperative->id, 'gender' => json_encode($gender), 'marital_status' => json_encode($maritalstatus), 'role' => ''],
            ['type_name' => 'Sans solde', 'color' => '#B078C6', 'cooperative_id' => $cooperative->id, 'gender' => json_encode($gender), 'marital_status' => json_encode($maritalstatus), 'role' => ''],
            ['type_name' => 'Compensation', 'color' => '#B078C6', 'cooperative_id' => $cooperative->id, 'gender' => json_encode($gender), 'marital_status' => json_encode($maritalstatus), 'role' => ''],
            ['type_name' => 'Fin de contrat', 'color' => '#B078C6', 'cooperative_id' => $cooperative->id, 'gender' => json_encode($gender), 'marital_status' => json_encode($maritalstatus), 'role' => '']
        ];

        LeaveType::insert($status);
    }
}
