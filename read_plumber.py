import sys
import subprocess
try:
    import pdfplumber
except ImportError:
    subprocess.check_call([sys.executable, "-m", "pip", "install", "pdfplumber"])
    import pdfplumber

text = ''
with pdfplumber.open('docs/specifica_gestionale_magazzino_calabria_verde.pdf') as pdf:
    for page in pdf.pages:
        text += page.extract_text() + '\n'

with open('docs/specifica.txt', 'w', encoding='utf-8') as f:
    f.write(text)
print("done")
