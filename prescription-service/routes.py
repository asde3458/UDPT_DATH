from flask import Blueprint, request, jsonify
from models import db, Prescription, PrescriptionItem
from datetime import datetime
from bson import ObjectId
from typing import Any

prescription_routes = Blueprint('prescriptions', __name__)

# Helper function to convert ObjectId to string
def serialize_prescription(prescription):
    prescription['_id'] = str(prescription['_id'])
    prescription['created_at'] = prescription['created_at'].isoformat() if prescription['created_at'] else None
    prescription['updated_at'] = prescription['updated_at'].isoformat() if prescription['updated_at'] else None
    return prescription

# Get all prescriptions (for doctors)
@prescription_routes.route('/', methods=['GET'])
def get_all_prescriptions():
    try:
        prescriptions = list(db.prescriptions.find())
        prescriptions = [serialize_prescription(p) for p in prescriptions]
        return jsonify(prescriptions), 200
    except Exception as e:
        return jsonify({'error': str(e)}), 500

# Get prescriptions by patient ID (for patients to view their prescriptions)
@prescription_routes.route('/patient/<patient_id>', methods=['GET'])
def get_patient_prescriptions(patient_id):
    try:
        # Get query parameters for filtering
        status = request.args.get('status')
        date = request.args.get('date')
        search = request.args.get('search')
        
        # Build query
        query = {'patient_id': patient_id}
        
        if status:
            query['status'] = status
        if date:
            # Convert date string to datetime for comparison
            date_obj = datetime.strptime(date, '%Y-%m-%d')
            query['created_at'] = {
                '$gte': date_obj,
                '$lt': date_obj.replace(hour=23, minute=59, second=59)
            }
        if search:
            # Search in doctor_name or diagnosis
            query['$or'] = [
                {'doctor_name': {'$regex': search, '$options': 'i'}},
                {'diagnosis': {'$regex': search, '$options': 'i'}}
            ]
        
        prescriptions = list(db.prescriptions.find(query))
        prescriptions = [serialize_prescription(p) for p in prescriptions]
        return jsonify(prescriptions), 200
    except Exception as e:
        return jsonify({'error': str(e)}), 500

# Get prescriptions by doctor ID (for doctors to view their prescriptions)
@prescription_routes.route('/doctor/<doctor_id>', methods=['GET'])
def get_doctor_prescriptions(doctor_id):
    try:
        # Get query parameters for filtering
        status = request.args.get('status')
        date = request.args.get('date')
        search = request.args.get('search')
        
        # Build query
        query = {'doctor_id': doctor_id}
        
        if status:
            query['status'] = status
        if date:
            # Convert date string to datetime for comparison
            date_obj = datetime.strptime(date, '%Y-%m-%d')
            query['created_at'] = {
                '$gte': date_obj,
                '$lt': date_obj.replace(hour=23, minute=59, second=59)
            }
        if search:
            # Search in patient_name or diagnosis
            query['$or'] = [
                {'patient_name': {'$regex': search, '$options': 'i'}},
                {'diagnosis': {'$regex': search, '$options': 'i'}}
            ]
        
        prescriptions = list(db.prescriptions.find(query))
        prescriptions = [serialize_prescription(p) for p in prescriptions]
        return jsonify(prescriptions), 200
    except Exception as e:
        return jsonify({'error': str(e)}), 500

# Get single prescription by ID
@prescription_routes.route('/<prescription_id>', methods=['GET'])
def get_prescription(prescription_id):
    try:
        prescription = db.prescriptions.find_one({'_id': ObjectId(prescription_id)})
        if not prescription:
            return jsonify({'error': 'Prescription not found'}), 404
        
        return jsonify(serialize_prescription(prescription)), 200
    except Exception as e:
        return jsonify({'error': str(e)}), 500

# Create new prescription (doctors only)
@prescription_routes.route('/', methods=['POST'])
def create_prescription():
    try:
        data = request.get_json()
        
        # Validate required fields
        required_fields = ['patient_id', 'patient_name', 'doctor_id', 'doctor_name', 'diagnosis', 'medications']
        for field in required_fields:
            if field not in data:
                return jsonify({'error': f'Missing required field: {field}'}), 400
        
        # Create prescription object
        prescription = Prescription(
            patient_id=data['patient_id'],
            doctor_id=data['doctor_id'],
            doctor_name=data['doctor_name'],
            diagnosis=data['diagnosis'],
            notes=data.get('notes'),
            status=data.get('status', 'pending')
        )
        
        # Add patient_name to prescription
        prescription.patient_name = data['patient_name']
        
        # Add medications
        for med_data in data['medications']:
            medication = PrescriptionItem.from_dict(med_data)
            prescription.medications.append(medication.to_dict())
        
        # Save to database
        result = db.prescriptions.insert_one(prescription.to_dict())
        
        # Return created prescription
        created_prescription = db.prescriptions.find_one({'_id': result.inserted_id})
        return jsonify(serialize_prescription(created_prescription)), 201
        
    except Exception as e:
        return jsonify({'error': str(e)}), 500

# Update prescription (doctors only)
@prescription_routes.route('/<prescription_id>', methods=['PUT'])
def update_prescription(prescription_id):
    try:
        data = request.get_json()
        
        # Check if prescription exists
        existing_prescription = db.prescriptions.find_one({'_id': ObjectId(prescription_id)})
        if not existing_prescription:
            return jsonify({'error': 'Prescription not found'}), 404
        
        # Update fields
        update_data: dict[str, Any] = {
            'updated_at': datetime.now(),
            'diagnosis': data.get('diagnosis', existing_prescription['diagnosis']),
            'notes': data.get('notes', existing_prescription['notes']),
            'medications': existing_prescription['medications']
        }
        
        # Update patient_name if provided
        if 'patient_name' in data:
            update_data['patient_name'] = data['patient_name']
        
        # Update medications if provided
        if 'medications' in data:
            medications = []
            for med_data in data['medications']:
                medication = PrescriptionItem.from_dict(med_data)
                medications.append(medication.to_dict())
            update_data['medications'] = medications
        
        # Update in database
        result = db.prescriptions.update_one(
            {'_id': ObjectId(prescription_id)},
            {'$set': update_data}
        )
        
        if result.modified_count == 0:
            return jsonify({'error': 'No changes made'}), 400
        
        # Return updated prescription
        updated_prescription = db.prescriptions.find_one({'_id': ObjectId(prescription_id)})
        return jsonify(serialize_prescription(updated_prescription)), 200
        
    except Exception as e:
        return jsonify({'error': str(e)}), 500

# Update prescription status (doctors and patients can update status)
@prescription_routes.route('/<prescription_id>/status', methods=['PATCH'])
def update_prescription_status(prescription_id):
    try:
        data = request.get_json()
        
        if 'status' not in data:
            return jsonify({'error': 'Status is required'}), 400
        
        # Validate status
        valid_statuses = ['pending', 'dispensed', 'completed']
        if data['status'] not in valid_statuses:
            return jsonify({'error': f'Invalid status. Must be one of: {valid_statuses}'}), 400
        
        # Check if prescription exists
        existing_prescription = db.prescriptions.find_one({'_id': ObjectId(prescription_id)})
        if not existing_prescription:
            return jsonify({'error': 'Prescription not found'}), 404
        
        # Update status
        result = db.prescriptions.update_one(
            {'_id': ObjectId(prescription_id)},
            {
                '$set': {
                    'status': data['status'],
                    'updated_at': datetime.now()
                }
            }
        )
        
        if result.modified_count == 0:
            return jsonify({'error': 'No changes made'}), 400
        
        # Return updated prescription
        updated_prescription = db.prescriptions.find_one({'_id': ObjectId(prescription_id)})
        return jsonify(serialize_prescription(updated_prescription)), 200
        
    except Exception as e:
        return jsonify({'error': str(e)}), 500

# Delete prescription (doctors only)
@prescription_routes.route('/<prescription_id>', methods=['DELETE'])
def delete_prescription(prescription_id):
    try:
        # Check if prescription exists
        existing_prescription = db.prescriptions.find_one({'_id': ObjectId(prescription_id)})
        if not existing_prescription:
            return jsonify({'error': 'Prescription not found'}), 404
        
        # Delete from database
        result = db.prescriptions.delete_one({'_id': ObjectId(prescription_id)})
        
        if result.deleted_count == 0:
            return jsonify({'error': 'Failed to delete prescription'}), 500
        
        return jsonify({'message': 'Prescription deleted successfully'}), 200
        
    except Exception as e:
        return jsonify({'error': str(e)}), 500

# Add medication to existing prescription (doctors only)
@prescription_routes.route('/<prescription_id>/medications', methods=['POST'])
def add_medication(prescription_id):
    try:
        data = request.get_json()
        
        # Check if prescription exists
        existing_prescription = db.prescriptions.find_one({'_id': ObjectId(prescription_id)})
        if not existing_prescription:
            return jsonify({'error': 'Prescription not found'}), 404
        
        # Create medication object
        medication = PrescriptionItem.from_dict(data)
        
        # Add medication to prescription
        result = db.prescriptions.update_one(
            {'_id': ObjectId(prescription_id)},
            {
                '$push': {'medications': medication.to_dict()},
                '$set': {'updated_at': datetime.now()}
            }
        )
        
        if result.modified_count == 0:
            return jsonify({'error': 'Failed to add medication'}), 500
        
        # Return updated prescription
        updated_prescription = db.prescriptions.find_one({'_id': ObjectId(prescription_id)})
        return jsonify(serialize_prescription(updated_prescription)), 200
        
    except Exception as e:
        return jsonify({'error': str(e)}), 500 