<?php
namespace App\Http\Livewire;
use Livewire\WithFileUploads;
use App\Models\Location;
use App\Models\Simpul;
use Livewire\Component;
class GrafLocation extends Component
{
    use WithFileUploads;

    public $locationId,$long,$lat,$title,$description,$image;
    public $geoJson;
    public $imageUrl;
    public $isEdit = false;

    private function loadLocations()
    {
        
        $locations = Location::orderBy('created_at', 'desc')->get();
        $costumlocations = [];
        foreach ($locations as $location) {
            $costumlocations[] = [
                'type' => 'Feature',
                'geometry' => [
                    'coordinates' => [$location->long, $location->lat],
                    'type' => 'point'
                ],
                'properties' => [
                    'locationId' => $location->id,
                    'title' => $location->title,
                    'image' => $location->image,
                    'description' => $location->description

                ]
            ];
            
        }
        $geoLocation = [
            'type' => 'FeatureCollection',
            'features' => $costumlocations
        ];

        $geoJson = collect($geoLocation)->toJson();
        $this->geoJson = $geoJson;
    }
    //simpull
    public $longg,$latt,$titlee;
    public $geoJsonn;
    private function loadSimpul()
    {
        $simpulss = Simpul::orderBy('created_at', 'desc')->get();
        $costumsimpuls = [];
        foreach ($simpulss as $Simpull) {
            $costumsimpuls[] = [
                'type' => 'Feature',
                'geometry' => [
                    'coordinates' => [$Simpull->long, $Simpull->lat],
                    'type' => 'point'
                ],
                'properties' => [
                    'locationId' => $Simpull->id,
                    'title' => $Simpull->title,
                ]
            ];
            
        }
        $geoSimpul = [
            'type' => 'FeatureCollection',
            'features' => $costumsimpuls
        ];
        $geoJsonn = collect($geoSimpul)->toJson();
        $this->geoJsonn = $geoJsonn;
    }

    public function render()
    {
        $this->loadLocations();
        $this->loadSimpul();
        return view('livewire.graf-location');
    }
}
