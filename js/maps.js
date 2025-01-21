// Initialize the map and set its view
const map = L.map('map').setView([7.99740, 124.26417], 16); // latitude and longitude

// OpenStreenApp tiles
L.tileLayer('https://a.tile.openstreetmap.org/16/7.99740/124.26417.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

// Add a marker at the center of the map
// L.marker([8.0016, 124.2928]).addTo(map)
//     .bindPopup('Mindanao State University')
//     .openPopup();