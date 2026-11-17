@php($isPrivate = (bool) old('is_private', isset($category) && $category->roles !== null))
@php($selectedRoles = (array) old('roles', $category->roles ?? []))
@php($iconType = old('icon_type', $category->icon_type ?? 'icon'))
@csrf

<div class="row gx-3">
    <div class="mb-3 col-md-6">
        <label class="form-label" for="nameInput">{{ trans('messages.fields.name') }}</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="nameInput" name="name" value="{{ old('name', $category->name ?? '') }}" required>

        @error('name')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="mb-3 col-md-6">
        <label class="form-label" for="parentSelect">{{ trans('wiki::admin.categories.parent') }}</label>

        <select class="form-select" id="parentSelect" name="parent_id">
            <option value="">{{ trans('messages.none') }}</option>
            @foreach($categories as $sub)
                <option value="{{ $sub->id }}" @selected((int) old('parent_id', $category->parent_id ?? 0) === $sub->id)>
                    {{ $sub->name }}
                </option>
            @endforeach
        </select>

        @error('parent_id')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="mb-3">
    <label class="form-label d-block">{{ trans('wiki::admin.categories.display') }}</label>

    <div class="form-check form-check-inline">
        <input type="radio" class="form-check-input" id="iconTypeIcon" name="icon_type" value="icon" @checked($iconType !== 'image')>
        <label class="form-check-label" for="iconTypeIcon">{{ trans('wiki::admin.categories.display_icon') }}</label>
    </div>

    <div class="form-check form-check-inline">
        <input type="radio" class="form-check-input" id="iconTypeImage" name="icon_type" value="image" @checked($iconType === 'image')>
        <label class="form-check-label" for="iconTypeImage">{{ trans('wiki::admin.categories.display_image') }}</label>
    </div>

    @error('icon_type')
    <span class="d-block text-danger small" role="alert"><strong>{{ $message }}</strong></span>
    @enderror
</div>

<div id="iconGroup" class="@if($iconType === 'image') d-none @endif">
    <div class="mb-3">
        <label class="form-label" for="iconInput">{{ trans('messages.fields.icon') }}</label>

        <div class="input-group @error('icon') has-validation @enderror">
            <span class="input-group-text"><i class="{{ $category->icon ?? 'bi bi-book' }}"></i></span>

            <input type="text" class="form-control @error('icon') is-invalid @enderror" id="iconInput" name="icon" value="{{ old('icon', $category->icon ?? '') }}" placeholder="bi bi-book" aria-labelledby="iconLabel">

            @error('icon')
            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <small id="iconLabel" class="form-text">@lang('messages.icons')</small>
    </div>
</div>

<div id="imageGroup" class="@if($iconType !== 'image') d-none @endif">
    <div class="row gx-3">
        <div class="mb-3 col-md-6">
            <label class="form-label" for="imageFileInput">{{ trans('wiki::admin.categories.image_file') }}</label>
            <input type="file" class="form-control @error('image_file') is-invalid @enderror" id="imageFileInput" name="image_file" accept=".png,.jpg,.jpeg,.gif,.webp,image/png,image/jpeg,image/gif,image/webp">

            @error('image_file')
            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <div class="mb-3 col-md-6">
            <label class="form-label" for="imageLinkInput">{{ trans('wiki::admin.categories.image_link') }}</label>
            <input type="url" class="form-control @error('image_link') is-invalid @enderror" id="imageLinkInput" name="image_link" value="{{ old('image_link', isset($category) && $category->hasExternalImage() ? $category->image : '') }}" placeholder="https://example.com/image.png">

            @error('image_link')
            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>

    <small class="form-text d-block mb-3">{{ trans('wiki::admin.categories.image_help') }}</small>

    @if(isset($category) && $category->image_url)
        <div class="mb-3">
            <span class="form-label d-block">{{ trans('wiki::admin.categories.image_current') }}</span>
            <img src="{{ $category->image_url }}" alt="" class="img-thumbnail" style="max-height: 4rem;">
        </div>
    @endif
</div>

<div class="mb-3">
    <label class="form-label" for="slugInput">{{ trans('messages.fields.slug') }}</label>
    <div class="input-group @error('slug') has-validation @enderror">
        <div class="input-group-text">{{ route('wiki.index') }}/</div>
        <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slugInput" name="slug" value="{{ old('slug', $category->slug ?? '') }}" required>

        @error('slug')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="mb-3 form-check form-switch">
    <input type="checkbox" class="form-check-input" id="privateSwitch" name="is_private" data-bs-toggle="collapse" data-bs-target="#rolesGroup" @checked($isPrivate)>
    <label class="form-check-label" for="privateSwitch">{{ trans('wiki::admin.categories.private') }}</label>
</div>

<div id="rolesGroup" class="{{ $isPrivate ? 'show' : 'collapse' }}">
    <div class="card card-body mb-2 pb-0">
        @foreach($roles as $role)
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="role{{ $role->id }}" name="roles[]" value="{{ $role->id }}" @checked(in_array($role->id, $selectedRoles))>
                <label class="form-check-label" for="role{{ $role->id }}">
                    <span class="badge" style="{{ $role->getBadgeStyle() }}">{{ $role->name }}</span>
                </label>
            </div>
        @endforeach
    </div>
</div>

<div class="mb-3 form-check form-switch">
    <input type="checkbox" class="form-check-input" id="enableSwitch" name="is_enabled" @checked(old('is_enabled', $category->is_enabled ?? true))>
    <label class="form-check-label" for="enableSwitch">{{ trans('wiki::admin.categories.enable') }}</label>
</div>

@push('footer-scripts')
    <script>
        document.querySelectorAll('input[name="icon_type"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                const isImage = document.getElementById('iconTypeImage').checked;

                document.getElementById('iconGroup').classList.toggle('d-none', isImage);
                document.getElementById('imageGroup').classList.toggle('d-none', ! isImage);
            });
        });
    </script>
@endpush
