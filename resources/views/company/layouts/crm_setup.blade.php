<div class="col-lg-3">
    <div class="card sticky-top">
        <div class="list-group list-group-flush" id="useradd-sidenav">
            <a href="{{route('organization.pipelines.index')}}"
               class="list-group-item list-group-item-action border-0 {{ (Request::route()->getName() == 'organization.pipelines.index' ) ? ' active' : '' }}">{{__('Pipeline')}}
                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
            </a>

            <a href="{{ route('organization.lead_stages.index') }}"
               class="list-group-item list-group-item-action border-0 {{ (Request::route()->getName() == 'organization.lead_stages.index' ) ? 'active' : '' }}">{{__('Lead Stages')}}
                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
            </a>

            {{--<a href="{{ route('organization.stages.index') }}"
               class="list-group-item list-group-item-action border-0 {{ (Request::route()->getName() == 'organization.stages.index' ) ? ' active' : '' }}">{{__('Deal Stages')}}
                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
            </a>--}}

            <a href="{{ route('organization.sources.index') }}"
               class="list-group-item list-group-item-action border-0 {{ (Request::route()->getName() == 'organization.sources.index' ) ? 'active' : '' }}   ">{{__('Sources')}}
                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
            </a>

            {{--<a href="{{ route('organization.labels.index') }}"
               class="list-group-item list-group-item-action border-0 {{ (Request::route()->getName() == 'organization.labels.index' ) ? 'active' : '' }}   ">{{__('Labels')}}
                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
            </a>--}}

        </div>
    </div>
</div>
