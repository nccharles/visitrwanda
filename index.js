const express = require('express');
const app = express();
const path = require('path');

//load static pages html and css
app.use(express.static(path.join(__dirname, "/trwanda")));
// viewed at http://localhost:8088

app.set('PORT', process.env.PORT || 8088);
app.listen(app.get('PORT'), function(error) {
    console.log('Server running at 8088');
});