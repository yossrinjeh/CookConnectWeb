import os
import cv2
import mysql.connector

# Load pre-trained Haar Cascade classifier for face detection
face_cascade = cv2.CascadeClassifier(cv2.data.haarcascades + 'haarcascade_frontalface_default.xml')

# Connect to the MySQL database
conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="pidev"
)

# Create a cursor object to execute SQL queries
cursor = conn.cursor()

# Directory containing images
image_dir = 'G:/Xampp 8/htdocs/CookConnect/public/images/'

# Default image if no face is detected
default_img = "avatar.png"

# Iterate over images
for filename in os.listdir(image_dir):
    if filename.endswith(".jpg") or filename.endswith(".png"):
        # Read image
        image_path = os.path.join(image_dir, filename)
        img = cv2.imread(image_path)
        
        # Convert image to grayscale for face detection
        gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
        
        # Detect faces
        faces = face_cascade.detectMultiScale(gray, scaleFactor=1.1, minNeighbors=5, minSize=(30, 30))
        
        # If no faces are detected, update the user's image in the database
        if len(faces) == 0:
            # Update the user's image in the database
            update_query = "UPDATE user SET image = %s WHERE image = %s"
            current_img = filename
            try:
                # Execute the update query with the default image
                cursor.execute(update_query, (default_img, current_img))

                # Commit the changes
                conn.commit()
                print(f"User image updated to default for {current_img} because no face was detected.")

            except mysql.connector.Error as err:
                print("Error updating user image:", err)

# Close cursor and connection
cursor.close()
conn.close()
