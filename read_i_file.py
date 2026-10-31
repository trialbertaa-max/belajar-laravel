with open('recovered_$I2V56TF.asd.asd', 'rb') as f:
    data = f.read()
    # In Windows recycle bin $I format: header(8 bytes), file size(8 bytes), file time(8 bytes), path string(UTF-16LE)
    path_bytes = data[24:]
    print("Original path:", path_bytes.decode('utf-16le', errors='ignore'))
