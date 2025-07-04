const express = require('express');
const mongoose = require('mongoose');
const cors = require('cors');

const app = express();

// Middleware
app.use(cors());
app.use(express.json());

// MongoDB connection
mongoose.connect('mongodb://localhost:27017/hospital_patient_service', {
    useNewUrlParser: true,
    useUnifiedTopology: true
});

// Patient Schema
const patientSchema = new mongoose.Schema({
    name: { type: String, required: true },
    dateOfBirth: { type: Date, required: true },
    gender: { type: String, required: true },
    address: { type: String, required: true },
    phoneNumber: { type: String, required: true },
    medicalHistory: [{
        condition: String,
        diagnosis: String,
        treatment: String,
        date: Date
    }]
});

const Patient = mongoose.model('Patient', patientSchema);

// Routes
app.post('/patients', async (req, res) => {
    try {
        const patient = new Patient(req.body);
        await patient.save();
        res.status(201).json(patient);
    } catch (error) {
        res.status(400).json({ error: error.message });
    }
});

app.get('/patients', async (req, res) => {
    try {
        const patients = await Patient.find();
        res.json(patients);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

app.get('/patients/:id', async (req, res) => {
    try {
        const patient = await Patient.findById(req.params.id);
        if (!patient) {
            return res.status(404).json({ error: 'Patient not found' });
        }
        res.json(patient);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

app.put('/patients/:id', async (req, res) => {
    try {
        const patient = await Patient.findByIdAndUpdate(
            req.params.id,
            req.body,
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

app.delete('/patients/:id', async (req, res) => {
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
app.post('/patients/:id/medical-history', async (req, res) => {
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

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
    console.log(`Patient service running on port ${PORT}`);
}); 