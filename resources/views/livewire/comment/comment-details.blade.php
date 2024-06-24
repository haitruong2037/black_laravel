<div class="modal fade" id="modal_comment_details1" tabindex="-1" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
        <h4 class="modal-title">Comment Details</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        </div>
        <div class="modal-body">
            @if(!empty($commentDetails))
                <form wire:submit="save">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <label>Created At:</label>
                            <p class="form-control">{{$commentDetails->created_at}} </p>
                        </div>
                        <div class="col-sm-6">
                            <label>User:</label>
                            <a href="{{route('admin.users.show', ['id' => $commentDetails->user?->id])}}" class="form-control">{{$commentDetails->user->name}}</a>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <label>Product:</label>
                            <div class="row">
                                <img src="{{ $commentDetails->product->url_image }}"
                                        alt="{{ $commentDetails->product->image }}"
                                        class="modal-details-custom-img col-2"> 
                                <div class="col-10">
                                    <a href="{{route('admin.products.show', ['id' => $commentDetails->product->id])}}" 
                                            class="custom-link-redirect">
                                        {{ $commentDetails->product->name }}
                                    </a> 
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label>Rate:</label>
                            <p class="form-control">{{$commentDetails->rate}} <i class="fas fa-star" style="color: #FFD43B;"></i></p>
                        </div>
                        <div class="col-sm-3">
                            <label>Status:</label>
                            <select wire:model="hidden" class="form-control">
                                <option value="0">Showing</option>
                                <option value="1">Hidden</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-12">
                            <label>Content:</label>
                            <textarea readonly class="form-control" cols="10" rows="3">{{$commentDetails->content}}</textarea>
                        </div>
                    </div>
                    @if (!empty($commentDetails->replies) && count($commentDetails->replies) > 0)
                        <div class="row mb-2 p-2">
                            <div style="max-height: 400px" class="col-12 overflow-auto border mt-4 rounded-lg p-2 py-4 bg-light">
                                @foreach ($commentDetails->replies as $item)
                                    <div class="mb-2 col-12 @if($item->admin_id) justify-content-end text-right @endif" >
                                        <label name="replyContent" class="block">
                                            @if($item->admin_id) 
                                                <a href="{{route("admin.manager_admin.show", ['id' => $item->admin_id])}}"
                                                    class="custom-link-redirect">
                                                    {{$item->admin->name}}
                                                </a> 
                                            @else 
                                                <a href="{{route("admin.users.show", ['id' => $item->user_id])}}"
                                                    class="custom-link-redirect">
                                                    {{$item->user->name}}
                                                </a> 
                                            @endif
                                        </label>
                                        <div class="comment-replies-action mt-1">
                                            <span>
                                                <span wire:click="$dispatch('handle_delete_comment_reply', { id: {{ $item->id }} })" class="comment-replies-action__remove text-danger">
                                                    <i class="far fa-trash-alt"></i>
                                                </span>               
                                                 <span class="p-2 mb-2 bg-white text-dark rounded-lg border">{{$item->content}}</span>  
                                            </span>
                                            <p class="text-secondary font-weight-normal mt-2 text-sm">{{$item->created_at}}</p>                         
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <div class="row mb-2">
                        <div class="col-12">
                            <label name="replyContent">Reply:</label>
                            <textarea wire:model="replyContent" class="form-control" name="" id="" cols="10" rows="3"></textarea>
                             @error('replyContent') <span class="error text-danger">{{ $message }}</span> @enderror 
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
    <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
