<template>
  <div class="sl-map-picker">
    <div class="toolbar">
      <input
        v-model="query"
        @keydown.enter.prevent="doSearch"
        @input="onQueryInput"
        class="search"
        type="text"
        :placeholder="placeholder"
        autocomplete="off"
      />
      <div class="toolbar-right">
        <select v-model="radiusKm" class="radius">
          <option :value="1">1 km</option>
          <option :value="2">2 km</option>
          <option :value="5">5 km</option>
          <option :value="10">10 km</option>
        </select>
        <button class="btn" type="button" @click="useMyLocation">My location</button>
        <button class="btn" type="button" @click="searchThisArea">Search this area</button>
      </div>
    </div>

    <div v-if="results.length" class="results">
      <div class="results-title">Results ({{ results.length }})</div>
      <button
        v-for="r in results"
        :key="r.key"
        class="result"
        type="button"
        @click="choose(r)"
      >
        <div class="name">{{ r.name }}</div>
        <div class="ll">{{ r.lat.toFixed(6) }}, {{ r.lng.toFixed(6) }}</div>
        <div class="meta">{{ r.source }} · {{ r.kind }}</div>
      </button>
    </div>

    <div :id="mapId" class="map" :style="{ height }"></div>

    <div v-if="model" class="status">
      Selected: <strong>{{ model.lat.toFixed(6) }}, {{ model.lng.toFixed(6) }}</strong>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch, nextTick } from "vue";
import L from "leaflet";

/** Props / Emits */
const props = defineProps({
  modelValue: { type: Object, default: null }, // {lat,lng} or null
  height: { type: String, default: "420px" },
  initialZoom: { type: Number, default: 8 },
  draggable: { type: Boolean, default: true },
  placeholder: {
    type: String,
    default:
      "Search anything in Sri Lanka (e.g., 'Apeksha Hospital', 'hardware shop', 'hardware shop near Maharagama')",
  },
});
const emit = defineEmits(["update:modelValue", "select"]);

/** Internal state */
const model = ref(props.modelValue ? { ...props.modelValue } : null);
watch(() => props.modelValue, v => { if (v) model.value = { ...v }; });

const mapId = `slmp-${Math.random().toString(36).slice(2,8)}`;
let map, marker;

/** Sri Lanka bounds */
const SL_BOUNDS = L.latLngBounds(
  L.latLng(5.916, 79.652),  // SW
  L.latLng(9.835, 81.881)   // NE
);

/** Map init */
function initMap() {
  const center = SL_BOUNDS.getCenter();
  map = L.map(mapId, {
    zoomControl: true,
    maxBounds: SL_BOUNDS.pad(0.05),
    maxBoundsViscosity: 1.0,
  }).setView(center, props.initialZoom);

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a>',
  }).addTo(map);

  if (model.value && isFinite(model.value.lat) && isFinite(model.value.lng)) {
    setSelection(model.value.lat, model.value.lng, true);
  }

  map.on("click", (e) => setSelection(e.latlng.lat, e.latlng.lng, true));
  setTimeout(() => map.invalidateSize(), 0);
}

onMounted(async () => { await nextTick(); initMap(); });
onBeforeUnmount(() => { if (map) map.remove(); map = null; marker = null; });

/** Selection helpers */
function setSelection(lat, lng, pan = true) {
  const cLat = Math.min(Math.max(lat, SL_BOUNDS.getSouth()), SL_BOUNDS.getNorth());
  const cLng = Math.min(Math.max(lng, SL_BOUNDS.getWest()),  SL_BOUNDS.getEast());
  model.value = { lat: cLat, lng: cLng };
  emit("update:modelValue", { ...model.value });
  emit("select", { ...model.value });

  if (!marker) {
    marker = L.marker([cLat, cLng], { draggable: props.draggable }).addTo(map);
    if (props.draggable) {
      marker.on("dragend", e => {
        const p = e.target.getLatLng();
        setSelection(p.lat, p.lng, false);
      });
    }
  } else {
    marker.setLatLng([cLat, cLng]);
  }
  if (pan) map.setView([cLat, cLng], 17);
}

/** Search state */
const query = ref("");
const results = ref([]);
const radiusKm = ref(2);
let debounceT;

/** Debounced input */
function onQueryInput() {
  if (debounceT) clearTimeout(debounceT);
  debounceT = setTimeout(doSearch, 350);
}

/** Query parser: detect "near X", coords, DMS, or category */
function parseNear(q) {
  // "something near somewhere"
  const m = /(.+)\s+near\s+(.+)/i.exec(q);
  return m ? { what: m[1].trim(), near: m[2].trim() } : null;
}
// Simple category map → OSM tags (can expand easily)
function detectOSMTags(text) {
  const q = (text || "").toLowerCase();
  const lib = [
    { keys: ["hardware", "hardware shop", "ironmongery"], tags: [{k:"shop",v:"hardware"}] },
    { keys: ["pharmacy", "drugstore"], tags: [{k:"amenity",v:"pharmacy"}] },
    { keys: ["hospital"], tags: [{k:"amenity",v:"hospital"}] },
    { keys: ["bank"], tags: [{k:"amenity",v:"bank"}] },
    { keys: ["atm"], tags: [{k:"amenity",v:"atm"}] },
    { keys: ["restaurant", "food"], tags: [{k:"amenity",v:"restaurant"}] },
    { keys: ["supermarket","grocery","market"], tags: [{k:"shop",v:"supermarket"}] },
    { keys: ["bookshop","book store"], tags: [{k:"shop",v:"books"}] },
    { keys: ["fuel","petrol","gas station"], tags: [{k:"amenity",v:"fuel"}] },
    { keys: ["hotel"], tags: [{k:"tourism",v:"hotel"}] },
    { keys: ["school"], tags: [{k:"amenity",v:"school"}] },
    { keys: ["police"], tags: [{k:"amenity",v:"police"}] },
    { keys: ["post office","postal"], tags: [{k:"amenity",v:"post_office"}] },
    { keys: ["bus stop"], tags: [{k:"highway",v:"bus_stop"}] },
    { keys: ["train","railway station","station"], tags: [{k:"railway",v:"station"}] },
    { keys: ["park"], tags: [{k:"leisure",v:"park"}] },
  ];
  for (const r of lib) if (r.keys.some(k => q.includes(k))) return r.tags;
  return null;
}

/** Main search */
async function doSearch() {
  results.value = [];
  const q = (query.value || "").trim();
  if (!q) return;

  // 1) Handle "near" queries: X near Y
  const near = parseNear(q);
  if (near) {
    // geocode Y → center, then:
    const place = await geocodeOne(near.near);
    if (place) {
      // if X is a category → Overpass around place
      const tags = detectOSMTags(near.what);
      if (tags) {
        results.value = await overpassAround(place.lat, place.lng, radiusKm.value, tags);
        if (!results.value.length) {
          // fallback to name search near place bbox
          results.value = await nominatimByName(near.what, bboxFromCenter(place.lat, place.lng, radiusKm.value));
        }
      } else {
        // name search near place bbox
        results.value = await nominatimByName(near.what, bboxFromCenter(place.lat, place.lng, radiusKm.value));
      }
      return;
    }
    // if geocoding Y fails, fall back to normal flow
  }

  // 2) If it's a category word → Overpass in current view
  const tags = detectOSMTags(q);
  if (tags) {
    results.value = await overpassInView(tags);
    if (!results.value.length) {
      // fallback: name search inside SL bbox
      results.value = await nominatimByName(q);
    }
    return;
  }

  // 3) Normal name/address search in SL bbox
  results.value = await nominatimByName(q);
}

/** Helpers: Nominatim search */
async function nominatimByName(q, bbox = null) {
  const url = new URL("https://nominatim.openstreetmap.org/search");
  url.searchParams.set("q", `${q} Sri Lanka`);
  url.searchParams.set("format", "json");
  url.searchParams.set("addressdetails", "1");
  url.searchParams.set("limit", "12");
  url.searchParams.set("countrycodes", "lk");
  if (bbox) {
    // bbox = [west, north, east, south]
    url.searchParams.set("viewbox", bbox.join(","));
    url.searchParams.set("bounded", "1");
  } else {
    url.searchParams.set("viewbox", `${SL_BOUNDS.getWest()},${SL_BOUNDS.getNorth()},${SL_BOUNDS.getEast()},${SL_BOUNDS.getSouth()}`);
    url.searchParams.set("bounded", "1");
  }

  const res = await fetch(url.toString(), {
    headers: { "User-Agent": "YourAppName/1.0 (contact@example.com)" },
  });
  const arr = await res.json();
  return (arr || []).map((r, i) => ({
    key: `nom-${r.osm_id}-${i}`,
    name: r.display_name,
    lat: Number(r.lat),
    lng: Number(r.lon),
    source: "Nominatim",
    kind: r.class || "place",
  }));
}

/** Single geocode (first result) */
async function geocodeOne(q) {
  const list = await nominatimByName(q);
  return list[0] || null;
}

/** Overpass: search tags in current view */
async function overpassInView(tags) {
  if (!map) return [];
  const b = map.getBounds();
  return overpassBBox(b.getSouth(), b.getWest(), b.getNorth(), b.getEast(), tags);
}

/** Overpass: search tags around center & radius (km) */
async function overpassAround(lat, lng, radiusKm, tags) {
  // Build a small bbox around center using approx degrees (1 deg ~ 111 km)
  const d = radiusKm / 111.0;
  const south = lat - d, north = lat + d, west = lng - d, east = lng + d;
  return overpassBBox(south, west, north, east, tags);
}

/** Overpass core */
async function overpassBBox(south, west, north, east, tags) {
  // Build union for multiple tags
  const parts = tags.map(t => `
    node["${t.k}"="${t.v}"](${south},${west},${north},${east});
    way ["${t.k}"="${t.v}"](${south},${west},${north},${east});
    relation["${t.k}"="${t.v}"](${south},${west},${north},${east});
  `).join("\n");

  const q = `
    [out:json][timeout:25];
    (
      ${parts}
    );
    out center 60;
  `.trim();

  const res = await fetch("https://overpass-api.de/api/interpreter", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded;charset=UTF-8" },
    body: new URLSearchParams({ data: q }).toString(),
  });

  const json = await res.json();
  const out = [];
  for (const el of json.elements || []) {
    const lat = el.lat ?? el.center?.lat;
    const lng = el.lon ?? el.center?.lon;
    if (!isFinite(lat) || !isFinite(lng)) continue;
    const name = el.tags?.name || niceFallbackName(tags, el);
    out.push({
      key: `ov-${el.type}-${el.id}`,
      name,
      lat: Number(lat),
      lng: Number(lng),
      source: "Overpass",
      kind: kindFromTags(el.tags),
    });
  }
  // Deduplicate by lat/lng+name
  const seen = new Set();
  return out.filter(r => {
    const k = `${r.name}|${r.lat.toFixed(6)}|${r.lng.toFixed(6)}`;
    if (seen.has(k)) return false;
    seen.add(k); return true;
  });
}

function niceFallbackName(tags, el) {
  const first = Object.values(tags || {})[0];
  return first ? `${first.v} (${el.type})` : (el.tags?.amenity || el.tags?.shop || "POI");
}
function kindFromTags(tags = {}) {
  return tags.amenity || tags.shop || tags.tourism || tags.highway || tags.railway || tags.leisure || "poi";
}

/** Build bbox array [W,N,E,S] from center + radiusKm */
function bboxFromCenter(lat, lng, radiusKm) {
  const d = radiusKm / 111.0;
  return [lng - d, lat + d, lng + d, lat - d];
}

/** Choose a result */
function choose(r) {
  setSelection(r.lat, r.lng, true);
  results.value = [];
  // leave the result name in the box so user knows what was chosen
  query.value = r.name;
}

/** Search this area (for categories) */
async function searchThisArea() {
  const tags = detectOSMTags(query.value || "");
  if (!tags) return; // meaningful for categories only
  results.value = await overpassInView(tags);
}

/** My location */
function useMyLocation() {
  if (!navigator.geolocation) return;
  navigator.geolocation.getCurrentPosition(
    pos => setSelection(pos.coords.latitude, pos.coords.longitude, true),
    () => {}
  );
}
</script>

<style scoped>
.sl-map-picker { display: grid; gap: 10px; }
.toolbar { display: grid; grid-template-columns: 1fr auto; gap: 8px; align-items: center; }
.toolbar-right { display: flex; gap: 8px; }
.search {
  padding: 8px 10px; border: 1px solid #d9d9d9; border-radius: 8px; outline: none;
}
.radius {
  padding: 8px 10px; border: 1px solid #d0d0d0; border-radius: 8px; background:#fff;
}
.btn {
  padding: 8px 10px; border: 1px solid #bdbdbd; background:#fff; border-radius: 8px; cursor: pointer;
}
.btn:hover { background:#f5f5f5; }
.results {
  border:1px solid #e0e0e0; border-radius: 8px; overflow:hidden; max-height: 280px; overflow-y:auto;
}
.results-title { font-size: 12px; color:#666; padding:6px 10px; background:#fafafa; border-bottom:1px solid #eee; }
.result {
  width:100%; text-align:left; padding:8px 10px; background:#fff; border:none; border-bottom:1px solid #eee; cursor:pointer;
}
.result:last-child { border-bottom:none; }
.result:hover { background:#f9f9f9; }
.name{ font-size:14px; } .ll{ font-size:12px; color:#666; } .meta{ font-size:11px; color:#888; }
.map { width: 100%; border: 1px solid #e0e0e0; border-radius: 10px; overflow: hidden; }
.status { font-size: 13px; color: #444; }
</style>
