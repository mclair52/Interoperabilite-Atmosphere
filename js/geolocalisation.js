'use strict';

function getGeolocalisation() {
    try {
        return fetch("https://ipapi.co/json/")
            .then(response => response.json())
            .then(data => {
                return data
            })
    }
    catch (error) {
        console.error('Error:', error);
    }
}

export { getGeolocalisation };