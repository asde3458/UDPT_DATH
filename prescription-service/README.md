# Prescription Service (Python Flask)

Service quản lý đơn thuốc cho hệ thống bệnh viện.

## Cài đặt

1. Cài đặt dependencies:
```bash
pip install -r requirements.txt
```

2. Đảm bảo MongoDB đang chạy trên localhost:27017

3. Chạy service:
```bash
python app.py
```

Service sẽ chạy trên port 3002.

## API Endpoints

### Quản lý đơn thuốc (Doctors)

- `GET /api/prescriptions` - Lấy tất cả đơn thuốc
- `GET /api/prescriptions/doctor/<doctor_id>` - Lấy đơn thuốc theo doctor
- `POST /api/prescriptions` - Tạo đơn thuốc mới
- `PUT /api/prescriptions/<id>` - Cập nhật đơn thuốc
- `DELETE /api/prescriptions/<id>` - Xóa đơn thuốc
- `POST /api/prescriptions/<id>/medications` - Thêm thuốc vào đơn

### Xem đơn thuốc (Patients)

- `GET /api/prescriptions/patient/<patient_id>` - Lấy đơn thuốc theo patient
- `GET /api/prescriptions/<id>` - Xem chi tiết đơn thuốc

### Cập nhật trạng thái (Doctors & Patients)

- `PATCH /api/prescriptions/<id>/status` - Cập nhật trạng thái đơn thuốc

## Trạng thái đơn thuốc

- `pending` - Chờ lấy thuốc
- `dispensed` - Đã lấy thuốc
- `completed` - Hoàn thành

## Cấu trúc dữ liệu

### Prescription
```json
{
  "patient_id": "string",
  "doctor_id": "string", 
  "doctor_name": "string",
  "diagnosis": "string",
  "notes": "string",
  "status": "pending|dispensed|completed",
  "medications": [...],
  "created_at": "datetime",
  "updated_at": "datetime"
}
```

### Medication
```json
{
  "medication_name": "string",
  "dosage": "string",
  "frequency": "string", 
  "duration": "string",
  "instructions": "string"
}
``` 