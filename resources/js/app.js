import './bootstrap';
import Alpine from 'alpinejs';
import { initGoiasMunicipalMap } from './goias-map';

window.Alpine = Alpine;
Alpine.start();

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => initGoiasMunicipalMap());
} else {
    initGoiasMunicipalMap();
}

