const mongoose = require('mongoose');

const appointmentSchema = new mongoose.Schema({
    patientId: {
        type: String,
        required: true
    },
    doctorId: {
        type: String,
        required: true
    },
    date: {
        type: Date,
        required: true
    },
    time: {
        type: String,
        required: true
    },
    status: {
        type: String,
        enum: ['scheduled', 'confirmed', 'cancelled', 'completed'],
        default: 'scheduled'
    },
    reason: {
        type: String,
        required: true
    },
    notes: {
        type: String
    },
    createdAt: {
        type: Date,
        default: Date.now
    },
    updatedAt: {
        type: Date,
        default: Date.now
    }
});

// Update the updatedAt timestamp before saving
appointmentSchema.pre('save', function (next) {
    this.updatedAt = Date.now();
    next();
});

// Create indexes for common queries
appointmentSchema.index({ patientId: 1, date: 1 });
appointmentSchema.index({ doctorId: 1, date: 1 });
appointmentSchema.index({ status: 1 });

module.exports = mongoose.model('Appointment', appointmentSchema); 