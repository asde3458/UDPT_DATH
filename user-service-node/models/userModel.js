const pool = require('../config/db');

async function findByUsername(username) {
    const [rows] = await pool.query('SELECT * FROM users WHERE username = ?', [username]);
    return rows[0];
}

async function createUser(username, hash, role, fullName) {
    await pool.query(
        'INSERT INTO users (username, password, role, full_name) VALUES (?, ?, ?, ?)',
        [username, hash, role, fullName]
    );
}

module.exports = { findByUsername, createUser }; 