import lxml.etree as ET

xslt_path = r'C:\Program Files\Microsoft Office\root\Office16\MML2OMML.XSL'
xslt = ET.parse(xslt_path)
transform = ET.XSLT(xslt)

mml = '<math xmlns="http://www.w3.org/1998/Math/MathML"><mi>G</mi><mo>(</mo><mi>x</mi><mo>,</mo><mi>y</mi><mo>)</mo><mo>=</mo><mfrac><mn>1</mn><mrow><mn>2</mn><mi>π</mi><msup><mi>σ</mi><mn>2</mn></msup></mrow></mfrac></math>'
tree = ET.fromstring(mml)
omml = transform(tree)
print("Success! OMML tag:", omml.getroot().tag)
