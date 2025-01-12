import { getGeolocalisation } from "./geolocalisation.js";

async function createMap(){
    const datas = await getGeolocalisation();
    const lat = datas.loc.split(',')[0];
    const lon = datas.loc.split(',')[1];
var map = L.map('map').setView([lat, lon], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    return map;
}


async function veloTemplate(map){
    const stations = fetch("https://api.cyclocity.fr/contracts/nancy/gbfs/station_information.json")
    .then(response => response.json())
    .then(data => {
        return data.data.stations
    })
    stations.forEach(async station => {
        const status = fetch("https://api.cyclocity.fr/contracts/nancy/gbfs/station_status.json")
        .then(response => response.json())
        .then(data => {
            return data.data.stations.find(station => station.station_id === stationId)
        })
        const marker = L.marker([station.lat, station.lon]).addTo(map);
        marker.bindPopup(`<b>${station.address}</b><br>${station.name}<br>Capicité restante : ${station.capacity}<br>Nombres de Vélos disponibles : ${status.num_bikes_available}<br>Emplacements disponibles : ${status.num_docks_available}`);
    });
}

export {
    veloTemplate,
    createMap,
};