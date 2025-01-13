<?php
$proxy = "www-cache:3128";

// API POUR LA GEOLOCALISATION

$ip_api_url = "http://ip-api.com/xml";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $ip_api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$xml_reponse = curl_exec($ch);
$geo_data = simplexml_load_string($xml_reponse);

$latitude = $geo_data->lat;     
$longitude = $geo_data->lon;
$region = $geo_data->regionName;
$city = $geo_data->city;
$ip = $geo_data->query;
$api_meteo_url = "https://www.infoclimat.fr/public-api/gfs/xml?_ll=" . $latitude . "," . $longitude . "&_auth=ARsDFFIsBCZRfFtsD3lSe1Q8ADUPeVRzBHgFZgtuAH1UMQNgUTNcPlU5VClSfVZkUn8AYVxmVW0Eb1I2WylSLgFgA25SNwRuUT1bPw83UnlUeAB9DzFUcwR4BWMLYwBhVCkDb1EzXCBVOFQoUmNWZlJnAH9cfFVsBGRSPVs1UjEBZwNkUjIEYVE6WyYPIFJjVGUAZg9mVD4EbwVhCzMAMFQzA2JRMlw5VThUKFJiVmtSZQBpXGtVbwRlUjVbKVIuARsDFFIsBCZRfFtsD3lSe1QyAD4PZA%3D%3D&_c=19f3aa7d766b6ba91191c8be71dd1ab2";
curl_close($ch);

//API POUR LA METEO

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_meteo_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
$xml_reponse = curl_exec($ch);
curl_close($ch);

$xml_data = simplexml_load_string($xml_reponse);
$xsl = new DOMDocument;
$xsl->load('meteo.xsl');
$proc = new XSLTProcessor;
$proc->importStyleSheet($xsl);

//API POUR LE TRAFIC
$traffic_url = "https://carto.g-ny.org/data/cifs/cifs_waze_v2.json";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $traffic_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
$reponse_trafic = curl_exec($ch);
curl_close($ch);
$xml_data_trafic = json_decode($reponse_trafic, true);


//API POUR LA QUALITE DE L'AIR
$air_url = "https://services3.arcgis.com/Is0UwT37raQYl9Jj/arcgis/rest/services/ind_grandest/FeatureServer/0/query?where=lib_zone%3D%27Nancy%27&objectIds=&time=&geometry=&geometryType=esriGeometryEnvelope&inSR=&spatialRel=esriSpatialRelIntersects&resultType=none&distance=0.0&units=esriSRUnit_Meter&returnGeodetic=false&outFields=*&returnGeometry=true&featureEncoding=esriDefault&multipatchOption=xyFootprint&maxAllowableOffset=&geometryPrecision=&outSR=&datumTransformation=&applyVCSProjection=false&returnIdsOnly=false&returnUniqueIdsOnly=false&returnCountOnly=false&returnExtentOnly=false&returnQueryGeometry=false&returnDistinctValues=false&cacheHint=false&orderByFields=&groupByFieldsForStatistics=&outStatistics=&having=&resultOffset=&resultRecordCount=&returnZ=false&returnM=false&returnExceededLimitFeatures=true&quantizationParameters=&sqlFormat=none&f=pjson&token=";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $air_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
$reponse_qualite = curl_exec($ch);
curl_close($ch);
$xml_data_qualite = json_decode($reponse_qualite, true);

//API POUR L'ADRESSE DE L'IUT
$iut_url = "https://api-adresse.data.gouv.fr/search/?q=2%20boulevard%20charlemagne";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $iut_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
$reponse_iut = curl_exec($ch);
curl_close($ch);
$xml_data_iut = json_decode($reponse_iut, true);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <link rel="stylesheet" href="css/Atmosphere.css" />
    <title>Prévisions météo</title>
</head>

<body>
    <h1 class="header">Prévisions Météo en temps réel</h1>
    <div class="meteo">
        <?php
        echo $proc->transformToXML($xml_data);
        ?>
    </div>
    <div id="qualite" class="qualite"></div>
    <div id="map" class="map"></div>
    <div class="API">
        <h2>API utilisées et lien Git</h2>
        <ul>
            <li>Lien Github: <a href="https://github.com/mclair52/Interoperabilite-Atmosphere">https://github.com/mclair52/Interoperabilite-Atmosphere</a></li>
            <li>API pour la géolocalisation : <a href="http://ip-api.com/xml">http://ip-api.com/xml</a></li>
            <li>API pour la météo : <a
                    href="https://www.infoclimat.fr/public-api/gfs/xml?_ll=48,6&_auth=ARsDFFIsBCZRfFtsD3lSe1Q8ADUPeVRzBHgFZgtuAH1UMQNgUTNcPlU5VClSfVZkUn8AYVxmVW0Eb1I2WylSLgFgA25SNwRuUT1bPw83UnlUeAB9DzFUcwR4BWMLYwBhVCkDb1EzXCBVOFQoUmNWZlJnAH9cfFVsBGRSPVs1UjEBZwNkUjIEYVE6WyYPIFJjVGUAZg9mVD4EbwVhCzMAMFQzA2JRMlw5VThUKFJiVmtSZQBpXGtVbwRlUjVbKVIuARsDFFIsBCZRfFtsD3lSe1QyAD4PZA%3D%3D&_c=19f3aa7d766b6ba91191c8be71dd1ab2">https://www.infoclimat.fr
                </a></li>
            <li>API pour le trafic : <a
                    href="https://carto.g-ny.org/data/cifs/cifs_waze_v2.json">https://carto.g-ny.org/data/cifs/cifs_waze_v2.json</a>
            </li>
            <li>API pour l'adresse de l'IUT : <a
                    href="https://api-adresse.data.gouv.fr/search/?q=2%20boulevard%20charlemagne">https://api-adresse.data.gouv.fr/search/?q=2%20boulevard%20charlemagne</a>
            </li>
            <li>API pour la qualité de l'aire : <a
                    href="https://services3.arcgis.com/Is0UwT37raQYl9Jj/arcgis/rest/services/ind_grandest/FeatureServer/0/query?where=lib_zone%3D%27Nancy%27&objectIds=&time=&geometry=&geometryType=esriGeometryEnvelope&inSR=&spatialRel=esriSpatialRelIntersects&resultType=none&distance=0.0&units=esriSRUnit_Meter&returnGeodetic=false&outFields=*&returnGeometry=true&featureEncoding=esriDefault&multipatchOption=xyFootprint&maxAllowableOffset=&geometryPrecision=&outSR=&datumTransformation=&applyVCSProjection=false&returnIdsOnly=false&returnUniqueIdsOnly=false&returnCountOnly=false&returnExtentOnly=false&returnQueryGeometry=false&returnDistinctValues=false&cacheHint=false&orderByFields=&groupByFieldsForStatistics=&outStatistics=&having=&resultOffset=&resultRecordCount=&returnZ=false&returnM=false&returnExceededLimitFeatures=true&quantizationParameters=&sqlFormat=none&f=pjson&token=">https://services3.arcgis.com</a>
            </li>

        </ul>
        Par Mathieu Clair
    </div>
    <script>
        const latitude = <?php echo $latitude; ?>;
        const longitude = <?php echo $longitude; ?>;
        const qualiteData = <?php echo json_encode($xml_data_qualite); ?>;
        const now = Date.now();
        let closestFeature = null;
        let closestTimeDifference = null;

        qualiteData.features.forEach(feature => {
            const attributes = feature.attributes;
            const startTime = new Date(attributes.date_ech).getTime();
            const timeDifference = Math.abs(startTime - now);
            if (closestTimeDifference === null || timeDifference < closestTimeDifference) {
                closestFeature = feature;
                closestTimeDifference = timeDifference;
            }
        });
        if (closestFeature) {
            document.getElementById('qualite').innerHTML = `
        <strong>Qualité de l'air à ${closestFeature.attributes.lib_zone} :</strong> 
        ${closestFeature.attributes.lib_qual}
    `;
        }

        var map = L.map('map').setView([latitude, longitude], 13);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19
        }).addTo(map);

        const trafficData = <?php echo json_encode($xml_data_trafic); ?>;
        const iutData = <?php echo json_encode($xml_data_iut); ?>;

        let iutCoord = null;
        iutData.features.forEach(feature => {
            if (feature.properties.label === "2 Boulevard Charlemagne 54000 Nancy") {
                iutCoord = feature.geometry.coordinates;
            }
        });


        if (iutCoord) {
            L.marker([iutCoord[1], iutCoord[0]])
                .addTo(map)
                .bindPopup("Vous êtes ici")
                .openPopup();
        }

        trafficData.incidents.forEach(incident => {
            const coords = incident.location.polyline.split(" ");
            const description = incident.description || "Pas de description";
            const startDate = incident.starttime || "Date de début inconnue";
            const endDate = incident.endtime || "Date de fin inconnue";


            L.marker([parseFloat(coords[0]), parseFloat(coords[1])])
                .addTo(map)
                .bindPopup(`
            <b>Description:</b> ${description} <br>
            <b>Début:</b> ${startDate} <br>
            <b>Fin:</b> ${endDate}
        `);
        });
    </script>
</body>

</html>