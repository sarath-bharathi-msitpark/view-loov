@php
    $priorityRaw = strtolower($bug->priority ?? '');
    $priClass    = match(true) {
        str_contains($priorityRaw, 'critical') => 'bvm-pri-critical',
        str_contains($priorityRaw, 'high')     => 'bvm-pri-high',
        str_contains($priorityRaw, 'medium')   => 'bvm-pri-medium',
        str_contains($priorityRaw, 'low')      => 'bvm-pri-low',
        default                                => 'bvm-pri-default',
    };
    $assignName = !empty($bug->assignTo) ? $bug->assignTo->name : null;
    $initials   = $assignName
        ? strtoupper(substr($assignName,0,1)) . (strpos($assignName,' ') !== false ? strtoupper(substr($assignName, strpos($assignName,' ')+1, 1)) : '')
        : '?';
@endphp

{{-- Data attributes carry all URLs — JS reads from here, no inline script needed --}}
<div class="bvm-body"
     data-bug-id="{{ $bug->id }}"
     data-upload-url="{{ route('organization.bug.comment.file.store', $bug->id) }}"
     data-comment-url="{{ route('organization.bug.comment.store', [$bug->project_id, $bug->id]) }}">

    {{-- ── HERO ── --}}
    <div class="bvm-hero">
        <h5 class="bvm-hero-title">{{ $bug->title }}</h5>
        <div class="bvm-hero-meta">
            <span class="bvm-pri-badge {{ $priClass }}">{{ ucfirst($bug->priority ?? 'No Priority') }}</span>
            @if($assignName)
            <span class="bvm-assign-chip">
                <span class="av">{{ $initials }}</span>
                {{ $assignName }}
            </span>
            @endif
            <span class="bvm-date-chip">
                <i class="ti ti-calendar"></i>
                {{ \Carbon\Carbon::parse($bug->created_at)->format('d M Y') }}
            </span>
        </div>
    </div>

    {{-- ── META GRID ── --}}
    <div class="bvm-meta-grid">
        <div class="bvm-meta-cell">
            <div class="bvm-meta-label"><i class="ti ti-tag"></i> Title</div>
            <p class="bvm-meta-value">{{ $bug->title }}</p>
        </div>
        <div class="bvm-meta-cell">
            <div class="bvm-meta-label"><i class="ti ti-flag"></i> Priority</div>
            <p class="bvm-meta-value">{{ ucfirst($bug->priority) }}</p>
        </div>
        <div class="bvm-meta-cell">
            <div class="bvm-meta-label"><i class="ti ti-calendar"></i> Created Date</div>
            <p class="bvm-meta-value">{{ \Carbon\Carbon::parse($bug->created_at)->format('d M Y, h:i A') }}</p>
        </div>
        <div class="bvm-meta-cell">
            <div class="bvm-meta-label"><i class="ti ti-user-check"></i> Assigned To</div>
            <p class="bvm-meta-value">{{ $assignName ?? '—' }}</p>
        </div>
    </div>

    @if(!empty($bug->description))
    <div class="bvm-desc-cell">
        <div class="bvm-meta-label" style="margin-bottom:6px;"><i class="ti ti-file-text"></i> Description</div>
<p class="bvm-desc-text">{!! $bug->description !!}</p>
    </div>
    @endif

    {{-- ── TABS ── --}}
    <ul class="bvm-tabs-bar">
        <li>
            <button class="bvm-tab-btn active" data-tab="bvm-comments">
                <i class="ti ti-message-circle"></i>
                {{ __('Comments') }}
                <span class="bvm-tab-count">{{ $bug->comments->count() }}</span>
            </button>
        </li>
        <li>
            <button class="bvm-tab-btn" data-tab="bvm-files" id="bvm-files-tab-btn">
                <i class="ti ti-paperclip"></i>
                {{ __('Files') }}
                <span class="bvm-tab-count" id="bvm-files-count">{{ $bug->bugFiles->count() }}</span>
            </button>
        </li>
    </ul>

    {{-- ── TAB CONTENT ── --}}
    <div class="bvm-tab-content">

        {{-- COMMENTS --}}
        <div class="bvm-tab-pane active" id="bvm-comments">
            <div class="bvm-comment-form-wrap" id="bvm-comment-form">
                <i class="ti ti-message"></i>
                <input type="text" id="bvm-comment-input" placeholder="{{ __('Write a comment...') }}">
                <button type="button" class="bvm-send-btn" id="bvm-comment-submit">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </div>

            <div id="bvm-comments-list">
                @forelse($bug->comments as $comment)
                    @php
                        $cUser   = $comment->createdBy;
                        $gender  = $cUser->employee->gender ?? null;
                        $profile = \App\Models\Utility::get_file($cUser->avatar);
                        $avatar  = ($gender === GENDER_MALE)
                            ? asset('assets/assestsnew/menimg.png')
                            : (($gender === GENDER_FEMALE)
                                ? asset('assets/assestsnew/femaile-report.svg')
                                : ($cUser->avatar ? $profile : asset('assets/assestsnew/menimg.png')));
                    @endphp
                    <div class="bvm-comment-item comment-box">
                        <img src="{{ $avatar }}" class="bvm-comment-avatar" alt="{{ $cUser->name }}">
                        <div class="bvm-comment-body">
                            <div class="bvm-comment-name">{{ $cUser->name }}</div>
                            <div class="bvm-comment-text">{{ $comment->comment }}</div>
                            <div class="bvm-comment-time"><i class="ti ti-clock"></i> {{ $comment->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="dropdown">
                            <button class="bvm-dot-btn" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical"></i></button>
                            <ul class="dropdown-menu dropdown-menu-end" style="z-index:99999;">
                                <li><a class="dropdown-item text-danger bvm-delete-comment" href="#" data-url="{{ route('organization.bug.comment.destroy', $comment->id) }}"><i class="ti ti-trash"></i> {{ __('Delete') }}</a></li>
                            </ul>
                        </div>
                    </div>
                @empty
                    <div class="bvm-empty" id="bvm-no-comments"><i class="ti ti-message-off"></i> {{ __('No comments yet.') }}</div>
                @endforelse
            </div>
        </div>

        {{-- FILES --}}
        <div class="bvm-tab-pane" id="bvm-files">

            <div class="bvm-files-header">
                <div class="bvm-files-title"><i class="ti ti-paperclip"></i> {{ __('Attachments') }}</div>
                <button class="bvm-add-file-btn" id="bvm-toggle-upload">
                    <i class="ti ti-plus"></i> {{ __('Add File') }}
                </button>
            </div>

            {{-- Upload area --}}
            <div class="bvm-upload-area" id="bvm-upload-area">
                <div class="bvm-upload-row">
                    <input type="file" id="bvm-file-input" class="form-control">
                    <button type="button" class="bvm-upload-confirm" id="bvm-upload-submit">
                        <i class="fa-solid fa-check" style="font-size:.78rem;"></i>
                    </button>
                </div>
                <div class="bvm-preview-wrap" id="bvm-preview-wrap">
                    <img   id="bvm-prev-img"   style="max-height:110px;display:none;" alt="">
                    <video id="bvm-prev-video" controls style="max-height:110px;display:none;"></video>
                    <audio id="bvm-prev-audio" controls style="display:none;"></audio>
                    <div   id="bvm-prev-generic" style="display:none;padding:10px;color:var(--text-muted);font-size:.82rem;text-align:center;">
                        <i class="ti ti-file" style="font-size:1.4rem;display:block;margin-bottom:4px;"></i>
                        <span id="bvm-prev-name"></span>
                    </div>
                </div>
            </div>

            {{-- File list --}}
            <div id="bvm-file-list">
                @forelse($bug->bugFiles as $file)
                    @php
                        $ext      = strtolower(ltrim($file->extension ?? '', '.'));
                        $fileUrl  = \App\Models\Utility::get_file($file->file);
                        $isImage  = in_array($ext, ['jpg','jpeg','png','gif','webp','svg']);
                        $isPdf    = $ext === 'pdf';
                        $isDoc    = in_array($ext, ['doc','docx']);
                        $isXls    = in_array($ext, ['xls','xlsx','csv']);
                        $isVideo  = in_array($ext, ['mp4','mov','webm','avi']);
                        $isAudio  = in_array($ext, ['mp3','wav','ogg']);
                        $iconClass = $isPdf  ? 'ti-file-type-pdf'
                                   : ($isDoc ? 'ti-file-type-doc'
                                   : ($isXls ? 'ti-file-type-xls'
                                   : ($isVideo ? 'ti-video'
                                   : ($isAudio ? 'ti-music' : 'ti-file'))));
                        $iconColor = $isPdf  ? '#e0003f'
                                   : ($isDoc ? '#185fa5'
                                   : ($isXls ? '#00c48c' : 'var(--accent)'));
                    @endphp
                    <div class="bvm-file-item bug-file" data-id="{{ $file->id }}">
                        <div class="bvm-file-icon">
                           <a href="{{ $fileUrl }}" target="_blank" rel="noopener" class="bvm-file-icon" style="text-decoration:none;">
                                @if($isImage)
                                    <img class="thumb" src="{{ $fileUrl }}" alt="{{ $file->name }}">
                                @else
                                    <i class="ti {{ $iconClass }}" style="color:{{ $iconColor }};font-size:1rem;"></i>
                                @endif
                            </a>
                        </div>
                        <div class="bvm-file-info">
                            <a href="{{ $fileUrl }}" target="_blank" rel="noopener" class="bvm-file-name" title="{{ $file->name }}" style="text-decoration:none;color:inherit;display:block;">{{ $file->name }}</a>
                            <span class="bvm-file-meta">
                                <i class="ti ti-clock"></i>
                                {{ \Carbon\Carbon::parse($file->created_at)->format('d M Y, h:i A') }}
                                @if(!empty($file->file_size)) &bull; {{ $file->file_size }} @endif
                            </span>
                        </div>
                        <div class="dropdown">
                            <button class="bvm-dot-btn" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical"></i></button>
                            <ul class="dropdown-menu dropdown-menu-end" style="z-index:99999;">
                                <li><a class="dropdown-item" href="{{ $fileUrl }}" target="_blank"><i class="ti ti-eye"></i> {{ __('View') }}</a></li>
                                <li><a class="dropdown-item" href="{{ $fileUrl }}" download="{{ $file->name }}"><i class="ti ti-download"></i> {{ __('Download') }}</a></li>
                                <li><a class="dropdown-item text-danger bvm-delete-file" href="#" data-url="{{ route('organization.bug.comment.file.destroy', $file->id) }}"><i class="ti ti-trash"></i> {{ __('Delete') }}</a></li>
                            </ul>
                        </div>
                    </div>
                @empty
                    <div class="bvm-empty" id="bvm-no-files"><i class="ti ti-paperclip-off"></i> {{ __('No files attached yet.') }}</div>
                @endforelse
            </div>
        </div>

    </div>{{-- end bvm-tab-content --}}
</div>{{-- end bvm-body --}}