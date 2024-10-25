import cv2
import numpy as np
import sys
import json
import base64
from io import BytesIO
import os
import traceback

def process_image(image_data):
    try:
        # Convert base64 string to image
        nparr = np.frombuffer(base64.b64decode(image_data), np.uint8)
        image = cv2.imdecode(nparr, cv2.IMREAD_COLOR)
        
        if image is None:
            raise ValueError("Failed to decode image")

        GSD = 0.5  # หน่วยเป็นเซนติเมตร/พิกเซล

        # ประมวลผลภาพ
        denoised_image = cv2.fastNlMeansDenoisingColored(image, None, 10, 10, 7, 21)
        gray_image = cv2.cvtColor(denoised_image, cv2.COLOR_BGR2GRAY)
        _, thresh_image = cv2.threshold(gray_image, 80, 255, cv2.THRESH_BINARY_INV)

        kernel = cv2.getStructuringElement(cv2.MORPH_RECT, (3, 3))
        morph_image = cv2.morphologyEx(thresh_image, cv2.MORPH_CLOSE, kernel)

        contours, _ = cv2.findContours(morph_image, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_NONE)

        # วาดคอนทัวร์
        morph_contour_image = cv2.cvtColor(morph_image, cv2.COLOR_GRAY2BGR)
        cv2.drawContours(morph_contour_image, contours, -1, (0, 255, 0), 1)

        # คำนวณความกว้างของรอยร้าว
        crack_widths = []
        for contour in contours:
            x, y, w, h = cv2.boundingRect(contour)
            crack_width_cm = w * GSD
            crack_widths.append(crack_width_cm)

        average_width = sum(crack_widths) / len(crack_widths) if crack_widths else 0

        # ประเมินความรุนแรง
        if average_width < 0.6:
            severity = "Low Severity (ขนาดเล็ก)"
        elif 0.6 <= average_width <= 1.9:
            severity = "Moderate Severity (ขนาดกลาง)"
        else:
            severity = "High Severity (ขนาดใหญ่)"

        # แปลงภาพผลลัพธ์เป็น base64
        _, processed_buffer = cv2.imencode('.jpg', morph_contour_image)
        processed_image_base64 = base64.b64encode(processed_buffer).decode('utf-8')

        # สร้าง dictionary ของผลลัพธ์
        results = {
            "average_width": round(average_width, 2),
            "severity": severity,
            "processed_image": processed_image_base64
        }

        return json.dumps(results)
    except Exception as e:
        error_details = {
            "error": str(e),
            "traceback": traceback.format_exc()
        }
        return json.dumps(error_details)

if __name__ == "__main__":
    try:
        # รับข้อมูลรูปภาพ base64 จาก stdin
        image_data = sys.stdin.read().strip()
        
        if not image_data:
            print(json.dumps({"error": "No input data received"}))
        else:
            # ประมวลผลและส่งผลลัพธ์กลับ
            result = process_image(image_data)
            print(result)
    except Exception as e:
        error_result = json.dumps({
            "error": str(e),
            "traceback": traceback.format_exc()
        })
        print(error_result)