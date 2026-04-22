const http = require('http'); // Node.js module to create servers

// Create a server
const server = http.createServer((req, res) => {
  console.log(`Request received: ${req.method} ${req.url}`); // NEW LINE 1
  res.writeHead(200, {'Content-Type': 'text/plain'}); // Status 200 and text response
  res.write('Hello World from Node.js!');
  res.write('\nCurrent time: ' + new Date().toLocaleString()); // NEW LINE 2
  res.end();
});

// Listen on port 3000
server.listen(3000, () => {
  console.log('Server is running on http://localhost:3000');
});


blaja hbdhbcugdyigcijbnhbeuwvecjdbjabchbc