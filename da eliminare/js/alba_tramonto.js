// File: /js/alba_tramonto.js
// Calcolo dell'orario di alba e tramonto in base a coordinate geografiche e data locale

// Converte i gradi in radianti
function toRadians(degrees) {
  return degrees * Math.PI / 180;
}

// Converte i radianti in gradi
function toDegrees(radians) {
  return radians * 180 / Math.PI;
}

// Calcola il giorno giuliano per una determinata data
function getJulianDay(date) {
  const year = date.getUTCFullYear();
  const month = date.getUTCMonth() + 1;
  const day = date.getUTCDate();
  const A = Math.floor(year / 100);
  const B = 2 - A + Math.floor(A / 4);
  return Math.floor(365.25 * (year + 4716)) +
         Math.floor(30.6001 * (month + 1)) +
         day + B - 1524.5;
}

// Calcola declinazione solare e correzione longitudinale del fuso orario
function getSolarTimeAtLongitude(date, longitude) {
  const JD = getJulianDay(date);
  const n = JD - 2451545.0;
  const L = (280.46 + 0.9856474 * n) % 360;
  const g = toRadians((357.528 + 0.9856003 * n) % 360);
  const lambda = toRadians((L + 1.915 * Math.sin(g) + 0.02 * Math.sin(2 * g)) % 360);
  const e = 23.439 - 0.0000004 * n;
  const delta = Math.asin(Math.sin(toRadians(e)) * Math.sin(lambda));
  const timeOffset = -longitude / 15; // correzione longitudine in ore
  return { delta, timeOffset };
}

// Calcola l'angolo orario H per alba o tramonto
function calculateHourAngle(lat, delta) {
  const cosH = (Math.cos(toRadians(90.833)) - Math.sin(toRadians(lat)) * Math.sin(delta)) /
               (Math.cos(toRadians(lat)) * Math.cos(delta));
  return Math.acos(cosH);
}

// Calcola l'orario locale dell'alba o tramonto
function calculateSolarEvent(lat, lon, date, type) {
  const { delta, timeOffset } = getSolarTimeAtLongitude(date, lon);
  const H = calculateHourAngle(lat, delta);
  const t = type === 'sunrise' ? -H : H; // negativo per alba, positivo per tramonto
  const eventUTC = 12 + toDegrees(t) / 15 + timeOffset;
  const local = new Date(date);
  local.setUTCHours(0, 0, 0, 0);
  local.setTime(local.getTime() + eventUTC * 60 * 60 * 1000);
  return local.toTimeString().slice(0, 5); // ritorna ora in formato HH:mm
}

// Wrapper per calcolo alba
function calcolaAlba(lat, lon, date) {
  return calculateSolarEvent(lat, lon, date, 'sunrise');
}

// Wrapper per calcolo tramonto
function calcolaTramonto(lat, lon, date) {
  return calculateSolarEvent(lat, lon, date, 'sunset');
}
