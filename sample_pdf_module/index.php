<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Merging & Indexing Prototype</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background-color: #f4f7f6; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h1 { font-size: 24px; color: #333; margin-top: 0; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: bold; margin-bottom: 8px; color: #555; }
        input[type="file"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        .btn { background-color: #0056b3; color: white; border: none; padding: 10px 20px; font-size: 16px; border-radius: 4px; cursor: pointer; }
        .btn:hover { background-color: #004494; }
        .help-text { font-size: 13px; color: #777; margin-top: 5px; }
    </style>
</head>
<body>

<div class="container">
    <h1>PDF Merging & Auto-Indexing</h1>
    <p>Upload a few sample PDF files. The script will use the filenames as "Event Names", generate a table of contents, merge the PDFs, and automatically stamp the page numbers!</p>
    
    <form action="process.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="pdfs">Select Event PDFs (Select multiple files):</label>
            <input type="file" name="pdf_files[]" id="pdfs" accept="application/pdf" multiple required>
            <div class="help-text">Hold CTRL (or CMD) to select multiple dummy PDF files.</div>
        </div>
        
        <button type="submit" class="btn">Generate Merged PDF</button>
    </form>
</div>

</body>
</html>
