<option {{ !$selected ?: 'selected' }} data-content="<div class='d-flex align-items-center text-left'>
    <div class='taskEmployeeImg border-0 d-inline-block mr-1'>
    <img class='rounded-circle' src='{{ asset('core/storage/app/avatar/' .$user->image) }}'>  
    </div><div class='f-10 font-weight-light my-1'>{{$user->lastname }} {{ $user->firstname }}</div></div>" value="{{ $userID ?? $user->id }}">
        {{ $user->lastname }} {{ $user->firstname }}
    </option>  