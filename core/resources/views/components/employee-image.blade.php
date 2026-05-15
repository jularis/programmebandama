<div class="taskEmployeeImg rounded-circle mr-1">
    <a href="{{ route('employees.show', $user->id) }}">
        <img data-toggle="tooltip" data-original-title="{{ $user->name }}"
            src="{{ asset('core/storage/app/avatar/' .$user->image) }}">
    </a>
</div>
