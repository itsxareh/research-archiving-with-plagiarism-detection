from PIL import Image
import pytesseract

# If on Windows, specify the path to the Tesseract executable
# pytesseract.pytesseract.tesseract_cmd = r'C:\Program Files\Tesseract-OCR\tesseract.exe'

# Load the image
image_path = "image.png"  # Replace with your image file
image = Image.open(image_path)

# Perform OCR
text = pytesseract.image_to_string(image)

# Print the extracted text
print("Extracted Text:")
print(text)