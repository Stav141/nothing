const express = require('express');
const path = require('path');
const bodyParser = require('body-parser');

const app = express();
const port = 3000;

app.use(bodyParser.json());
app.use(express.static(path.join(__dirname)));

// Route to serve the Main.html file when accessing the root
app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'Main.html'));  // Assuming your HTML file is named "Main.html"
});

// Start the server
app.listen(port, () => {
    console.log(`Server is running at http://localhost:${port}`);
});
