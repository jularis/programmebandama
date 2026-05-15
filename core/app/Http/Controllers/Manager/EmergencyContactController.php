<?php
namespace App\Http\Controllers\Manager;
use App\Http\Controllers\AccountBaseController;
use App\Http\Helpers\Reply;
use App\Http\Requests\Admin\Employee\StoreEmergencyContactRequest;
use App\Models\EmergencyContact;

class EmergencyContactController extends AccountBaseController
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->pageTitle = __('app.addContact');

        $this->userId = request()->user_id ? request()->user_id : null;

        return view('manager.profile-settings.emergency-contacts.create', $this->data)->render();
    }

    public function store(StoreEmergencyContactRequest $request)
    {
        $manager = auth()->user();
        $emergencyContact = new EmergencyContact();
        $emergencyContact->user_id = !is_null($request->user_id) ? $request->user_id : user()->id;
        $emergencyContact->name = $request->name;
        $emergencyContact->mobile = $request->mobile;
        $emergencyContact->email = $request->email;
        $emergencyContact->relation = $request->relationship;
        $emergencyContact->address = $request->address;
        $emergencyContact->cooperative_id = $manager->cooperative_id;
        $emergencyContact->added_by = user()->id;
        $emergencyContact->save();

        $this->contacts = EmergencyContact::where('user_id', $emergencyContact->user_id)->get();
        $html = view('manager.profile-settings.emergency-contacts.data', $this->data)->render();

        return Reply::successWithData(__('messages.employeeEmergencyContact'), ['html' => $html]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EmergencyContact  $emergencyContact
     * @return \Illuminate\Http\Response
     */
    public function show(EmergencyContact $emergencyContact)
    {
       

        $this->pageTitle = __('modules.emergencyContact.emergencyContact');
        $this->contact = $emergencyContact;

        return view('manager.profile-settings.emergency-contacts.show', $this->data)->render();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EmergencyContact  $emergencyContact
     * @return \Illuminate\Http\Response
     */
    public function edit(EmergencyContact $emergencyContact)
    {
         
        $this->pageTitle = __('app.editContact');
        $this->contact = $emergencyContact;

        return view('manager.profile-settings.emergency-contacts.edit', $this->data)->render();
    }

    public function update(StoreEmergencyContactRequest $request, EmergencyContact $emergencyContact)
    {
        $manager = auth()->user();
        $emergencyContact->cooperative_id = $manager->cooperative_id;
        $emergencyContact->name = $request->name;
        $emergencyContact->mobile = $request->mobile;
        $emergencyContact->email = $request->email;
        $emergencyContact->relation = $request->relationship;
        $emergencyContact->address = $request->address;
        $emergencyContact->last_updated_by = user()->id;
        $emergencyContact->save();

        $this->contacts = EmergencyContact::where('user_id', $emergencyContact->user_id)->get();
        $html = view('manager.profile-settings.emergency-contacts.data', $this->data)->render();

        return Reply::successWithData(__('messages.employeeEmergencyContact'), ['html' => $html]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EmergencyContact  $emergencyContact
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmergencyContact $emergencyContact)
    {
       
        $emergencyContact->delete();

        return Reply::successWithData(__('messages.deleteSuccess'), ['redirectUrl' => route('profile-settings.index').'?tab=emergency-contacts']);
    }

}
