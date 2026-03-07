// filename: /js/map_comuni.js

import comuni from './comuni_data.js';

const province = [
  "Catanzaro",
  "Cosenza",
  "Crotone",
  "Reggio Calabria",
  "Vibo Valentia"
];

const provinceSelect = document.createElement('select');
provinceSelect.id = "province-select";
provinceSelect.className = "vertical-select";

const comuniSelect = document.createElement('select');
comuniSelect.id = "comuni-select";
comuniSelect.className = "vertical-select";

function initProvinceSelect() {
  provinceSelect.innerHTML = '<option value="">Seleziona Provincia</option>';
  province.forEach(prov => {
    const option = document.createElement('option');
    option.value = prov;
    option.textContent = prov;
    provinceSelect.appendChild(option);
  });
}

function updateComuniSelect(selectedProvince) {
  comuniSelect.innerHTML = '<option value="">Seleziona Comune</option>';
  comuni
    .filter(comune => comune.provincia === selectedProvince)
    .forEach(comune => {
      const option = document.createElement('option');
      option.value = comune.nome;
      option.textContent = comune.nome;
      comuniSelect.appendChild(option);
    });
}

function getComuneFromCoordinates(lat, lng) {
  let minDist = Infinity;
  let closestComune = null;
  comuni.forEach(comune => {
    const dx = comune.lat - lat;
    const dy = comune.lng - lng;
    const dist = Math.sqrt(dx * dx + dy * dy);
    if (dist < minDist) {
      minDist = dist;
      closestComune = comune;
    }
  });
  return closestComune;
}

export function insertSelectMenus(beforeElementId) {
  const reference = document.getElementById(beforeElementId);
  if (!reference) return;

  const wrapper = document.createElement('div');
  wrapper.style.marginBottom = '15px';
  wrapper.appendChild(provinceSelect);
  wrapper.appendChild(comuniSelect);

  reference.parentNode.insertBefore(wrapper, reference);
  initProvinceSelect();

  provinceSelect.addEventListener('change', () => {
    updateComuniSelect(provinceSelect.value);
  });
}

export function autoSelectComune(lat, lng) {
  const comune = getComuneFromCoordinates(lat, lng);
  if (comune) {
    provinceSelect.value = comune.provincia;
    updateComuniSelect(comune.provincia);
    comuniSelect.value = comune.nome;
  }
}
