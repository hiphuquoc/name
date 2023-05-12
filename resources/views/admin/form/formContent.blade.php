<div class="formBox">
    <div class="formBox_full">
        <div class="formBox_full_item">
            <!-- One Column -->
            <textarea class="form-control" id="content"  name="content" rows="20">{{ old('content') ?? $content ?? '' }}</textarea>
        </div>
        <div class="formBox_full_item">
            <!-- One Column -->
            <textarea class="form-control" id="en_content"  name="en_content" rows="20">{{ old('en_content') ?? $enContent ?? '' }}</textarea>
        </div>
    </div>
</div>

@push('scripts-custom')
    {{-- @include('admin.script.tiny', ['id' => 'content']) --}}
@endpush