import os
import re
from flask import Flask, request, jsonify, send_from_directory
from PyPDF2 import PdfReader
from docx import Document
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
import sqlite3
import mysql.connector
from mysql.connector import Error
import nltk
nltk.download('punkt_tab')
from nltk.tokenize import sent_tokenize

app = Flask(__name__)
app.config['MAX_CONTENT_LENGTH'] = 20 * 1024 * 1024

def get_db():
    try:
        conn = mysql.connector.connect(
            host='localhost',
            user='root', 
            password='', 
            database='online_thesis' 
        )
        if conn.is_connected():
            print ("Connected to MySQL")
            return conn
    except Error as e:
        print(f"Error connecting to MySQL: {e}")
        return None

def init_db():
    with get_db() as conn:
        cursor = conn.cursor()
        cursor.execute('''CREATE TABLE IF NOT EXISTS students_data (
                             id INT AUTO_INCREMENT PRIMARY KEY,
                             student_id INT NOT NULL
                          )''')
        cursor.execute('''CREATE TABLE IF NOT EXISTS archive_research (
                             id INT AUTO_INCREMENT PRIMARY KEY,
                             archive_id INT NOT NULL,
                             student_id VARCHAR(20) NOT NULL,
                             content TEXT NOT NULL
                          )''')
        cursor.execute('''CREATE TABLE IF NOT EXISTS plagiarism_results (
                             id INT AUTO_INCREMENT PRIMARY KEY,
                             archive_id INT NOT NULL,
                             submitted_sentence TEXT NOT NULL,
                             existing_sentence TEXT NOT NULL,
                             similar_archive_id INT NOT NULL,
                             similarity_percentage DECIMAL(5,2) NOT NULL,
                             is_plagiarized BOOLEAN NOT NULL,
                             FOREIGN KEY (archive_id) REFERENCES archive_research(id),
                             FOREIGN KEY (similar_archive_id) REFERENCES archive_research(id)
                          )''')
        conn.commit()

def extract_text(file_path, file_type):
    content = ""
    if file_type == 'application/pdf':
        reader = PdfReader(file_path)
        for page in reader.pages:
            content += page.extract_text()
    elif file_type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
        doc = Document(file_path)
        for para in doc.paragraphs:
            content += para.text
    else:
        with open(file_path, 'r') as file:
            content = file.read()
    return content

def normalize_text(text):
    # Convert to lowercase
    text = text.lower()
    
    # Remove extra spaces and line breaks
    text = re.sub(r'\s+', ' ', text)
    
    # Normalize hyphenated words (removes spaces around hyphens)
    text = re.sub(r'\s*-\s*', '-', text)
    
    # Remove non-alphanumeric characters except spaces and hyphens
    text = re.sub(r'[^\w\s-]', '', text)
    
    return text

def split_into_sentences(text):
    sentences =  sent_tokenize(text)
    return [sentence for sentence in sentences if len(sentence.split()) >= 7]

def calculate_similarity(submitted_content, submissions):
    submitted_sentences = [normalize_text(sentence) for sentence in split_into_sentences(submitted_content)]

    
    # Prepare a list of sentences and their corresponding submission IDs
    all_existing_sentences = []
    all_existing_submission_ids = []

    for submission_id, content in submissions:
        sentences = [normalize_text(sentence) for sentence in split_into_sentences(content)]
        all_existing_sentences.extend(sentences)
        all_existing_submission_ids.extend([submission_id] * len(sentences))  # Match each sentence with its submission ID

    # Combine submitted and existing sentences
    all_sentences = submitted_sentences + all_existing_sentences

    if not all_sentences:
        return []

    # Vectorize the sentences
    vectorizer = TfidfVectorizer(lowercase=True, stop_words=None, token_pattern=r'\b\w+\b').fit_transform(all_sentences)

    # Similarity between submitted sentences and all existing ones
    similarity_matrix = cosine_similarity(vectorizer[:len(submitted_sentences)], vectorizer[len(submitted_sentences):])

    # Find matching sentences
    matching_results = []
    for i, submitted_sentence in enumerate(submitted_sentences):
        for j, existing_sentence in enumerate(all_existing_sentences):
            similarity_score = similarity_matrix[i][j]
            if similarity_score > 0.5:
                matching_results.append({
                    'submitted_sentence': submitted_sentence,
                    'existing_sentence': existing_sentence,
                    'similarity_percentage': similarity_score * 100,
                    'similar_archive_id': all_existing_submission_ids[j]
                })

    return matching_results

@app.route('/')
def index():    
    return send_from_directory('.', 'student/project_list.php')

@app.route('/upload_research', methods=['POST'])
def upload_file():
    print("Received a request")
    print("Form Data:", request.form) 
    print("Files Data:", request.files)
    if 'file' not in request.files:
        return jsonify(error="No file part"), 400

    file = request.files['file']
    if file.filename == '':
        return jsonify(error="No selected file"), 400

    archive_id = request.form.get('archive_id')
    student_id = request.form.get('student_id')
    print("Student Id:", student_id)
    project_title = request.form.get('project_title')
    date_of_submit = request.form.get('date_of_submit')
    project_year = request.form.get('year')
    department_id = request.form.get('department_id')
    course_id = request.form.get('course_id')
    abstract = request.form.get('abstract')
    keywords = request.form.get('keywords')
    project_members = request.form.get('project_members')
    pdf_path = request.form.get('pdf_path')
    owner_email = request.form.get('owner_email')
    
    if not student_id:
        return jsonify(error="No student ID provided"), 400

    upload_folder = os.path.join(os.getcwd(), 'pdf_files')
    os.makedirs(upload_folder, exist_ok=True)
    file_path = os.path.join(upload_folder, file.filename)
    file.save(file_path)

    try:
        file_type = file.content_type
        content = extract_text(file_path, file_type)

        if not content:
            return jsonify(error="Failed to extract content from file"), 400

        with get_db() as conn:
            cursor = conn.cursor()
            cursor.execute("SELECT id FROM students_data WHERE id = %s", (student_id,))
            if cursor.fetchone() is None:
                return jsonify(error="Student ID does not exist"), 400

            cursor.execute("INSERT INTO archive_research (archive_id, student_id, department_id, course_id, project_title, dateOFSubmit, project_year, project_abstract, keywords, content, research_owner_email, project_members, documents) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)", (archive_id, student_id, department_id, course_id, project_title, date_of_submit, project_year, abstract, keywords, content, owner_email, project_members, pdf_path))
            conn.commit()
            archive_id = cursor.lastrowid

            cursor.execute("SELECT id, content FROM archive_research WHERE student_id != %s", (student_id,))
            submissions = cursor.fetchall()

            similarity_results = []
            if submissions:
                matching_sentences = calculate_similarity(content, submissions)
                print("Matching sentences", matching_sentences)
                
                accurate_percentage = 0
                plagiarized_count = 0
                total_sentences = len(split_into_sentences(content))

                print("Total sentences", total_sentences)
                for match in matching_sentences:
                    similarity_results.append({
                        'submitted_sentence': match['submitted_sentence'],
                        'existing_sentence': match['existing_sentence'],
                        'similarity_percentage': match['similarity_percentage'],
                        'similar_archive_id': match['similar_archive_id'], 
                    })
                    
                    cursor.execute("INSERT INTO plagiarism_results (archive_id, similar_archive_id, submitted_sentence, existing_sentence, similarity_percentage, is_plagiarized) VALUES (%s, %s, %s, %s, %s, %s)",
                                (archive_id, match['similar_archive_id'], match['submitted_sentence'], match['existing_sentence'], match['similarity_percentage'], True))
                    conn.commit()

                    plagiarized_count += 1
                    accurate_percentage += float(match['similarity_percentage'])


                plagiarism_percentage = (accurate_percentage / plagiarized_count) if total_sentences > 0 and plagiarized_count > 0 else 0
                print("Plagiarized counts", plagiarized_count)
                print("Plagiarism percentage", plagiarism_percentage)
                if plagiarism_percentage > 0:
                    cursor.execute("INSERT INTO plagiarism_summary (archive_id, similar_archive_id, plagiarism_percentage) VALUES (%s, %s, %s)", 
                                   (archive_id, match['similar_archive_id'], plagiarism_percentage))
                    conn.commit()
            return jsonify(plagiarized=bool(similarity_results), similarityResults=similarity_results, plagiarism_percentage=plagiarism_percentage)
    except Exception as e:
        print(f"Error: {str(e)}") 
        return jsonify(error=str(e)), 500

@app.route('/check_db_connection', methods=['GET'])
def check_db_connection():
    try:
        with get_db() as conn:
            conn.execute("SELECT 1")
        return jsonify(status="Database connection successful"), 200
    except Exception as e:
        return jsonify(status="Database connection failed", error=str(e)), 500
    
if __name__ == '__main__':
    init_db()
    app.run(debug=True, port=3000)