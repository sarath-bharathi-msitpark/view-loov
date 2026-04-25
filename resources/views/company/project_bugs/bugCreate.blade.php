{{ Form::open([
    'route' => ['organization.task.bug.store', $project_id],
    'id'    => 'bug-create-form',
    'class' => 'needs-validation',
    'novalidate',
    'enctype' => 'multipart/form-data'
]) }}

<div class="modal-body">
    <div class="row g-3">

        <div class="form-group col-md-6">
            {{ Form::label('title', __('Title'), ['class' => 'form-label']) }}
            <x-required></x-required>
            {{ Form::text('title', '', ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Title')]) }}
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('priority', __('Priority'), ['class' => 'form-label']) }}
            <x-required></x-required>
            {!! Form::select('priority', $priority, null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('start_date', __('Start Date'), ['class' => 'form-label']) }}
            <x-required></x-required>
            {{ Form::date('start_date', '', ['class' => 'form-control', 'required' => 'required']) }}
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('due_date', __('Due Date'), ['class' => 'form-label']) }}
            <x-required></x-required>
            {{ Form::date('due_date', '', ['class' => 'form-control', 'required' => 'required']) }}
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('status', __('Bug Status'), ['class' => 'form-label']) }}
            <x-required></x-required>
            {!! Form::select('status', $status, null, ['class' => 'form-control', 'required' => 'required', 'id' => 'bug-status-sel']) !!}
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('assign_to', __('Assigned To'), ['class' => 'form-label']) }}
            <x-required></x-required>
            {{ Form::select('assign_to', $users, null, ['class' => 'form-control', 'required' => 'required', 'id' => 'bug-assign-sel']) }}
        </div>

        <div class="form-group col-md-12 editor_pad_remover">
            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
            <textarea class="form-control summernote-simple-2" name="description" rows="6" placeholder="{{ __('Enter Description') }}"></textarea>
        </div>

        {{-- ══ MULTI FILE UPLOAD ══ --}}
        <div class="form-group col-md-12">
            <label class="form-label fw-bold">
                {{ __('Attachments') }}
                <span class="text-muted fw-normal" style="font-size:.8rem;">({{ __('Optional · Multiple files allowed') }})</span>
            </label>

            <div id="bc-dropzone"
                 style="border:2px dashed #c8caed;border-radius:10px;padding:22px 16px;text-align:center;cursor:pointer;background:#f7f8fc;transition:border-color .15s,background .15s;">
                <div style="pointer-events:none;">
                    <i class="ti ti-cloud-upload" style="font-size:1.8rem;color:#4f52ff;display:block;margin-bottom:6px;"></i>
                    <p style="margin:0;font-size:.84rem;color:#7c7f9a;">{{ __('Click or drag & drop files here') }}</p>
                    <p style="margin:4px 0 0;font-size:.76rem;color:#aab0c8;">{{ __('Images, PDFs, Docs, Videos accepted') }}</p>
                </div>
            </div>

            <input type="file" id="bc-real-input" multiple style="display:none;">

            <div id="bc-preview-grid" style="display:none;margin-top:14px;gap:10px;flex-wrap:wrap;"></div>

            <div id="bc-count-bar" style="display:none;margin-top:8px;font-size:.78rem;color:#7c7f9a;align-items:center;justify-content:space-between;">
                <span id="bc-count-label"></span>
                <button type="button" id="bc-clear-all" style="background:none;border:none;color:#ff4f6a;font-size:.78rem;cursor:pointer;padding:0;">
                    {{ __('Remove all') }}
                </button>
            </div>
        </div>

    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <button type="button" id="bc-submit-btn" class="btn btn-primary">{{ __('Create') }}</button>
</div>

{{ Form::close() }}

<script>
(function () {

    /* ── Summernote init ── */
    if (typeof window.jQuery !== 'undefined' && typeof window.jQuery.fn.summernote !== 'undefined') {
        try {
            window.jQuery('.summernote-simple-2').summernote({
                height: 180,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link']],
                    ['view', ['codeview']]
                ],
                popover: { image: [], link: [], air: [] },
            });
        } catch(e) {}
    }

    /* ── stored files array ── */
    var storedFiles = [];

    var dropzone    = document.getElementById('bc-dropzone');
    var realInput   = document.getElementById('bc-real-input');
    var previewGrid = document.getElementById('bc-preview-grid');
    var countBar    = document.getElementById('bc-count-bar');
    var countLabel  = document.getElementById('bc-count-label');
    var clearAllBtn = document.getElementById('bc-clear-all');
    var submitBtn   = document.getElementById('bc-submit-btn');
    var form        = document.getElementById('bug-create-form');

    if (!dropzone || !realInput || !form) return;

    /* ── open file picker on dropzone click ── */
    dropzone.addEventListener('click', function () {
        realInput.value = '';
        realInput.click();
    });

    /* ── drag & drop ── */
    dropzone.addEventListener('dragover', function (e) {
        e.preventDefault();
        dropzone.style.borderColor = '#4f52ff';
        dropzone.style.background  = '#eeeeff';
    });
    dropzone.addEventListener('dragleave', function () {
        dropzone.style.borderColor = '#c8caed';
        dropzone.style.background  = '#f7f8fc';
    });
    dropzone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropzone.style.borderColor = '#c8caed';
        dropzone.style.background  = '#f7f8fc';
        addFiles(e.dataTransfer.files);
    });

    /* ── file picker change ── */
    realInput.addEventListener('change', function () {
        addFiles(realInput.files);
        realInput.value = '';
    });

    /* ── add files to storedFiles array ── */
    function addFiles(fileList) {
        for (var i = 0; i < fileList.length; i++) {
            storedFiles.push(fileList[i]);
        }
        renderPreviews();
    }

    /* ── render preview cards ── */
    function renderPreviews() {
        previewGrid.innerHTML = '';

        if (storedFiles.length === 0) {
            previewGrid.style.display = 'none';
            countBar.style.display    = 'none';
            return;
        }

        previewGrid.style.display = 'flex';
        countBar.style.display    = 'flex';
        countLabel.textContent    = storedFiles.length + ' ' + (storedFiles.length === 1 ? '{{ __("file selected") }}' : '{{ __("files selected") }}');

        storedFiles.forEach(function (file, index) {
            previewGrid.appendChild(makeCard(file, index));
        });
    }

    /* ── build one preview card ── */
    function makeCard(file, index) {
        var card = document.createElement('div');
        card.style.cssText = 'position:relative;width:100px;border:1px solid #e8eaf2;border-radius:8px;overflow:hidden;background:#fff;flex-shrink:0;';

        var removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.innerHTML = '&times;';
        removeBtn.style.cssText = 'position:absolute;top:3px;right:3px;width:18px;height:18px;border-radius:50%;background:rgba(18,20,42,.55);color:#fff;border:none;font-size:.75rem;cursor:pointer;display:flex;align-items:center;justify-content:center;z-index:2;';
        removeBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            storedFiles.splice(index, 1);
            renderPreviews();
        });

        var media = document.createElement('div');
        media.style.cssText = 'width:100%;height:70px;display:flex;align-items:center;justify-content:center;background:#f7f8fc;overflow:hidden;';

        var type = file.type ? file.type.toLowerCase() : '';
        var url  = URL.createObjectURL(file);

        if (type.indexOf('image/') === 0) {
            var img = document.createElement('img');
            img.src = url;
            img.style.cssText = 'width:100%;height:70px;object-fit:cover;';
            media.appendChild(img);
        } else if (type.indexOf('video/') === 0) {
            var vid = document.createElement('video');
            vid.src = url;
            vid.style.cssText = 'width:100%;height:70px;object-fit:cover;';
            media.appendChild(vid);
        } else {
            var iconMap = {
                'application/pdf': { icon: 'ti-file-type-pdf', color: '#e0003f' },
                'application/msword': { icon: 'ti-file-type-doc', color: '#185fa5' },
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document': { icon: 'ti-file-type-docx', color: '#185fa5' },
                'application/vnd.ms-excel': { icon: 'ti-file-type-xls', color: '#00c48c' },
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': { icon: 'ti-file-type-xlsx', color: '#00c48c' },
            };
            var ic = iconMap[type] || { icon: 'ti-file', color: '#7c7f9a' };
            media.innerHTML = '<i class="ti ' + ic.icon + '" style="font-size:2rem;color:' + ic.color + ';"></i>';
        }

        var nameEl = document.createElement('p');
        nameEl.textContent = file.name;
        nameEl.style.cssText = 'margin:0;padding:4px 5px;font-size:.62rem;color:#12142a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;text-align:center;border-top:1px solid #e8eaf2;';

        card.appendChild(removeBtn);
        card.appendChild(media);
        card.appendChild(nameEl);
        return card;
    }

    /* ── remove all ── */
    clearAllBtn.addEventListener('click', function () {
        storedFiles = [];
        renderPreviews();
    });

    /* ══ SUBMIT ══ */
    submitBtn.addEventListener('click', function () {

        /* Sync Summernote HTML content back to textarea */
        if (typeof window.jQuery !== 'undefined' && typeof window.jQuery.fn.summernote !== 'undefined') {
            try {
                window.jQuery('.summernote-simple-2').each(function () {
                    window.jQuery(this).val(window.jQuery(this).summernote('code'));
                });
            } catch(e) {}
        }

        /* Basic HTML5 validation */
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        var fd = new FormData(form);

        /* Remove any auto-added empty file inputs */
        fd.delete('files[]');
        fd.delete('files');

        /* Append each stored file as files[] */
        storedFiles.forEach(function (file) {
            fd.append('files[]', file, file.name);
        });

        /* Disable button to prevent double submit */
        submitBtn.disabled = true;
        submitBtn.textContent = '{{ __("Saving...") }}';

        fetch(form.action, {
            method: 'POST',
            body: fd,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        })
        .then(function (response) {
            return response.json().then(function (data) {
                return { status: response.status, data: data };
            });
        })
        .then(function (result) {
            if (result.data && result.data.success) {
                var modal = document.getElementById('commonModal');
                if (modal && typeof bootstrap !== 'undefined') {
                    var bsModal = bootstrap.Modal.getInstance(modal);
                    if (bsModal) bsModal.hide();
                }
                if (typeof show_toastr === 'function') {
                    show_toastr('{{ __("Success") }}', result.data.message || '{{ __("Bug created successfully.") }}', 'success');
                }
                setTimeout(function () { window.location.reload(); }, 800);
            } else {
                submitBtn.disabled = false;
                submitBtn.textContent = '{{ __("Create") }}';
                var msg = (result.data && result.data.error) ? result.data.error : '{{ __("Something went wrong. Please try again.") }}';
                if (typeof show_toastr === 'function') {
                    show_toastr('{{ __("Error") }}', msg, 'error');
                } else {
                    alert(msg);
                }
            }
        })
        .catch(function (err) {
            submitBtn.disabled = false;
            submitBtn.textContent = '{{ __("Create") }}';
            console.error('Bug create error:', err);
            if (typeof show_toastr === 'function') {
                show_toastr('{{ __("Error") }}', '{{ __("Something went wrong. Please try again.") }}', 'error');
            }
        });
    });

    /* ── select2 safely ── */
    if (typeof window.jQuery !== 'undefined' && typeof window.jQuery.fn.select2 !== 'undefined') {
        var $modal = window.jQuery('#commonModal');
        try { window.jQuery('#bug-status-sel').select2({ dropdownParent: $modal, width: '100%' }); } catch(e) {}
        try { window.jQuery('#bug-assign-sel').select2({ dropdownParent: $modal, width: '100%' }); } catch(e) {}
    }

})();
</script>