{{Form::model($coupon, array('route' => array('general.coupons.update', $coupon->id), 'method' => 'PUT', 'class'=>'needs-validation', 'novalidate')) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-12">
            {{Form::label('name',__('Name'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::text('name',null,array('class'=>'form-control font-style','required'=>'required', 'placeholder'=>__('Enter Name')))}}
        </div>
        <div class="form-group col-md-6">
            {{Form::label('discount',__('Discount'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::number('discount',null,array('class'=>'form-control','required'=>'required','step'=>'0.01', 'placeholder'=>__('Enter Discount')))}}
            <span class="small">{{__('Note: Discount in Percentage')}}</span>
        </div>
        <div class="form-group col-md-6">
            {{Form::label('limit',__('Limit'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::number('limit',null,array('class'=>'form-control','required'=>'required', 'placeholder'=>__('Enter Limit')))}}
        </div>
        <div class="form-group col-md-12">
            {{Form::label('code',__('Code'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::text('code',null,array('class'=>'form-control','required'=>'required', 'placeholder'=>__('Enter Code')))}}
        </div>

    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
</div>
{{ Form::close() }}
