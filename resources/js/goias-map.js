import L from 'leaflet';

/**
 * Normaliza o nome do município para comparação resiliente (trim + lowercase).
 *
 * @param {string} name
 * @returns {string}
 */
function normalizeCityKey(name) {
    return String(name || '')
        .trim()
        .toLocaleLowerCase('pt-BR');
}

/**
 * Inicializa o mapa coroplético municipal de Goiás via Leaflet + GeoJSON IBGE.
 */
export function initGoiasMunicipalMap() {
    const mapSection = document.getElementById('mapa-goias');
    const container = document.getElementById('leaflet-mapa-goias');

    if (!mapSection || !container || container.dataset.initialized === 'true') {
        return;
    }

    container.dataset.initialized = 'true';

    const geojsonUrl = mapSection.dataset.geojsonUrl || '/geo/goias-municipios.geojson';
    const filterBaseUrl = mapSection.dataset.filterBaseUrl || '/igrejas';
    const activeQuery = mapSection.dataset.activeQuery || '';
    const activeCity = mapSection.dataset.activeCity || '';

    let rawPresence = {};
    try {
        rawPresence = JSON.parse(mapSection.dataset.presenceByCity || '{}') || {};
    } catch {
        rawPresence = {};
    }

    // Mapa normalizado: chave minúscula -> { canonicalName, count }
    const presenceLookup = new Map();
    Object.entries(rawPresence).forEach(([cityName, count]) => {
        const numericCount = Number(count) || 0;
        if (numericCount > 0) {
            presenceLookup.set(normalizeCityKey(cityName), {
                canonicalName: cityName.trim(),
                count: numericCount,
            });
        }
    });

    const normalizedActiveCity = normalizeCityKey(activeCity);

    const map = L.map(container, {
        center: [-15.95, -49.6],
        zoom: 6,
        minZoom: 5,
        maxZoom: 11,
        zoomControl: true,
        attributionControl: true,
        scrollWheelZoom: false,
    });

    map.attributionControl.setPrefix('Malha Municipal IBGE · CBN-GO');

    const getFeatureStyle = (feature) => {
        const munName = feature?.properties?.name || '';
        const normName = normalizeCityKey(munName);
        const presence = presenceLookup.get(normName);
        const hasPresence = Boolean(presence && presence.count > 0);
        const isSelectedCity = normalizedActiveCity !== '' && normName === normalizedActiveCity;

        if (hasPresence) {
            return {
                fillColor: isSelectedCity ? '#C2410C' : '#E56B20',
                weight: isSelectedCity ? 2.2 : 1.2,
                opacity: 1,
                color: isSelectedCity ? '#171717' : '#9A3412',
                fillOpacity: isSelectedCity ? 0.92 : 0.82,
            };
        }

        return {
            fillColor: '#E5E7EB',
            weight: 0.8,
            opacity: 1,
            color: '#9CA3AF',
            fillOpacity: 0.75,
        };
    };

    const navigateToCityFilter = (cityName) => {
        const url = new URL(filterBaseUrl, window.location.origin);
        if (activeQuery.trim() !== '') {
            url.searchParams.set('q', activeQuery.trim());
        }
        url.searchParams.set('cidade', cityName);
        window.location.assign(url.toString());
    };

    fetch(geojsonUrl)
        .then((response) => {
            if (!response.ok) {
                throw new Error(`Falha ao carregar GeoJSON (${response.status})`);
            }
            return response.json();
        })
        .then((geojsonData) => {
            const geoJsonLayer = L.geoJSON(geojsonData, {
                style: getFeatureStyle,
                onEachFeature: (feature, layer) => {
                    const munName = feature?.properties?.name || 'Município';
                    const presence = presenceLookup.get(normalizeCityKey(munName));
                    const count = presence ? presence.count : 0;
                    const hasPresence = count > 0;

                    const statusText = hasPresence
                        ? `${count} ${count === 1 ? 'congregação filiada' : 'congregações filiadas'}`
                        : 'Sem congregação filiada';

                    const tooltipHtml = hasPresence
                        ? `<div class="cbn-map-tooltip"><strong>${munName}</strong><br/><span class="cbn-map-tooltip-active">${statusText}</span><br/><small>Clique para filtrar</small></div>`
                        : `<div class="cbn-map-tooltip"><strong>${munName}</strong><br/><span class="cbn-map-tooltip-empty">Sem congregação filiada</span></div>`;

                    layer.bindTooltip(tooltipHtml, {
                        sticky: true,
                        direction: 'top',
                        opacity: 0.96,
                    });

                    layer.on({
                        mouseover: (event) => {
                            const targetLayer = event.target;
                            targetLayer.setStyle({
                                weight: 2.2,
                                color: '#171717',
                                fillOpacity: hasPresence ? 0.95 : 0.9,
                            });
                            targetLayer.bringToFront();
                        },
                        mouseout: (event) => {
                            geoJsonLayer.resetStyle(event.target);
                        },
                        click: (event) => {
                            if (hasPresence) {
                                navigateToCityFilter(presence.canonicalName || munName);
                            } else {
                                L.popup({ closeButton: true, maxWidth: 240 })
                                    .setLatLng(event.latlng)
                                    .setContent(
                                        `<div class="text-xs leading-relaxed">
                                            <strong class="block text-sm text-neutral-900">${munName} (GO)</strong>
                                            <span class="text-neutral-600">Sem congregação filiada neste filtro.</span>
                                        </div>`
                                    )
                                    .openOn(map);
                            }
                        },
                    });
                },
            }).addTo(map);

            const bounds = geoJsonLayer.getBounds();
            if (bounds.isValid()) {
                map.fitBounds(bounds, { padding: [14, 14] });
            }

            // Observa redimensionamento (ex: abrir/recolher mapa no mobile)
            if (typeof ResizeObserver !== 'undefined') {
                const observer = new ResizeObserver(() => {
                    map.invalidateSize();
                    if (bounds.isValid()) {
                        map.fitBounds(bounds, { padding: [14, 14] });
                    }
                });
                observer.observe(container);
            }
        })
        .catch(() => {
            container.setAttribute('data-map-error', 'true');
        });
}
