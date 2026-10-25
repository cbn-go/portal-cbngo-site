import './bootstrap';
import Alpine from 'alpinejs';
import heroCarousel from './hero-carousel';

window.Alpine = Alpine;
Alpine.data('heroCarousel', heroCarousel);
Alpine.start();

// O Leaflet só é baixado nas páginas que exibem o mapa de Goiás, mantendo o bundle principal leve.
function loadGoiasMap() {
    if (!document.getElementById('leaflet-mapa-goias')) {
        return;
    }

    import('./goias-map').then(({ initGoiasMunicipalMap }) => initGoiasMunicipalMap());
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadGoiasMap);
} else {
    loadGoiasMap();
}
