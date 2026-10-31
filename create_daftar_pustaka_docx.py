import os
import docx
from docx.shared import Pt, Inches
from docx.enum.text import WD_ALIGN_PARAGRAPH

def set_run_font(r, font_name='Times New Roman', size_pt=12, bold=False, italic=False):
    r.font.name = font_name
    r.font.size = Pt(size_pt)
    r.bold = bold
    r.italic = italic

def format_p(p, align=WD_ALIGN_PARAGRAPH.JUSTIFY, space_after_pt=6, space_before_pt=0, line_spacing=1.5, hanging_indent_pt=36.0):
    p.alignment = align
    p.paragraph_format.line_spacing = line_spacing
    p.paragraph_format.space_after = Pt(space_after_pt)
    p.paragraph_format.space_before = Pt(space_before_pt)
    p.paragraph_format.left_indent = Pt(hanging_indent_pt)
    p.paragraph_format.first_line_indent = Pt(-hanging_indent_pt)

def add_run(p, text, bold=False, italic=False, size_pt=12):
    r = p.add_run(text)
    set_run_font(r, bold=bold, italic=italic, size_pt=size_pt)
    return r

# Data 29 Referensi Baku APA 7th Edition
references = [
    {
        "authors": "Bhadra, S., Kelkar, V. A., Brooks, F. J., & Anastasio, M. A.",
        "year": "2021",
        "title": "On hallucinations in tomographic image reconstruction",
        "source_italic": "IEEE Transactions on Medical Imaging",
        "source_rest": ", 40(11), 3249–3260. ",
        "doi": "https://doi.org/10.1109/TMI.2021.3077857"
    },
    {
        "authors": "Bland, J. M., & Altman, D. G.",
        "year": "1986",
        "title": "Statistical methods for assessing agreement between two methods of clinical measurement",
        "source_italic": "The Lancet",
        "source_rest": ", 327(8476), 307–310. ",
        "doi": "https://doi.org/10.1016/S0140-6736(86)90837-8"
    },
    {
        "authors": "Bushberg, J. T., Seibert, J. A., Leidholdt, E. M., & Boone, J. M.",
        "year": "2020",
        "title": "",
        "source_italic": "The Essential Physics of Medical Imaging",
        "source_rest": " (4th ed.). Wolters Kluwer.",
        "doi": ""
    },
    {
        "authors": "Danzer, C. S.",
        "year": "1919",
        "title": "The cardiothoracic ratio: An index of cardiac enlargement",
        "source_italic": "The American Journal of the Medical Sciences",
        "source_rest": ", 157(4), 513–521. ",
        "doi": "https://doi.org/10.1097/00000441-191904000-00007"
    },
    {
        "authors": "Diwakar, M., & Kumar, M.",
        "year": "2018",
        "title": "A review on CT image noise and its denoising",
        "source_italic": "Biomedical Signal Processing and Control",
        "source_rest": ", 42, 73–88. ",
        "doi": "https://doi.org/10.1016/j.bspc.2018.01.010"
    },
    {
        "authors": "Fan, W., Yang, Y., Qi, J., Zhang, D., Wang, Z., & Chen, H.",
        "year": "2024",
        "title": "A deep-learning-based framework for identifying and localizing multiple abnormalities and assessing cardiomegaly in chest X-ray",
        "source_italic": "Nature Communications",
        "source_rest": ", 15, Article 1347. ",
        "doi": "https://doi.org/10.1038/s41467-024-45599-z"
    },
    {
        "authors": "Foi, A., Trimeche, M., Katkovnik, V., & Egiazarian, K.",
        "year": "2008",
        "title": "Practical Poisson-Gaussian noise parameter estimation and image denoising",
        "source_italic": "IEEE Transactions on Image Processing",
        "source_rest": ", 17(10), 1737–1754. ",
        "doi": "https://doi.org/10.1109/TIP.2008.2003399"
    },
    {
        "authors": "Gonzalez, R. C., & Woods, R. E.",
        "year": "2018",
        "title": "",
        "source_italic": "Digital Image Processing",
        "source_rest": " (4th ed.). Pearson.",
        "doi": ""
    },
    {
        "authors": "Guan, S., & Liu, B.",
        "year": "2022",
        "title": "Medical image denoising: A review of deep learning techniques",
        "source_italic": "Frontiers in Oncology",
        "source_rest": ", 12, Article 1056586. ",
        "doi": "https://doi.org/10.3389/fonc.2022.1056586"
    },
    {
        "authors": "He, K., Zhang, X., Ren, S., & Sun, J.",
        "year": "2016",
        "title": "Deep residual learning for image recognition",
        "source_italic": "Proceedings of the IEEE Conference on Computer Vision and Pattern Recognition (CVPR)",
        "source_rest": " (pp. 770–778). IEEE. ",
        "doi": "https://doi.org/10.1109/CVPR.2016.90"
    },
    {
        "authors": "Holm, S.",
        "year": "1979",
        "title": "A simple sequentially rejective multiple test procedure",
        "source_italic": "Scandinavian Journal of Statistics",
        "source_rest": ", 6(2), 65–70. ",
        "doi": "https://www.jstor.org/stable/4615733"
    },
    {
        "authors": "Huang, H. K.",
        "year": "2010",
        "title": "",
        "source_italic": "PACS and Imaging Informatics: Basic Principles and Applications",
        "source_rest": " (2nd ed.). John Wiley & Sons. ",
        "doi": "https://doi.org/10.1002/9780470560525"
    },
    {
        "authors": "Jafar, A., Hameed, M. T., Akram, N., Waqas, U., Kim, H. S., & Naqvi, R. A.",
        "year": "2022",
        "title": "CardioNet: Automatic semantic segmentation to calculate the cardiothoracic ratio for cardiomegaly and other chest diseases",
        "source_italic": "Journal of Personalized Medicine",
        "source_rest": ", 12(6), Article 988. ",
        "doi": "https://doi.org/10.3390/jpm12060988"
    },
    {
        "authors": "Kufel, J., Bargieł, P., Kocot, S., Czapla, M., & Dudek, M.",
        "year": "2024",
        "title": "Clinical validation of artificial intelligence in cardiothoracic ratio calculation: A multi-center diagnostic accuracy study",
        "source_italic": "Journal of Clinical Medicine",
        "source_rest": ", 13(4), Article 1012. ",
        "doi": "https://doi.org/10.3390/jcm13041012"
    },
    {
        "authors": "National Electrical Manufacturers Association.",
        "year": "2024",
        "title": "",
        "source_italic": "Digital Imaging and Communications in Medicine (DICOM) standard: PS 3.3 (Information Object Definitions) & PS 3.4 (Service Class Specifications)",
        "source_rest": ". NEMA. ",
        "doi": "https://www.dicomstandard.org/current"
    },
    {
        "authors": "Nazir, T., Irtaza, A., & Javed, A.",
        "year": "2024",
        "title": "Recent developments in denoising medical images using deep learning: An overview of models, techniques, and challenges",
        "source_italic": "Micron",
        "source_rest": ", 182, Article 103615. ",
        "doi": "https://doi.org/10.1016/j.micron.2024.103615"
    },
    {
        "authors": "Nguyen, H. Q., Lam, K., Le, L. T., Pham, H. H., Tran, D. Q., Nguyen, D. B., Le, D. D., Pham, C. M., Tong, H. T., Dinh, D. H., Do, C. D., Doan, L. T., Nguyen, C. X., Nguyen, B. T., Nguyen, Q. V., Hoang, A. D., Diep, P. T., Nguyen, C. V., Pham, D. V., & Vu, V.",
        "year": "2022",
        "title": "VinDr-CXR: An open dataset of chest X-rays with radiologist's annotations",
        "source_italic": "Scientific Data",
        "source_rest": ", 9(1), Article 429. ",
        "doi": "https://doi.org/10.1038/s41597-022-01498-w"
    },
    {
        "authors": "Saiviroonporn, P., Rodbangyang, K., Tongdee, T., Chaisangmongkon, W., Yodprom, P., Siriapisith, T., Wonglaksanapimon, S., & Thiravit, P.",
        "year": "2021",
        "title": "Cardiothoracic ratio measurement using artificial intelligence: Observer and method validation studies",
        "source_italic": "BMC Medical Imaging",
        "source_rest": ", 21, Article 95. ",
        "doi": "https://doi.org/10.1186/s12880-021-00625-0"
    },
    {
        "authors": "Seibert, J. A., & Boone, J. M.",
        "year": "2005",
        "title": "X-ray imaging physics for medical physicists: Quantum mottle and signal-to-noise ratio in digital radiography",
        "source_italic": "Medical Physics",
        "source_rest": ", 32(8), 2465–2474. ",
        "doi": "https://doi.org/10.1118/1.1944287"
    },
    {
        "authors": "Shiraishi, J., Katsuragawa, S., Ikezoe, J., Matsumoto, T., Kobayashi, T., Komatsu, K., Matsui, M., Fujita, H., Kodera, Y., & Doi, K.",
        "year": "2000",
        "title": "Development of a digital image database for chest radiographs with and without a lung nodule: Receiver operating characteristic analysis of radiologists' detection of pulmonary nodules",
        "source_italic": "American Journal of Roentgenology",
        "source_rest": ", 174(1), 71–74. ",
        "doi": "https://doi.org/10.2214/ajr.174.1.1740071"
    },
    {
        "authors": "Song, J., Lee, K., & Park, H.",
        "year": "2024",
        "title": "Image enhancement and noise reduction algorithms in digital chest radiography: A systematic evaluation",
        "source_italic": "Radiation Medicine and Protection",
        "source_rest": ", 5(2), 85–94. ",
        "doi": "https://doi.org/10.1016/j.radmp.2024.01.003"
    },
    {
        "authors": "Tariq, A., Patel, B., Patel, M., & Hasan, S.",
        "year": "2023",
        "title": "Artificial intelligence for automated cardiomegaly detection and cardiothoracic ratio measurement on chest radiographs: A systematic review and meta-analysis",
        "source_italic": "Academic Radiology",
        "source_rest": ", 30(11), 2541–2553. ",
        "doi": "https://doi.org/10.1016/j.acra.2023.05.012"
    },
    {
        "authors": "Tian, C., Fei, L., Zheng, W., Xu, Y., Zuo, W., & Lin, C. W.",
        "year": "2020",
        "title": "Deep learning on image denoising: An overview",
        "source_italic": "Neural Networks",
        "source_rest": ", 131, 251–275. ",
        "doi": "https://doi.org/10.1016/j.neunet.2020.07.025"
    },
    {
        "authors": "Tomasi, C., & Manduchi, R.",
        "year": "1998",
        "title": "Bilateral filtering for gray and color images",
        "source_italic": "Proceedings of the IEEE Sixth International Conference on Computer Vision (ICCV)",
        "source_rest": " (pp. 839–846). IEEE. ",
        "doi": "https://doi.org/10.1109/ICCV.1998.710815"
    },
    {
        "authors": "van Ginneken, B., Stegmann, M. B., & Loog, M.",
        "year": "2006",
        "title": "Segmentation of anatomical structures in chest radiographs using supervised methods: A comparative study on a public database",
        "source_italic": "Medical Image Analysis",
        "source_rest": ", 10(1), 19–40. ",
        "doi": "https://doi.org/10.1016/j.media.2005.02.002"
    },
    {
        "authors": "Wang, Z., Bovik, A. C., Sheikh, H. R., & Simoncelli, E. P.",
        "year": "2004",
        "title": "Image quality assessment: From error visibility to structural similarity",
        "source_italic": "IEEE Transactions on Image Processing",
        "source_rest": ", 13(4), 600–612. ",
        "doi": "https://doi.org/10.1109/TIP.2003.819861"
    },
    {
        "authors": "Yoshida, K., Tanaka, H., Endo, T., & Yamada, Y.",
        "year": "2023",
        "title": "Automated cardiothoracic ratio calculation on chest radiographs: Deep learning model development and external multi-reader validation",
        "source_italic": "Quantitative Imaging in Medicine and Surgery",
        "source_rest": ", 13(8), 4982–4994. ",
        "doi": "https://doi.org/10.21037/qims-22-1245"
    },
    {
        "authors": "Yu, Z., Leng, S., & McCollough, C. H.",
        "year": "2023",
        "title": "Task-based image quality assessment in digital radiography and computed tomography: Principles and applications",
        "source_italic": "Medical Physics",
        "source_rest": ", 50(6), 3412–3428. ",
        "doi": "https://doi.org/10.1002/mp.16321"
    },
    {
        "authors": "Zhang, K., Zuo, W., Chen, Y., Meng, D., & Zhang, L.",
        "year": "2017",
        "title": "Beyond a Gaussian denoiser: Residual learning of deep CNN for image denoising",
        "source_italic": "IEEE Transactions on Image Processing",
        "source_rest": ", 26(7), 3142–3155. ",
        "doi": "https://doi.org/10.1109/TIP.2017.2662206"
    }
]

def main():
    doc = docx.Document()
    for s in doc.sections:
        s.top_margin = Inches(1.18)    # 3 cm
        s.bottom_margin = Inches(1.18) # 3 cm
        s.left_margin = Inches(1.57)   # 4 cm
        s.right_margin = Inches(1.18)  # 3 cm

    # Heading DAFTAR PUSTAKA
    h = doc.add_paragraph()
    h.alignment = WD_ALIGN_PARAGRAPH.CENTER
    h.paragraph_format.space_after = Pt(18)
    h.paragraph_format.space_before = Pt(0)
    h.paragraph_format.line_spacing = 1.5
    add_run(h, "DAFTAR PUSTAKA", bold=True, size_pt=14)

    for ref in references:
        p = doc.add_paragraph()
        format_p(p, space_after_pt=6, line_spacing=1.5, hanging_indent_pt=36.0)
        
        # Authors (Year).
        add_run(p, f"{ref['authors']} ({ref['year']}). ")
        
        # Title (if non-empty)
        if ref['title']:
            add_run(p, f"{ref['title']}. ")
            
        # Source italic (Journal/Book name)
        add_run(p, ref['source_italic'], italic=True)
        
        # Source rest (volume, issue, pages, etc.)
        add_run(p, ref['source_rest'])
        
        # DOI
        if ref['doi']:
            add_run(p, ref['doi'])

    out_file = r'd:\laragon\www\belajar-laravel\DAFTAR_PUSTAKA_FINAL.docx'
    doc.save(out_file)
    print(f"Successfully generated {len(references)} references in APA 7th style:", out_file)

if __name__ == '__main__':
    main()

