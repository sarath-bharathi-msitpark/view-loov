<div class="modal-body p-0">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group bug_view_model">
                <b>{{ __('Title')}} :</b>
                <p class="m-0 p-0">{{$bug->title}}</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group bug_view_model">
                <b>{{ __('Priority')}} :</b>
                <p class="m-0 p-0">{{ucfirst($bug->priority)}}</p>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group bug_view_model">
                <b>{{ __('Created Date')}} :</b>
                <p class="m-0 p-0">{{$bug->created_at}}</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group bug_view_model">
                <b>{{ __('Assign to')}} :</b>
                <p class="m-0 p-0">{{(!empty($bug->assignTo)?$bug->assignTo->name:'')}}</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group bug_view_model">
                <b>{{ __('Description')}} :</b>
                <p class="m-0 p-0">{{$bug->description}}</p>
            </div>
        </div>
    </div>

    <!--<div class="row">-->
    <!--    <div class="col-12">-->
    <!--        <ul class="nav nav-tabs" id="myTab" role="tablist">-->
    <!--            <li class="nav-item mb-2">-->
    <!--                <a class="btn btn-outline-primary btn-sm ml-1 active show" data-bs-toggle="tab"-->
    <!--                   href="#profile" role="tab" aria-selected="false">{{__('Comments')}}</a>-->
    <!--            </li>-->
    <!--            <li class="nav-item mb-2">-->
    <!--                <a class="btn btn-outline-primary btn-sm ml-1" id="contact-tab" data-bs-toggle="tab" href="#contact"-->
    <!--                   role="tab" aria-controls="contact" aria-selected="false">{{__('Files')}}</a>-->
    <!--            </li>-->
    <!--        </ul>-->

    <!--        <div class="tab-content pt-4" id="myTabContent">-->
    <!--            <div class="tab-pane fade active show" id="profile" role="tabpanel" aria-labelledby="profile-tab">-->
    <!--                <div class="form-group m-0">-->
    <!--                    <form method="post" id="form-comment"-->
    <!--                          data-action="{{route('organization.bug.comment.store',[$bug->project_id,$bug->id])}}">-->
    <!--                        @csrf-->
    <!--                        <textarea class="form-control" name="comment" placeholder="{{ __('Write message')}}"-->
    <!--                                  id="example-textarea" rows="3" required></textarea>-->
    <!--                        <div class="text-end mt-1">-->
    <!--                            <div class="btn-group mb-2 ml-2 d-none d-sm-inline-block">-->
    <!--                                <button type="button"-->
    <!--                                        class="btn btn-primary btn-sm ml-1 text-white">{{ __('Submit')}}</button>-->
    <!--                            </div>-->
    <!--                        </div>-->
    <!--                    </form>-->
    <!--                    <div class="comment-holder" id="comments">-->
    <!--                        @foreach($bug->comments as $comment)-->
    <!--                            <div class="media">-->
    <!--                                <div class="media-body">-->
    <!--                                    <div class="d-flex justify-content-between align-items-end">-->
    <!--                                        <div>-->
    <!--                                            <h5 class="mt-0">{{(!empty($comment->user)?$comment->user->name:'')}}</h5>-->
    <!--                                            <p class="mb-0 text-xs">{{$comment->comment}}</p>-->
    <!--                                        </div>-->
    <!--                                        <a href="#" class="btn btn-sm red btn-danger delete-comment"-->
    <!--                                           data-url="{{route('organization.bug.comment.destroy',$comment->id)}}">-->
    <!--                                            <i class="ti ti-trash"></i>-->
    <!--                                        </a>-->

    <!--                                    </div>-->
    <!--                                </div>-->
    <!--                            </div>-->
    <!--                        @endforeach-->
    <!--                    </div>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--            <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">-->
    <!--                <div class="form-group m-0">-->
    <!--                    <form method="post" id="form-file" enctype="multipart/form-data"-->
    <!--                          data-url="{{ route('organization.bug.comment.file.store',$bug->id) }}">-->
    <!--                        @csrf-->
    <!--                        <div class="row">-->
    <!--                            <div class="col-6">-->
    <!--                                <div class="choose-file form-group">-->
    <!--                                    <label for="file" class="form-label">-->
    <!--                                        <div>{{__('file here')}}</div>-->
    <!--                                        <input type="file" class="form-control" name="file" id="file"-->
    <!--                                               data-filename="file_update">-->
    <!--                                    </label>-->
    <!--                                    <p class="file_update"></p>-->
    <!--                                </div>-->
    <!--                                <span class="invalid-feedback" id="file-error" role="alert"></span>-->
    <!--                            </div>-->
    <!--                            <div class="col-4">-->
    <!--                                <div class="btn-group  ml-2 mt-4 d-none d-sm-inline-block">-->
    <!--                                    <button type="submit"-->
    <!--                                            class="btn btn-primary btn-sm ml-1 text-white">{{ __('Upload')}}</button>-->
    <!--                                </div>-->
    <!--                            </div>-->
    <!--                        </div>-->
    <!--                    </form>-->
    <!--                    <div class="row mt-3" id="comments-file">-->
    <!--                        @foreach($bug->bugFiles as $file)-->
    <!--                            <div class="col-8 mb-2 file-{{$file->id}}">-->
    <!--                                <h5 class="mt-0 mb-1 font-weight-bold text-sm"> {{$file->name}}</h5>-->
    <!--                                <p class="m-0 text-xs">{{$file->file_size}}</p>-->
    <!--                            </div>-->
    <!--                            <div class="col-4 mb-2 file-{{$file->id}}">-->
    <!--                                <div class="comment-trash" style="float: right">-->
    <!--                                    <a download href="{{asset(Storage::url('bugs/'.$file->file))}}"-->
    <!--                                       class="btn btn-sm btn-primary me-1">-->
    <!--                                        <i class="ti ti-download"></i>-->
    <!--                                    </a>-->
    <!--                                    <a href="#" class="btn btn-sm red btn-danger delete-comment-file m-0 px-2"-->
    <!--                                       data-id="{{$file->id}}"-->
    <!--                                       data-url="{{route('organization.bug.comment.file.destroy',[$file->id])}}">-->
    <!--                                        <i class="ti ti-trash"></i>-->
    <!--                                    </a>-->
    <!--                                </div>-->
    <!--                            </div>-->
    <!--                        @endforeach-->
    <!--                    </div>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</div>-->
    
                                        <div class="row">
                                            <div class="col-12 gap-3 bottom_deviders mt-3">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h4><i class="fa-regular fa-comment me-3"></i>Activity</h4>
                                            </div>
                                            <div class="comment-box d-flex align-items-center px-3 py-2">
                                                <label class="circle-icon bg-light text-primary m-0" for="fileUpload">
                                                    <i class="fas fa-plus"></i>
                                                </label>
                                                <input type="file" id="fileUpload" class="d-none">

                                                <div class="d-flex align-items-center gap-2 ms-2">
                                                    <i class="far fa-smile emoji-icon fs-4"
                                                        style="cursor: pointer;"></i>
                                                    <i class="fas fa-at mention-icon fs-4" style="cursor: pointer;"></i>

                                                    <input type="text" class="comment-input flex-grow-1"
                                                        placeholder="Add Comments">
                                                </div>

                                                <div class="ms-auto">
                                                    <button class="send-btn">
                                                        <i class="fas fa-paper-plane"></i>
                                                    </button>
                                                </div>
                                            </div>


                                            <div class="comment-box d-flex align-items-center p-3 mb-2 mt-4">
                                                <img src="./assest/woman2.png" class="rounded-circle me-2" alt="avatar">
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">
                                                        <strong>Admin</strong>
                                                        <span class="badge bg-light text-primary border me-2"><i
                                                                class="fas fa-file-word me-1"></i>Reports</span>
                                                        <span class="text-muted">Lorem ipsum dolor sit amet,
                                                            consectetur adipiscing elit, sed do</span>
                                                    </p>
                                                    <p class="text-primary mb-1" style="font-size: 14px;">30 Apr
                                                        2025 10:55 pm</p>


                                                    <div class="edit-input d-none mt-2">
                                                        <textarea class="custom_textarea"
                                                            rows="1">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do</textarea>
                                                        <button class="btn btn-sm btn-primary mt-1">Update</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex gap-3">
                                                <a href="#"
                                                    class="text-decoration-none text-muted small edit-btn">Edit</a>
                                                <a href="#"
                                                    class="text-decoration-none text-muted small delete-btn">Delete</a>
                                            </div>
                                        </div>
                                        
                                            <div class="form-group  col-md-12 mt-3">
                                            <label class="fo    rm-label fw-bold">Upload Image<span class="text-danger">*</span></label>
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
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>


{{--<div class="modal-footer">--}}
{{--    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">--}}
{{--</div>--}}



    <script>
        document.getElementById('startDateCheck').addEventListener('change', function () {
            document.getElementById('startDateInput').disabled = !this.checked;
        });
    </script>

    <script>
        function findCommentBox(button) {
            // Traverse upwards to find the .comment-box from outside
            let parent = button.parentElement;
            while (parent && !parent.previousElementSibling?.classList?.contains('comment-box')) {
                parent = parent.parentElement;
            }
            return parent?.previousElementSibling || null;
        }

        // Reply toggle
        document.querySelectorAll('.reply-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const box = findCommentBox(btn);
                const reply = box?.querySelector('.reply-input');
                if (reply) reply.classList.toggle('d-none');
            });
        });

        // Edit toggle
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const box = findCommentBox(btn);
                const edit = box?.querySelector('.edit-input');
                if (edit) edit.classList.toggle('d-none');
            });
        });

        // Delete
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                if (confirm("Delete this comment?")) {
                    const box = findCommentBox(btn);
                    if (box) {
                        box.remove(); // remove comment
                        btn.closest('.d-flex').remove(); // remove edit/delete buttons
                    }
                }
            });
        });
    </script>


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
