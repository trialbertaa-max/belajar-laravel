import lxml.etree as ET

xslt_path = r'C:\Program Files\Microsoft Office\root\Office16\MML2OMML.XSL'
xslt = ET.parse(xslt_path)
transform = ET.XSLT(xslt)

mml1 = '''<math xmlns="http://www.w3.org/1998/Math/MathML">
  <msub><mi>G</mi><mi>σ</mi></msub>
  <mo>(</mo><mi>x</mi><mo>,</mo><mi>y</mi><mo>)</mo>
  <mo>=</mo>
  <mfrac>
    <mn>1</mn>
    <mrow><mn>2</mn><mi>π</mi><msup><mi>σ</mi><mn>2</mn></msup></mrow>
  </mfrac>
  <mi>exp</mi>
  <mfenced>
    <mrow>
      <mo>-</mo>
      <mfrac>
        <mrow><msup><mi>x</mi><mn>2</mn></msup><mo>+</mo><msup><mi>y</mi><mn>2</mn></msup></mrow>
        <mrow><mn>2</mn><msup><mi>σ</mi><mn>2</mn></msup></mrow>
      </mfrac>
    </mrow>
  </mfenced>
</math>'''

mml2 = '''<math xmlns="http://www.w3.org/1998/Math/MathML">
  <msub><mi>I</mi><mtext>Gaussian</mtext></msub>
  <mo>(</mo><mi>p</mi><mo>)</mo>
  <mo>=</mo>
  <mfrac>
    <mn>1</mn>
    <msub><mi>W</mi><mi>p</mi></msub>
  </mfrac>
  <munder>
    <mo>∑</mo>
    <mrow><mi>q</mi><mo>∈</mo><mi>Ω</mi></mrow>
  </munder>
  <msub><mi>G</mi><mi>σ</mi></msub>
  <mo>(</mo><mo>‖</mo><mi>p</mi><mo>-</mo><mi>q</mi><mo>‖</mo><mo>)</mo>
  <mo>·</mo>
  <mi>I</mi>
  <mo>(</mo><mi>q</mi><mo>)</mo>
</math>'''

mml3 = '''<math xmlns="http://www.w3.org/1998/Math/MathML">
  <msub><mi>W</mi><mi>p</mi></msub>
  <mo>=</mo>
  <munder>
    <mo>∑</mo>
    <mrow><mi>q</mi><mo>∈</mo><mi>Ω</mi></mrow>
  </munder>
  <msub><mi>G</mi><mi>σ</mi></msub>
  <mo>(</mo><mo>‖</mo><mi>p</mi><mo>-</mo><mi>q</mi><mo>‖</mo><mo>)</mo>
</math>'''

for idx, mml in enumerate([mml1, mml2, mml3], 1):
    tree = ET.fromstring(mml)
    omml = transform(tree)
    print(f"Eq {idx}: OMML transformed successfully. Root tag: {omml.getroot().tag}")
