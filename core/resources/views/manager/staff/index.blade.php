@extends('manager.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Staff')</th>
                                    <th>@lang('Role')</th>
                                    <th>@lang('Type de compte')</th>
                                    <th>@lang('Email')</th>
                                    <th>@lang('Adresse')</th>
                                    <th>@lang('Ajouté le')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($staffs as $staff)
                                    <tr>
                                        <td>
                                            <span>{{ __($staff->fullname) }}</span>
                                            <br>
                                            <a href="{{ route('manager.staff.edit', encrypt($staff->id)) }}">
                                                <span>@</span>{{ __($staff->username) }}
                                            </a>
                                        </td>
                                        <td>
                                            @if (!empty($staff->getRoleNames()))
                                                    @foreach ($staff->getRoleNames() as $v)
                                                        <span class="badge badge--success">{{ $v }}</span>
                                                    @endforeach
                                                @endif
                                        </td>
                                        <td>
                                            <span>{{ $staff->type_compte }}</span>
                                        </td>
                                        <td>
                                            <span>{{ $staff->email }}<br>{{$staff->mobile }}</span>
                                        </td>
                                        <td>
                                            <span>{{ $staff->adresse }}</span>
                                        </td>

                                        <td>
                                            {{ showDateTime($staff->created_at) }}
                                        </td>

                                        <td>
                                            @php
                                                echo $staff->statusBadge;
                                            @endphp
                                        </td>

                                        <td>
                                        <a href="{{ route('manager.staff.magasin.index', $staff->id) }}"
                                                class="btn btn-sm btn-outline--warning"><i
                                                    class="las la-home"></i>@lang('Magasins')</a>
                                            <a href="{{ route('manager.staff.edit', encrypt($staff->id)) }}"
                                                class="btn btn-sm btn-outline--primary"><i
                                                    class="las la-pen"></i>@lang('Editer')</a>
                                            @if ($staff->status == Status::BAN_USER)
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--success confirmationBtn"
                                                    data-action="{{ route('manager.staff.status', $staff->id) }}"
                                                    data-question="@lang('Êtes-vous sûr d\'activer ce staff?')">
                                                    <i class="la la-eye"></i> @lang('Active')
                                                </button>
                                            @else
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--danger  confirmationBtn"
                                                    data-action="{{ route('manager.staff.status', $staff->id) }}"
                                                    data-question="@lang('Êtes-vous sûr de désactiver ce staff?')">
                                                    <i class="la la-eye-slash"></i> @lang('Désactive')
                                                </button>
                                            @endif
                                            <a href="javascript:void();"
                                                class="btn btn-sm btn-outline--danger confirmationBtn"
                                                data-action="{{ route('manager.staff.delete', encrypt($staff->id)) }}"
                                                data-question="@lang('Êtes-vous sûr de supprimer ce staff?')"
                                                ><i
                                                    class="las la-trash"></i>@lang('Delete')</a>
                                            <a href="{{ route('manager.staff.stafflogin', $staff->id) }}"
                                                class="btn btn-sm btn-outline--success " 
                                                target="_blank"><i
                                                    class="las la-sign-in-alt"></i>
                                                @lang('Connexion')</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($staffs->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($staffs) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection


@push('breadcrumb-plugins')
    <x-search-form placeholder="Search here" />
    <a href="{{ route('manager.staff.create') }}" class="btn  btn-outline--primary h-45 addNewCooperative"><i
            class="las la-plus"></i>@lang("Ajouter nouveau")</a>
    <a href="{{ route('manager.staff.exportExcel.staffAll') }}" class="btn  btn-outline--warning h-45"><i class="las la-cloud-download-alt"></i> @lang('Exporter en Excel')</a>
    
@endpush
