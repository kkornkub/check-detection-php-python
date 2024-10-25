Pavement Crack Analysis System (ระบบวิเคราะห์รอยร้าวถนน)
ระบบวิเคราะห์รอยร้าวถนนด้วยการประมวลผลภาพ โดยใช้ OpenCV ในการตรวจจับและวิเคราะห์ขนาดของรอยร้าว พร้อมระบุระดับความรุนแรง
ความสามารถของระบบ

📸 อัปโหลดและประมวลผลรูปภาพรอยร้าวถนน
🔍 ตรวจจับรอยร้าวด้วย Computer Vision
📏 วัดขนาดความกว้างของรอยร้าว
📊 ประเมินระดับความรุนแรงของรอยร้าว
🖼️ แสดงผลภาพที่ผ่านการวิเคราะห์พร้อมการระบุตำแหน่งรอยร้าว

เทคโนโลยีที่ใช้

Python 3.9+
PHP 7.4+
OpenCV
NumPy
Bootstrap 5
HTML/CSS

การติดตั้ง
ความต้องการของระบบ

Python 3.9 หรือสูงกว่า
PHP 7.4 หรือสูงกว่า
Web Server (Apache/Nginx)
pip (Python package manager)

ขั้นตอนการติดตั้ง

Clone repository:

bashCopygit clone https://github.com/yourusername/pavement-crack-analysis.git
cd pavement-crack-analysis

ติดตั้ง Python dependencies:

bashCopypip install opencv-python numpy matplotlib

สร้างโฟลเดอร์สำหรับเก็บรูปภาพ:

bashCopymkdir uploads
chmod 777 uploads  # สำหรับ Linux/Mac

แก้ไขการตั้งค่า Python path ในไฟล์ index.php:

phpCopy$python_path = 'C:\\Path\\To\\Your\\Python\\python.exe'; // แก้ไขตาม path ที่ติดตั้ง Python
โครงสร้างไฟล์
Copypavement-crack-analysis/
├── index.php              # หน้าเว็บหลักและการประมวลผล PHP
├── crack_detection.py     # สคริปต์ Python สำหรับวิเคราะห์รูปภาพ
├── uploads/              # โฟลเดอร์เก็บรูปภาพที่อัปโหลด
└── README.md             # เอกสารอธิบายโครงการ
วิธีการใช้งาน

เปิดเว็บเบราว์เซอร์และไปที่ URL ของระบบ
คลิกปุ่ม "เลือกไฟล์" เพื่อเลือกรูปภาพรอยร้าวที่ต้องการวิเคราะห์
คลิกปุ่ม "วิเคราะห์รูปภาพ" เพื่อเริ่มการประมวลผล
ระบบจะแสดงผลการวิเคราะห์ ประกอบด้วย:

ขนาดเฉลี่ยของรอยร้าว
ระดับความรุนแรง
ภาพต้นฉบับและภาพที่ผ่านการวิเคราะห์



การประเมินความรุนแรง

🟢 ขนาดเล็ก (Low Severity): ความกว้างน้อยกว่า 0.6 ซม.
🟡 ขนาดกลาง (Moderate Severity): ความกว้าง 0.6-1.9 ซม.
🔴 ขนาดใหญ่ (High Severity): ความกว้างมากกว่า 1.9 ซม.

การแก้ไขปัญหาเบื้องต้น

Error: No module named 'cv2'

ติดตั้ง OpenCV: pip install opencv-python


Permission denied on uploads folder

ตรวจสอบสิทธิ์การเขียนไฟล์ในโฟลเดอร์ uploads
สำหรับ Linux/Mac: chmod 777 uploads


Python path not found

ตรวจสอบ path ของ Python ในไฟล์ index.php
ใช้คำสั่ง where python (Windows) หรือ which python (Linux/Mac) เพื่อหา path ที่ถูกต้อง



การพัฒนาเพิ่มเติม

 เพิ่มการวิเคราะห์ประเภทของรอยร้าว
 เพิ่มการคำนวณพื้นที่รอยร้าว
 เพิ่มการส่งออกรายงานในรูปแบบ PDF
 เพิ่มการบันทึกประวัติการวิเคราะห์
 พัฒนา API สำหรับการเรียกใช้งานจากภายนอก