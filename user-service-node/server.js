require('dotenv').config();
const express = require('express');
const cors = require('cors');
const bodyParser = require('body-parser');
const userRoutes = require('./routes/userRoutes');

const app = express();
app.use(cors());
app.use(bodyParser.json());

app.use('/api', userRoutes);

const port = process.env.PORT || 5000;
app.listen(port, () => console.log(`User service (Node.js) running on port ${port}`)); 