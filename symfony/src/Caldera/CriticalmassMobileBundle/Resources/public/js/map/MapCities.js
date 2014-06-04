MapCities = function(map)
{
    this.map = map;
};

MapCities.prototype.map = null;

MapCities.prototype.cityMarkersArray = [];

MapCities.prototype.drawCityMarkers = function()
{
    var criticalmassIcon = L.icon({
        iconUrl: '/bundles/calderacriticalmasscore/images/marker/criticalmassblue.png',
        iconSize: [25, 41],
        iconAnchor: [13, 41],
        popupAnchor: [0, -36],
        shadowUrl: '/bundles/calderacriticalmasscore/images/marker/defaultshadow.png',
        shadowSize: [41, 41],
        shadowAnchor: [13, 41]
    });

    var cities = CityFactory.getAllCities();

    for (slug in cities)
    {
        var city = cities[slug];
        var marker;
        var ride = RideFactory.getRideFromStorageBySlug(slug);

        if (ride != null && ride.getHasLocation())
        {
            marker = L.marker([ride.getLatitude(), ride.getLongitude()], { riseOnHover: true, icon: criticalmassIcon });
            marker.bindPopup('<h3>' + ride.getTitle() + '</h3><p>' + ride.getDescription() + '</p>');
        }
        else
        if (ride != null)
        {
            marker = L.marker([city.getLatitude(), city.getLongitude()], { riseOnHover: true, icon: criticalmassIcon });
            marker.bindPopup('<h3>' + ride.getTitle() + '</h3><p>' + ride.getDescription() + '</p>');
        }
        else
        {
            marker = L.marker([city.getLatitude(), city.getLongitude()], { riseOnHover: true, icon: criticalmassIcon });
            marker.bindPopup('<h3>' + city.getTitle() + '</h3><p>' + city.getDescription() + '</p>');
        }

        var this2 = this;
        marker.slug = slug;
        marker.addTo(this.map.map);

        this.cityMarkersArray[slug] = marker;
        this.cityMarkersArray[slug].on('click', function(e) { alert(e.slug); this2.map.parentPage.switchCityBySlug(e.slug); });
    }
};