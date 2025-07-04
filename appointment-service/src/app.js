const express = require('express');
const cors = require('cors');
const connectDB = require('./config/database');
const appointmentRoutes = require('./routes/appointments');

const app = express();

// Connect to MongoDB
connectDB();

// Middleware
app.use(cors());
app.use(express.json());

// Routes
app.use('/api/appointments', appointmentRoutes);

// Error handling middleware
app.use((err, req, res, next) => {
    console.error(err.stack);
    res.status(500).json({ error: 'Something went wrong!' });
});

module.exports = app; 