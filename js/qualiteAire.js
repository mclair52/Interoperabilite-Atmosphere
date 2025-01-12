'use strict';

export async function fetchAirQuality(latitude, longitude) {
    const airQualityUrl = `https://services3.arcgis.com/geojson?lat=${latitude}&lon=${longitude}&apikey=VOTRE_CLE_API_AIR`;
    const response = await fetch(airQualityUrl);
    if (!response.ok) {
        throw new Error("Erreur lors de la récupération des données de qualité de l'air.");
    }
    const airQualityData = await response.json();
    document.getElementById("air-quality").textContent = 
        `Qualité de l'air : ${airQualityData.quality || "Indisponible"}`;
}
