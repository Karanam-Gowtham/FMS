import sys
try:
    import PyPDF2
except ImportError:
    import subprocess
    subprocess.check_call([sys.executable, "-m", "pip", "install", "PyPDF2"])
    import PyPDF2

def extract_text(pdf_path, txt_path):
    with open(pdf_path, 'rb') as f:
        reader = PyPDF2.PdfReader(f)
        text = ''
        for i in range(min(50, len(reader.pages))): # extract first 50 pages should cover index/criteria
            text += reader.pages[i].extract_text() + '\n'
    
    with open(txt_path, 'w', encoding='utf-8') as f:
        f.write(text)

extract_text(r"e:\set\xampp\htdocs\mini\FMS\nba.pdf", r"e:\set\xampp\htdocs\mini\FMS\nba_extracted.txt")
extract_text(r"e:\set\xampp\htdocs\mini\FMS\NAAC - Autonomous Colleges Manual in Multi Languages.pdf", r"e:\set\xampp\htdocs\mini\FMS\naac_extracted.txt")
print("Done extracting")
