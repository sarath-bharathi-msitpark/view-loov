<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

<!-- Font -->
<link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">

<!-- Libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<style>
body {
    margin: 0;
    padding: 0;
}

/* A4 @ 300 DPI */
.page {
    width: 2480px;
    height: 3508px;
    position: relative;
}

.page img {
    width: 100%;
    height: 100%;
    display: block;
}

/* Common text styles */
.subject,
.name,
.email,
.phone {
    font-family: 'Righteous', cursive;
    /*font-weight: 600;*/
    font-style: normal;
    letter-spacing: 0;
    text-align: center;
    position: absolute;
    width: 100%;
}

.subject {
    top: 1350px;
    font-size: 50px;
    line-height: 1;
    color: #FFFFFF;
}

.name {
    top: 1630px;
    font-size: 64px;
    line-height: 1;
}

.email {
    top: 2240px;
    font-size: 64px;
    line-height: 1;
}

.phone {
    top: 1940px;
    font-size: 64px;
    line-height: 1;
}

/* Hide pages from screen but keep renderable */
#pages {
    position: absolute;
    left: -99999px;
    top: 0;
}
</style>
</head>

<body>

<div id="pages">
@foreach ($pages as $key => $page)
    <div class="page pdf-page">
        <img src="{{ asset($page['image']) }}" alt="Page {{ $key + 1 }}">

        {{-- First page --}}
        @if($key === 0)
            <div class="subject">
                {{ $subject }}
            </div>
        @elseif($key === count($pages) - 1)
            <div class="name">{{ $user['name'] }}</div>
            <div class="email">{{ $user['email'] }}</div>
            <div class="phone">{{ $user['phone'] }}</div>
        @endif
    </div>
@endforeach
</div>


<script>
const { jsPDF } = window.jspdf;

/* Wait until all images are loaded */
function waitForImages() {
    const images = document.querySelectorAll('.pdf-page img');
    return Promise.all(
        Array.from(images).map(img => {
            if (img.complete) return Promise.resolve();
            return new Promise(resolve => {
                img.onload = img.onerror = resolve;
            });
        })
    );
}

async function generatePdf() {

    // Wait for fonts
    await document.fonts.ready;

    // Wait for images
    await waitForImages();

    const pdf = new jsPDF('p', 'mm', 'a4');
    const pages = document.querySelectorAll('.pdf-page');

    for (let i = 0; i < pages.length; i++) {

        const canvas = await html2canvas(pages[i], {
            scale: 2,
            useCORS: true,
            backgroundColor: '#ffffff'
        });

        const imgData = canvas.toDataURL('image/jpeg', 1.0);

        if (i > 0) pdf.addPage();

        pdf.addImage(imgData, 'JPEG', 0, 0, 210, 297);
    }

    pdf.save(@json($pdfName));
}
</script>
<script>
window.addEventListener('load', async () => {
    await generatePdf();

    setTimeout(() => {
        window.close();
    }, 800);
});
</script>


</body>
</html>
