var CriticalMass = CriticalMass || {};

CriticalMass.loadModule = function (name, context, options, callback) {
    require([name], function (Module) {
        var module = new Module(context, options);

        if (callback) {
            callback(module);
        }
    });
};

require.config({
    baseUrl: '/js/',
    shim: {
        'leaflet-sleep': {
            deps: ['leaflet'],
            exports: 'L.Map.Sleep'
        },
        'leaflet.extra-markers': {
            deps: ['leaflet'],
            exports: 'L.ExtraMarkers'
        },
        'leaflet-markercluster': {
            deps: ['leaflet'],
            exports: 'L.MarkerClusterGroup'
        },
        'Polyline.encoded': {
            deps: ['leaflet'],
            exports: 'L.PolylineUtil'
        },
        'typeahead.jquery': {
            deps: ['jquery'],
            init: function ($) {
                return require.s.contexts._.registry['typeahead.js'].factory($);
            }
        },
        'bloodhound': {
            deps: [],
            exports: 'Bloodhound'
        }
    }
});
