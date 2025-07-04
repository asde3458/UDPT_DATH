const express = require('express');
const router = express.Router();
const Appointment = require('../models/Appointment');

// Get appointments with filters
router.get('/', async (req, res) => {
    try {
        const { doctorId, patientId, status, startDate, endDate } = req.query;
        const query = {};

        if (doctorId) query.doctorId = doctorId;
        if (patientId) query.patientId = patientId;
        if (status) query.status = status;
        if (startDate || endDate) {
            query.date = {};
            if (startDate) query.date.$gte = new Date(startDate);
            if (endDate) query.date.$lte = new Date(endDate);
        }

        const appointments = await Appointment.find(query).sort({ date: 1, time: 1 });
        res.json(appointments);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// Get specific appointment
router.get('/:id', async (req, res) => {
    try {
        const appointment = await Appointment.findById(req.params.id);
        if (!appointment) {
            return res.status(404).json({ error: 'Appointment not found' });
        }
        res.json(appointment);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// Create new appointment
router.post('/', async (req, res) => {
    try {
        // Check for conflicting appointments
        const conflictingAppointment = await Appointment.findOne({
            doctorId: req.body.doctorId,
            date: req.body.date,
            time: req.body.time,
            status: { $nin: ['cancelled', 'completed'] }
        });

        if (conflictingAppointment) {
            return res.status(400).json({ error: 'Time slot is already booked' });
        }

        const appointment = new Appointment(req.body);
        await appointment.save();
        res.status(201).json(appointment);
    } catch (error) {
        res.status(400).json({ error: error.message });
    }
});

// Update appointment
router.put('/:id', async (req, res) => {
    try {
        const appointment = await Appointment.findByIdAndUpdate(
            req.params.id,
            { ...req.body, updatedAt: Date.now() },
            { new: true }
        );
        if (!appointment) {
            return res.status(404).json({ error: 'Appointment not found' });
        }
        res.json(appointment);
    } catch (error) {
        res.status(400).json({ error: error.message });
    }
});

// Update appointment status
router.patch('/:id/status', async (req, res) => {
    try {
        const { status } = req.body;
        if (!['scheduled', 'confirmed', 'cancelled', 'completed'].includes(status)) {
            return res.status(400).json({ error: 'Invalid status' });
        }

        const appointment = await Appointment.findByIdAndUpdate(
            req.params.id,
            { status, updatedAt: Date.now() },
            { new: true }
        );

        if (!appointment) {
            return res.status(404).json({ error: 'Appointment not found' });
        }
        res.json(appointment);
    } catch (error) {
        res.status(400).json({ error: error.message });
    }
});

// Delete appointment
router.delete('/:id', async (req, res) => {
    try {
        const appointment = await Appointment.findByIdAndDelete(req.params.id);
        if (!appointment) {
            return res.status(404).json({ error: 'Appointment not found' });
        }
        res.json({ message: 'Appointment deleted successfully' });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

module.exports = router; 