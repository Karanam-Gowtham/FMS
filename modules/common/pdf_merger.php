<?php
    include '../../includes/connection.php';
    include '../../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Merger Pro</title>
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
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-image: url('../../assets/img/gmr_landing_page.jpg');
            background-size: cover;
            background-position: center;
            color: #1f2937;
            line-height: 1.5;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5); /* Adjust the opacity as needed */
            z-index: -1;
        }

        .container11 {
            margin-top: 100px;
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        

        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #111827;
        }

        .subtitle {
            color: #6b7280;
        }

        .upload-container {
            margin-top: 100px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }

        .drop-zone {
            border: 2px dashed var(--border-color);
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
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
            transition: background-color 0.3s ease;
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
            padding: 0.25rem;
            font-size: 1.25rem;
            transition: color 0.3s ease;
        }

        .remove-file:hover {
            color: var(--danger-hover);
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
            transition: all 0.3s ease;
        }

        .merge-btn:hover:not(:disabled) {
            background: var(--primary-hover);
        }

        .merge-btn:disabled {
            background: #9ca3af;
            cursor: not-allowed;
        }

        footer {
            text-align: center;
            margin-top: 2rem;
            color:rgb(240, 243, 249);
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <div class="container11">
        

        <div class="upload-container">
            <div class="drop-zone" id="dropZone">
                
            <h1>PDF Merger</h1>
            <p class="subtitle">Combine multiple PDF files into one document</p>
                <div class="drop-zone-content">
                    <svg class="pdf-icon" viewBox="0 0 24 24" width="48" height="48">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                        <polyline points="14 2 14 8 20 8" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                        <line x1="12" y1="18" x2="12" y2="12" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                        <line x1="9" y1="15" x2="15" y2="15" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                    </svg>
                    <h3>Drag & Drop PDF files here</h3>
                    <p>or</p>
                    <input type="file" id="fileInput" multiple accept=".pdf" class="file-input">
                    <button class="browse-btn" onclick="document.getElementById('fileInput').click()">Browse Files</button>
                </div>
            </div>

            <div class="file-list" id="fileList">
                <h3>Selected Files</h3>
                <ul id="selectedFiles"></ul>
            </div>

            <button id="mergeBtn" class="merge-btn" disabled>Merge PDFs</button>
        </div>

        <footer>
            <p>Supported file type: PDF • Maximum file size: 50MB</p>
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/pdf-lib/dist/pdf-lib.min.js"></script>
        <script >
            let selectedFiles = [];

            const dropZone = document.getElementById('dropZone');
            const fileInput = document.getElementById('fileInput');
            const selectedFilesList = document.getElementById('selectedFiles');
            const mergeBtn = document.getElementById('mergeBtn');

            // Drag and drop handlers
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.classList.add('dragover');
            });

            dropZone.addEventListener('dragleave', () => {
                dropZone.classList.remove('dragover');
            });

            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.classList.remove('dragover');
                const files = Array.from(e.dataTransfer.files).filter(file => file.type === 'application/pdf');
                handleFiles(files);
            });

            fileInput.addEventListener('change', (e) => {
                const files = Array.from(e.target.files);
                handleFiles(files);
            });

            function handleFiles(files) {
                files.forEach(file => {
                    if (!selectedFiles.some(f => f.name === file.name)) {
                        selectedFiles.push(file);
                        addFileToList(file);
                    }
                });
                updateMergeButton();
            }

            function addFileToList(file) {
                const li = document.createElement('li');
                li.innerHTML = `
                    <span>${file.name}</span>
                    <button class="remove-file" onclick="removeFile('${file.name}')">&times;</button>
                `;
                selectedFilesList.appendChild(li);
            }

            function removeFile(fileName) {
                selectedFiles = selectedFiles.filter(file => file.name !== fileName);
                updateFileList();
                updateMergeButton();
            }

            function updateFileList() {
                selectedFilesList.innerHTML = '';
                selectedFiles.forEach(file => addFileToList(file));
            }

            function updateMergeButton() {
                mergeBtn.disabled = selectedFiles.length < 2;
            }

            mergeBtn.addEventListener('click', async () => {
                if (selectedFiles.length < 2) return;

                try {
                    mergeBtn.disabled = true;
                    mergeBtn.textContent = 'Merging...';

                    const PDFLib = window.PDFLib;
                    const mergedPdf = await PDFLib.PDFDocument.create();

                    for (const file of selectedFiles) {
                        const fileArrayBuffer = await file.arrayBuffer();
                        const pdf = await PDFLib.PDFDocument.load(fileArrayBuffer);
                        const pages = await mergedPdf.copyPages(pdf, pdf.getPageIndices());
                        pages.forEach(page => mergedPdf.addPage(page));
                    }

                    const mergedPdfFile = await mergedPdf.save();

                    // Create a Blob and URL
                    const blob = new Blob([mergedPdfFile], { type: 'application/pdf' });
                    const url = URL.createObjectURL(blob);

                    // Clear previous buttons if any
                    const existingControls = document.getElementById('pdfControls');
                    if (existingControls) existingControls.remove();

                    // Create buttons
                    const controlsDiv = document.createElement('div');
                    controlsDiv.id = 'pdfControls';
                    controlsDiv.style.textAlign = 'center';
                    controlsDiv.style.marginTop = '20px';

                    // View Button
                    const viewBtn = document.createElement('button');
                    viewBtn.textContent = 'View';
                    viewBtn.className = 'browse-btn';
                    viewBtn.style.marginRight = '10px';
                    viewBtn.onclick = () => window.open(url, '_blank');

                    // Download Button
                    const downloadBtn = document.createElement('button');
                    downloadBtn.textContent = 'Download';
                    downloadBtn.className = 'browse-btn';
                    downloadBtn.onclick = () => {
                        const link = document.createElement('a');
                        link.href = url;
                        link.download = 'merged.pdf';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        URL.revokeObjectURL(url);
                    };

                    // Append buttons to the controls div
                    controlsDiv.appendChild(viewBtn);
                    controlsDiv.appendChild(downloadBtn);

                    // Append controls div to the upload-container
                    document.querySelector('.upload-container').appendChild(controlsDiv);

                } catch (error) {
                    console.error('Error merging PDFs:', error);
                    alert('Error merging PDFs. Please try again.');
                } finally {
                    mergeBtn.disabled = false;
                    mergeBtn.textContent = 'Merge PDFs';
                }
            });

</script>
</body>
</html>
