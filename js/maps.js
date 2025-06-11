/**
 * EventBooking Cameroon - Maps Integration
 * OpenStreetMap implementation using Leaflet.js
 */

// Cameroon major cities coordinates
const CAMEROON_CITIES = {
    'yaoundé': [3.8480, 11.5174],
    'douala': [4.0511, 9.7679],
    'bamenda': [5.9597, 10.1494],
    'bafoussam': [5.4737, 10.4176],
    'garoua': [9.3265, 13.3991],
    'maroua': [10.5913, 14.3153],
    'ngaoundéré': [7.3167, 13.5833],
    'bertoua': [4.5833, 13.6833],
    'buea': [4.1559, 9.2669],
    'limbe': [4.0186, 9.2056],
    'kribi': [2.9333, 9.9167],
    'ebolowa': [2.9167, 11.1500],
    'sangmelima': [2.9333, 11.9833],
    'mbalmayo': [3.5167, 11.5000],
    'edea': [3.8000, 10.1333]
};

// Default center (Yaoundé)
const DEFAULT_CENTER = [3.8480, 11.5174];

/**
 * Initialize a basic map
 */
function initializeMap(containerId, options = {}) {
    const defaultOptions = {
        center: DEFAULT_CENTER,
        zoom: 13,
        zoomControl: true,
        scrollWheelZoom: true
    };
    
    const mapOptions = { ...defaultOptions, ...options };
    
    try {
        const map = L.map(containerId, mapOptions);
        
        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);
        
        return map;
    } catch (error) {
        console.error('Error initializing map:', error);
        return null;
    }
}

/**
 * Get coordinates for a location in Cameroon
 */
function getCoordinatesForLocation(location) {
    if (!location) return DEFAULT_CENTER;
    
    const locationLower = location.toLowerCase();
    
    // Check for exact city matches
    for (const [city, coords] of Object.entries(CAMEROON_CITIES)) {
        if (locationLower.includes(city)) {
            return coords;
        }
    }
    
    // Check for partial matches
    for (const [city, coords] of Object.entries(CAMEROON_CITIES)) {
        if (locationLower.includes(city.substring(0, 4))) {
            return coords;
        }
    }
    
    return DEFAULT_CENTER;
}

/**
 * Create a custom marker icon
 */
function createCustomIcon(type = 'default', color = '#667eea') {
    const icons = {
        'default': 'fas fa-map-marker-alt',
        'event': 'fas fa-calendar-check',
        'venue': 'fas fa-building',
        'office': 'fas fa-briefcase',
        'landmark': 'fas fa-info'
    };
    
    const icon = icons[type] || icons['default'];
    
    return L.divIcon({
        html: `
            <div style="
                background: linear-gradient(135deg, ${color}, ${adjustColor(color, -20)}); 
                color: white; 
                width: 40px; 
                height: 40px; 
                border-radius: 50% 50% 50% 0; 
                display: flex; 
                align-items: center; 
                justify-content: center; 
                font-size: 16px; 
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                border: 3px solid white;
                transform: rotate(-45deg);
            ">
                <i class="${icon}" style="transform: rotate(45deg);"></i>
            </div>
        `,
        className: 'custom-map-icon',
        iconSize: [40, 40],
        iconAnchor: [20, 40],
        popupAnchor: [0, -40]
    });
}

/**
 * Adjust color brightness
 */
function adjustColor(color, amount) {
    const usePound = color[0] === '#';
    const col = usePound ? color.slice(1) : color;
    const num = parseInt(col, 16);
    let r = (num >> 16) + amount;
    let g = (num >> 8 & 0x00FF) + amount;
    let b = (num & 0x0000FF) + amount;
    r = r > 255 ? 255 : r < 0 ? 0 : r;
    g = g > 255 ? 255 : g < 0 ? 0 : g;
    b = b > 255 ? 255 : b < 0 ? 0 : b;
    return (usePound ? '#' : '') + (r << 16 | g << 8 | b).toString(16).padStart(6, '0');
}

/**
 * Create a professional popup
 */
function createPopup(title, content, options = {}) {
    const defaultOptions = {
        maxWidth: 300,
        className: 'custom-popup'
    };
    
    const popupOptions = { ...defaultOptions, ...options };
    
    return `
        <div style="
            text-align: center; 
            padding: 15px; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-width: 250px;
        ">
            <div style="
                background: linear-gradient(135deg, #667eea, #764ba2); 
                color: white; 
                padding: 12px; 
                margin: -15px -15px 15px -15px; 
                border-radius: 8px 8px 0 0;
            ">
                <h6 style="margin: 0; font-weight: bold;">${title}</h6>
            </div>
            
            <div style="text-align: left; line-height: 1.6;">
                ${content}
            </div>
        </div>
    `;
}

/**
 * Add a marker with popup to map
 */
function addMarkerToMap(map, coordinates, title, content, iconType = 'default', color = '#667eea') {
    if (!map) return null;
    
    const icon = createCustomIcon(iconType, color);
    const marker = L.marker(coordinates, { icon: icon }).addTo(map);
    
    if (title && content) {
        const popup = createPopup(title, content);
        marker.bindPopup(popup);
    }
    
    return marker;
}

/**
 * Initialize contact page map
 */
function initializeContactMap() {
    const map = initializeMap('map', {
        center: [3.8480, 11.5174],
        zoom: 14
    });
    
    if (!map) return;
    
    // Add main office marker
    const mainMarker = addMarkerToMap(
        map,
        [3.8480, 11.5174],
        'EventBooking Cameroon',
        `
            <p style="margin-bottom: 8px;">
                <i class="fas fa-map-marker-alt me-2" style="color: #667eea;"></i>
                <strong>Address:</strong><br>
                Avenue Kennedy, Plateau District<br>
                Yaoundé, Centre Region, Cameroon
            </p>
            <p style="margin-bottom: 8px;">
                <i class="fas fa-phone me-2" style="color: #667eea;"></i>
                <strong>Phone:</strong> +237 652 731 798
            </p>
            <p style="margin-bottom: 15px;">
                <i class="fas fa-envelope me-2" style="color: #667eea;"></i>
                <strong>Email:</strong> nkumbelarry@gmail.com
            </p>
            <div style="text-align: center;">
                <a href="https://www.openstreetmap.org/search?query=Avenue%20Kennedy%20Yaound%C3%A9%20Cameroon" 
                   target="_blank" 
                   style="
                       background: linear-gradient(135deg, #667eea, #764ba2); 
                       color: white; 
                       padding: 8px 16px; 
                       text-decoration: none; 
                       border-radius: 20px; 
                       font-size: 14px;
                       display: inline-block;
                   ">
                    <i class="fas fa-external-link-alt me-1"></i>View Larger Map
                </a>
            </div>
        `,
        'office',
        '#667eea'
    );
    
    if (mainMarker) {
        mainMarker.openPopup();
    }
    
    // Add service area circle
    L.circle([3.8480, 11.5174], {
        color: '#667eea',
        fillColor: '#667eea',
        fillOpacity: 0.1,
        radius: 800,
        weight: 2,
        opacity: 0.6
    }).addTo(map);
    
    return map;
}

/**
 * Initialize event details map
 */
function initializeEventMap(venue, location, eventName, eventDate, eventTime) {
    const coordinates = getCoordinatesForLocation(location);
    
    const map = initializeMap('event-map', {
        center: coordinates,
        zoom: 15
    });
    
    if (!map) return;
    
    // Add event marker
    const eventMarker = addMarkerToMap(
        map,
        coordinates,
        eventName,
        `
            <p style="margin-bottom: 12px;">
                <i class="fas fa-building me-2" style="color: #dc3545;"></i>
                <strong>Venue:</strong><br>
                ${venue}
            </p>
            <p style="margin-bottom: 12px;">
                <i class="fas fa-map-marker-alt me-2" style="color: #dc3545;"></i>
                <strong>Location:</strong><br>
                ${location}
            </p>
            <p style="margin-bottom: 12px;">
                <i class="fas fa-calendar me-2" style="color: #dc3545;"></i>
                <strong>Date:</strong> ${eventDate}
            </p>
            <p style="margin-bottom: 20px;">
                <i class="fas fa-clock me-2" style="color: #dc3545;"></i>
                <strong>Time:</strong> ${eventTime}
            </p>
            <div style="text-align: center;">
                <button onclick="getDirections('${venue}, ${location}')" 
                        style="
                            background: linear-gradient(135deg, #dc3545, #fd7e14); 
                            color: white; 
                            padding: 10px 20px; 
                            border: none; 
                            border-radius: 25px; 
                            font-size: 14px;
                            cursor: pointer;
                            margin-right: 10px;
                        ">
                    <i class="fas fa-directions me-1"></i>Get Directions
                </button>
                <button onclick="shareLocation('${eventName}', '${venue}', '${location}')" 
                        style="
                            background: #6c757d; 
                            color: white; 
                            padding: 10px 20px; 
                            border: none; 
                            border-radius: 25px; 
                            font-size: 14px;
                            cursor: pointer;
                        ">
                    <i class="fas fa-share me-1"></i>Share
                </button>
            </div>
        `,
        'event',
        '#dc3545'
    );
    
    if (eventMarker) {
        eventMarker.openPopup();
    }
    
    // Add event area circle
    L.circle(coordinates, {
        color: '#dc3545',
        fillColor: '#dc3545',
        fillOpacity: 0.2,
        radius: 500,
        weight: 3,
        opacity: 0.8
    }).addTo(map);
    
    return map;
}

/**
 * Get directions to a location
 */
function getDirections(destination) {
    const encodedDestination = encodeURIComponent(destination);
    
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const userLat = position.coords.latitude;
            const userLng = position.coords.longitude;
            const directionsUrl = `https://www.openstreetmap.org/directions?from=${userLat},${userLng}&to=${encodedDestination}`;
            window.open(directionsUrl, '_blank');
        }, function(error) {
            const directionsUrl = `https://www.openstreetmap.org/search?query=${encodedDestination}`;
            window.open(directionsUrl, '_blank');
        });
    } else {
        const directionsUrl = `https://www.openstreetmap.org/search?query=${encodedDestination}`;
        window.open(directionsUrl, '_blank');
    }
}

/**
 * Share location
 */
function shareLocation(eventName, venue, location) {
    const shareText = `📍 ${eventName} at ${venue}, ${location}`;
    
    if (navigator.share) {
        navigator.share({
            title: eventName,
            text: shareText,
            url: window.location.href
        });
    } else {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(shareText + ' - ' + window.location.href);
            showToast('Location copied to clipboard!', 'success');
        } else {
            const textArea = document.createElement('textarea');
            textArea.value = shareText + ' - ' + window.location.href;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            showToast('Location copied to clipboard!', 'success');
        }
    }
}

/**
 * Show toast notification
 */
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 5000);
}

// Export functions for global use
window.initializeContactMap = initializeContactMap;
window.initializeEventMap = initializeEventMap;
window.getDirections = getDirections;
window.shareLocation = shareLocation;
