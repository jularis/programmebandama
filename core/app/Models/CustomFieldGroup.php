<?php

namespace App\Models;

use App\Traits\HasCooperative;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

 
class CustomFieldGroup extends BaseModel
{

    use HasCooperative;

    const ALL_FIELDS = [
        ['name' => 'Client', 'model' => ClientDetails::CUSTOM_FIELD_MODEL],
        ['name' => 'Employee', 'model' => EmployeeDetail::CUSTOM_FIELD_MODEL],
        ['name' => 'Project', 'model' => Project::CUSTOM_FIELD_MODEL],
        ['name' => 'Invoice', 'model' => Invoice::CUSTOM_FIELD_MODEL],
        ['name' => 'Estimate', 'model' => Estimate::CUSTOM_FIELD_MODEL],
        ['name' => 'Task', 'model' => Task::CUSTOM_FIELD_MODEL],
        ['name' => 'Expense', 'model' => Expense::CUSTOM_FIELD_MODEL],
        ['name' => 'Lead', 'model' => Lead::CUSTOM_FIELD_MODEL],
        ['name' => 'Product', 'model' => Product::CUSTOM_FIELD_MODEL],
        ['name' => 'Ticket', 'model' => Ticket::CUSTOM_FIELD_MODEL],
        ['name' => 'Time Log', 'model' => ProjectTimeLog::CUSTOM_FIELD_MODEL],
        ['name' => 'Contract', 'model' => Contract::CUSTOM_FIELD_MODEL]
    ];

    public $timestamps = false;

    public function customField(): HasMany
    {
        return $this->HasMany(CustomField::class);
    }

    public static function customFieldsDataMerge($model)
    {
        $customFields = CustomField::exportCustomFields($model);

        $customFieldsDataMerge = [];

        foreach ($customFields as $customField) {
            $customFieldsData = [
                $customField->name => [
                    'data' => $customField->name,
                    'name' => $customField->name,
                    'title' => $customField->label,
                    'visible' => $customField['visible'],
                    'orderable' => false,
                ]
            ];

            $customFieldsDataMerge = array_merge($customFieldsDataMerge, $customFieldsData);
        }

        return $customFieldsDataMerge;
    }

    /**
     * Get the custom field group's name.
     */
    protected function fields(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->customField->map(function ($item) {
                    if (in_array($item->type, ['select', 'radio', 'checkbox'])) {
                        $item->values = json_decode($item->values);

                        return $item;
                    }

                    return $item;
                });
            },
        );
    }

}
