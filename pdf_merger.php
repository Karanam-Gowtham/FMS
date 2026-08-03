<?php
include_once 'includes/connection.php';
include_once 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Merger Pro - FMS</title>
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-hover: #1d4ed8;
            --danger-color: #dc2626;
            --danger-hover: #b91c1c;
            --background: #f8fafc;
            --border-color: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            background-image: url('assets/img/gmr_landing_page.jpg');
            background-size: cover;
            background-position: center;
            color: #1f2937;
            line-height: 1.5;
        }

        .page-overlay {
            min-height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            padding-bottom: 2rem;
        }

        .container11 {
            margin: 120px auto 0;
            max-width: 800px;
            padding: 2rem 1rem;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #111827;
            text-align: center;
        }

        .subtitle {
            text-align: center;
            color: #6b7280;
            margin-bottom: 1.5rem;
        }

        .upload-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 6px rgba(0, 0, 0, 0.15);
            padding: 2rem;
        }

        .drop-zone {
            border: 2px dashed var(--border-color);
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            transition: 0.3s;
            margin-bottom: 1.5rem;
        }

        .drop-zone.dragover {
            border-color: var(--primary-color);
            background: #eff6ff;
        }

        .drop-zone-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }

        .pdf-icon {
            color: var(--primary-color);
        }

        .file-input {
            display: none;
        }

        .browse-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
        }

        .browse-btn:hover {
            background: var(--primary-hover);
        }

        .file-list {
            margin-bottom: 1.5rem;
        }

        .file-list h3 {
            margin-bottom: 1rem;
            color: #374151;
        }

        .file-list ul {
            list-style: none;
            max-height: 200px;
            overflow-y: auto;
        }

        .file-list li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            background: #f9fafb;
            border-radius: 6px;
            margin-bottom: 0.5rem;
        }

        .remove-file {
            background: none;
            border: none;
            color: var(--danger-color);
            cursor: pointer;
            font-size: 1.25rem;
        }

        .merge-btn {
            width: 100%;
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
        }

        .merge-btn:disabled {
            background: #9ca3af;
            cursor: not-allowed;
        }

        footer {
            text-align: center;
            margin-top: 2rem;
            color: #e5e7eb;
            font-size: 0.875rem;
        }
    </style>
</head>

<body>

<div class="page-overlay">
    <div class="container11">

        <div class="upload-container">

            <div class="drop-zone" id="dropZone">
                <h1>PDF Merger</h1>
                <p class="subtitle">Combine multiple PDF files into one document</p>

                <div class="drop-zone-content">
                    <svg class="pdf-icon" viewBox="0 0 24 24" width="48" height="48">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"
                              fill="none" stroke="currentColor" stroke-width="2"/>
                        <polyline points="14 2 14 8 20 8"
                                  fill="none" stroke="currentColor" stroke-width="2"/>
                    </svg>

                    <h3>Drag & Drop PDF files here</h3>
                    <p>or</p>

                    <input type="file" id="fileInput" multiple accept=".pdf" class="file-input">
                    <button class="browse-btn"
                            onclick="document.getElementById('fileInput').click()">
                        Browse Files
                    </button>
                </div>
            </div>

            <div class="file-list">
                <h3>Selected Files</h3>
                <ul id="selectedFiles"></ul>
            </div>

            <button id="mergeBtn" class="merge-btn" disabled>Merge PDFs</button>
        </div>

        <footer>
            Supported file type: PDF • Maximum file size: 50MB
        </footer>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/pdf-lib/dist/pdf-lib.min.js"></script>

<script>
let selectedFiles = [];

const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');
const selectedFilesList = document.getElementById('selectedFiles');
const mergeBtn = document.getElementById('mergeBtn');

const MAX_SIZE = 50 * 1024 * 1024; // 50MB

dropZone.addEventListener('dragover', e => {
    e.preventDefault();
    dropZone.classList.add('dragover');
});

dropZone.addEventListener('dragleave', () => {
    dropZone.classList.remove('dragover');
});

dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('dragover');
    handleFiles([...e.dataTransfer.files]);
});

fileInput.addEventListener('change', e => handleFiles([...e.target.files]));

function isPdfFile(file) {
    return (file.type === 'application/pdf') || file.name.toLowerCase().endsWith('.pdf');
}

function formatBytes(bytes) {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function handleFiles(files) {
    const skipped = [];

    files.forEach(file => {
        if (!isPdfFile(file)) {
            skipped.push({ file, reason: 'not-pdf' });
            return;
        }
        if (file.size > MAX_SIZE) {
            skipped.push({ file, reason: 'too-large' });
            return;
        }

        const exists = selectedFiles.some(f => f.name === file.name && f.size === file.size);
        if (!exists) selectedFiles.push(file);
    });

    if (skipped.length) {
        const msgs = skipped.map(s => `${s.file.name} — ${s.reason === 'too-large' ? 'file too large (' + formatBytes(s.file.size) + ')' : 'not a PDF'}`);
        alert('Some files were skipped:\n' + msgs.join('\n'));
    }

    renderFiles();
    mergeBtn.disabled = selectedFiles.length < 2;
}

function renderFiles() {
    selectedFilesList.innerHTML = '';
    selectedFiles.forEach(file => {
        const li = document.createElement('li');
        li.innerHTML = `<span>${file.name} <small style="color:#6b7280; margin-left:8px">(${formatBytes(file.size)})</small></span>
                        <button class="remove-file" aria-label="Remove ${file.name}"
                        onclick="removeFile('${file.name.replace(/'/g, "\\'")}', '${file.size}')">&times;</button>`;
        selectedFilesList.appendChild(li);
    });
}

function removeFile(name, size) {
    selectedFiles = selectedFiles.filter(f => !(f.name === name && String(f.size) === String(size)));
    renderFiles();
    mergeBtn.disabled = selectedFiles.length < 2;
}

mergeBtn.onclick = async () => {
    if (selectedFiles.length < 2) return;

    mergeBtn.disabled = true;
    const originalText = mergeBtn.textContent;
    mergeBtn.textContent = 'Merging...';

    try {
        const mergedPdf = await PDFLib.PDFDocument.create();
        for (const file of selectedFiles) {
            const arrayBuffer = await file.arrayBuffer();
            const pdf = await PDFLib.PDFDocument.load(arrayBuffer);
            const pages = await mergedPdf.copyPages(pdf, pdf.getPageIndices());
            pages.forEach(p => mergedPdf.addPage(p));
        }

        const mergedBytes = await mergedPdf.save();
        const blob = new Blob([mergedBytes], { type: 'application/pdf' });
        const url = URL.createObjectURL(blob);

        const a = document.createElement('a');
        a.href = url;
        a.download = 'merged.pdf';
        document.body.appendChild(a);
        a.click();
        a.remove();

        setTimeout(() => URL.revokeObjectURL(url), 10000);
    } catch (err) {
        console.error('Merge failed', err);
        alert('Failed to merge PDF files: ' + (err && err.message ? err.message : err));
    } finally {
        mergeBtn.disabled = selectedFiles.length < 2;
        mergeBtn.textContent = originalText;
    }
};
</script>

</body>
</html>
