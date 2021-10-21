<?php
namespace App\Http\Livewire;
use App\Models\Simpul;
use App\Models\Location;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use phpDocumentor\Reflection\Types\This;

class Simpull extends Component
{
    public $locationId,$long,$lat,$title,$image;
    public $longg,$latt,$titlee;
    public $geoJson;
    public $imageUrl;
    public $isEdit = false;
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

        $geoJson = collect($geoSimpul)->toJson();
        $this->geoJson = $geoJson;
    }

    Private function clearForm(){
        $this->long = '';
        $this->lat ='';
        $this->title ='';
    }

public function saveSimpul(){
    $this->validate([
        'long' => 'required',
        'lat' => 'required',
        'title' => 'required',
    ]);
            Simpul::create([
                'long' => $this->long,
                'lat' => $this->lat,
                'title' => $this->title,
            ]);
            $this->clearForm();
            $this->loadSimpul();
            $this->dispatchBrowserEvent('simpulAdded',$this->geoJson);
    }

    public function findLocationById($id)
    {
        $Simpull = Simpul::findOrFail($id);
        $this->simpulId = $id;
        $this->long = $Simpull->long;
        $this->lat = $Simpull->lat;
        $this->title = $Simpull->title;
        $this->isEdit = true;
    }
    public function updateSimpul(){
        $this->validate([
            'long' => 'required',
            'lat' => 'required',
            'title' => 'required',

        ]);
        $Simpull = Simpul::findOrFail($this->simpulId);
    
        if($this->title){
    
            $updateData = [
                'title' => $this->title,
            ];
        }
        else {   $updateData = [
            'title' => $this->title,
                ];
            }
            $Simpull->update($updateData);
            $this->clearForm();
            $this->loadSimpul();
            $this->dispatchBrowserEvent('updateLocation',$this->geoJson);
    }

    public function deleteSimpul(){
        $Simpull = Simpul::findOrFail($this->simpulId);
        $Simpull->delete();
        
        $this->clearForm();
        $this->isEdit=false;
        $this->dispatchBrowserEvent('deleteSimpul', $Simpull->id);
    }
    //mappplocaki
    private function loadLocations()
    {
    $locations = Location::orderBy('created_at', 'desc')->get();
    $costumlocations = [];
    foreach ($locations as $location) {
        $costumlocations[] = [
            'type' => 'Feature',
            'geometry' => [
                'coordinates' => [$location->longg, $location->latt],
                'type' => 'point'
            ],
            'properties' => [
                'locationId' => $location->id,
                'title' => $location->titlee,
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



    public function render()
    {
        $this->loadSimpul();
        
        return view('livewire.simpul');
    }
}


