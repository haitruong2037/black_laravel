<?php 

namespace App\Respositories;

use App\Http\Requests\StoreSliderRequest;
use App\Http\Requests\UpdateSliderRequest;
use App\Interfaces\SliderRepositoryInterface;
use App\Models\Slider;

class SliderRepository implements SliderRepositoryInterface
{
    private Slider $slider;

    public function __construct(Slider $slider)
    {
        $this->slider = $slider;
    }
    
    public function getAllSliders()
    {
        return $this->slider->all();
    }

    public function getSliderById($sliderId)
    {
        return $this->slider->findOrFail($sliderId);
    }

    public function getPushedSlider()
    {
        return $this->slider->where('is_published', true)->get();
    }

    public function createSlider(StoreSliderRequest $storeSliderRequest)
    {
        return $this->slider->create($storeSliderRequest->toArray());
    }

    public function updateSlider($sliderId, UpdateSliderRequest $updateSliderRequest)
    {
        $slider = $this->slider->findOrFail($sliderId);

        $slider->update($updateSliderRequest->toArray());
    }

    public function deleteSlider($sliderId)
    {
        $slider = $this->slider->findOrFail($sliderId);
        $slider->delete();
    }


}
