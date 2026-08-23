<?php

namespace App\Jobs;

use App\Models\EmployeeDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Spatie\Permission\Models\Role;

class ImportEmployeeJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $row;
    protected array $columns;
    protected object $cooperative;

    public function __construct(array $row, array $columns, object $cooperative)
    {
        $this->row = $row;
        $this->columns = $columns;
        $this->cooperative = $cooperative;
    }

    public function handle(): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        $name = trim((string) $this->value('name'));
        $email = trim((string) $this->value('email'));
        $employeeId = trim((string) $this->value('employee_id'));
        $joiningDate = $this->dateValue($this->value('joining_date'));

        if ($name === '' || $email === '' || $employeeId === '' || !$joiningDate) {
            throw new \InvalidArgumentException('Nom, email, matricule employe et date embauche sont obligatoires.');
        }

        DB::transaction(function () use ($name, $email, $employeeId, $joiningDate) {
            $employeeDetail = EmployeeDetail::where('cooperative_id', $this->cooperative->id)
                ->where('employee_id', $employeeId)
                ->first();

            $user = $employeeDetail?->user;

            if (!$user) {
                $user = User::where('cooperative_id', $this->cooperative->id)
                    ->where('email', $email)
                    ->first();
            }

            $parts = preg_split('/\s+/', $name, 2);
            $firstname = $parts[0] ?? $name;
            $lastname = $parts[1] ?? $name;

            if (!$user) {
                $user = new User();
                $user->cooperative_id = $this->cooperative->id;
                $user->user_type = 'staff';
                $user->type_compte = 'web';
                $user->password = Hash::make('azerty');
                $user->username = $this->uniqueUsername($firstname, $lastname);
            }

            $user->firstname = $firstname;
            $user->lastname = $lastname;
            $user->email = $email;
            $user->mobile = $this->value('mobile');
            $user->genre = $this->normaliseGender($this->value('gender'));
            $user->adresse = $this->value('address');
            $user->status = $user->status ?? 1;
            $user->save();

            $role = Role::where('name', 'employee')->first();
            if ($role && !$user->hasRole($role->name)) {
                $user->assignRole($role->name);
            }

            $employee = $employeeDetail ?: EmployeeDetail::firstOrNew(['user_id' => $user->id]);
            $employee->user_id = $user->id;
            $employee->cooperative_id = $this->cooperative->id;
            $employee->employee_id = $employeeId;
            $employee->joining_date = $joiningDate;
            $employee->address = $this->value('address');
            $employee->hourly_rate = $this->numericValue($this->value('hourly_rate'));
            $employee->calendar_view = 'task,events,holiday,tickets,leaves';
            $employee->save();
        });
    }

    protected function value(string $field): mixed
    {
        if (!array_key_exists($field, $this->columns)) {
            return null;
        }

        $index = $this->columns[$field];
        return $this->row[$index] ?? null;
    }

    protected function dateValue(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return Carbon::instance(ExcelDate::excelToDateTimeObject($value))->format('Y-m-d');
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    protected function numericValue(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? (float) $value : null;
    }

    protected function normaliseGender(mixed $value): ?string
    {
        $gender = Str::lower(trim((string) $value));

        return match ($gender) {
            'homme', 'male', 'm' => 'male',
            'femme', 'female', 'f' => 'female',
            default => $gender ?: null,
        };
    }

    protected function uniqueUsername(string $firstname, string $lastname): string
    {
        $base = Str::limit(Str::slug($firstname, ''), 12, '') . Str::limit(Str::slug($lastname, ''), 1, '');
        $base = $base ?: 'employee';
        $username = $base;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base . $counter;
            $counter++;
        }

        return $username;
    }
}
