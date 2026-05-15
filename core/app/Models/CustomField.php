<?php

namespace App\Models;

use App\Traits\HasCooperative;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\HasOne;
 
class CustomField extends BaseModel
{

    use HasCooperative;

    public $timestamps = false;

    protected $guarded = ['id'];

    public function leadCustomForm()
    {
        return $this->hasOne(LeadCustomForm::class, 'custom_fields_id');
    }

    public function ticketCustomForm()
    {
        return $this->hasOne(TicketCustomForm::class, 'custom_fields_id');
    }

    public function customFieldGroup()
    {
        return $this->hasOne(CustomFieldGroup::class, 'custom_field_group_id');
    }

    public function fieldGroup()
    {
        return $this->belongsTo(CustomFieldGroup::class, 'custom_field_group_id');
    }

    public static function exportCustomFields($model)
    {
        $customFieldsGroupsId = CustomFieldGroup::where('model', $model::CUSTOM_FIELD_MODEL)->select('id')->first();
        $customFields = collect();

        if ($customFieldsGroupsId) {
            $customFields = CustomField::where('custom_field_group_id', $customFieldsGroupsId->id)->where(function ($q)
            {
                return $q->where('export', 1)->orWhere('visible', 'true');
            })->get();
        }

        return $customFields;
    }

    public static function customFieldData($datatables, $model, $relation = null)
    {
        $customFields = CustomField::exportCustomFields($model);
        $customFieldNames = [];
        $customFieldsId = $customFields->pluck('id');
        $fieldData = DB::table('custom_fields_data')->where('model', $model)->whereIn('custom_field_id', $customFieldsId)->select('id', 'custom_field_id', 'model_id', 'value')->get();

        foreach ($customFields as $customField) {
            $datatables->addColumn($customField->name, function ($row) use ($fieldData, $customField, $relation) {

                $finalData = $fieldData->filter(function ($value) use ($customField, $row, $relation) {
                    return ($value->custom_field_id == $customField->id) && ($value->model_id == ($relation ? $row->{$relation}->id : $row->id));
                })->first();

                if ($customField->type == 'select') {
                    $data = $customField->values;
                    $data = json_decode($data); // string to array

                    return $finalData ? (($finalData->value >= 0 && $finalData->value != null) ? $data[$finalData->value] : '--') : '--';
                }

                if($customField->type == 'file') {
                    return $finalData ? '<a href="'.asset_url_local_s3('custom_fields/' .$finalData->value).'" target="__blank" class="text-dark-grey">'.__('app.storageSetting.viewFile').'</a>' : '--';
                }

                return $finalData ? $finalData->value : '--';
            });

            // This will use for datatable raw column
            if($customField->type == 'file') {
                $customFieldNames[] = $customField->name;
            }

        }

        return $customFieldNames;
    }

    public static function generateUniqueSlug($label, $moduleId)
    {
        $slug = str_slug($label);
        $count = CustomField::where('name', $slug)->where('custom_field_group_id', $moduleId)->count();

        if ($count > 0) {
            $i = 1;

            while (CustomField::where('name', $slug . '-' . $i)->where('custom_field_group_id', $moduleId)->count() > 0) {
                $i++;
            }

            $slug .= '-' . $i;
        }

        return $slug;

    }

}
