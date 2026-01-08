<p class="text-muted small">
    Upload foto profil kamu. Format yang didukung: JPG, PNG, WebP. Maksimal 3MB.
</p>

<form method="POST" action="{{ route('profile.avatar.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PATCH')

    <div class="d-flex align-items-center gap-4">
        {{-- Avatar Preview --}}
        <div class="position-relative">
            <img id="avatar-preview"
                 class="rounded-circle object-fit-cover border shadow-sm"
                 style="width: 120px; height: 120px;"
                 src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/default-avatar.png') }}"
                 alt="{{ $user->name }}">

            @if($user->avatar)
                <button type="button"
                        onclick="if(confirm('Hapus foto profil?')) document.getElementById('delete-avatar-form').submit()"
                        class="btn btn-danger btn-sm rounded-circle position-absolute top-0 start-100 translate-middle p-1"
                        style="width: 28px; height: 28px; font-size: 14px;"
                        title="Hapus foto">
                    ×
                </button>
            @endif
        </div>

        {{-- Upload Input --}}
        <div class="flex-grow-1">
            <input type="file"
                   name="avatar"
                   id="avatar"
                   accept="image/jpeg,image/png,image/webp"
                   onchange="previewAvatar(event)"
                   class="form-control @error('avatar') is-invalid @enderror">

            @error('avatar')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary px-4">
            Simpan Foto Profil
        </button>
    </div>
</form>

{{-- Hidden Form untuk Hapus Avatar --}}
<form id="delete-avatar-form" action="{{ route('profile.avatar.destroy') }}" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

<script>
    function previewAvatar(event) {
        const file = event.target.files[0];
        if (file) {
            // Validasi ukuran file (maks 3MB)
            if (file.size > 3 * 1024 * 1024) {
                alert('Ukuran file maksimal 3MB!');
                event.target.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatar-preview').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    }
</script>
