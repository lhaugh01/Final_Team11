const express = require('express');
const mysql = require('mysql2');
const cors = require('cors');
const app = express();

app.use(cors());
app.use(express.json());

// Your database credentials
const db = mysql.createConnection({
  host: '35.212.125.126',
  port: '3306',
  user: 'uxj34ztsgesvj',
  password: 'tufts12345#',
  database: 'dbs5nqdmdcgi92'
});

// Test connection
db.connect((err) => {
  if (err) {
    console.error('Error connecting to database:', err.message);
  } else {
    console.log('Connected to MySQL database');
  }
});

// Add movie name to user's search history
app.post('/api/update-history', (req, res) => {
    const { userId, movieTitle } = req.body;
  
    const getHistory = 'SELECT search_history FROM users WHERE id = ?';
    db.query(getHistory, [userId], (err, result) => {
      if (err) return res.status(500).send('Error fetching history');
  
      let history = result[0]?.search_history || '';
      const updatedHistory = history ? `${history},${movieTitle}` : movieTitle;
  
      const update = 'UPDATE users SET search_history = ? WHERE id = ?';
      db.query(update, [updatedHistory, userId], (err, result) => {
        if (err) return res.status(500).send('Error updating history');
        res.send({ message: 'Search history updated' });
      });
    });
  });
  
// Example endpoint: Get all users
app.get('/api/users', (req, res) => {
  db.query('SELECT * FROM users', (err, results) => {
    if (err) return res.status(500).json({ error: err.message });
    res.json(results);
  });
});

// Start server
const PORT = 3001;
app.listen(PORT, () => {
  console.log(`Server running at http://localhost:${PORT}`);
});