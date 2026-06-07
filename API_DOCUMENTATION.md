# Attendance System API Documentation

This document provides a comprehensive guide to all API endpoints available in the Attendance Management System.

## Base URL
```
http://localhost:8000/api
```

---

## Students Management

### List All Students
**GET** `/students`

Returns a list of all students.

```bash
curl http://localhost:8000/api/students
```

**Response:**
```json
[
  {
    "id": 1,
    "name": "Ahmad Fauzi",
    "email": "ahmad.fauzi@sekolah.ac.id",
    "card_id": "CARD-001",
    "face_id": "FACE-001",
    "department": "Teknik Alat Berat",
    "enrolled_date": "2024-01-15",
    "nisn": "0012345678",
    "phone": "081234567890",
    "address": "Jl. Merdeka No. 123, Jakarta Pusat",
    "created_at": "2026-05-25T14:18:08.000000Z",
    "updated_at": "2026-05-25T14:18:08.000000Z"
  }
]
```

### Get Specific Student
**GET** `/students/{id}`

Returns a student with their attendance records and leave requests.

### Create Student
**POST** `/students`

Create a new student record.

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john.doe@sekolah.ac.id",
  "card_id": "CARD-006",
  "face_id": "FACE-006",
  "department": "Teknik Informatika",
  "enrolled_date": "2024-03-01",
  "nisn": "0012345683",
  "phone": "081234567895",
  "address": "Jl. Sudirman No. 300, Jakarta"
}
```

### Update Student
**PUT** `/students/{id}`

Update an existing student record.

### Delete Student
**DELETE** `/students/{id}`

Delete a student record.

---

## Teachers Management

### List All Teachers
**GET** `/teachers`

Returns a list of all teachers.

### Get Specific Teacher
**GET** `/teachers/{id}`

Returns a teacher with their leave requests.

### Create Teacher
**POST** `/teachers`

Create a new teacher record.

**Request Body:**
```json
{
  "name": "Dr. Muhammad Ali",
  "email": "muhammad.ali@sekolah.ac.id",
  "card_id": "TCH-CARD-006",
  "face_id": "TCH-FACE-006",
  "subject": "Kewarganegaraan",
  "enrolled_date": "2022-08-01",
  "phone": "082234567895",
  "address": "Jl. Gatot Subroto No. 400, Jakarta"
}
```

### Update Teacher
**PUT** `/teachers/{id}`

Update an existing teacher record.

### Delete Teacher
**DELETE** `/teachers/{id}`

Delete a teacher record.

---

## Attendance Records

### List Attendance Records
**GET** `/attendance`

Query parameters:
- `student_id` - Filter by student ID
- `date` - Filter by specific date (YYYY-MM-DD)
- `status` - Filter by status (present, late, absent)

### Create Attendance Record
**POST** `/attendance`

Create a new attendance record.

**Request Body:**
```json
{
  "student_id": 1,
  "timestamp": "2026-05-25 08:30:00",
  "method": "face",
  "status": "present",
  "location": "Main Gate"
}
```

### Get Daily Statistics
**GET** `/attendance/statistics`

Query parameters:
- `date` - Specific date (defaults to today)

**Response:**
```json
{
  "date": "2026-05-25",
  "present": 85,
  "late": 12,
  "absent": 3,
  "total": 100
}
```

---

## Leave Requests

### List Leave Requests
**GET** `/leave-requests`

Query parameters:
- `status` - Filter by status (pending, approved, rejected)
- `student_id` - Filter by student
- `teacher_id` - Filter by teacher

### Create Leave Request
**POST** `/leave-requests`

Create a new leave request.

**Request Body:**
```json
{
  "student_id": 1,
  "reason": "Medical Appointment",
  "start_date": "2026-05-26",
  "end_date": "2026-05-27",
  "description": "Need to visit the doctor for check-up"
}
```

### Approve Leave Request
**POST** `/leave-requests/{id}/approve`

Approve a pending leave request.

### Reject Leave Request
**POST** `/leave-requests/{id}/reject`

Reject a pending leave request.

---

## Bulk Import

### Import Students from CSV
**POST** `/import/students`

Upload a CSV file to import multiple students.

**Request:**
```bash
curl -F "file=@students.csv" http://localhost:8000/api/import/students
```

**CSV Format:**
```
name,email,card_id,face_id,department,enrolled_date,nisn,phone,address
Ahmad Fauzi,ahmad.fauzi@sekolah.ac.id,CARD-001,FACE-001,Teknik Alat Berat,2024-01-15,0012345678,081234567890,Jl. Merdeka No. 123
```

### Import Teachers from CSV
**POST** `/import/teachers`

Upload a CSV file to import multiple teachers.

### Import Attendance from CSV
**POST** `/import/attendance`

Upload a CSV file to import attendance records.

---

## Reports & Statistics

### Student Attendance Recap
**GET** `/reports/student/{id}/attendance-recap`

Query parameters:
- `month` - Month number (1-12)
- `year` - Year

Returns attendance statistics for a specific student in a given month.

### Student Attendance History
**GET** `/reports/student/{id}/attendance-history`

Query parameters:
- `limit` - Number of records to return (default: 50)

Returns recent attendance records for a student.

### Admin Attendance Recap
**GET** `/reports/admin/attendance-recap`

Query parameters:
- `date` - Specific date (YYYY-MM-DD)
- `department` - Filter by department

Returns attendance statistics for all students or a specific department.

### Daily Statistics
**GET** `/reports/daily-statistics`

Query parameters:
- `start_date` - Start date (default: 30 days ago)
- `end_date` - End date (default: today)

Returns daily attendance statistics for a date range.

### Department Summary
**GET** `/reports/department-summary`

Query parameters:
- `date` - Specific date (YYYY-MM-DD)

Returns attendance summary grouped by department.

### Student Summary
**GET** `/reports/student-summary`

Query parameters:
- `month` - Month number (1-12)
- `year` - Year

Returns attendance summary for all students.

---

## Profiles

### Get Student Profile
**GET** `/profile/student/{id}`

Returns student profile with monthly attendance statistics and recent attendance records.

### Update Student Profile
**PUT** `/profile/student/{id}`

Update student profile information.

**Request Body:**
```json
{
  "name": "Ahmad Fauzi Updated",
  "phone": "081234567999",
  "address": "Jl. Merdeka No. 456, Jakarta Barat"
}
```

### Get Teacher Profile
**GET** `/profile/teacher/{id}`

Returns teacher profile with pending leave requests.

### Update Teacher Profile
**PUT** `/profile/teacher/{id}`

Update teacher profile information.

### Get Teacher Attendance
**GET** `/profile/teacher/{id}/attendance`

Returns teacher attendance information (if available).

---

## Error Responses

All error responses follow this format:

```json
{
  "message": "Error message",
  "errors": {
    "field_name": ["Error details"]
  }
}
```

### Common HTTP Status Codes
- `200` - Success
- `201` - Created
- `400` - Bad Request
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

---

## Testing the API

### Using cURL

```bash
# List all students
curl http://localhost:8000/api/students

# Get specific student
curl http://localhost:8000/api/students/1

# Create student
curl -X POST http://localhost:8000/api/students \
  -H "Content-Type: application/json" \
  -d '{"name": "Test Student", "email": "test@example.com", "department": "IT"}'

# Get attendance statistics
curl "http://localhost:8000/api/attendance/statistics?date=2026-05-25"
```

### Using Postman

1. Import the API endpoints into Postman
2. Create requests for each endpoint
3. Test with sample data
4. Save collections for future reference

---

## Database Structure

### Students Table
- id (Primary Key)
- name (String)
- email (Unique)
- card_id (Unique, Nullable)
- face_id (Unique, Nullable)
- department (String)
- enrolled_date (DateTime, Nullable)
- nisn (String, Nullable)
- phone (String, Nullable)
- address (String, Nullable)

### Teachers Table
- id (Primary Key)
- name (String)
- email (Unique)
- card_id (Unique, Nullable)
- face_id (Unique, Nullable)
- subject (String)
- enrolled_date (DateTime, Nullable)
- phone (String, Nullable)
- address (String, Nullable)

### Attendance Records Table
- id (Primary Key)
- student_id (Foreign Key)
- timestamp (DateTime)
- method (Enum: face, card)
- status (Enum: present, late, absent)
- location (String, Nullable)

### Leave Requests Table
- id (Primary Key)
- student_id (Foreign Key, Nullable)
- teacher_id (Foreign Key, Nullable)
- reason (String)
- start_date (Date)
- end_date (Date)
- description (Text)
- status (Enum: pending, approved, rejected)
- submitted_at (DateTime)

---

## Running the Laravel Server

```bash
php artisan serve
```

The API will be available at `http://localhost:8000/api`

---

## Notes

- All timestamps are in UTC timezone
- Authentication middleware can be added by uncommenting auth:sanctum in api.php
- CSV import files should have headers in the first row
- Attendance status is automatically determined based on timestamp hour (8+ hours = late)
