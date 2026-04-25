<style>
    .modal_imagepreview {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.9);
    }

    .modal_imagepreview .modal-content {
        position: relative;
        margin: auto;
        padding: 20px;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #000;
        border-radius: 0px !important;
    }

    .modal_imagepreview .modal-img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        border-radius: 8px;
    }

    .modal_imagepreview .close {
        position: absolute;
        top: 20px;
        right: 30px;
        color: #fff;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
        z-index: 1001;
        background: #000;
        height: 40px;
        width: 40px;
        border-radius: 100px;
        border: 3px solid #fff;
        display: flex;
        justify-content: center;
        align-items: center;
        padding-bottom: 10px;
    }

    .modal_imagepreview .close:hover {
        color: #ccc;
    }
</style>

<div class="modal fade modal-xl" id="screenPopup{{ $incident->id }}" tabindex="-1"
     aria-labelledby="screenPopupLabel{{ $incident->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 overflow-hidden p-0">
            <div class="modal-body p-0 bg-light">
                <div class="container-fluid g-0">

                    {{-- Header --}}
                    <div class="row g-0">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center p-3 border-bottom"
                                 style="background-color: #E9F2F4;">
                                <div>
                                    <span class="fw-semibold">{{ $incident->employee->name ?? 'Unknown' }} -</span>
                                    <span class="fw-semifold">
                                        {{ \Carbon\Carbon::parse($incident->capture_date_and_time)->format('d/m/Y') }}
                                        &nbsp; {{ \Carbon\Carbon::parse($incident->capture_date_and_time)->format('h:i A') }}
                                    </span>
                                </div>
                                <div class="ms-auto">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-0 h-100">
                        {{-- Screenshot Section --}}
                        <div class="col-md-7 p-3">
                            <div class="h-100 position-relative">
                                <div id="screenshot-container-{{ $incident->id }}" class="screenshot-container"
                                     style="position: relative;overflow:hidden;">
                                    <img id="screenshot-img-{{ $incident->id }}"
                                         src="{{ \App\Models\Utility::get_file($incident->screenshot) }}"
                                         class="screenshot-img img-fluid w-100 h-100"
                                         alt="Screen Snapshot"/>

                                    <button class="btn_round"
                                            onclick="openModal({{ $incident->id }}, this.parentElement.querySelector('.screenshot-img'))"
                                            style="position: absolute; top: 10px; right: 10px; z-index: 10;">
                                        <img src="{{ asset('assets/assestsnew/maximize-2.svg') }}" alt="Maximize">
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Application Log --}}
                        <div class="col-md-5 p-3 h-100">
                            <div class="light_blue_box">
                                <h5 class="p-3 mb-0">Application Log</h5>
                            </div>
                            <div class="bg-white shadow-lg" style="border-radius: 0px 0px 10px 10px;">
                                <div class="row justify-content-center">
                                    <div class="col-md-11">
                                        @forelse($incident->applicationLog as $log)
                                            <div class="bg-light rounded-3 p-2 mt-3 mb-3">
                                                <div class="d-flex align-items-center gap-1">
                                                    @if ($log->icon?->image)
                                                        <img
                                                            src="{{ \App\Models\Utility::get_file($log->icon->image) }}"
                                                            alt="App Icon" style="width: 36px; height: 36px;">
                                                    @else
                                                        <img
                                                            src="{{ asset('assets/assestsnew/' . ($log->is_browser ? 'screenshot_globe.svg' : 'screenshot_system.svg')) }}"
                                                            alt="Default Icon" style="width: 40px; height: 40px;">
                                                    @endif
                                                    <div>
                                                        <h6 class="text-black fw-semibold mb-1">{{ $log->application_name }}</h6>
                                                    </div>
                                                    <div
                                                        class="d-flex align-items-center justify-content-start gap-1 ms-auto">
                                                        <img src="{{ asset('assets/assestsnew/clock.svg') }}" alt="">
                                                        <p class="mb-0"
                                                           style="color: #676767;">{{ \Carbon\Carbon::parse($log->screen_time)->format('i\\m:s\\s') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-12 text-center">No application logs found.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Productivity --}}
                        <div class="row m-0 p-3">
                            <div class="col-12 main_popupscreenshort">
                                <div class="row justify-content-center">
                                    <div class="col-12 my-3">
                                        <div class="d-flex justify-content-between">
                                            <h5>Productivity Details</h5>
                                            <span>Activity Level : <b>{{ $incident->action_percentage ?? 0 }}%</b></span>
                                        </div>
                                    </div>
                                    <div class="col-12 my-3">
                                        <div class="progress px-0">
                                            <div class="progress-bar myProgressBar" role="progressbar"
                                                 style="width: {{ $incident->action_percentage ?? 0 }}%;"
                                                 aria-valuenow="{{ $incident->action_percentage ?? 0 }}"
                                                 aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-8 my-3 popupgryboxes">
                                        <div class="row text-center">
                                            @php
                                                $totalDurationInSeconds = 0;
                                                foreach ($incident->applicationLog as $log) {
                                                    $screenTimeParts = explode(':', $log->screen_time);
                                                    $totalDurationInSeconds += ($screenTimeParts[0] * 60) + $screenTimeParts[1];
                                                }
                                                $minutes = floor($totalDurationInSeconds / 60);
                                                $seconds = $totalDurationInSeconds % 60;
                                                $formattedDuration = sprintf("%02dm:%02ds", $minutes, $seconds);
                                            @endphp
                                            <div class="col-md-4 my-2">
                                                <b>{{ $formattedDuration ?? '00m:00s' }}</b>
                                                <span>Duration</span>
                                            </div>
                                            <div class="col-md-4 my-2">
                                                <b>{{ $incident->keyboard_action_count ?? 0 }}</b>
                                                <span>Key Presses</span>
                                            </div>
                                            <div class="col-md-4 my-2">
                                                <b>{{ $incident->mouse_action_count ?? 0 }}</b>
                                                <span>Mouse Click</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- end row -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Fullscreen Image Modal --}}
    <div id="imageModal-{{ $incident->id }}" class="modal_imagepreview">
        <div class="modal-content" id="modalImg_content-{{ $incident->id }}">
            <span class="close" onclick="closeModal({{ $incident->id }})">&times;</span>
            <img id="modalImg-{{ $incident->id }}" class="modal-img" src="" alt="">
        </div>
    </div>
</div>

<script>
    function openModal(id, imgElement) {
        const modal = document.getElementById('imageModal-' + id);
        const modalImg = document.getElementById('modalImg-' + id);
        modal.style.display = 'block';
        modalImg.src = imgElement.src;
        modalImg.alt = imgElement.alt;
        document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
        const modal = document.getElementById('imageModal-' + id);
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    function enableZoomAndPan(containerId, imageId) {
        const container = document.getElementById(containerId);
        const image = document.getElementById(imageId);
        if (!container || !image) return;
        let scale = 1, scaleStep = 0.1, minScale = 1, maxScale = 3;
        let translateX = 0, translateY = 0;
        let isDragging = false, startX = 0, startY = 0;

        function updateTransform() {
            image.style.transform = `scale(${scale}) translate(${translateX / scale}px, ${translateY / scale}px)`;
        }

        function clamp(v, min, max) {
            return Math.max(min, Math.min(v, max));
        }

        function getBounds() {
            const containerWidth = container.clientWidth;
            const containerHeight = container.clientHeight;
            const imageWidth = image.naturalWidth * scale;
            const imageHeight = image.naturalHeight * scale;
            const maxX = Math.max(0, (imageWidth - containerWidth) / 2);
            const maxY = Math.max(0, (imageHeight - containerHeight) / 2);
            return {minX: -maxX, maxX: maxX, minY: -maxY, maxY: maxY};
        }

        container.addEventListener("wheel", e => {
            e.preventDefault();
            const rect = container.getBoundingClientRect();
            const offsetX = e.clientX - rect.left;
            const offsetY = e.clientY - rect.top;
            const prevScale = scale;
            scale = e.deltaY < 0 ? Math.min(scale + scaleStep, maxScale) : Math.max(scale - scaleStep, minScale);
            if (scale === 1) {
                translateX = translateY = 0;
            } else {
                const zoomFactor = scale / prevScale;
                translateX = (translateX - offsetX) * zoomFactor + offsetX;
                translateY = (translateY - offsetY) * zoomFactor + offsetY;
                const bounds = getBounds();
                translateX = clamp(translateX, bounds.minX, bounds.maxX);
                translateY = clamp(translateY, bounds.minY, bounds.maxY);
            }
            updateTransform();
        });
        container.addEventListener("mousedown", e => {
            if (scale <= 1) return;
            isDragging = true;
            startX = e.clientX - translateX;
            startY = e.clientY - translateY;
            container.style.cursor = "grabbing";
        });
        window.addEventListener("mouseup", () => {
            isDragging = false;
            container.style.cursor = scale > 1 ? "grab" : "default";
        });
        window.addEventListener("mousemove", e => {
            if (!isDragging) return;
            translateX = e.clientX - startX;
            translateY = e.clientY - startY;
            const bounds = getBounds();
            translateX = clamp(translateX, bounds.minX, bounds.maxX);
            translateY = clamp(translateY, bounds.minY, bounds.maxY);
            updateTransform();
        });
        return {
            resetZoom: () => {
                scale = 1;
                translateX = translateY = 0;
                updateTransform();
                container.style.cursor = "default";
            }
        };
    }

    document.getElementById('screenPopup{{ $incident->id }}').addEventListener('shown.bs.modal', function () {
        enableZoomAndPan("screenshot-container-{{ $incident->id }}", "screenshot-img-{{ $incident->id }}");
    });
    document.getElementById('imageModal-{{ $incident->id }}').addEventListener('click', function (e) {
        if (e.target === this) closeModal({{ $incident->id }});
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeModal({{ $incident->id }});
    });
</script>

<script>
    document.querySelectorAll('.modal').forEach(modal => {
        const img = modal.querySelector('.modal-img');
        let scale = 1;
        let originX = 50;
        let originY = 50;

        // When mouse moves over the image, track position for zoom origin
        img?.addEventListener('mousemove', e => {
            const rect = img.getBoundingClientRect();
            originX = ((e.clientX - rect.left) / rect.width) * 100;
            originY = ((e.clientY - rect.top) / rect.height) * 100;
            img.style.transformOrigin = `${originX}% ${originY}%`;
        });

        // Scroll to zoom
        img?.addEventListener('wheel', e => {
            e.preventDefault();
            if (e.deltaY < 0) {
                scale += 0.1; // zoom in
            } else {
                scale -= 0.1; // zoom out
                if (scale < 1) scale = 1; // don't go smaller than original
            }
            img.style.transform = `scale(${scale})`;
        });

        // Reset zoom when modal closes
        modal.addEventListener('hidden.bs.modal', () => {
            scale = 1;
            img.style.transform = 'scale(1)';
            img.style.transformOrigin = 'center center';
        });
    });
</script>
