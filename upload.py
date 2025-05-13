import os
from flask import Flask, request, jsonify, send_from_directory
from PyPDF2 import PdfReader
from docx import Document
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
import sqlite3
import mysql.connector
from mysql.connector import Error

app = Flask(__name__)

def get_db():
    try:
        conn = mysql.connector.connect(
            host='localhost',
            user='root', 
            password='', 
            database='elearning' 
        )
        if conn.is_connected():
            return conn
    except Error as e:
        print(f"Error connecting to MySQL: {e}")
        return None

def init_db():
    with get_db() as conn:
        cursor = conn.cursor()
        cursor.execute('''CREATE TABLE IF NOT EXISTS students_data (
                             id INT AUTO_INCREMENT PRIMARY KEY,
                             name VARCHAR(255) NOT NULL
                          )''')
        cursor.execute('''CREATE TABLE IF NOT EXISTS research_content (
                             id INT AUTO_INCREMENT PRIMARY KEY,
                             student_id INT NOT NULL,
                             content TEXT NOT NULL,
                             FOREIGN KEY (student_id) REFERENCES students_data(id)
                          )''')
        cursor.execute('''CREATE TABLE IF NOT EXISTS plagiarism_results (
                             id INT AUTO_INCREMENT PRIMARY KEY,
                             submission_id INT NOT NULL,
                             similar_submission_id INT NOT NULL,
                             similarity_percentage DECIMAL(5,2) NOT NULL,
                             is_plagiarized BOOLEAN NOT NULL,
                             FOREIGN KEY (submission_id) REFERENCES research_content(id),
                             FOREIGN KEY (similar_submission_id) REFERENCES research_content(id)
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

def calculate_similarity(new_content, existing_contents):
    vectorizer = TfidfVectorizer().fit_transform([new_content] + existing_contents)
    vectors = vectorizer.toarray()
    cosine_matrix = cosine_similarity(vectors)
    return cosine_matrix[0][1:]

@app.route('/')
def index():    
    return send_from_directory('.', '/student/project_list.php')

@app.route('/upload_research', methods=['POST'])
def upload_file():
    if 'file' not in request.files:
        return jsonify(error="No file part"), 400

    file = request.files['file']
    if file.filename == '':
        return jsonify(error="No selected file"), 400

    student_id = request.form.get('student_id')
    if not student_id:
        return jsonify(error="No student ID provided"), 400

    upload_folder = os.path.join(os.getcwd(), 'uploads')
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
            cursor.execute("SELECT id FROM students WHERE id = %s", (student_id,))
            if cursor.fetchone() is None:
                return jsonify(error="Student ID does not exist"), 400

            cursor.execute("INSERT INTO research_content (student_id, content) VALUES (%s, %s)", (student_id, content))
            archive_id = cursor.lastrowid

            cursor.execute("SELECT id, content FROM research_content WHERE student_id != %s", (student_id,))
            submissions = cursor.fetchall()

            similarity_results = []
            if submissions:
                existing_contents = [row[1] for row in submissions]
                similarities = calculate_similarity(content, existing_contents)

                for i, similarity in enumerate(similarities):
                    similarity_percentage = similarity * 100
                    if similarity_percentage > 0:
                        similarity_results.append({ 
                            'similar_submission_id': submissions[i][0],
                            'similarity_percentage': similarity_percentage
                        })
                        conn.execute("INSERT INTO plagiarism_result (archive_id, similar_archive_id, similarity_percentage) VALUES (?, ?, ?)",
                                     (archive_id, submissions[i][0], similarity_percentage, True))

            return jsonify(plagiarized=bool(similarity_results), similarityResults=similarity_results)
    except Exception as e:
        return jsonify(error=str(e)), 500
    finally:
        os.remove(file_path)

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
    app.run(debug=True)
