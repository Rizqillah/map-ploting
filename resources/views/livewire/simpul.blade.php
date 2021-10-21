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
                        wire:submit.prevent="updateSimpul"
                        @else
                        wire:submit.prevent="saveSimpul"
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
                        <label>Simpul</label>
                        <input wire:model="title" type="text" class="form-control">
                        @error('title') <small class="text-danger">{{$message}}</small> @enderror
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-dark text-white btn-block">{{$isEdit ? "Edit simpul" : "Tambah simpul" }}</button>
                        @if($isEdit)
                        <button wire:click="deleteSimpul" type="submit" class="btn btn-danger text-white btn-block">Hapus simpul</button>
                        @endif
                    </div>
                </form>
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
    
            const loadSimpul = (geoJson) => {
                geoJson.features.forEach((location) => {
                    const {
                        geometry
                        , properties
                    } = location
                    const {
                        iconSize
                        , locationId
                        , title
                    } = properties
                    let markerElement = document.createElement('div')
                    markerElement.className = 'marker' + locationId
                    markerElement.id = locationId
                    markerElement.style.backgroundImage = 'url(https://e7.pngegg.com/pngimages/760/399/png-clipart-map-computer-icons-flat-design-location-logo-location-icon-photography-heart-thumbnail.png)'
                    markerElement.style.backgroundSize = 'cover'
                    markerElement.style.width = '30px'
                    markerElement.style.height = '30px'
                    const content = `
                <div style="overflow-y, auto;max-height:400px,width:100% ">
                <table class="table table-sm mt-2">
                    <tbody>
                        <tr>
                            <td>Simpul</td>
                            <td>${title}</td>
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
            loadSimpul({!! $geoJson !!})
            window.addEventListener('locationAdded', (e) => {
                loadSimpul(JSON.parse(e.detail))
            })
            window.addEventListener('updateSimpul', (e) => {
                loadSimpul(JSON.parse(e.detail))
                $('.mapboxgl.popup').remove()
            })
            window.addEventListener('deleteSimpul', (e) => {
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
            //load tempat
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
                markerElement.style.width = '30px'
                markerElement.style.height = '30px'

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

        })

    </script>
@endpush
    
