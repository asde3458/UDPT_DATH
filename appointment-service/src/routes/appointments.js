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
            if (endDate) {
                const end = new Date(endDate);
                end.setDate(end.getDate() + 1);
                query.date.$lt = end;
            }
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
        // Validate required fields
        const { patientId, patientName, doctorId, doctorName, date, time } = req.body;
        if (!patientId || !patientName || !doctorId || !doctorName || !date || !time) {
            return res.status(400).json({ error: 'Missing required fields' });
        }

        // Check for conflicting appointments
        const conflictingAppointment = await Appointment.findOne({
            doctorId: doctorId,
            date: new Date(date),
            time: time,
            status: { $nin: ['cancelled', 'completed'] }
        });

        if (conflictingAppointment) {
            return res.status(400).json({ error: 'Time slot is already booked' });
        }

        const appointment = new Appointment({
            ...req.body,
            date: new Date(date)
        });
        await appointment.save();
        res.status(201).json(appointment);
    } catch (error) {
        res.status(400).json({ error: error.message });
    }
});

// Update appointment
router.put('/:id', async (req, res) => {
    try {
        const updates = { ...req.body };

        // If date is provided, convert it to Date object
        if (updates.date) {
            updates.date = new Date(updates.date);
        }

        // If updating time slot, check for conflicts
        if ((updates.date || updates.time) && updates.status !== 'cancelled') {
            const appointment = await Appointment.findById(req.params.id);
            if (!appointment) {
                return res.status(404).json({ error: 'Appointment not found' });
            }

            const conflictingAppointment = await Appointment.findOne({
                _id: { $ne: req.params.id },
                doctorId: updates.doctorId || appointment.doctorId,
                date: updates.date || appointment.date,
                time: updates.time || appointment.time,
                status: { $nin: ['cancelled', 'completed'] }
            });

            if (conflictingAppointment) {
                return res.status(400).json({ error: 'Time slot is already booked' });
            }
        }

        const appointment = await Appointment.findByIdAndUpdate(
            req.params.id,
            { ...updates, updatedAt: Date.now() },
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