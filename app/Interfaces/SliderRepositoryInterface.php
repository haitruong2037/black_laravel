<?php

namespace App\Interfaces;

use App\Http\Requests\StoreSliderRequest;
use App\Http\Requests\UpdateSliderRequest;

interface SliderRepositoryInterface
{
    public function getAllSliders();
    public function getSliderById($sliderId);
    public function getPushedSlider();
    public function createSlider(StoreSliderRequest $storeSliderRequest);
    public function updateSlider($sliderId, UpdateSliderRequest $updateSliderRequest);
    public function deleteSlider($sliderId);
}