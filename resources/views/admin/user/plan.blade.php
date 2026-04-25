<div class="modal-body">
    <div class="card">
        <div class="card-body table-border-style">
            <div class="table-responsive">
                <table class="table datatable">
                    @foreach($plans as $plan)
                        <tr>
                            <td><h6>{{$plan->name}} ({{isset($admin_payment_setting['currency']) ? $admin_payment_setting['currency'] : '$'}}{{ number_format($plan->getTotalPlanAmount($plan->id), 2, '.', ',') }}) {{' / '. $plan->duration}}</h6></td>
                            <td>{{__('Users')}} : {{$plan->max_users}}</td>
                            <td>
                                @if($user->plan==$plan->id)
                                    <span class="btn btn-sm btn-primary my-auto"><i class="ti ti-check "></i></span>
                                @else
                                    <a href="{{route('admin.plan.active',[$user->id,$plan->id])}}" class="btn btn-sm btn-warning my-auto" title="{{__('Click to Upgrade Plan')}}"><i class="ti ti-shopping-cart-plus"></i></a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</div>
