<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    MAPBOX
                </div>
                <div class="card-body">
                    <div wire:ignore id='map' style='width: 100%; height: 85vh'></div>
                <div id="map"></div> 
                </div>
            </div>
        </div>
        <div class="col-md-4 ">
            <div class="card ">
                <div class="card-header bg-dark text-white">
                    From
                </div>
                <div class="card-body">

            <form 
                    @if($isEdit)
                    wire:submit.prevent="updateLocation"
                    @else
                    wire:submit.prevent="saveLocation"
                    @endif
            >    
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Longtitude</label>
                            <input wire:model="long" type="text" class="form-control">
                            @error('long') <small class="text-danger">{{$message}}</small> @enderror
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Latitude</label>
                            <input wire:model="lat" type="text" class="form-control">
                            @error('lat') <small class="text-danger">{{$message}}</small> @enderror
                        </div>
                    </div>
                </div>
                        <div class="form-group">
                            <label>title</label>
                            <input wire:model="title" type="text" class="form-control">
                            @error('title') <small class="text-danger">{{$message}}</small> @enderror
                                
                        </div>

                        <div class="form-group">
                            <label>description</label>
                            <textarea wire:model="description" class="form-control"> </textarea>
                            @error('description')<small class="text-danger">{{$message}}</small> @enderror
                        </div>
                        
                        <div class="form-group">
                            
                            <div class="mb-3">
                                <label for="formFile" class="form-label">picture</label>
                                <input wire:model="image" type="file" class="form-control"  id="formFile">  
                            </div>
                                @error('image')<small class="text-danger">{{ $message }}</small> @enderror    
                            @if($image)
                                <img src="{{$image->temporaryUrl()}}" class="img-fluid">
                            @endif
                            @if($imageUrl && !$image)
                            <img src="{{asset('/storage/images/'.$imageUrl)}}" class="img-fluid">
                        @endif
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-dark text-white btn-block">{{$isEdit ? "upload Location" : "submit Location" }}</button>
                            @if($isEdit)
                            <button wire:click="deleteLocation" type="submit" class="btn btn-danger text-white btn-block">delete Lokasi</button>
                            @endif
                        </div>
            </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:load', () => {
        const defaultLocation = [110.38479959232711, -7.8286141156119555]
        mapboxgl.accessToken = '{{ env("MAPBOX_KEY") }}';
        var map = new mapboxgl.Map({
            container: 'map'
            , center: defaultLocation
            , zoom: 15.40
            , style: 'mapbox://styles/mapbox/streets-v11'
        });

        const loadLocations = (geoJson) => {
            geoJson.features.forEach((location) => {
                const {
                    geometry
                    , properties
                } = location
                const {
                    iconSize
                    , locationId
                    , title
                    , image
                    , description
                } = properties
                let markerElement = document.createElement('div')
                markerElement.className = 'marker' + locationId
                markerElement.id = locationId
                markerElement.style.backgroundImage = 'url(https://e7.pngegg.com/pngimages/337/215/png-clipart-computer-icons-location-google-maps-location-icon-black-map-thumbnail.png)'
                markerElement.style.backgroundSize = 'cover'
                markerElement.style.width = '50px'
                markerElement.style.height = '50px'

                const imageStorage = '{{asset("/storage/images")}}' + '/' + image
                const content = `
            <div style="overflow-y, auto;max-height:400px,width:100% ">
            <table class="table table-sm mt-2">
                <tbody>
                    <tr>
                        <td>judul</td>
                        <td>${title}</td>
                    </tr>
                    <tr>
                        <td>foto</td>
                        <td><img src="${imageStorage}" loading="lazy" class="img-fluid"></td>
                    </tr>
                    <tr>
                        <td>description</td>
                        <td>${description}</td>
                    </tr>
                </tbody>
            </table>
            </div>
            `
                markerElement.addEventListener('click', (e) => {
                        const locationId = e.srcElement.id
                        @this.findLocationById(locationId)
                    }
                )
                
                const popUp = new mapboxgl.Popup({
                    offset: 25
                }).setHTML(content).setMaxWidth("400px")
                new mapboxgl.Marker(markerElement)
                    .setLngLat(geometry.coordinates)
                    .setPopup(popUp)
                    .addTo(map)
            })
        }

        loadLocations({!! $geoJson !!})
        
        window.addEventListener('locationAdded', (e) => {
            loadLocations(JSON.parse(e.detail))
        })
        window.addEventListener('updateLocation', (e) => {
            loadLocations(JSON.parse(e.detail))
            $('.mapboxgl.popup').remove()
        })
        window.addEventListener('deleteLocation', (e) => {
            $('.marker' + e.detail).remove()
            $('.mapboxgl.popup').remove()
        })
        
        map.addControl(
            new MapboxGeocoder({
                accessToken: mapboxgl.accessToken
                , mapboxgl: mapboxgl
            })
        )
        map.addControl(new mapboxgl.NavigationControl())
        map.on('click', (e) => {
            const longtitude = e.lngLat.lng
            const lattitude = e.lngLat.lat
            @this.long = longtitude
            @this.lat = lattitude
        })
/*
        map.addControl(
            new MapboxDirections({
                accessToken: mapboxgl.accessToken
            })
            , 'top-left'
        );
**/
        map.addControl(
            new mapboxgl.GeolocateControl({
                positionOptions: {
                    enableHighAccuracy: true
                },
                // When active the map will receive updates to the device's location as it changes.
                trackUserLocation: true,
                // Draw an arrow next to the location dot to indicate which direction the device is heading.
                showUserHeading: true
            })
        );
    })
</script>
@endpush
