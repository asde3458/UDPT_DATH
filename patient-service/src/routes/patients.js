const express = require('express');
const router = express.Router();
const Patient = require('../models/Patient');

// Get all patients (for doctors only)
router.get('/', async (req, res) => {
    try {
        const patients = await Patient.find();
        res.json(patients);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// Get patient by userId (for both patient and doctors)
router.get('/user/:userId', async (req, res) => {
    try {
        const patient = await Patient.findOne({ userId: req.params.userId });
        if (!patient) {
            return res.status(404).json({ error: 'Patient not found' });
        }
        res.json(patient);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// Create new patient
router.post('/', async (req, res) => {
    try {
        const patient = new Patient(req.body);
        await patient.save();
        res.status(201).json(patient);
    } catch (error) {
        res.status(400).json({ error: error.message });
    }
});

// Update patient
router.put('/:id', async (req, res) => {
    try {
        const patient = await Patient.findByIdAndUpdate(
            req.params.id,
            { ...req.body, updatedAt: Date.now() },
            { new: true }
        );
        if (!patient) {
            return res.status(404).json({ error: 'Patient not found' });
        }
        res.json(patient);
    } catch (error) {
        res.status(400).json({ error: error.message });
    }
});

// Delete patient
router.delete('/:id', async (req, res) => {
    try {
        const patient = await Patient.findByIdAndDelete(req.params.id);
        if (!patient) {
            return res.status(404).json({ error: 'Patient not found' });
        }
        res.json({ message: 'Patient deleted successfully' });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// Add medical history
router.post('/:id/medical-history', async (req, res) => {
    try {
        const patient = await Patient.findById(req.params.id);
        if (!patient) {
            return res.status(404).json({ error: 'Patient not found' });
        }

        patient.medicalHistory.push(req.body);
        await patient.save();
        res.json(patient);
    } catch (error) {
        res.status(400).json({ error: error.message });
    }
});

// Get patient's medical history
router.get('/:id/medical-history', async (req, res) => {
    try {
        const patient = await Patient.findById(req.params.id);
        if (!patient) {
            return res.status(404).json({ error: 'Patient not found' });
        }
        res.json(patient.medicalHistory);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

module.exports = router; 