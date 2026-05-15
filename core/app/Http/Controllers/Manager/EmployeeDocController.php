<?php
namespace App\Http\Controllers\Manager;
use App\Http\Controllers\AccountBaseController;
use App\Http\Helpers\Files;
use App\Http\Helpers\Reply;
use App\Http\Requests\EmployeeDocs\CreateRequest;
use App\Http\Requests\EmployeeDocs\UpdateRequest;
use App\Models\EmployeeDocument;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeDocController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.employeeDocs'; 
    }

    public function create()
    { 
        $this->user = User::findOrFail(user()->id);

        return view('manager.profile-settings.ajax.employee.create', $this->data);
    }

    public function store(CreateRequest $request)
    {
        $manager = auth()->user();

        $fileFormats = explode(',', global_setting()->allowed_file_types);

        if($request->file) {
            $file_request = $request->file;
            if (!in_array($file_request->getClientMimeType(), $fileFormats)) {
                return Reply::error(__('messages.employeeDocsAllowedFormat'));
            }
        }

        $file = new EmployeeDocument();

        $file->name = $request->name;
     
        $filename = Files::uploadLocalOrS3($request->file, EmployeeDocument::FILE_PATH . '/' . $request->user_id);

        $file->user_id = $request->user_id;
        $file->filename = $request->file->getClientOriginalName();
        $file->hashname = $filename;
        $file->size = $request->file->getSize();
        $file->cooperative_id = $manager->cooperative_id;
        $file->save();

        $this->files = EmployeeDocument::where('user_id', $request->user_id)->orderBy('id', 'desc')->get();
        $view = view('manager.employees.files.show', $this->data)->render();

        return Reply::successWithData(__('messages.recordSaved'), ['status' => 'success', 'view' => $view]);
    }

    public function edit($id)
    {
        $this->file = EmployeeDocument::findOrFail($id); 

        return view('manager.employees.files.edit', $this->data);
    }

    public function update(UpdateRequest $request, $id)
    {
        $manager = auth()->user();
        $file = EmployeeDocument::findOrFail($id);

        $file->name = $request->name;

        if ($request->file) {
            $filename = Files::uploadLocalOrS3($request->file, EmployeeDocument::FILE_PATH . '/' . $file->user_id);
            $file->filename = $request->file->getClientOriginalName();
            $file->hashname = $filename;
            $file->size = $request->file->getSize();
        }
        $file->cooperative_id = $manager->cooperative_id;
        $file->save();

        return Reply::success(__('messages.updateSuccess'));
    }

    public function destroy($id)
    {
        $file = EmployeeDocument::findOrFail($id); 


        Files::deleteFile($file->hashname, EmployeeDocument::FILE_PATH . '/' . $file->user_id);

        EmployeeDocument::destroy($id);

        $this->files = EmployeeDocument::where('user_id', $file->user_id)->orderBy('id', 'desc')->get();

        $view = view('manager.employees.files.show', $this->data)->render();

        return Reply::successWithData(__('messages.deleteSuccess'), ['view' => $view]);

    }

    public function download($id)
    {
        $this->file = EmployeeDocument::whereRaw('md5(id) = ?', $id)->firstOrFail(); 

        return download_local_s3($this->file, EmployeeDocument::FILE_PATH . '/' . $this->file->user_id . '/' . $this->file->hashname);

    }

}
