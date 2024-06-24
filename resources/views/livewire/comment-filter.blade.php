<div class="row col-4">
    <div class="form-group col-6">
        <select wire:change="setSelectedCategory($event.target.value)" wire:model="selectedCategory" class="form-control" name="category" id="category">
            <option value="">Select Category</option>
            @if (count($categories) > 0)
                @foreach ($categories as $item)
                    <option value="{{ $item->id }}">
                        {{ $item->name }}
                    </option>
                @endforeach
            @endif
        </select>
    </div>
    <div class="form-group col-6">
        <select wire:model="selectedProduct" class="form-control" name="product" id="product">
            <option value="">Select Product</option>
            @if (count($products) > 0)
                @foreach ($products as $item)
                    <option value="{{ $item->id }}"
                        @if (request()->input('product') == $item->id) selected @endif>
                        {{ $item->name }}
                    </option>
                @endforeach
            @endif
        </select>
    </div>
</div>
           