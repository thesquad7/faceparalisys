import cv2
import mediapipe as mp

import numpy as np
import joblib

mp_face_mesh = mp.solutions.face_mesh

model_file = "trained_XGBoost_model_learning_rate=0.2_max_depth=7_n_estimators=300.joblib"
model_folder = "model/"


model_filename =  model_folder+model_file
model = joblib.load(model_filename)

def get_unique(connections):
    indices = set()
    for connection in connections:
        indices.update(connection)
    return sorted(indices)
peta_wajah = mp_face_mesh.FACEMESH_FACE_OVAL
peta_alis = mp_face_mesh.FACEMESH_LEFT_EYEBROW | mp_face_mesh.FACEMESH_RIGHT_EYEBROW
peta_hidung = mp_face_mesh.FACEMESH_NOSE
peta_mulut = mp_face_mesh.FACEMESH_LIPS
peta_point4d = peta_wajah | peta_alis | peta_hidung | peta_mulut
peta_point4d = get_unique(peta_point4d)
_model = list(peta_point4d)

face_mesh = mp_face_mesh.FaceMesh(static_image_mode=True, max_num_faces=1, min_detection_confidence=0.5)
dataX = {}
persentage_data = 0
def get_face_landmarks(image):
    results = face_mesh.process(cv2.cvtColor(np.array(image), cv2.COLOR_BGR2RGB))
    exclude_indices = ['13', '22', '23', '40', '41', '44', '49', '55', '57', '76', 
           '87', '89', '91', '93', '96', '98', '112', '113', '134', 
           '137', '155', '163', '169', '171', '199', '201', '203', 
           '205', '14', '23', '24', '41', '42', '45', '50', '56', 
           '58', '77', '88', '90', '92', '94', '97', '99', '113', 
           '114', '135', '138', '156', '164', '170', '172', '200', 
           '202', '204', '206']
    unique_indices = sorted(set(int(i) for i in exclude_indices))
    if results.multi_face_landmarks:
        for face_landmarks in results.multi_face_landmarks:
            faced = face_landmarks.landmark
            landmarks = []
            base_landmark =[]
            for index in _model:
                x = int(faced[index].x * image.width)
                y = int(faced[index].y * image.height)
                dataX[index] = ([x,y])
                base_landmark.extend([x, y])
                landmarks = [value for i, value in enumerate(base_landmark) if i not in unique_indices]
        return landmarks, face_landmarks
    else:
        return None, None

def detect_face_similarity(model, face_data):
    face_data = np.array(face_data).reshape(1, -1)  
    prediction = model.predict_proba(face_data)
    print(prediction)
    similarity_percentage = int(prediction[0][0] * 100)
    print (similarity_percentage)
    if similarity_percentage <= 50 :
        detection =  "Tidak ada indikasi Bell's Palsy"
    else:
        detection = "Terdeteksi Bell's Palsy"
    return detection, similarity_percentage

def draw_landmarks(image,p,kalimat, nama):
    img = np.array(image)
    for index in dataX:
        cv2.circle(img, (dataX[index][0], dataX[index][1]), 3, (0, 50, 255), -1)
        cv2.putText(img,f'Nama : {nama}',(20,30), cv2.FONT_HERSHEY_PLAIN, 2 , (255 ,255 , 255) , 2)
        cv2.putText(img,f'{kalimat}',(20,60), cv2.FONT_HERSHEY_DUPLEX, 1, (140 , 255 , 200) , 2)
        cv2.putText(img,f'Persentase Deteksi :{p}%',(20,92), cv2.FONT_HERSHEY_DUPLEX, 1, (0 , 140 , 200) , 2)
    cv2.imshow("hasil",img)
    cv2.waitKey(0)
    return img
