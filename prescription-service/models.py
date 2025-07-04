from pymongo import MongoClient
from datetime import datetime
import os

# MongoDB connection
MONGODB_URI = os.getenv('MONGODB_URI', 'mongodb://localhost:27017/hospital_prescription_service')
client = MongoClient(MONGODB_URI)
db = client.get_database()

class Prescription:
    def __init__(self, patient_id, doctor_id, doctor_name, diagnosis, notes=None, status="pending"):
        self.patient_id = patient_id
        self.patient_name = None  # Will be set separately
        self.doctor_id = doctor_id
        self.doctor_name = doctor_name
        self.diagnosis = diagnosis
        self.notes = notes
        self.status = status  # pending, dispensed, completed
        self.created_at = datetime.now()
        self.updated_at = datetime.now()
        self.medications = []

    def to_dict(self):
        return {
            'patient_id': self.patient_id,
            'patient_name': self.patient_name,
            'doctor_id': self.doctor_id,
            'doctor_name': self.doctor_name,
            'diagnosis': self.diagnosis,
            'notes': self.notes,
            'status': self.status,
            'created_at': self.created_at,
            'updated_at': self.updated_at,
            'medications': self.medications
        }

    @staticmethod
    def from_dict(data):
        prescription = Prescription(
            patient_id=data['patient_id'],
            doctor_id=data['doctor_id'],
            doctor_name=data['doctor_name'],
            diagnosis=data['diagnosis'],
            notes=data.get('notes'),
            status=data.get('status', 'pending')
        )
        prescription.medications = data.get('medications', [])
        prescription.created_at = data.get('created_at', datetime.now())
        prescription.updated_at = data.get('updated_at', datetime.now())
        return prescription

class PrescriptionItem:
    def __init__(self, medication_name, dosage, frequency, duration, instructions=None):
        self.medication_name = medication_name
        self.dosage = dosage
        self.frequency = frequency
        self.duration = duration
        self.instructions = instructions

    def to_dict(self):
        return {
            'medication_name': self.medication_name,
            'dosage': self.dosage,
            'frequency': self.frequency,
            'duration': self.duration,
            'instructions': self.instructions
        }

    @staticmethod
    def from_dict(data):
        return PrescriptionItem(
            medication_name=data['medication_name'],
            dosage=data['dosage'],
            frequency=data['frequency'],
            duration=data['duration'],
            instructions=data.get('instructions')
        ) 