import sys
try:
    import PyPDF2
    with open('docs/specifica_gestionale_magazzino_calabria_verde.pdf', 'rb') as f:
        reader = PyPDF2.PdfReader(f)
        for page in reader.pages:
            print(page.extract_text())
except ImportError:
    print("PyPDF2 non installato, provo con fitz")
    try:
        import fitz
        doc = fitz.open('docs/specifica_gestionale_magazzino_calabria_verde.pdf')
        for page in doc:
            print(page.get_text())
    except ImportError:
        print("Manca anche fitz (PyMuPDF). Installalo con: pip install PyMuPDF")
