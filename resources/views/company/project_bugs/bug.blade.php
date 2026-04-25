@extends('company.layouts.company')
@section('page-title') {{__('Manage Bug Report')}} @endsection
@section('page-icon') {{ asset('assets/assestsnew/project_bug1.svg') }} @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('organization.projects.index')}}">{{__('Project')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('organization.projects.show',$project->id)}}">{{ucwords($project->project_name)}}</a></li>
    <li class="breadcrumb-item">{{__('Bug Report')}}</li>
@endsection

@section('action-btn')
    <div class="float-end d-flex gap-2 align-items-center">
        <a href="{{ route('organization.task.bug.kanban',$project->id) }}" data-bs-toggle="tooltip" title="{{__('Grid View')}}" class="br-icon-btn">
            <i class="ti ti-layout-grid" style="color:unset"></i>
        </a>
        <a href="#" data-size="lg" data-url="{{ route('organization.task.bug.create',$project->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create New Bug')}}" class="br-add-btn">
            <i class="ti ti-plus"></i><span>{{__('New Bug')}}</span>
        </a>
    </div>
@endsection

@push('css-page')
    <link rel="stylesheet" href="{{ asset('css/summernote/summernote-bs4.css') }}">
@endpush

@push('script-page')
    <script src="{{ asset('css/summernote/summernote-bs4.js') }}"></script>
    <script>
        $(document).on('shown.bs.modal', '#commonModal', function () {
            if (typeof $.fn.summernote !== 'undefined') {
                $(this).find('.summernote-simple-2').each(function () {
                    if (!$(this).data('summernote-initialized')) {
                        $(this).summernote({
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
                        $(this).data('summernote-initialized', true);
                    }
                });
            }
        });
    </script>
@endpush

@section('content')
@include('company.layouts.partials.nav')

<style>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@500;600;700&family=DM+Sans:wght@300;400;500&display=swap');
:root{--accent:#4f52ff;--accent-soft:#eeeeff;--accent-glow:rgba(79,82,255,.16);--danger:#ff4f6a;--danger-soft:#fff0f3;--success:#00c48c;--success-soft:#e6faf5;--warn:#ff9d0a;--warn-soft:#fff8ec;--surface:#ffffff;--surface-2:#f7f8fc;--border:#e8eaf2;--text-pri:#12142a;--text-muted:#7c7f9a;--row-hover:#f4f5ff;--shadow:0 2px 20px rgba(18,20,42,.07);--radius:14px;--radius-sm:8px;--radius-xs:6px}
.br-page{padding:24px 0 56px;font-family:'DM Sans',sans-serif}

/* ══ STATUS COUNT CARDS ══ */
.br-status-cards{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:16px}
.br-sc-card{display:flex;align-items:center;gap:12px;padding:14px 20px;background:var(--sc-bg);border:1.5px solid var(--sc-border);border-radius:var(--radius-sm);cursor:pointer;transition:all .18s;min-width:130px;flex:1;position:relative;overflow:hidden;user-select:none}
.br-sc-card::after{content:'';position:absolute;inset:0;background:var(--sc-text,#4f52ff);opacity:0;transition:opacity .13s;pointer-events:none}
.br-sc-card:hover::after{opacity:.05}
.br-sc-card.active-card{border-color:var(--sc-text);box-shadow:0 0 0 3px color-mix(in srgb,var(--sc-text) 18%,transparent)}
.br-sc-card.active-card::after{opacity:.07}
.br-sc-dot{width:10px;height:10px;border-radius:50%;background:var(--sc-dot);flex-shrink:0;box-shadow:0 0 0 3px color-mix(in srgb,var(--sc-dot) 22%,transparent)}
.br-sc-info{display:flex;flex-direction:column;gap:1px}
.br-sc-label{font-size:.72rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.08em;white-space:nowrap}
.br-sc-count{font-family:'Syne',sans-serif;font-size:1.5rem;font-weight:700;color:var(--sc-text);line-height:1.1}
.br-sc-total{--sc-bg:#eeeeff;--sc-border:rgba(79,82,255,.22);--sc-dot:#4f52ff;--sc-text:#4f52ff}

/* ══ FILTER BAR ══ */
.br-filter-bar{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow);padding:14px 18px;display:flex;align-items:flex-start;gap:10px;flex-wrap:wrap;margin-bottom:16px}
.br-search-wrap{display:flex;align-items:center;flex:1;min-width:200px;max-width:320px;background:var(--surface-2);border:1px solid var(--border);border-radius:50px;padding:7px 14px;gap:8px;transition:border-color .15s,box-shadow .15s}
.br-search-wrap:focus-within{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-glow);background:var(--surface)}
.br-search-wrap i{color:var(--text-muted);font-size:.85rem;flex-shrink:0}
.br-search-input{border:none;background:transparent;font-family:'DM Sans',sans-serif;font-size:.85rem;color:var(--text-pri);outline:none;width:100%}
.br-search-input::placeholder{color:var(--text-muted)}
.br-ms-wrap{position:relative;min-width:150px}
.br-ms-trigger{display:flex;align-items:center;gap:8px;background:var(--surface-2);border:1px solid var(--border);border-radius:50px;padding:7px 14px;font-size:.84rem;font-family:'DM Sans',sans-serif;color:var(--text-pri);cursor:pointer;transition:border-color .15s,box-shadow .15s;user-select:none;white-space:nowrap}
.br-ms-trigger:hover{border-color:#c5c9df}
.br-ms-trigger.open{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-glow);background:var(--surface)}
.br-ms-trigger i.chevron{margin-left:auto;font-size:.7rem;color:var(--text-muted);transition:transform .15s}
.br-ms-trigger.open i.chevron{transform:rotate(180deg)}
.br-ms-badge{background:var(--accent);color:#fff;border-radius:50%;width:18px;height:18px;display:inline-flex;align-items:center;justify-content:center;font-size:.65rem;font-weight:700;flex-shrink:0}
.br-ms-dropdown{position:absolute;top:calc(100% + 6px);left:0;z-index:999;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-sm);box-shadow:0 8px 24px rgba(18,20,42,.12);min-width:200px;overflow:hidden;display:none;flex-direction:column}
.br-ms-dropdown.open{display:flex}
.br-ms-search-wrap{padding:10px 12px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:7px}
.br-ms-search-wrap i{color:var(--text-muted);font-size:.78rem}
.br-ms-search{border:none;background:transparent;outline:none;font-family:'DM Sans',sans-serif;font-size:.82rem;color:var(--text-pri);width:100%}
.br-ms-list{max-height:220px;overflow-y:auto;padding:6px 0}
.br-ms-item{display:flex;align-items:center;gap:9px;padding:8px 14px;cursor:pointer;transition:background .1s;font-size:.84rem;color:var(--text-pri)}
.br-ms-item:hover{background:var(--row-hover)}
.br-ms-checkbox{width:16px;height:16px;border-radius:4px;border:1.5px solid var(--border);display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .12s;background:var(--surface)}
.br-ms-item.selected .br-ms-checkbox{background:var(--accent);border-color:var(--accent)}
.br-ms-item.selected .br-ms-checkbox::after{content:'';width:9px;height:5px;border-left:2px solid #fff;border-bottom:2px solid #fff;transform:rotate(-45deg) translate(1px,-1px);display:block}
.br-ms-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0}
.br-ms-footer{padding:8px 12px;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:8px}
.br-ms-clear-btn{font-size:.78rem;color:var(--text-muted);cursor:pointer;background:none;border:none;padding:0;font-family:'DM Sans',sans-serif}
.br-ms-clear-btn:hover{color:var(--danger)}
.br-ms-apply-btn{font-size:.78rem;color:#fff;cursor:pointer;background:var(--accent);border:none;padding:5px 14px;border-radius:50px;font-family:'DM Sans',sans-serif;font-weight:600}
.br-reset-btn{display:inline-flex;align-items:center;gap:6px;padding:7px 16px;border-radius:50px;border:1px solid var(--border);background:var(--surface);font-size:.83rem;font-family:'DM Sans',sans-serif;color:var(--text-muted);cursor:pointer}
.br-reset-btn i{font-size:.8rem}
.br-filter-spacer{flex:1}
.br-active-chips{display:flex;flex-wrap:wrap;gap:6px;width:100%;margin-top:4px}
.br-chip{display:inline-flex;align-items:center;gap:5px;padding:3px 10px 3px 8px;background:var(--accent-soft);border:1px solid rgba(79,82,255,.2);border-radius:50px;font-size:.75rem;color:var(--accent);font-weight:500}
.br-chip-remove{cursor:pointer;opacity:.6;font-size:.7rem}

/* ══ TABLE CARD ══ */
.br-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden}
.br-show-row{display:flex;align-items:center;gap:8px;padding:13px 22px;border-bottom:1px solid var(--border);font-size:.83rem;color:var(--text-muted)}
.br-entries-select{appearance:none;background:var(--surface-2);border:1px solid var(--border);border-radius:var(--radius-xs);padding:4px 24px 4px 8px;font-size:.82rem;font-family:'DM Sans',sans-serif;color:var(--text-pri);cursor:pointer;outline:none}
.br-table{width:100%;border-collapse:collapse;font-size:.875rem}
.br-table thead th{padding:11px 16px;font-family:'Syne',sans-serif;font-size:.67rem;font-weight:700;letter-spacing:.09em;text-transform:uppercase;color:var(--text-muted);background:var(--surface-2);border-bottom:1px solid var(--border);white-space:nowrap}
.br-table thead th.col-num{width:46px;text-align:center}
.br-table thead th:last-child{text-align:center}
.br-table tbody tr{border-bottom:1px solid var(--border);transition:background .13s}
.br-table tbody tr:last-child{border-bottom:none}
.br-table tbody tr:hover{background:var(--row-hover)}
.br-table tbody td{padding:14px 16px;vertical-align:middle;color:var(--text-pri)}
.br-table tbody td.col-num{text-align:center;color:var(--text-muted);font-size:.8rem;width:46px}
.br-table tbody td:last-child{text-align:center}
.bug-id-chip{display:inline-flex;align-items:center;gap:5px;background:var(--accent-soft);color:var(--accent);border:1px solid rgba(79,82,255,.18);border-radius:var(--radius-xs);padding:3px 9px;font-family:'Syne',sans-serif;font-size:.72rem;font-weight:700;cursor:pointer;transition:all .15s;text-decoration:none}
.bug-id-chip:hover{background:var(--accent);color:#fff;box-shadow:0 2px 10px var(--accent-glow)}
.assignee-cell{display:flex;align-items:center;gap:9px}
.assignee-av{width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,var(--accent),#8486ff);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.68rem;font-weight:700;flex-shrink:0}
.assignee-name{font-size:.84rem;font-weight:500}
.bug-title-cell{font-weight:500;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;display:block}
.date-cell{color:var(--text-muted);font-size:.82rem;white-space:nowrap}
.status-badge{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:50px;font-size:.72rem;font-weight:600;white-space:nowrap}
.status-badge::before{content:'';width:6px;height:6px;border-radius:50%;display:inline-block}
.status-open{background:var(--warn-soft);color:var(--warn)}.status-open::before{background:var(--warn)}
.status-in-progress{background:var(--accent-soft);color:var(--accent)}.status-in-progress::before{background:var(--accent)}
.status-resolved{background:var(--success-soft);color:var(--success)}.status-resolved::before{background:var(--success)}
.status-closed{background:#f0f0f5;color:#888}.status-closed::before{background:#aaa}
.status-default{background:var(--surface-2);color:var(--text-muted)}.status-default::before{background:var(--text-muted)}
.priority-badge{display:inline-flex;align-items:center;gap:5px;padding:3px 9px;border-radius:var(--radius-xs);font-size:.72rem;font-weight:600}
.priority-critical{background:#fff0f3;color:#e0003f}.priority-high{background:var(--danger-soft);color:var(--danger)}.priority-medium{background:var(--warn-soft);color:var(--warn)}.priority-low{background:var(--success-soft);color:var(--success)}.priority-default{background:var(--surface-2);color:var(--text-muted)}
.creator-cell{font-size:.82rem;color:var(--text-muted);display:flex;align-items:center;gap:5px}
.file-badge-wrap{display:flex;align-items:center;gap:5px;flex-wrap:wrap}
.file-badge-main{display:inline-flex;align-items:center;gap:5px;padding:3px 9px;border-radius:var(--radius-xs);background:var(--surface-2);border:1px solid var(--border);font-size:.75rem;color:var(--text-muted);white-space:nowrap}
.file-more-badge{display:inline-flex;align-items:center;justify-content:center;padding:3px 8px;border-radius:var(--radius-xs);background:var(--accent-soft);border:1px solid rgba(79,82,255,.18);font-size:.72rem;font-weight:700;color:var(--accent);cursor:pointer}
.file-more-badge:hover{background:var(--accent);color:#fff}
.action-wrap{display:flex;align-items:center;justify-content:center;gap:5px}
.act-btn{width:30px;height:30px;border-radius:var(--radius-xs);display:inline-flex;align-items:center;justify-content:center;border:1px solid var(--border);background:var(--surface);color:var(--text-muted);transition:all .13s;text-decoration:none}
.act-btn i{font-size:.78rem}
.act-btn.edit-btn:hover{background:var(--accent-soft);border-color:rgba(79,82,255,.22);color:var(--accent)}
.act-btn.del-btn:hover{background:var(--danger-soft);border-color:rgba(255,79,106,.18);color:var(--danger)}
.br-empty{padding:60px 20px;text-align:center;color:var(--text-muted)}
.br-empty i{font-size:2.2rem;opacity:.25;display:block;margin-bottom:10px}
.br-empty p{font-size:.88rem;margin:0}
.br-bottom-bar{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;padding:14px 22px;border-top:1px solid var(--border);background:var(--surface)}
.br-showing{font-size:.82rem;color:var(--text-muted);white-space:nowrap}
.br-showing strong{color:var(--text-pri)}
.br-pag{display:flex;align-items:center;gap:3px;list-style:none;margin:0;padding:0}
.br-pag li a,.br-pag li span{display:inline-flex;align-items:center;justify-content:center;min-width:34px;height:34px;padding:0 6px;border-radius:var(--radius-xs);border:1px solid var(--border);background:var(--surface);font-size:.82rem;color:var(--text-muted);text-decoration:none;cursor:pointer}
.br-pag li a:hover{background:var(--accent-soft);border-color:rgba(79,82,255,.22);color:var(--accent)}
.br-pag li.active_pagination a,.br-pag li.active_pagination span{background:var(--accent);border-color:var(--accent);color:#fff;font-weight:600}
.br-pag li.disabled a,.br-pag li.disabled span{opacity:.35;cursor:not-allowed;pointer-events:none}
.br-right-controls{display:flex;align-items:center;gap:10px;font-size:.82rem;color:var(--text-muted)}
.br-rows-select,.br-entries-select{appearance:none;background:var(--surface-2);border:1px solid var(--border);border-radius:var(--radius-xs);padding:5px 24px 5px 8px;font-size:.82rem;font-family:'DM Sans',sans-serif;color:var(--text-pri);cursor:pointer;outline:none}
.br-goto-input{width:52px;text-align:center;border:1px solid var(--border);border-radius:var(--radius-xs);padding:5px 6px;font-size:.82rem;font-family:'DM Sans',sans-serif;color:var(--text-pri);background:var(--surface-2);outline:none}
.br-go-btn{padding:5px 16px;border-radius:var(--radius-xs);background:var(--accent);color:#fff;border:none;font-size:.82rem;font-family:'DM Sans',sans-serif;font-weight:600;cursor:pointer;box-shadow:0 2px 8px var(--accent-glow)}
.br-icon-btn{display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:var(--radius-xs);border:1px solid var(--border);background:var(--surface);color:var(--text-muted);text-decoration:none}
.br-icon-btn:hover{background:var(--accent-soft);color:var(--accent)}
.br-add-btn{display:inline-flex;align-items:center;gap:7px;padding:7px 16px;border-radius:var(--radius-xs);background:var(--accent);color:#fff;font-size:.84rem;font-weight:600;font-family:'DM Sans',sans-serif;text-decoration:none;box-shadow:0 2px 10px var(--accent-glow)}
.br-add-btn:hover{opacity:.9;color:#fff}

/* ══ CUSTOM BUG MODAL ══ */
.bug-modal-overlay{position:fixed;inset:0;z-index:99999;background:rgba(18,20,42,.55);display:flex;align-items:center;justify-content:center;padding:20px;opacity:0;pointer-events:none;transition:opacity .2s}
.bug-modal-overlay.show{opacity:1;pointer-events:all}
.bug-modal-box{background:var(--surface);border-radius:var(--radius);box-shadow:0 20px 60px rgba(18,20,42,.25);width:100%;max-width:760px;max-height:88vh;overflow:hidden;display:flex;flex-direction:column;transform:translateY(20px);transition:transform .2s}
.bug-modal-overlay.show .bug-modal-box{transform:translateY(0)}
.bug-modal-header{padding:16px 22px 12px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-shrink:0}
.bug-modal-header h5{font-family:'Syne',sans-serif;font-size:.95rem;font-weight:700;margin:0;color:var(--text-pri)}
.bug-modal-close-btn{width:28px;height:28px;border-radius:var(--radius-xs);border:1px solid var(--border);background:var(--surface);display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--text-muted);font-size:.75rem}
.bug-modal-close-btn:hover{background:var(--danger-soft);border-color:rgba(255,79,106,.2);color:var(--danger)}
.bug-modal-scroll{overflow-y:auto;flex:1}

/* ══ BUG SHOW CONTENT ══ */
.bvm-body{font-family:'DM Sans',sans-serif;color:var(--text-pri)}
.bvm-hero{background:var(--surface);border-bottom:1px solid var(--border);padding:18px 24px 14px}
.bvm-hero-title{font-family:'Syne',sans-serif;font-size:.95rem;font-weight:700;margin:0 0 9px;display:flex;align-items:center;gap:8px}
.bvm-hero-title::before{content:'';width:8px;height:8px;border-radius:50%;background:var(--accent);flex-shrink:0;box-shadow:0 0 0 3px var(--accent-glow)}
.bvm-hero-meta{display:flex;flex-wrap:wrap;gap:6px;align-items:center}
.bvm-pri-badge{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:50px;font-size:.72rem;font-weight:600;border:1px solid transparent}
.bvm-pri-badge::before{content:'';width:5px;height:5px;border-radius:50%;display:inline-block}
.bvm-pri-critical{background:var(--danger-soft);color:var(--danger);border-color:rgba(255,79,106,.18)}.bvm-pri-critical::before{background:var(--danger)}
.bvm-pri-high{background:#fff4f0;color:#e05a2b;border-color:rgba(224,90,43,.18)}.bvm-pri-high::before{background:#e05a2b}
.bvm-pri-medium{background:var(--warn-soft);color:var(--warn);border-color:rgba(255,157,10,.18)}.bvm-pri-medium::before{background:var(--warn)}
.bvm-pri-low{background:var(--success-soft);color:var(--success);border-color:rgba(0,196,140,.18)}.bvm-pri-low::before{background:var(--success)}
.bvm-pri-default{background:var(--surface-2);color:var(--text-muted);border-color:var(--border)}.bvm-pri-default::before{background:var(--text-muted)}
.bvm-assign-chip{display:inline-flex;align-items:center;gap:6px;padding:3px 10px 3px 4px;background:var(--accent-soft);border:1px solid rgba(79,82,255,.18);border-radius:50px;color:var(--accent);font-size:.75rem;font-weight:500}
.bvm-assign-chip .av{width:20px;height:20px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;font-size:.6rem;font-weight:700;color:#fff}
.bvm-date-chip{display:inline-flex;align-items:center;gap:5px;font-size:.73rem;color:var(--text-muted);padding:3px 9px;background:var(--surface-2);border:1px solid var(--border);border-radius:50px}
.bvm-meta-grid{display:grid;grid-template-columns:1fr 1fr}
.bvm-meta-cell{padding:12px 24px;border-right:1px solid var(--border);border-bottom:1px solid var(--border)}
.bvm-meta-cell:nth-child(even){border-right:none}
.bvm-meta-cell:nth-last-child(-n+2){border-bottom:none}
.bvm-meta-label{font-size:.68rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--text-muted);margin-bottom:4px;display:flex;align-items:center;gap:5px}
.bvm-meta-value{font-size:.875rem;color:var(--text-pri);font-weight:500;margin:0}
.bvm-desc-cell{padding:12px 24px;border-bottom:1px solid var(--border)}
.bvm-desc-text{font-size:.875rem;color:var(--text-pri);line-height:1.65;margin:0}
.bvm-tabs-bar{display:flex;border-bottom:1px solid var(--border);background:var(--surface);padding:0 24px;list-style:none;margin:0}
.bvm-tab-btn{display:inline-flex;align-items:center;gap:7px;padding:11px 16px;font-family:'DM Sans',sans-serif;font-size:.83rem;font-weight:500;color:var(--text-muted);border:none;background:transparent;border-bottom:2px solid transparent;margin-bottom:-1px;cursor:pointer;transition:color .15s,border-color .15s;white-space:nowrap}
.bvm-tab-btn.active,.bvm-tab-btn:hover{color:var(--accent);border-bottom-color:var(--accent)}
.bvm-tab-count{background:var(--surface-2);color:var(--text-muted);border-radius:50px;padding:1px 7px;font-size:.68rem;font-weight:700}
.bvm-tab-btn.active .bvm-tab-count{background:var(--accent);color:#fff}
.bvm-tab-content{background:var(--surface-2);padding:16px 24px 20px;min-height:160px}
.bvm-tab-pane{display:none}
.bvm-tab-pane.active{display:block}
.bvm-comment-form-wrap{display:flex;align-items:center;gap:10px;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:8px 8px 8px 14px;box-shadow:var(--shadow);margin-bottom:14px}
.bvm-comment-form-wrap i{color:var(--text-muted);font-size:.85rem;flex-shrink:0}
.bvm-comment-form-wrap input{flex:1;border:none;background:transparent;font-family:'DM Sans',sans-serif;font-size:.875rem;color:var(--text-pri);outline:none}
.bvm-comment-form-wrap input::placeholder{color:var(--text-muted)}
.bvm-send-btn{width:34px;height:34px;border-radius:var(--radius-xs);background:var(--accent);border:none;color:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;box-shadow:0 2px 8px var(--accent-glow)}
.bvm-send-btn i{font-size:.78rem;color:#fff!important}
.bvm-comment-item{display:flex;align-items:flex-start;gap:10px;padding:12px 14px;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-sm);margin-bottom:8px}
.bvm-comment-avatar{width:32px;height:32px;border-radius:50%;object-fit:cover;flex-shrink:0;border:2px solid var(--border)}
.bvm-comment-body{flex:1;min-width:0}
.bvm-comment-name{font-size:.82rem;font-weight:600;color:var(--text-pri);margin-bottom:3px}
.bvm-comment-text{font-size:.84rem;color:#3a3d5c;line-height:1.55;word-break:break-word}
.bvm-comment-time{display:flex;align-items:center;gap:4px;font-size:.72rem;color:var(--accent);margin-top:4px}
.bvm-dot-btn{background:transparent;border:none;padding:4px 6px;border-radius:var(--radius-xs);color:var(--text-muted);cursor:pointer;line-height:1}
.bvm-dot-btn:hover{background:var(--surface-2)}
.bvm-files-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}
.bvm-files-title{font-family:'Syne',sans-serif;font-size:.85rem;font-weight:700;color:var(--text-pri);display:flex;align-items:center;gap:7px}
.bvm-files-title i{color:var(--accent)}
.bvm-add-file-btn{display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:var(--radius-xs);background:var(--accent-soft);color:var(--accent);border:1px solid rgba(79,82,255,.2);font-size:.78rem;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif}
.bvm-add-file-btn:hover{background:var(--accent);color:#fff}
.bvm-upload-area{background:var(--surface);border:2px dashed var(--border);border-radius:var(--radius-sm);padding:14px;margin-bottom:14px;display:none}
.bvm-upload-area.open{display:block}
.bvm-upload-row{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.bvm-upload-area .form-control{flex:1;min-width:160px;border:1px solid var(--border);border-radius:var(--radius-xs);font-size:.83rem;background:var(--surface-2);padding:6px 10px;outline:none}
.bvm-upload-confirm{width:36px;height:36px;border-radius:var(--radius-xs);background:var(--success);border:none;color:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0}
.bvm-preview-wrap{margin-top:10px;text-align:center;background:var(--surface-2);border-radius:var(--radius-xs);padding:8px;border:1px solid var(--border);display:none}
.bvm-preview-wrap.show{display:block}
.bvm-file-item{display:flex;align-items:center;gap:12px;padding:11px 14px;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-sm);margin-bottom:8px}
.bvm-file-item:hover{box-shadow:var(--shadow)}
.bvm-file-icon{width:38px;height:38px;border-radius:var(--radius-xs);background:var(--accent-soft);display:flex;align-items:center;justify-content:center;flex-shrink:0;overflow:hidden;cursor:pointer;text-decoration:none}
.bvm-file-icon img.thumb{width:38px;height:38px;object-fit:cover}
.bvm-file-icon i{color:var(--accent);font-size:.9rem}
.bvm-file-info{flex:1;min-width:0}
.bvm-file-name{font-size:.84rem;font-weight:600;color:var(--text-pri);margin:0 0 2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;cursor:pointer;text-decoration:none;display:block}
.bvm-file-name:hover{color:var(--accent);text-decoration:underline}
.bvm-file-meta{font-size:.72rem;color:var(--text-muted);display:flex;align-items:center;gap:6px}
.bvm-empty{text-align:center;padding:28px 16px;color:var(--text-muted);font-size:.84rem}
.bvm-empty i{font-size:1.6rem;opacity:.22;display:block;margin-bottom:8px}
.dropdown-menu{z-index:999999!important}
</style>

<div class="br-page">

    {{-- ══ STATUS COUNT CARDS ══ --}}
    @php
        $cardColorMap = [
            'open'     => ['bg'=>'#fff8ec','border'=>'rgba(255,157,10,.25)', 'dot'=>'#ff9d0a','text'=>'#ff9d0a'],
            'progress' => ['bg'=>'#eeeeff','border'=>'rgba(79,82,255,.25)',  'dot'=>'#4f52ff','text'=>'#4f52ff'],
            'resolv'   => ['bg'=>'#e6faf5','border'=>'rgba(0,196,140,.25)',  'dot'=>'#00c48c','text'=>'#00c48c'],
            'close'    => ['bg'=>'#f4f4f8','border'=>'rgba(170,170,170,.3)', 'dot'=>'#aaa',   'text'=>'#888888'],
        ];
        $defaultCardColor = ['bg'=>'#f0f0ff','border'=>'rgba(136,132,255,.25)','dot'=>'#8884ff','text'=>'#6c6fff'];
    @endphp

    <div class="br-status-cards" id="br-status-cards">
        @foreach($bugStatus as $bs)
            @php
                $tl  = strtolower($bs->title);
                $col = $defaultCardColor;
                foreach($cardColorMap as $key => $c) {
                    if(str_contains($tl, $key)) { $col = $c; break; }
                }
                $cnt = $statusCounts[$bs->id] ?? 0;
            @endphp
            <div class="br-sc-card"
                 data-status-filter="{{ $tl }}"
                 style="--sc-bg:{{ $col['bg'] }};--sc-border:{{ $col['border'] }};--sc-dot:{{ $col['dot'] }};--sc-text:{{ $col['text'] }};">
                <div class="br-sc-dot"></div>
                <div class="br-sc-info">
                    <span class="br-sc-label">{{ $bs->title }}</span>
                    <span class="br-sc-count">{{ $cnt }}</span>
                </div>
            </div>
        @endforeach

        {{-- Total card --}}
        <div class="br-sc-card br-sc-total" data-status-filter="__all__">
            <div class="br-sc-dot"></div>
            <div class="br-sc-info">
                <span class="br-sc-label">{{ __('Total') }}</span>
                <span class="br-sc-count" id="br-sc-total-count">{{ $bugs->count() }}</span>
            </div>
        </div>
    </div>

    {{-- ══ FILTER BAR ══ --}}
    <div class="br-filter-bar">
        <div class="br-search-wrap">
            <i class="ti ti-search"></i>
            <input type="text" id="br-search" class="br-search-input" placeholder="Search by title or bug ID…" autocomplete="off">
        </div>

        <div class="br-ms-wrap">
            <div class="br-ms-trigger" id="ms-assignee-trigger"><i class="ti ti-user"></i><span>Assigned To</span><span class="br-ms-badge" id="ms-assignee-count" style="display:none;"></span><i class="ti ti-chevron-down chevron"></i></div>
            <div class="br-ms-dropdown" id="ms-assignee-dropdown">
                <div class="br-ms-search-wrap"><i class="ti ti-search"></i><input type="text" class="br-ms-search" id="ms-assignee-search" placeholder="Search user…"></div>
                <div class="br-ms-list" id="ms-assignee-list">
                    @php $assigneeList = $bugs->map(fn($b) => !empty($b->assignTo) ? $b->assignTo->name : null)->filter()->unique()->sort()->values(); @endphp
                    @foreach($assigneeList as $name)
                    <div class="br-ms-item" data-value="{{ strtolower($name) }}" data-label="{{ $name }}">
                        <div class="br-ms-checkbox"></div>
                        <div class="assignee-av" style="width:20px;height:20px;font-size:.58rem;">{{ strtoupper(substr($name,0,1)) }}{{ strpos($name,' ')!==false ? strtoupper(substr($name,strpos($name,' ')+1,1)) : '' }}</div>
                        <span>{{ $name }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="br-ms-footer"><button class="br-ms-clear-btn" data-target="assignee">Clear</button><button class="br-ms-apply-btn" data-target="assignee">Apply</button></div>
            </div>
        </div>

        <div class="br-ms-wrap">
            <div class="br-ms-trigger" id="ms-priority-trigger"><i class="ti ti-flag"></i><span>Priority</span><span class="br-ms-badge" id="ms-priority-count" style="display:none;"></span><i class="ti ti-chevron-down chevron"></i></div>
            <div class="br-ms-dropdown" id="ms-priority-dropdown">
                <div class="br-ms-list" id="ms-priority-list">
                    <div class="br-ms-item" data-value="critical" data-label="Critical"><div class="br-ms-checkbox"></div><span class="br-ms-dot" style="background:#e0003f;"></span>Critical</div>
                    <div class="br-ms-item" data-value="high" data-label="High"><div class="br-ms-checkbox"></div><span class="br-ms-dot" style="background:#ff4f6a;"></span>High</div>
                    <div class="br-ms-item" data-value="medium" data-label="Medium"><div class="br-ms-checkbox"></div><span class="br-ms-dot" style="background:#ff9d0a;"></span>Medium</div>
                    <div class="br-ms-item" data-value="low" data-label="Low"><div class="br-ms-checkbox"></div><span class="br-ms-dot" style="background:#00c48c;"></span>Low</div>
                </div>
                <div class="br-ms-footer"><button class="br-ms-clear-btn" data-target="priority">Clear</button><button class="br-ms-apply-btn" data-target="priority">Apply</button></div>
            </div>
        </div>

        <div class="br-ms-wrap">
            <div class="br-ms-trigger" id="ms-status-trigger"><i class="ti ti-circle-check"></i><span>Status</span><span class="br-ms-badge" id="ms-status-count" style="display:none;"></span><i class="ti ti-chevron-down chevron"></i></div>
            <div class="br-ms-dropdown" id="ms-status-dropdown">
                <div class="br-ms-list" id="ms-status-list">
                    @foreach($bugStatus as $bs)
                        @php
                            $titleLower = strtolower($bs->title);
                            $dotColor = match(true) {
                                str_contains($titleLower, 'open')     => '#ff9d0a',
                                str_contains($titleLower, 'progress') => '#4f52ff',
                                str_contains($titleLower, 'resolv')   => '#00c48c',
                                str_contains($titleLower, 'close')    => '#aaa',
                                default                               => '#8884ff',
                            };
                        @endphp
                        <div class="br-ms-item" data-value="{{ $titleLower }}" data-label="{{ $bs->title }}">
                            <div class="br-ms-checkbox"></div>
                            <span class="br-ms-dot" style="background:{{ $dotColor }};"></span>
                            {{ $bs->title }}
                        </div>
                    @endforeach
                </div>
                <div class="br-ms-footer"><button class="br-ms-clear-btn" data-target="status">Clear</button><button class="br-ms-apply-btn" data-target="status">Apply</button></div>
            </div>
        </div>

        <span class="br-filter-spacer"></span>
        <button class="br-reset-btn" id="br-reset-btn"><i class="ti ti-refresh"></i> Reset</button>
        <div class="br-active-chips" id="br-active-chips" style="display:none;"></div>
    </div>

    {{-- ══ TABLE CARD ══ --}}
    <div class="br-card">
        <div class="br-show-row">
            <span>Show</span>
            <select class="br-entries-select" id="br-per-page"><option value="5">5</option><option value="10">10</option><option value="20">20</option><option value="50">50</option></select>
            <span>entries</span>
        </div>
        <div class="table-responsive">
            <table class="br-table">
                <thead><tr>
                    <th class="col-num">#</th>
                    <th>{{__('Bug ID')}}</th>
                    <th>{{__('Assigned To')}}</th>
                    <th>{{__('Bug Title')}}</th>
                    <th>{{__('Start Date')}}</th>
                    <th>{{__('Due Date')}}</th>
                    <th>{{__('Status')}}</th>
                    <th>{{__('Priority')}}</th>
                    <th>{{__('Files')}}</th>
                    <th>{{__('Created By')}}</th>
                    <th>{{__('Actions')}}</th>
                </tr></thead>
                <tbody id="br-tbody">
                @forelse($bugs as $bug)
                    @php
                        $statusTitle = strtolower(!empty($bug->bug_status) ? $bug->bug_status->title : '');
                        $statusClass = match(true){
                            str_contains($statusTitle,'open')     => 'status-open',
                            str_contains($statusTitle,'progress') => 'status-in-progress',
                            str_contains($statusTitle,'resolv')   => 'status-resolved',
                            str_contains($statusTitle,'close')    => 'status-closed',
                            default                               => 'status-default'
                        };
                        $priorityRaw   = strtolower($bug->priority ?? '');
                        $priorityClass = match(true){
                            str_contains($priorityRaw,'critical') => 'priority-critical',
                            str_contains($priorityRaw,'high')     => 'priority-high',
                            str_contains($priorityRaw,'medium')   => 'priority-medium',
                            str_contains($priorityRaw,'low')      => 'priority-low',
                            default                               => 'priority-default'
                        };
                        $assignName = !empty($bug->assignTo) ? $bug->assignTo->name : '';
                        $initials   = $assignName
                            ? strtoupper(substr($assignName,0,1)).(strpos($assignName,' ')!==false ? strtoupper(substr($assignName,strpos($assignName,' ')+1,1)) : '')
                            : '?';
                        $fileCount = $bug->bugFiles ? $bug->bugFiles->count() : 0;
                    @endphp
                    <tr data-title="{{ strtolower($bug->title) }}"
                        data-assign="{{ strtolower($assignName) }}"
                        data-status="{{ $statusTitle }}"
                        data-priority="{{ $priorityRaw }}"
                        data-bugid="{{ strtolower($bug->task_uid) }}">
                        <td class="col-num br-row-num"></td>
                        <td>
                            <a href="#" class="bug-id-chip open-bug-modal" data-bug-id="{{ $bug->id }}" data-project-id="{{ $project->id }}">
                                <i class="ti ti-bug"></i> {{ $bug->task_uid }}
                            </a>
                        </td>
                        <td>
                            @if($assignName)
                                <div class="assignee-cell">
                                    <div class="assignee-av">{{ $initials }}</div>
                                    <span class="assignee-name">{{ $assignName }}</span>
                                </div>
                            @else
                                <span style="color:var(--text-muted);">—</span>
                            @endif
                        </td>
                        <td><span class="bug-title-cell" title="{{ $bug->title }}">{{ $bug->title }}</span></td>
                        <td><span class="date-cell"><i class="ti ti-calendar"></i> {{ Auth::user()->dateFormat($bug->start_date) }}</span></td>
                        <td><span class="date-cell"><i class="ti ti-calendar-due"></i> {{ Auth::user()->dateFormat($bug->due_date) }}</span></td>
                        <td><span class="status-badge {{ $statusClass }}">{{ !empty($bug->bug_status) ? $bug->bug_status->title : '—' }}</span></td>
                        <td><span class="priority-badge {{ $priorityClass }}">{{ ucfirst($bug->priority ?? '—') }}</span></td>
                        <td>
                            @if($fileCount === 0)
                                <span style="color:var(--text-muted);font-size:.8rem;">—</span>
                            @elseif($fileCount === 1)
                                @php $f = $bug->bugFiles->first(); @endphp
                                <div class="file-badge-wrap">
                                    <a href="{{ \App\Models\Utility::get_file($f->file) }}" target="_blank" class="file-badge-main">
                                        <i class="ti ti-paperclip"></i>
                                        <span style="max-width:100px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $f->name }}</span>
                                    </a>
                                </div>
                            @else
                                @php $ff = $bug->bugFiles->first(); @endphp
                                <div class="file-badge-wrap">
                                    <a href="{{ \App\Models\Utility::get_file($ff->file) }}" target="_blank" class="file-badge-main">
                                        <i class="ti ti-paperclip"></i>
                                        <span style="max-width:80px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $ff->name }}</span>
                                    </a>
                                    <span class="file-more-badge open-bug-modal" data-bug-id="{{ $bug->id }}" data-project-id="{{ $project->id }}" data-open-tab="files">+{{ $fileCount - 1 }}</span>
                                </div>
                            @endif
                        </td>
                        <td><span class="creator-cell"><i class="ti ti-user"></i> {{ $bug->createdBy->name }}</span></td>
                        <td class="Action">
                            <div class="action-wrap">
                                <a href="#" class="act-btn edit-btn copy_com"
                                   data-url="{{ route('organization.task.bug.edit',[$project->id,$bug->id]) }}"
                                   data-ajax-popup="true" data-size="xl"
                                   data-bs-toggle="tooltip" title="{{__('Edit')}}"
                                   data-title="{{__('Edit Bug')}}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                {!! Form::open(['method'=>'DELETE','route'=>['organization.task.bug.destroy',$project->id,$bug->id],'style'=>'display:inline']) !!}
                                <a href="#" class="act-btn del-btn bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                                {!! Form::close() !!}
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr id="br-server-empty">
                        <td colspan="11">
                            <div class="br-empty"><i class="ti ti-bug-off"></i><p>{{__('No bugs reported yet.')}}</p></div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="br-bottom-bar">
            <span class="br-showing">Showing <strong id="br-show-from">–</strong>–<strong id="br-show-to">–</strong> of <strong id="br-show-total">–</strong> entries</span>
            <ul class="br-pag" id="br-pag-list"></ul>
            <div class="br-right-controls">
                <span>Rows:</span>
                <select class="br-rows-select" id="br-per-page-2"><option value="5">5</option><option value="10">10</option><option value="20">20</option><option value="50">50</option></select>
                <span>Go to</span>
                <input type="number" min="1" class="br-goto-input" id="br-goto-input" placeholder="—">
                <button class="br-go-btn" id="br-go-btn">Go</button>
            </div>
        </div>
    </div>
</div>

{{-- ══ CUSTOM BUG DETAIL MODAL ══ --}}
<div class="bug-modal-overlay" id="bug-detail-modal">
    <div class="bug-modal-box">
        <div class="bug-modal-header">
            <h5 id="bm-modal-title">Bug Details</h5>
            <button class="bug-modal-close-btn" id="bug-modal-close-btn"><i class="ti ti-x"></i></button>
        </div>
        <div class="bug-modal-scroll" id="bm-modal-scroll">
            <div style="text-align:center;padding:50px;color:var(--text-muted);">
                <i class="ti ti-loader" style="font-size:2rem;"></i>
            </div>
        </div>
    </div>
</div>

<script>
(function(){

/* ══ URL MAP ══ */
const bugShowUrls = {
    @foreach($bugs as $bug)
    '{{ $bug->id }}': '{{ route('organization.task.bug.show', [$project->id, $bug->id]) }}',
    @endforeach
};

/* ══ TABLE SETUP ══ */
const tbody = document.getElementById('br-tbody');
if(!tbody) return;
const allRows = Array.from(tbody.querySelectorAll('tr[data-title]'));

let perPage = 5, currentPage = 1, filteredRows = [...allRows];
const msState = { assignee: new Set(), priority: new Set(), status: new Set() };

/* ══ ACTIVE CARD FILTER STATE ══ */
let activeCardStatus = ''; // '' = show all

/* ══ STATUS CARD CLICK ══ */
document.querySelectorAll('.br-sc-card').forEach(card => {
    card.addEventListener('click', function() {
        const sf = this.dataset.statusFilter;
        if(sf === '__all__') {
            // Total card clicked — clear card filter
            activeCardStatus = '';
            document.querySelectorAll('.br-sc-card').forEach(c => c.classList.remove('active-card'));
        } else if(sf === activeCardStatus) {
            // Same card clicked again — deselect
            activeCardStatus = '';
            document.querySelectorAll('.br-sc-card').forEach(c => c.classList.remove('active-card'));
        } else {
            activeCardStatus = sf;
            document.querySelectorAll('.br-sc-card').forEach(c => c.classList.remove('active-card'));
            this.classList.add('active-card');
        }
        applyFilters();
    });
});

/* ══ MULTI-SELECT DROPDOWNS ══ */
['assignee','priority','status'].forEach(key => {
    const trigger  = document.getElementById('ms-'+key+'-trigger');
    const dropdown = document.getElementById('ms-'+key+'-dropdown');
    const countEl  = document.getElementById('ms-'+key+'-count');
    const list     = document.getElementById('ms-'+key+'-list');
    const srch     = document.getElementById('ms-'+key+'-search');
    if(!trigger || !dropdown) return;

    trigger.addEventListener('click', e => {
        e.stopPropagation();
        const open = dropdown.classList.contains('open');
        closeAllDD();
        if(!open){ dropdown.classList.add('open'); trigger.classList.add('open'); if(srch) srch.focus(); }
    });

    if(srch) srch.addEventListener('input', () => {
        const q = srch.value.toLowerCase();
        list.querySelectorAll('.br-ms-item').forEach(i => {
            i.style.display = (i.dataset.label||'').toLowerCase().includes(q) ? '' : 'none';
        });
    });

    list.addEventListener('click', e => {
        const item = e.target.closest('.br-ms-item');
        if(!item) return;
        const v = item.dataset.value;
        msState[key].has(v) ? msState[key].delete(v) : msState[key].add(v);
        item.classList.toggle('selected', msState[key].has(v));
        updateBadge(key, countEl);
    });

    const footer = dropdown.querySelector('.br-ms-footer');
    if(footer){
        footer.querySelector('.br-ms-clear-btn').addEventListener('click', () => {
            msState[key].clear();
            list.querySelectorAll('.br-ms-item').forEach(i => i.classList.remove('selected'));
            updateBadge(key, countEl);
        });
        footer.querySelector('.br-ms-apply-btn').addEventListener('click', () => {
            closeAllDD();
            applyFilters();
        });
    }
});

function closeAllDD(){
    ['assignee','priority','status'].forEach(k => {
        const d = document.getElementById('ms-'+k+'-dropdown');
        const t = document.getElementById('ms-'+k+'-trigger');
        if(d) d.classList.remove('open');
        if(t) t.classList.remove('open');
    });
}
document.addEventListener('click', closeAllDD);
document.querySelectorAll('.br-ms-dropdown').forEach(d => d.addEventListener('click', e => e.stopPropagation()));

function updateBadge(key, el){
    const n = msState[key].size;
    if(el){ el.textContent = n; el.style.display = n ? 'inline-flex' : 'none'; }
}

/* ══ APPLY FILTERS (includes card filter) ══ */
function applyFilters(){
    const q = (document.getElementById('br-search').value || '').toLowerCase().trim();
    filteredRows = allRows.filter(r => {
        const mQ = !q
            || (r.dataset.title  || '').includes(q)
            || (r.dataset.assign || '').includes(q)
            || (r.dataset.bugid  || '').includes(q);
        const mA = msState.assignee.size === 0 || [...msState.assignee].some(v => (r.dataset.assign   || '').includes(v));
        const mP = msState.priority.size === 0 || [...msState.priority].some(v => (r.dataset.priority || '').includes(v));
        const mS = msState.status.size   === 0 || [...msState.status].some(v   => (r.dataset.status   || '').includes(v));
        const mC = !activeCardStatus || (r.dataset.status || '').includes(activeCardStatus);
        return mQ && mA && mP && mS && mC;
    });
    currentPage = 1;
    renderChips();
    updateStatusCards();
    render();
}

/* ══ UPDATE STATUS CARD COUNTS AFTER FILTER ══ */
function updateStatusCards(){
    document.querySelectorAll('.br-sc-card[data-status-filter]').forEach(card => {
        const sf      = card.dataset.statusFilter;
        const countEl = card.querySelector('.br-sc-count');
        if(!countEl) return;
        if(sf === '__all__'){
            countEl.textContent = filteredRows.length;
            return;
        }
        const cnt = filteredRows.filter(r => (r.dataset.status || '').includes(sf)).length;
        countEl.textContent = cnt;
    });
}

/* ══ ACTIVE FILTER CHIPS ══ */
function renderChips(){
    const wrap = document.getElementById('br-active-chips');
    if(!wrap) return;
    wrap.innerHTML = '';
    let has = false;
    const cols = {
        assignee: { bg:'#eeeeff', border:'rgba(79,82,255,.2)',  color:'#4f52ff' },
        priority: { bg:'#fff8ec', border:'rgba(255,157,10,.2)', color:'#ff9d0a' },
        status:   { bg:'#e6faf5', border:'rgba(0,196,140,.2)',  color:'#00c48c' },
    };
    ['assignee','priority','status'].forEach(key => {
        msState[key].forEach(val => {
            has = true;
            const item  = document.querySelector('#ms-'+key+'-list .br-ms-item[data-value="'+val+'"]');
            const label = item ? item.dataset.label : val;
            const c     = cols[key];
            const chip  = document.createElement('span');
            chip.className = 'br-chip';
            chip.style.cssText = 'background:'+c.bg+';border-color:'+c.border+';color:'+c.color+';';
            chip.innerHTML = '<i class="ti ti-'+(key==='assignee'?'user':key==='priority'?'flag':'circle-check')+'" style="font-size:.7rem;"></i> '
                + label
                + ' <span class="br-chip-remove" data-key="'+key+'" data-val="'+val+'">✕</span>';
            wrap.appendChild(chip);
        });
    });
    wrap.style.display = has ? 'flex' : 'none';
    wrap.querySelectorAll('.br-chip-remove').forEach(btn => {
        btn.addEventListener('click', () => {
            const k = btn.dataset.key, v = btn.dataset.val;
            msState[k].delete(v);
            const item = document.querySelector('#ms-'+k+'-list .br-ms-item[data-value="'+v+'"]');
            if(item) item.classList.remove('selected');
            updateBadge(k, document.getElementById('ms-'+k+'-count'));
            applyFilters();
        });
    });
}

/* ══ RENDER TABLE ROWS ══ */
function render(){
    allRows.forEach(r => r.style.display = 'none');
    const total    = filteredRows.length;
    const totalPgs = Math.max(1, Math.ceil(total / perPage));
    if(currentPage > totalPgs) currentPage = totalPgs;
    const start = (currentPage - 1) * perPage;
    const end   = Math.min(start + perPage, total);

    filteredRows.slice(start, end).forEach((row, i) => {
        row.style.display = '';
        const nc = row.querySelector('.br-row-num');
        if(nc) nc.textContent = start + i + 1;
    });

    const sf = document.getElementById('br-show-from');
    const st = document.getElementById('br-show-to');
    const so = document.getElementById('br-show-total');
    if(sf) sf.textContent = total ? start + 1 : 0;
    if(st) st.textContent = end;
    if(so) so.textContent = total;

    renderPagination(totalPgs);

    // JS empty row
    const emptyEl = tbody.querySelector('.br-js-empty');
    if(!total){
        if(!emptyEl){
            const tr = document.createElement('tr');
            tr.className = 'br-js-empty';
            tr.innerHTML = '<td colspan="11"><div class="br-empty"><i class="ti ti-search-off"></i><p>No bugs match your filters.</p></div></td>';
            tbody.appendChild(tr);
        } else {
            emptyEl.style.display = '';
        }
    } else if(emptyEl){
        emptyEl.style.display = 'none';
    }
}

/* ══ PAGINATION ══ */
function renderPagination(t){
    const pl = document.getElementById('br-pag-list');
    if(!pl) return;
    pl.innerHTML = '';
    const mk = (html, p, dis, act) => {
        const li = document.createElement('li');
        if(dis) li.classList.add('disabled');
        if(act) li.classList.add('active_pagination');
        const a = document.createElement('a');
        a.href = '#';
        a.innerHTML = html;
        if(!dis) a.addEventListener('click', e => { e.preventDefault(); currentPage = p; render(); });
        li.appendChild(a);
        return li;
    };
    pl.appendChild(mk('&#171;', 1, currentPage === 1));
    pl.appendChild(mk('<i class="fa-solid fa-chevron-left" style="font-size:.6rem;"></i>', currentPage - 1, currentPage === 1));
    let s = Math.max(1, currentPage - 2), e = Math.min(t, s + 4);
    if(e - s < 4) s = Math.max(1, e - 4);
    for(let p = s; p <= e; p++) pl.appendChild(mk(p, p, false, p === currentPage));
    pl.appendChild(mk('<i class="fa-solid fa-chevron-right" style="font-size:.6rem;"></i>', currentPage + 1, currentPage === t));
    pl.appendChild(mk('&#187;', t, currentPage === t));
}

function syncPerPage(v){
    perPage = parseInt(v);
    const p1 = document.getElementById('br-per-page');
    const p2 = document.getElementById('br-per-page-2');
    if(p1) p1.value = v;
    if(p2) p2.value = v;
    currentPage = 1;
    render();
}

/* ══ SEARCH ══ */
const srchEl = document.getElementById('br-search');
if(srchEl) srchEl.addEventListener('input', applyFilters);

/* ══ RESET ══ */
const rstBtn = document.getElementById('br-reset-btn');
if(rstBtn) rstBtn.addEventListener('click', () => {
    if(srchEl) srchEl.value = '';
    ['assignee','priority','status'].forEach(k => {
        msState[k].clear();
        document.querySelectorAll('#ms-'+k+'-list .br-ms-item').forEach(i => i.classList.remove('selected'));
        updateBadge(k, document.getElementById('ms-'+k+'-count'));
    });
    activeCardStatus = '';
    document.querySelectorAll('.br-sc-card').forEach(c => c.classList.remove('active-card'));
    applyFilters();
});

/* ══ PER PAGE SELECTS ══ */
const p1 = document.getElementById('br-per-page');
const p2 = document.getElementById('br-per-page-2');
if(p1) p1.addEventListener('change', e => syncPerPage(e.target.value));
if(p2) p2.addEventListener('change', e => syncPerPage(e.target.value));

/* ══ GOTO PAGE ══ */
const goBtn  = document.getElementById('br-go-btn');
const gotoIn = document.getElementById('br-goto-input');
if(goBtn) goBtn.addEventListener('click', () => {
    const t  = Math.max(1, Math.ceil(filteredRows.length / perPage));
    const pg = parseInt(gotoIn.value);
    if(!isNaN(pg)){ currentPage = Math.min(Math.max(1, pg), t); render(); }
    if(gotoIn) gotoIn.value = '';
});
if(gotoIn) gotoIn.addEventListener('keydown', e => { if(e.key === 'Enter' && goBtn) goBtn.click(); });

/* ══ INITIAL RENDER ══ */
if(allRows.length) { applyFilters(); }

/* ══════════════════════════════════════════
   CUSTOM MODAL OPEN / CLOSE
══════════════════════════════════════════ */
const modal       = document.getElementById('bug-detail-modal');
const modalScroll = document.getElementById('bm-modal-scroll');
const modalTitle  = document.getElementById('bm-modal-title');
const closeBtn    = document.getElementById('bug-modal-close-btn');

function openBugModal(bugId, openTab){
    const url = bugShowUrls[String(bugId)];
    if(!url) return;
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
    modalTitle.textContent = 'Loading...';
    modalScroll.innerHTML = '<div style="text-align:center;padding:50px;color:var(--text-muted);"><i class="ti ti-loader" style="font-size:2rem;"></i></div>';

    fetch(url, { headers:{ 'X-Requested-With':'XMLHttpRequest', 'Accept':'text/html' } })
    .then(r => r.text())
    .then(html => {
        modalScroll.innerHTML = html;

        const hero = modalScroll.querySelector('.bvm-hero-title');
        if(hero) modalTitle.textContent = hero.textContent.trim().replace(/^\./,'').trim();

        const body      = modalScroll.querySelector('.bvm-body');
        const uploadUrl  = body ? body.dataset.uploadUrl  : '';
        const commentUrl = body ? body.dataset.commentUrl : '';

        /* TAB SWITCHING */
        modalScroll.querySelectorAll('.bvm-tab-btn').forEach(btn => {
            btn.addEventListener('click', function(){
                modalScroll.querySelectorAll('.bvm-tab-btn').forEach(b => b.classList.remove('active'));
                modalScroll.querySelectorAll('.bvm-tab-pane').forEach(p => p.classList.remove('active'));
                btn.classList.add('active');
                const pane = modalScroll.querySelector('#' + btn.dataset.tab);
                if(pane) pane.classList.add('active');
            });
        });

        if(openTab === 'files'){
            setTimeout(() => {
                const fb = modalScroll.querySelector('#bvm-files-tab-btn,[data-tab="bvm-files"]');
                if(fb) fb.click();
            }, 80);
        }

        /* TOGGLE UPLOAD */
        const toggleBtn  = modalScroll.querySelector('#bvm-toggle-upload');
        const uploadArea = modalScroll.querySelector('#bvm-upload-area');
        if(toggleBtn && uploadArea) toggleBtn.addEventListener('click', () => uploadArea.classList.toggle('open'));

        /* FILE PREVIEW */
        const fileInput   = modalScroll.querySelector('#bvm-file-input');
        const prevWrap    = modalScroll.querySelector('#bvm-preview-wrap');
        const prevImg     = modalScroll.querySelector('#bvm-prev-img');
        const prevVideo   = modalScroll.querySelector('#bvm-prev-video');
        const prevAudio   = modalScroll.querySelector('#bvm-prev-audio');
        const prevGeneric = modalScroll.querySelector('#bvm-prev-generic');
        const prevName    = modalScroll.querySelector('#bvm-prev-name');
        function hideAllPrev(){ [prevImg,prevVideo,prevAudio,prevGeneric].forEach(el => { if(el) el.style.display = 'none'; }); }
        if(fileInput){
            fileInput.addEventListener('change', function(){
                const file = this.files[0];
                if(!file){ if(prevWrap) prevWrap.classList.remove('show'); return; }
                const url  = URL.createObjectURL(file);
                const type = file.type.toLowerCase();
                hideAllPrev();
                if(prevWrap) prevWrap.classList.add('show');
                if(type.startsWith('image/'))     { if(prevImg)   { prevImg.src   = url; prevImg.style.display   = 'block'; } }
                else if(type.startsWith('video/')){ if(prevVideo) { prevVideo.src = url; prevVideo.style.display = 'block'; } }
                else if(type.startsWith('audio/')){ if(prevAudio) { prevAudio.src = url; prevAudio.style.display = 'block'; } }
                else { if(prevName) prevName.textContent = file.name; if(prevGeneric) prevGeneric.style.display = 'block'; }
            });
        }

        /* FILE UPLOAD */
        const uploadSubmit = modalScroll.querySelector('#bvm-upload-submit');
        if(uploadSubmit && uploadUrl){
            uploadSubmit.addEventListener('click', function(){
                const file = fileInput ? fileInput.files[0] : null;
                if(!file){ show_toastr('Error','Please select a file!','error'); return; }
                const fd = new FormData();
                fd.append('file', file);
                fd.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                uploadSubmit.disabled = true;
                uploadSubmit.innerHTML = '<i class="fa fa-spinner fa-spin" style="font-size:.78rem;"></i>';
                fetch(uploadUrl, { method:'POST', body:fd, headers:{ 'X-Requested-With':'XMLHttpRequest', 'Accept':'application/json' } })
                .then(r => r.json())
                .then(data => {
                    if(data.error){ show_toastr('Error', data.error, 'error'); return; }
                    const ext    = (data.extension || '').replace('.','').toLowerCase();
                    const isImg  = ['jpg','jpeg','png','gif','webp','svg'].includes(ext);
                    let iconHtml = isImg
                        ? '<img class="thumb" src="'+data.file_url+'" style="width:38px;height:38px;object-fit:cover;" alt="">'
                        : (function(){
                            let ic = 'ti-file', col = 'var(--accent)';
                            if(ext==='pdf'){ ic='ti-file-type-pdf'; col='#e0003f'; }
                            else if(['doc','docx'].includes(ext)){ ic='ti-file-type-doc'; col='#185fa5'; }
                            else if(['xls','xlsx','csv'].includes(ext)){ ic='ti-file-type-xls'; col='#00c48c'; }
                            else if(['mp4','mov','webm'].includes(ext)) ic='ti-video';
                            else if(['mp3','wav'].includes(ext)) ic='ti-music';
                            return '<i class="ti '+ic+'" style="color:'+col+';font-size:1rem;"></i>';
                          })();

                    const newItem =
                        '<div class="bvm-file-item bug-file" data-id="'+data.id+'">'
                        + '<a href="'+data.file_url+'" target="_blank" rel="noopener" class="bvm-file-icon">'+iconHtml+'</a>'
                        + '<div class="bvm-file-info">'
                        + '<a href="'+data.file_url+'" target="_blank" rel="noopener" class="bvm-file-name">'+data.name+'</a>'
                        + '<span class="bvm-file-meta"><i class="ti ti-clock"></i> Uploaded just now'+(data.file_size?' &bull; '+data.file_size:'')+'</span>'
                        + '</div>'
                        + '<div class="dropdown"><button class="bvm-dot-btn" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical"></i></button>'
                        + '<ul class="dropdown-menu dropdown-menu-end" style="z-index:999999;">'
                        + '<li><a class="dropdown-item" href="'+data.file_url+'" target="_blank"><i class="ti ti-eye"></i> View</a></li>'
                        + '<li><a class="dropdown-item" href="'+data.file_url+'" download="'+data.name+'"><i class="ti ti-download"></i> Download</a></li>'
                        + '<li><a class="dropdown-item text-danger bvm-delete-file" href="#" data-url="'+data.deleteUrl+'"><i class="ti ti-trash"></i> Delete</a></li>'
                        + '</ul></div></div>';

                    const noFiles  = modalScroll.querySelector('#bvm-no-files');
                    if(noFiles) noFiles.remove();
                    const fileList = modalScroll.querySelector('#bvm-file-list');
                    if(fileList) fileList.insertAdjacentHTML('afterbegin', newItem);
                    const countEl  = modalScroll.querySelector('#bvm-files-count');
                    if(countEl) countEl.textContent = parseInt(countEl.textContent || 0) + 1;
                    if(fileInput) fileInput.value = '';
                    hideAllPrev();
                    if(prevWrap)  prevWrap.classList.remove('show');
                    if(uploadArea) uploadArea.classList.remove('open');
                    show_toastr('Success','File uploaded successfully!','success');
                })
                .catch(() => show_toastr('Error','File upload failed!','error'))
                .finally(() => {
                    uploadSubmit.disabled = false;
                    uploadSubmit.innerHTML = '<i class="fa-solid fa-check" style="font-size:.78rem;"></i>';
                });
            });
        }

        /* COMMENT SUBMIT */
        const commentInput  = modalScroll.querySelector('#bvm-comment-input');
        const commentSubmit = modalScroll.querySelector('#bvm-comment-submit');
        if(commentSubmit && commentUrl){
            commentSubmit.addEventListener('click', function(){
                const text = (commentInput ? commentInput.value : '').trim();
                if(!text){ show_toastr('Error','Please write a comment!','error'); return; }
                commentSubmit.disabled = true;
                commentSubmit.innerHTML = '<i class="fa fa-spinner fa-spin" style="font-size:.78rem;"></i>';
                const fd = new FormData();
                fd.append('comment', text);
                fd.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                fetch(commentUrl, { method:'POST', body:fd, headers:{ 'X-Requested-With':'XMLHttpRequest', 'Accept':'application/json' } })
                .then(r => r.json())
                .then(data => {
                    if(!data.is_success){ show_toastr('Error','Something went wrong!','error'); return; }
                    const newComment =
                        '<div class="bvm-comment-item comment-box">'
                        + '<img src="'+data.data.avatar+'" class="bvm-comment-avatar" alt="'+data.data.name+'">'
                        + '<div class="bvm-comment-body">'
                        + '<div class="bvm-comment-name">'+data.data.name+'</div>'
                        + '<div class="bvm-comment-text">'+data.data.comment+'</div>'
                        + '<div class="bvm-comment-time"><i class="ti ti-clock"></i> just now</div>'
                        + '</div>'
                        + '<div class="dropdown"><button class="bvm-dot-btn" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical"></i></button>'
                        + '<ul class="dropdown-menu dropdown-menu-end" style="z-index:999999;">'
                        + '<li><a class="dropdown-item text-danger bvm-delete-comment" href="#" data-url="'+data.data.deleteUrl+'"><i class="ti ti-trash"></i> Delete</a></li>'
                        + '</ul></div></div>';
                    const list  = modalScroll.querySelector('#bvm-comments-list');
                    const empty = list ? list.querySelector('.bvm-empty') : null;
                    if(empty) empty.remove();
                    if(list) list.insertAdjacentHTML('afterbegin', newComment);
                    if(commentInput) commentInput.value = '';
                    show_toastr('Success','Comment added!','success');
                })
                .catch(() => show_toastr('Error','Something went wrong!','error'))
                .finally(() => {
                    commentSubmit.disabled = false;
                    commentSubmit.innerHTML = '<i class="fa-solid fa-paper-plane"></i>';
                });
            });
        }

        /* Bootstrap dropdowns in modal */
        if(typeof bootstrap !== 'undefined'){
            modalScroll.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(el => {
                try{ new bootstrap.Dropdown(el); } catch(e){}
            });
        }
    })
    .catch(() => {
        modalScroll.innerHTML = '<div style="text-align:center;padding:40px;color:var(--text-muted);">Failed to load. Please try again.</div>';
    });
}

function closeBugModal(){
    modal.classList.remove('show');
    document.body.style.overflow = '';
}

/* ── Trigger open ── */
document.addEventListener('click', function(e){
    const trigger = e.target.closest('.open-bug-modal');
    if(trigger){ e.preventDefault(); openBugModal(trigger.dataset.bugId, trigger.dataset.openTab || ''); }
});

/* ── Close ── */
if(closeBtn) closeBtn.addEventListener('click', closeBugModal);
if(modal)    modal.addEventListener('click', e => { if(e.target === modal) closeBugModal(); });
document.addEventListener('keydown', e => { if(e.key === 'Escape') closeBugModal(); });

/* ── Delete handlers (inside modal) ── */
document.addEventListener('click', function(e){
    /* Comment delete */
    const dc = e.target.closest('.bvm-delete-comment');
    if(dc && modal.classList.contains('show')){
        e.preventDefault();
        Swal.fire({
            title:'Are you sure?', text:'Delete this comment?', icon:'warning',
            showCancelButton:true, confirmButtonColor:'#d33', cancelButtonColor:'#3085d6',
            confirmButtonText:'Yes, delete!', customClass:{ popup:'swal-zindex-fix' }
        }).then(r => {
            if(!r.isConfirmed) return;
            fetch(dc.dataset.url, { method:'DELETE', headers:{ 'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'X-Requested-With':'XMLHttpRequest' } })
            .then(r => r.json())
            .then(() => { dc.closest('.comment-box') && dc.closest('.comment-box').remove(); show_toastr('Success','Comment deleted!','success'); })
            .catch(() => show_toastr('Error','Unable to delete!','error'));
        });
    }

    /* File delete */
    const df = e.target.closest('.bvm-delete-file');
    if(df && modal.classList.contains('show')){
        e.preventDefault();
        Swal.fire({
            title:'Are you sure?', text:'Delete this file?', icon:'warning',
            showCancelButton:true, confirmButtonColor:'#d33', cancelButtonColor:'#3085d6',
            confirmButtonText:'Yes, delete!', customClass:{ popup:'swal-zindex-fix' }
        }).then(r => {
            if(!r.isConfirmed) return;
            fetch(df.dataset.url, { method:'DELETE', headers:{ 'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'X-Requested-With':'XMLHttpRequest' } })
            .then(r => r.json())
            .then(() => {
                const item    = df.closest('.bug-file');
                if(item) item.remove();
                const countEl = modalScroll.querySelector('#bvm-files-count');
                if(countEl){ const n = parseInt(countEl.textContent || 0) - 1; countEl.textContent = Math.max(0, n); }
                const list    = modalScroll.querySelector('#bvm-file-list');
                if(list && !list.querySelector('.bug-file')){
                    list.innerHTML = '<div class="bvm-empty" id="bvm-no-files"><i class="ti ti-paperclip-off"></i> No files attached yet.</div>';
                }
                show_toastr('Success','File deleted!','success');
            })
            .catch(() => show_toastr('Error','Unable to delete!','error'));
        });
    }
});

})();
</script>

<style>
.swal-zindex-fix{ z-index:9999999!important }
.swal2-container{ z-index:9999999!important }
</style>

@endsection