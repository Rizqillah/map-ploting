<?php

namespace App\Http\Livewire;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use App\Models\Location;
use phpDocumentor\Reflection\Types\This;

class MapLocation extends Component
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

    Private function clearForm(){
        $this->long = '';
        $this->lat ='';
        $this->title ='';
        $this->description ='';
        $this->image ='';
    }

public function saveLocation(){
    $this->validate([
        'long' => 'required',
        'lat' => 'required',
        'title' => 'required',
        'description' => 'required',
        'image' => 'image|max:2048|required',
    ]);
        $imageName = md5($this->image.microtime()).'.'.$this->image->extension();

        storage::putFileAs(
            'public/images',
            $this->image,
            $imageName
        );
            Location::create([
                'long' => $this->long,
                'lat' => $this->lat,
                'title' => $this->title,
                'description' => $this->description,
                'image' =>$imageName,
            ]);

            $this->clearForm();
            $this->loadLocations();
            $this->dispatchBrowserEvent('locationAdded',$this->geoJson);
    }

    public function findLocationById($id){
        $location = Location::findOrFail($id);
        $this->locationId = $id;
        $this->long = $location->long;
        $this->lat = $location->lat;
        $this->title = $location->title;
        $this->description = $location->description;
        $this->imageUrl = $location->image; 
        $this->isEdit = true;
    }
    
public function updateLocation(){
    $this->validate([
        'long' => 'required',
        'lat' => 'required',
        'title' => 'required',
        'description' => 'required',
    ]);
    $location = Location::findOrFail($this->locationId);

    if($this->image){
        $imageName = md5($this->image.microtime()).'.'.$this->image->extension();
        storage::putFileAs(
            'public/images',
            $this->image,
            $imageName
        );

        $updateData = [
            'title' => $this->title,
            'description' => $this->description,
            'image' => $imageName
        ];
    }
    else {   $updateData = [
        'title' => $this->title,
        'description' => $this->description,
        
            ];
        }

        $location->update($updateData);

        $this->imageUrl ="";

        $this->clearForm();
        $this->loadLocations();
        $this->dispatchBrowserEvent('updateLocation',$this->geoJson);
}

    public function deleteLocation(){
        $location = Location::findOrFail($this->locationId);
        $location->delete();
        
        $this->imageUrl ="";
        $this->clearForm();
        $this->isEdit=false;
        $this->dispatchBrowserEvent('deleteLocation', $location->id);
    }

    public function render()
    {
        $this->loadLocations();
        return view('livewire.map-location');
        
    }
}
