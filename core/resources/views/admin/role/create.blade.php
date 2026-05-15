@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.roles.store') }}">
                        @csrf
                     
                        <table class="table table-bordered permission-table">
						        <thead>
						        <tr>
						            <th class="text-center"><label for="name" class="form-label">Nom de rôle</label>
                            <input value="{{ old('name') }}" 
                                type="text" 
                                class="form-control" 
                                name="name" 
                                placeholder="Name" required></th>
						        </tr>
						        <tr>
						            <th class="text-center"> <div class="checkbox">
                                        <input type="checkbox" class="checkAll" name="all_permission">
                                        <label for="select_all">Selectionner toutes les Permissions</label>
						            	</div></th> 
						        </tr>
						         
						        </thead>
                                <tbody>
                                <?php
                                use Illuminate\Support\Str;
            $i=1;
            $existe =array();

            ?>
                                @forelse($permissions as $permission)
                                <?php
                                $permissionName = Str::replaceFirst('manager.', '', $permission->name);
             if(!in_array(Str::before($permissionName,"."),$existe)) {
               echo "<tr style='background:#C1C1C1'>";
               echo '<td>'.strtoupper(Str::before($permissionName,".")).'</td></tr>';
              }
             ?>     
                  <tr> 
                                    <td class="text-left">
						                <div class="icheckbox_square-blue checked" aria-checked="false" aria-disabled="false">
							                <div class="checkbox">
								            <input type="checkbox" value="{{ $permission->name }}" id="permission[{{ $permission->name }}]"
                                            class='permission'
                                             name="permission[{{ $permission->name }}]"
                                            >
								            <label for="products-index">{{ Str::after($permissionName,".") }}</label>
							            	</div>
						            	</div>
						            </td>
                                    </tr>
                                <?php
              $existe[] = Str::before($permissionName,".");
               ?>
						        
                                @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                        @endforelse
                                </tbody>
                </table>
 
                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn--primary w-100 h-45 "> @lang('Envoyer')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script type="text/javascript">
        (function($) {
            "use strict";
           
            $(".permission").on('change', function(e) {
                let totalLength = $(".permission").length;
                let checkedLength = $(".permission:checked").length;
                if (totalLength == checkedLength) {
                    $('.checkAll').prop('checked', true);
                } else {
                    $('.checkAll').prop('checked', false);
                }
                if (checkedLength) {
                    $('.dispatch').removeClass('d-none')
                } else {
                    $('.dispatch').addClass('d-none')
                }
            });

            $('.checkAll').on('change', function() {
                if ($('.checkAll:checked').length) {
                    $('.permission').prop('checked', true);
                } else {
                    $('.permission').prop('checked', false);
                }
                $(".permission").change();
            });

        })(jQuery)
    </script>
@endsection
@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.roles.index') }}" />
@endpush
@push('script')
    <script type="text/javascript">
        (function($) {
            "use strict";
           
            $(".permission").on('change', function(e) {
                let totalLength = $(".permission").length;
                let checkedLength = $(".permission:checked").length;
                if (totalLength == checkedLength) {
                    $('.checkAll').prop('checked', true);
                } else {
                    $('.checkAll').prop('checked', false);
                }
                if (checkedLength) {
                    $('.dispatch').removeClass('d-none')
                } else {
                    $('.dispatch').addClass('d-none')
                }
            });

            $('.checkAll').on('change', function() {
                if ($('.checkAll:checked').length) {
                    $('.permission').prop('checked', true);
                } else {
                    $('.permission').prop('checked', false);
                }
                $(".permission").change();
            });

        })(jQuery)
    </script>
@endpush