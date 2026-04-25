



{{ Form::model($bug, array('route' => array('organization.task.bug.update', $project_id,$bug->id ), 'method' => 'POST', 'class'=>'needs-validation', 'novalidate')) }}
<div class="modal-body">
    {{-- start for ai module--}}
    @php
        $plan= \App\Models\Utility::getChatGPTSettings();
    @endphp
    @if($plan->chatgpt == 1)
        <div class="text-end">
            <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm" data-ajax-popup-over="true"
               data-url="{{ route('organization.generate',['project bug']) }}"
               data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
                <i class="fas fa-robot"></i> <span>{{__('Generate with AI')}}</span>
            </a>
        </div>
    @endif
    {{-- end for ai module--}}
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('title', __('Title'),['class'=>'form-label']) }}
            <x-required></x-required>
            {{ Form::text('title', null, array('class' => 'form-control','required'=>'required', 'placeholder'=>__('Enter Title'))) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('priority', __('Priority'),['class'=>'form-label']) }}
            <x-required></x-required>
            {!! Form::select('priority', $priority, null,array('class' => 'form-control select','required'=>'required')) !!}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('start_date', __('Start Date'),['class'=>'form-label']) }}
            <x-required></x-required>
            {{ Form::date('start_date', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="form-group  col-md-6">
            {{ Form::label('due_date', __('Due Date'),['class'=>'form-label']) }}
            <x-required></x-required>
            {{ Form::date('due_date', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('status', __('Bug Status'),['class'=>'form-label']) }}
            <x-required></x-required>
            {!! Form::select('status', $status, null,array('class' => 'form-control select','required'=>'required')) !!}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('assign_to', __('Assigned To'),['class'=>'form-label']) }}
            <x-required></x-required>
            {{ Form::select('assign_to', $users, null,array('class' => 'form-control select','required'=>'required')) }}
        </div>
    </div>
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('description', __('Description'),['class'=>'form-label']) }}
            {!! Form::textarea('description', null, ['class'=>'custom_textarea mt-0','rows'=>'2', 'placeholder'=>__('Enter Description')]) !!}
        </div>
        
         <div class="form-group  col-md-12">
            <label class="form-label fw-bold">Upload Image<span class="text-danger">*</span></label>
          <div class="upload_box">
            <input id="fileName" class="file_name_input" placeholder="Browse" readonly>
            <button id="chooseFileBtn" type="button" class="choose_btn">Choose</button>
            <input id="realFileInput" type="file" accept="image/*" hidden>
          </div>
          <div class="preview_box mt-3 text-center">
            <img id="previewImage" class="img-fluid d-none rounded shadow-sm">
          </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
</div>
{{Form::close()}}

<script>
document.addEventListener('shown.bs.modal', function (event) {
  const modal = event.target;
  
  const chooseBtn = modal.querySelector("#chooseFileBtn");
  const fileInput = modal.querySelector("#realFileInput");
  const fileName = modal.querySelector("#fileName");
  const preview = modal.querySelector("#previewImage");

  if (!chooseBtn || !fileInput) return; // safety check

  // Reset on open
  fileName.value = "";
  preview.src = "";
  preview.classList.add("d-none");

  chooseBtn.onclick = () => {
    fileInput.value = "";
    fileInput.click();
  };

  fileInput.onchange = () => {
    const f = fileInput.files[0];
    if (!f) {
      fileName.value = "";
      preview.classList.add("d-none");
      return;
    }
    fileName.value = f.name;
    const r = new FileReader();
    r.onload = (e) => {
      preview.src = e.target.result;
      preview.classList.remove("d-none");
    };
    r.readAsDataURL(f);
  };
});
</script>


