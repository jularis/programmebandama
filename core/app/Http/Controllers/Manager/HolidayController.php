<?php
namespace App\Http\Controllers\Manager;
use App\Http\Controllers\AccountBaseController;

use App\DataTables\HolidayDataTable;
use App\Http\Helpers\Reply;
use App\Http\Requests\CommonRequest;
use App\Http\Requests\Holiday\CreateRequest;
use App\Http\Requests\Holiday\UpdateRequest;
use App\Models\AttendanceSetting;
use App\Models\GoogleCalendarModule;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Services\Google;
use Illuminate\Support\Facades\DB;

class HolidayController extends AccountBaseController
{

    
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.holiday'; 
    }

    public function index()
    {
         
        $this->activeSettingMenu = 'holidays_settings';
        if (request('start') && request('end')) {
            $holidayArray = array();

            $holidays = Holiday::orderBy('date', 'ASC');

            if (request()->searchText != '') {
                $holidays->where('holidays.occassion', 'like', '%' . request()->searchText . '%');
            }

            $holidays = $holidays->get();

            foreach ($holidays as $key => $holiday) {

                $holidayArray[] = [
                    'id' => $holiday->id,
                    'title' => $holiday->occassion,
                    'start' => $holiday->date->format('Y-m-d'),
                    'end' => $holiday->date->format('Y-m-d'),
                ];
            }

            return $holidayArray;
        }

        return view('manager.holiday.calendar.index', $this->data);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|mixed|void
     */
    public function create()
    {
        

        $this->redirectUrl = request()->date ? route('manager.holidays.index') : route('manager.holidays.table_view');
        $this->date = request()->date ? Carbon::parse(request()->date)->timezone(cooperative()->timezone)->translatedFormat('Y-m-d') : '';

        if (request()->ajax()) {
            $this->pageTitle = __('app.menu.holiday');
            $html = view('manager.holiday.ajax.create', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'manager.holiday.ajax.create';

        return view('manager.holiday.create', $this->data);

    }

    /**
     *
     * @param CreateRequest $request
     * @return void
     */
    public function store(CreateRequest $request)
    { 

        $occassions = $request->occassion;
        $dates = $request->date;

        foreach ($dates as $index => $value) {
            if ($value != '') {

                $holiday = new Holiday();
                $holiday->date = Carbon::createFromFormat('Y-m-d', $value);
                $holiday->occassion = $occassions[$index];
                $holiday->cooperative_id = auth()->user()->cooperative_id;
              
                $holiday->save();

                if ($holiday) {
                    $holiday->event_id = $this->googleCalendarEvent($holiday);
                    $holiday->save();
                }
            }
        }

        if (request()->has('type')) {
            return redirect(route('manager.holidays.index'));
        }

        $redirectUrl = urldecode($request->redirect_url);

        if ($redirectUrl == '') {
            $redirectUrl = route('manager.holidays.index');
        }

        return Reply::successWithData(__('messages.recordSaved'), ['redirectUrl' => $redirectUrl]);

    }

    /**
     * Display the specified holiday.
     */
    public function show(Holiday $holiday)
    {
        $this->holiday = $holiday;
     
        $this->pageTitle = __('app.menu.holiday');

        if (request()->ajax()) {
            $html = view('manager.holiday.ajax.show', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'manager.holiday.ajax.show';

        return view('manager.holiday.create', $this->data);

    }

    /**
     * @param Holiday $holiday
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|mixed|void
     */
    public function edit(Holiday $holiday)
    {
        $this->holiday = $holiday;
         
        $this->pageTitle = __('app.menu.holiday');

        if (request()->ajax()) {
            $html = view('manager.holiday.ajax.edit', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'manager.holiday.ajax.edit';

        return view('manager.holiday.create', $this->data);

    }

    /**
     * @param UpdateRequest $request
     * @param Holiday $holiday
     * @return array|void
     */
    public function update(UpdateRequest $request, Holiday $holiday)
    {
       
        $data = $request->all();
        $data['date'] = Carbon::createFromFormat('Y-m-d', $request->date)->format('Y-m-d');

        $holiday->update($data);

        if ($holiday) {
            $holiday->event_id = $this->googleCalendarEvent($holiday);
            $holiday->save();
        }

        return Reply::successWithData(__('messages.updateSuccess'), ['redirectUrl' => route('manager.holidays.index')]);

    }

    /**
     * @param Holiday $holiday
     * @return array|void
     */
    public function destroy(Holiday $holiday)
    { 
        $holiday->delete();

        return Reply::successWithData(__('messages.deleteSuccess'), ['redirectUrl' => route('manager.holidays.index')]);

    }

    public function tableView(HolidayDataTable $dataTable)
    { 
        $this->pageTitle = __('app.menu.listView');
        $this->currentYear = now()->format('Y');
        $this->currentMonth = now()->month;

        /* year range from last 5 year to next year */
        $years = [];
        $latestFifthYear = (int)now()->subYears(5)->format('Y');
        $nextYear = (int)now()->addYear()->format('Y');

        for ($i = $latestFifthYear; $i <= $nextYear; $i++) {
            $years[] = $i;
        }

        $this->years = $years;

        return $dataTable->render('manager.holiday.index', $this->data);
    }

    public function applyQuickAction(Request $request)
    { 
        if ($request->action_type === 'delete') {
            $this->deleteRecords($request);

            return Reply::success(__('messages.deleteSuccess'));
        }

        return Reply::error(__('messages.selectAction'));


    }

    protected function deleteRecords($request)
    { 
        Holiday::whereIn('id', explode(',', $request->row_ids))->delete();
    }

    public function markHoliday()
    { 
        return view('manager.holiday.mark-holiday.index', $this->data);
    }

    public function markDayHoliday(CommonRequest $request)
    { 
        
        if (!$request->has('office_holiday_days')) {
            return Reply::error(__('messages.checkDayHoliday'));
        }
        
        $year = now()->format('Y');

        if ($request->has('year')) {
            $year = $request->has('year');
        }
        
       
        if ($request->office_holiday_days != null && count($request->office_holiday_days) > 0) {
            foreach ($request->office_holiday_days as $holiday) {
                $day = $holiday;
                
                $dateArray = $this->getDateForSpecificDayBetweenDates($year . '-01-01', $year . '-12-31', ($day));
                
                foreach ($dateArray as $date) {
                    Holiday::firstOrCreate([
                        'date' => $date,
                        'cooperative_id' => request()->cooperativeId,
                        'occassion' => $request->occassion ? $request->occassion : now()->weekday($day)->translatedFormat('l')
                    ]);
                    
                }

                $this->googleCalendarEventMulti($day, $year);

            }
        }

        $redirectUrl = 'table-view';

        if (url()->previous() == route('manager.holidays.index')) {
            $redirectUrl = route('manager.holidays.index');
        }

        return Reply::successWithData(__('messages.recordSaved'), ['redirectUrl' => $redirectUrl]);
    }

    public function getDateForSpecificDayBetweenDates($startDate, $endDate, $weekdayNumber)
    {
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);

        $dateArr = [];

        do {
            if (date('w', $startDate) != $weekdayNumber) {
                $startDate += (24 * 3600); // add 1 day
            }
        } while (date('w', $startDate) != $weekdayNumber);


        while ($startDate <= $endDate) {
            $dateArr[] = date('Y-m-d', $startDate);
            $startDate += (7 * 24 * 3600); // add 7 days
        }

        return ($dateArr);
    }

    protected function googleCalendarEvent($event)
    {
        $module = GoogleCalendarModule::first();
        $googleAccount = cooperative();

        if ($googleAccount->google_calendar_status == 'active' && $googleAccount->google_calendar_verification_status == 'verified' && $googleAccount->token && $module->holiday_status == 1) {

            $google = new Google();

            if ($event->date) {
                $date = \Carbon\Carbon::parse($event->date)->shiftTimezone($googleAccount->timezone);

                // Create event
                $google = $google->connectUsing($googleAccount->token);

                $eventData = new \Google_Service_Calendar_Event(array(
                    'summary' => $event->occassion,
                    'location' => $googleAccount->address,
                    'colorId' => 1,
                    'start' => array(
                        'dateTime' => $date->copy()->startOfDay(),
                        'timeZone' => $googleAccount->timezone,
                    ),
                    'end' => array(
                        'dateTime' => $date->copy()->endOfDay(),
                        'timeZone' => $googleAccount->timezone,
                    ),
                    'reminders' => array(
                        'useDefault' => false,
                        'overrides' => array(
                            array('method' => 'email', 'minutes' => 24 * 60),
                            array('method' => 'popup', 'minutes' => 10),
                        ),
                    ),
                ));

                try {
                    if ($event->event_id) {
                        $results = $google->service('Calendar')->events->patch('primary', $event->event_id, $eventData);
                    }
                    else {
                        $results = $google->service('Calendar')->events->insert('primary', $eventData);
                    }

                    return $results->id;
                } catch (\Google\Service\Exception $error) {
                    if (is_null($error->getErrors())) {
                        // Delete google calendar connection data i.e. token, name, google_id
                        $googleAccount->name = null;
                        $googleAccount->token = null;
                        $googleAccount->google_id = null;
                        $googleAccount->google_calendar_verification_status = 'non_verified';
                        $googleAccount->save();
                    }
                }
            }
        }

        return $event->event_id;
    }

    protected function googleCalendarEventMulti($day, $year)
    {
        $googleAccount = cooperative();
        $module = GoogleCalendarModule::first();

        if ($googleAccount->google_calendar_status == 'active' && $googleAccount->google_calendar_verification_status == 'verified' && $googleAccount->token && $module->holiday_status == 1) {
            $google = new Google();

            $allDays = $this->getDateForSpecificDayBetweenDates($year . '-01-01', $year . '-12-31', $day);

            $holiday = Holiday::where(DB::raw('DATE(`date`)'), $allDays[0])->first();

            $startDate = Carbon::parse($allDays[0]);

            $frequency = 'WEEKLY';

            $eventData = new \Google_Service_Calendar_Event();
            $eventData->setSummary(now()->startOfWeek($day)->translatedFormat('l'));
            $eventData->setColorId(7);
            $eventData->setLocation('');

            $start = new \Google_Service_Calendar_EventDateTime();
            $start->setDateTime($startDate);
            $start->setTimeZone($googleAccount->timezone);

            $eventData->setStart($start);

            $end = new \Google_Service_Calendar_EventDateTime();
            $end->setDateTime($startDate);
            $end->setTimeZone($googleAccount->timezone);

            $eventData->setEnd($end);

            $dy = substr(now()->startOfWeek($day)->translatedFormat('l'), 0, 2);

            $eventData->setRecurrence(array('RRULE:FREQ=' . $frequency . ';COUNT=' . count($allDays) . ';BYDAY=' . $dy));

            // Create event
            $google->connectUsing($googleAccount->token);
            // array for multiple

            try {
                if ($holiday->event_id) {
                    $results = $google->service('Calendar')->events->patch('primary', $holiday->event_id, $eventData);
                }
                else {
                    $results = $google->service('Calendar')->events->insert('primary', $eventData);
                }

                $holidays = Holiday::where('occassion', now()->startOfWeek($day)->translatedFormat('l'))->get();

                foreach ($holidays as $holiday) {
                    $holiday->event_id = $results->id;
                    $holiday->save();
                }

                return;
            } catch (\Google\Service\Exception $error) {
                if (is_null($error->getErrors())) {
                    // Delete google calendar connection data i.e. token, name, google_id
                    $googleAccount->name = null;
                    $googleAccount->token = null;
                    $googleAccount->google_id = null;
                    $googleAccount->google_calendar_verification_status = 'non_verified';
                    $googleAccount->save();
                }
            }

        }
    }

}
