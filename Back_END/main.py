import cv2
from fastapi import FastAPI, File, UploadFile, Form
from fastapi.responses import JSONResponse,FileResponse
from pydantic import BaseModel

from datetime import datetime
from io import BytesIO
from PIL import Image
import base64
from ml_operator import get_face_landmarks,draw_landmarks,detect_face_similarity,model,dataX

app = FastAPI()

@app.post("/detectingbell")
async def detecting_bell(
    file: UploadFile = File(...),
    name: str = Form(...),
    detect: str = Form(...)
):
    contents = await file.read()
    image = Image.open(BytesIO(contents))
    face_landmarks, mp_face_landmarks = get_face_landmarks(image)
    if not face_landmarks:
        return JSONResponse(status_code=400, content={"message": "No face detected"})
    p, similarity_percentage = detect_face_similarity(model, face_landmarks)
    image_with_landmarks = draw_landmarks(image,similarity_percentage,p,name)
    # print(face_landmarks)
    _, buffer = cv2.imencode('.png', image_with_landmarks)
    img_str = base64.b64encode(buffer).decode('utf-8')

    return {
        "name": name,
        "detect": detect,
        "similarity_percentage": p,
        "image_with_landmarks": img_str
    }

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)
