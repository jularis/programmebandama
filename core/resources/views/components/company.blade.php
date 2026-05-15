
<div class="media align-items-center mw-250">
    @if (!is_null($cooperative))
        <a href="{{ route('superadmin.companies.show', [$cooperative->id]) }}" class="position-relative">
            <img src="{{ $cooperative->logo_url }}" class="mr-2  taskEmployeeImg rounded"
                alt="{{ $cooperative->cooperative_name }}" title="{{ $cooperative->cooperative_name }}">
        </a>
        <div class="media-body">
            <h5 class="mb-0 f-12">
                <a href="{{ route('superadmin.companies.show', [$cooperative->id]) }}"  class="text-darkest-grey">{{ ucfirst($cooperative->cooperative_name) }}</a>
            </h5>

            @if(module_enabled('Subdomain'))
                @if(!is_null($cooperative->sub_domain))
                <p class="mb-0 f-12 text-dark-grey">
                    <a href="http://{{ $cooperative->sub_domain }}"  class="text-dark-grey" target="_blank">{{ $cooperative->sub_domain }}</a>
                </p>
                @else
                    <p class="mb-0 f-11 text-red">
                        {{__('superadmin.subdomainNotAdded')}}
                    </p>
                @endif
             @endif
        </div>
    @else
        --
    @endif
</div>
