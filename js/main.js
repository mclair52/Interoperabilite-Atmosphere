'use strict';
import { getGeolocalisation } from "./geolocalisation.js";
import { fetchMeteo } from "./meteo.js";
import { fetchAirQuality } from "./qualiteAire.js";
import { createMap } from "./map.js";


window.addEventListener('load', async () => {
    try {
        const date = Date.now();
        const data = await getGeolocalisation();
        const meteo = await fetchMeteo();
        const map = createMap();
        meteoTemplate(meteo);
        veloTemplate(map);
        await fetchAirQuality(data);
    } catch (error) {
        console.error("Erreur :", error);
    }
});
