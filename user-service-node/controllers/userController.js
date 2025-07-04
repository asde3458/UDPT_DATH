const bcrypt = require('bcryptjs');
const userModel = require('../models/userModel');

exports.register = async (req, res) => {
    const { username, password, role } = req.body;
    if (!username || !password || !role) return res.status(400).json({ error: 'Missing fields' });
    try {
        const existing = await userModel.findByUsername(username);
        if (existing) return res.status(400).json({ error: 'Username already exists' });
        const hash = await bcrypt.hash(password, 10);
        await userModel.createUser(username, hash, role);
        res.status(201).json({ message: 'User created successfully' });
    } catch (err) {
        res.status(500).json({ error: 'Database error', detail: err.message });
    }
};

exports.login = async (req, res) => {
    const { username, password } = req.body;
    try {
        const user = await userModel.findByUsername(username);
        if (!user) return res.status(401).json({ error: 'Invalid credentials' });
        const match = await bcrypt.compare(password, user.password);
        if (!match) return res.status(401).json({ error: 'Invalid credentials' });
        res.json({ id: user.id, username: user.username, role: user.role });
    } catch (err) {
        res.status(500).json({ error: 'Database error', detail: err.message });
    }
}; 