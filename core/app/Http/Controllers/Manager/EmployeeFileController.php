<?php
namespace App\Http\Controllers\Manager;
use App\Http\Controllers\AccountBaseController;
use App\Models\Leave;

use App\Models\LeaveFile;
use App\Http\Helpers\Files;
use App\Http\Helpers\Reply;
use Illuminate\Http\Request; 

class EmployeeFileController extends AccountBaseController
{
    public function index(Request $request)
    {
        $ret = array();
        if ($request->hasFile('file')) {
               
            $files = $request->file('file');
            foreach ($files as $fileData) { 
                $ret =  $fileData->store('public/contratsTravail');  
            }
            echo json_encode($ret);
        }
    }

    public function store(Request $request)
    {
 
        $ret = array();
        if ($request->hasFile('myfile')) {
               
            $files = $request->file('myfile');
            foreach ($files as $fileData) { 
                $ret =  $fileData->store('public/contratsTravail');  
            }
            echo json_encode($ret);
        }
 

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        $file = LeaveFile::findOrFail($id);
        $this->leave = Leave::findorFail($file->leave_id);
        Files::deleteFile($file->hashname, LeaveFile::FILE_PATH . '/' . $file->leave_id);

        LeaveFile::destroy($id);

        $this->files = LeaveFile::where('leave_id', $file->leave_id)->orderBy('id', 'desc')->get();
        $view = view('leaves.files.show', $this->data)->render();

        return Reply::successWithData(__('messages.deleteSuccess'), ['view' => $view]);

    }

    public function download($id)
    {
        $file = LeaveFile::whereRaw('md5(id) = ?', $id)->firstOrFail();

        return download_local_s3($file, LeaveFile::FILE_PATH . '/' . $file->leave_id . '/' . $file->hashname);

    }

}
