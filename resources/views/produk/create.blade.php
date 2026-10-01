@extends('layouts.template')

@section('page-title', 'Tambah Produk')
@section('tanpa-judul', true)

@section('content')
<style>
    .produk-create { padding:10px 0 24px; color:#3f302b; }
    .create-topline { display:flex; justify-content:space-between; align-items:flex-start; gap:16px; margin:0 4px 15px; }
    .create-breadcrumb { margin-bottom:7px; color:#9a887e; font-size:.65rem; }
    .create-breadcrumb a { color:#997867; text-decoration:none; }
    .create-breadcrumb i { margin:0 5px; color:#b29b8d; font-size:.58rem; }
    .create-title-row { display:flex; align-items:center; gap:10px; }
    .create-title-icon { display:grid; place-items:center; width:27px; height:27px; border-radius:50%; background:#f5e7df; color:#91634f; font-size:.9rem; }
    .create-title-row h1 { margin:0; color:#513b31; font:600 1.38rem 'Playfair Display',serif; }
    .create-subtitle { margin:3px 0 0 37px; color:#8a7a73; font-size:.68rem; }
    .create-date { padding-top:9px; color:#80675c; text-align:right; font-size:.65rem; white-space:nowrap; }
    .create-layout { display:grid; grid-template-columns:minmax(0,1.45fr) minmax(280px,.88fr); gap:10px; align-items:stretch; }
    .create-left,.create-right { display:flex; min-width:0; flex-direction:column; gap:10px; }
    .create-panel { min-width:0; padding:14px; border:1px solid #efe4dd; border-radius:9px; background:#fff; box-shadow:0 2px 10px rgba(80,48,34,.025); }
    .create-panel-title { display:flex; align-items:center; gap:8px; margin:0 0 12px; color:#503c32; font-size:.75rem; font-weight:600; }
    .create-panel-title i { color:#8b6250; font-size:.85rem; }
    .create-preview-tag { margin-left:auto; padding:3px 8px; border-radius:20px; background:#fde9ec; color:#ca7280; font-size:.56rem; font-weight:500; }
    .create-fields { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); column-gap:15px; row-gap:10px; }
    .create-field { min-width:0; }
    .create-field.full { grid-column:1 / -1; }
    .create-label { display:block; margin-bottom:5px; color:#5c483e; font-size:.66rem; font-weight:500; }
    .create-label .required { color:#dc7377; }
    .create-input-wrap { position:relative; }
    .create-input-icon { position:absolute; top:50%; left:10px; color:#a18473; font-size:.75rem; transform:translateY(-50%); pointer-events:none; }
    .create-control { display:block; width:100%; min-height:34px; padding:7px 10px 7px 30px; border:1px solid #eadfd8; border-radius:6px; background:#fff; color:#4f4038; font-size:.65rem; }
    .create-control::placeholder { color:#b0a49c; }
    .create-control:focus { border-color:#bd927d; outline:2px solid rgba(189,146,125,.15); }
    .create-control.is-invalid { border-color:#dc7377; }
    .create-field-error { margin-top:4px; color:#c44f58; font-size:.59rem; }
    .create-toggle-row { display:flex; align-items:center; justify-content:space-between; min-height:34px; }
    .create-toggle-label { color:#468b5b; font-size:.68rem; font-weight:600; }
    .create-toggle-help { margin-top:2px; color:#9a8e87; font-size:.56rem; }
    .create-switch { position:relative; width:34px; height:19px; flex:none; }
    .create-switch input { position:absolute; width:1px; height:1px; opacity:0; }
    .create-switch span { position:absolute; inset:0; border-radius:20px; background:#c7c1bc; cursor:pointer; transition:background .18s ease; }
    .create-switch span::before { position:absolute; top:2px; left:2px; width:15px; height:15px; border-radius:50%; background:#fff; content:''; transition:transform .18s ease; }
    .create-switch input:checked + span { background:#36a66d; }
    .create-switch input:checked + span::before { transform:translateX(15px); }
    .create-switch input:focus-visible + span { outline:2px solid #8b6250; outline-offset:2px; }
    .create-description { min-height:94px; padding:9px 10px; resize:vertical; }
    .create-description-meta { display:flex; justify-content:space-between; color:#9a8e87; font-size:.55rem; }
    .photo-upload-layout { display:grid; grid-template-columns:minmax(150px,1.25fr) minmax(0,2.35fr); gap:10px; }
    .photo-dropzone { display:flex; min-height:84px; flex-direction:column; align-items:center; justify-content:center; padding:12px; border:1px dashed #d9c5b9; border-radius:7px; background:#fcf8f5; color:#8c6755; text-align:center; cursor:pointer; transition:border-color .15s,background .15s; }
    .photo-dropzone:hover,.photo-dropzone.dragging { border-color:#a97b63; background:#f8eee8; }
    .photo-dropzone i { margin-bottom:5px; font-size:1.05rem; }
    .photo-dropzone strong { font-size:.63rem; font-weight:500; }
    .photo-dropzone small { margin-top:4px; color:#9b8e86; font-size:.54rem; }
    .photo-input-hidden { display:none; }
    .photo-previews { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:6px; }
    .photo-slot { position:relative; display:grid; min-width:0; min-height:84px; place-items:center; overflow:hidden; border:1px solid #eee3dc; border-radius:7px; background:#fff; color:#b49a8b; }
    .photo-slot img { width:100%; height:100%; min-height:84px; object-fit:cover; }
    .photo-slot.empty { background:#fdfaf8; }
    .photo-slot.empty i { font-size:.9rem; }
    .preview-product { display:grid; grid-template-columns:106px minmax(0,1fr); gap:12px; align-items:center; }
    .preview-image-box { display:grid; width:106px; height:106px; place-items:center; overflow:hidden; border-radius:7px; background:#f4ece6; color:#a47b66; }
    .preview-image-box img { width:100%; height:100%; object-fit:cover; }
    .preview-image-box i { font-size:1.7rem; }
    .preview-product-name { margin:0 0 6px; color:#60483b; font-size:.82rem; font-weight:600; }
    .preview-category { display:inline-block; padding:4px 8px; border-radius:20px; background:#f5e9e4; color:#956c58; font-size:.57rem; }
    .preview-price { margin-top:11px; color:#332820; font-size:.95rem; font-weight:700; }
    .preview-stock { margin-top:5px; color:#77836f; font-size:.6rem; }
    .preview-stock i { color:#35a96b; }
    .preview-summary { flex:1; }
    .summary-row { display:flex; justify-content:space-between; gap:12px; padding:8px 0; border-bottom:1px solid #f1e9e4; color:#8a7a73; font-size:.62rem; }
    .summary-row:last-of-type { border-bottom:0; }
    .summary-row strong { color:#5b4a40; font-size:.63rem; font-weight:500; text-align:right; }
    .summary-status { padding:3px 8px; border-radius:20px; background:#e2f4e8; color:#368b5a; font-size:.57rem; }
    .summary-status.inactive { background:#f1e9e5; color:#8d7c72; }
    .create-actions { display:grid; gap:6px; margin-top:14px; }
    .create-submit,.create-back { display:flex; align-items:center; justify-content:center; gap:7px; min-height:34px; border-radius:6px; font-size:.65rem; text-decoration:none; }
    .create-submit { border:1px solid #d97883; background:#e9808b; color:#fff; font-weight:600; }
    .create-submit:hover { background:#d96f7b; color:#fff; }
    .create-submit:disabled { border-color:#cbbcb4; background:#cbbcb4; }
    .create-back { border:1px solid #e7b1b3; background:#fff; color:#d8737d; }
    .create-back:hover { background:#fdf3f3; color:#b65d67; }
    .create-empty-category { padding:10px; border:1px solid #f0d8b8; border-radius:6px; background:#fff8ed; color:#785e40; font-size:.65rem; }
    @media (max-width:950px) {
        .create-layout { grid-template-columns:minmax(0,1fr); }
        .create-right { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); align-items:stretch; }
        .create-right .summary-panel { grid-column:span 2; }
        .create-topline { align-items:flex-start; }
    }
    @media (max-width:600px) {
        .produk-create { padding-top:7px; }
        .create-topline { flex-direction:column; }
        .create-date { padding:0; text-align:left; }
        .create-fields { grid-template-columns:minmax(0,1fr); }
        .create-field.full { grid-column:auto; }
        .create-right { grid-template-columns:minmax(0,1fr); }
        .create-right .summary-panel { grid-column:auto; }
        .photo-upload-layout { grid-template-columns:minmax(0,1fr); }
        .photo-previews { grid-template-columns:repeat(5,minmax(0,1fr)); }
        .photo-slot { min-height:58px; }
        .photo-slot img { min-height:58px; }
    }
</style>

<div class="produk-create">
    <header class="create-topline">
        <div>
            <div class="create-breadcrumb"><a href="{{ route('produk.index') }}"><i class="bi bi-house-door"></i> Produk</a><i class="bi bi-chevron-right"></i> Tambah Produk</div>
            <div class="create-title-row"><span class="create-title-icon"><i class="bi bi-cake2"></i></span><h1>Tambah Produk</h1></div>
            <p class="create-subtitle">Lengkapi informasi produk dengan detail agar mudah ditemukan oleh pelanggan.</p>
        </div>
        <div class="create-date"><i class="bi bi-calendar3 me-1"></i>{{ now()->translatedFormat('l, d F Y') }}<br>{{ now()->format('H:i') }} WIB</div>
    </header>

    <form action="{{ route('produk.store') }}" method="POST" enctype="multipart/form-data" id="produk-create-form">
        @csrf
        <div class="create-layout">
            <div class="create-left">
                <section class="create-panel">
                    <h2 class="create-panel-title"><i class="bi bi-clipboard-data"></i> Informasi Produk</h2>
                    @if($kategoris->isEmpty())
                        <div class="create-empty-category mb-3">Belum ada kategori. <a href="{{ route('kategori.create') }}">Tambahkan kategori</a> terlebih dahulu.</div>
                    @endif
                    <div class="create-fields">
                        <div class="create-field">
                            <label class="create-label" for="name_produk">Nama Produk <span class="required">*</span></label>
                            <div class="create-input-wrap"><i class="create-input-icon bi bi-cake2"></i><input class="create-control @error('name_produk') is-invalid @enderror" type="text" name="name_produk" id="name_produk" placeholder="Contoh: Brownies Panggang" value="{{ old('name_produk') }}" required></div>
                            @error('name_produk')<div class="create-field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="create-field">
                            <label class="create-label" for="kategori_id">Kategori <span class="required">*</span></label>
                            <div class="create-input-wrap"><i class="create-input-icon bi bi-folder2-open"></i><select class="create-control @error('kategori_id') is-invalid @enderror" name="kategori_id" id="kategori_id" required @disabled($kategoris->isEmpty())><option value="">Pilih kategori produk</option>@foreach($kategoris as $kategori)<option value="{{ $kategori->id_kategori }}" @selected(old('kategori_id') == $kategori->id_kategori)>{{ $kategori->nama_kategori }}</option>@endforeach</select></div>
                            @error('kategori_id')<div class="create-field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="create-field">
                            <label class="create-label" for="harga">Harga <span class="required">*</span></label>
                            <div class="create-input-wrap"><i class="create-input-icon bi bi-currency-dollar"></i><input class="create-control @error('harga') is-invalid @enderror" type="number" min="0" step="1" name="harga" id="harga" placeholder="Contoh: 65000" value="{{ old('harga') }}" required></div>
                            @error('harga')<div class="create-field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="create-field">
                            <label class="create-label" for="stok">Stok <span class="required">*</span></label>
                            <div class="create-input-wrap"><i class="create-input-icon bi bi-box-seam"></i><input class="create-control @error('stok') is-invalid @enderror" type="number" min="0" step="1" name="stok" id="stok" placeholder="Contoh: 10" value="{{ old('stok') }}" required></div>
                            @error('stok')<div class="create-field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="create-field">
                            <label class="create-label" for="stok_minimum">Stok Minimum</label>
                            <div class="create-input-wrap"><i class="create-input-icon bi bi-exclamation-triangle"></i><input class="create-control @error('stok_minimum') is-invalid @enderror" type="number" min="0" step="1" name="stok_minimum" id="stok_minimum" placeholder="Contoh: 3" value="{{ old('stok_minimum', 3) }}" required></div>
                            @error('stok_minimum')<div class="create-field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="create-field">
                            <label class="create-label" for="is_active">Status Produk</label>
                            <input type="hidden" name="is_active" value="0">
                            <div class="create-toggle-row">
                                <div><div class="create-toggle-label" id="toggle-label">Aktif</div><div class="create-toggle-help">Nonaktifkan jika produk tidak dijual.</div></div>
                                <label class="create-switch"><input type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', true))><span></span></label>
                            </div>
                            @error('is_active')<div class="create-field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="create-field full">
                            <label class="create-label" for="deskripsi">Deskripsi Produk <span class="required">*</span></label>
                            <textarea class="create-control create-description @error('deskripsi') is-invalid @enderror" name="deskripsi" id="deskripsi" maxlength="500" placeholder="Tulis deskripsi lengkap tentang produk..." required>{{ old('deskripsi') }}</textarea>
                            <div class="create-description-meta"><span>@error('deskripsi')<span class="create-field-error">{{ $message }}</span>@enderror</span><span><span id="description-count">{{ strlen(old('deskripsi', '')) }}</span>/500</span></div>
                        </div>
                    </div>
                </section>

                <section class="create-panel">
                    <h2 class="create-panel-title"><i class="bi bi-images"></i> Gambar Produk</h2>
                    <div class="photo-upload-layout">
                        <label class="photo-dropzone" id="photo-dropzone" for="fotos">
                            <i class="bi bi-cloud-arrow-up"></i><strong>Klik atau tarik foto ke sini</strong><small>JPG, PNG, WebP | Maks. 5 MB per foto</small>
                        </label>
                        <div class="photo-previews" id="photo-previews" aria-live="polite"></div>
                    </div>
                    <input class="photo-input-hidden" type="file" name="fotos[]" id="fotos" accept="image/jpeg,image/png,image/webp" multiple>
                    <div class="photo-count" id="photo-count">0 dari 5 foto dipilih</div>
                    @if($errors->has('fotos') || $errors->has('fotos.*'))<div class="create-field-error">{{ $errors->first('fotos') ?: $errors->first('fotos.0') }}</div>@endif
                </section>
            </div>

            <aside class="create-right">
                <section class="create-panel">
                    <h2 class="create-panel-title"><i class="bi bi-eye"></i> Preview Produk <span class="create-preview-tag">Preview</span></h2>
                    <div class="preview-product">
                        <div class="preview-image-box"><img id="preview-image" alt="Preview produk" hidden><i class="bi bi-cake2" id="preview-image-empty"></i></div>
                        <div>
                            <h3 class="preview-product-name" id="preview-name">Nama Produk</h3>
                            <span class="preview-category" id="preview-category">Kategori</span>
                            <div class="preview-price" id="preview-price">Rp 0</div>
                            <div class="preview-stock"><i class="bi bi-circle-fill me-1"></i> Stok tersedia: <span id="preview-stock">0</span> pcs</div>
                            <div class="preview-stock"><i class="bi bi-lock me-1"></i> Stok minimum: <span id="preview-minimum">3</span> pcs</div>
                        </div>
                    </div>
                    <p class="small text-muted mb-0 mt-3" id="preview-description">Deskripsi produk akan terlihat di sini.</p>
                </section>

                <section class="create-panel summary-panel">
                    <h2 class="create-panel-title"><i class="bi bi-info-circle"></i> Ringkasan</h2>
                    <div class="preview-summary">
                        <div class="summary-row"><span>Harga</span><strong id="summary-price">Rp 0</strong></div>
                        <div class="summary-row"><span>Stok</span><strong><span id="summary-stock">0</span> pcs</strong></div>
                        <div class="summary-row"><span>Stok Minimum</span><strong><span id="summary-minimum">3</span> pcs</strong></div>
                        <div class="summary-row"><span>Status</span><strong><span class="summary-status" id="summary-status">Aktif</span></strong></div>
                    </div>
                    <div class="create-actions">
                        <button type="submit" class="create-submit" @disabled($kategoris->isEmpty())><i class="bi bi-floppy"></i> Simpan Produk</button>
                        <a href="{{ route('produk.index') }}" class="create-back"><i class="bi bi-arrow-left"></i> Kembali</a>
                    </div>
                </section>
            </aside>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    (() => {
        const form = document.getElementById('produk-create-form');
        const fields = {
            name: document.getElementById('name_produk'),
            category: document.getElementById('kategori_id'),
            price: document.getElementById('harga'),
            stock: document.getElementById('stok'),
            minimum: document.getElementById('stok_minimum'),
            active: document.getElementById('is_active'),
            description: document.getElementById('deskripsi'),
        };
        const photosInput = document.getElementById('fotos');
        const dropzone = document.getElementById('photo-dropzone');
        const previews = document.getElementById('photo-previews');
        const objectUrls = [];

        function updatePreview() {
            const name = fields.name.value.trim() || 'Nama Produk';
            const category = fields.category.selectedOptions[0]?.textContent.trim() || 'Kategori';
            const price = Number(fields.price.value || 0).toLocaleString('id-ID');
            const stock = fields.stock.value || '0';
            const minimum = fields.minimum.value || '0';
            const active = fields.active.checked;

            document.getElementById('preview-name').textContent = name;
            document.getElementById('preview-category').textContent = category;
            document.getElementById('preview-price').textContent = `Rp ${price}`;
            document.getElementById('summary-price').textContent = `Rp ${price}`;
            document.getElementById('preview-stock').textContent = stock;
            document.getElementById('summary-stock').textContent = stock;
            document.getElementById('preview-minimum').textContent = minimum;
            document.getElementById('summary-minimum').textContent = minimum;
            document.getElementById('toggle-label').textContent = active ? 'Aktif' : 'Nonaktif';
            const status = document.getElementById('summary-status');
            status.textContent = active ? 'Aktif' : 'Nonaktif';
            status.classList.toggle('inactive', !active);
            document.getElementById('preview-description').textContent = fields.description.value || 'Deskripsi produk akan terlihat di sini.';
        }

        function renderPhotos(files) {
            objectUrls.splice(0).forEach((url) => URL.revokeObjectURL(url));
            previews.replaceChildren();

            Array.from(files).slice(0, 5).forEach((file, index) => {
                const url = URL.createObjectURL(file);
                objectUrls.push(url);
                const slot = document.createElement('div');
                slot.className = 'photo-slot';
                const image = document.createElement('img');
                image.src = url;
                image.alt = `Foto produk ${index + 1}`;
                slot.append(image);
                previews.append(slot);

                if (index === 0) {
                    const previewImage = document.getElementById('preview-image');
                    previewImage.src = url;
                    previewImage.hidden = false;
                    document.getElementById('preview-image-empty').hidden = true;
                }
            });

            for (let index = files.length; index < 5; index++) {
                const slot = document.createElement('div');
                slot.className = 'photo-slot empty';
                slot.innerHTML = '<i class="bi bi-plus-lg" aria-hidden="true"></i>';
                previews.append(slot);
            }

            if (files.length === 0) {
                document.getElementById('preview-image').hidden = true;
                document.getElementById('preview-image-empty').hidden = false;
            }
            document.getElementById('photo-count').textContent = `${files.length} dari 5 foto dipilih`;
        }

        Object.values(fields).forEach((field) => field.addEventListener('input', updatePreview));
        fields.category.addEventListener('change', updatePreview);
        fields.active.addEventListener('change', updatePreview);
        fields.description.addEventListener('input', () => {
            document.getElementById('description-count').textContent = fields.description.value.length;
        });
        photosInput.addEventListener('change', () => {
            if (photosInput.files.length > 5) {
                photosInput.setCustomValidity('Pilih maksimal 5 foto.');
                photosInput.reportValidity();
                photosInput.value = '';
            }
            photosInput.setCustomValidity('');
            renderPhotos(photosInput.files);
        });
        ['dragenter', 'dragover'].forEach((eventName) => dropzone.addEventListener(eventName, (event) => {
            event.preventDefault();
            dropzone.classList.add('dragging');
        }));
        ['dragleave', 'drop'].forEach((eventName) => dropzone.addEventListener(eventName, (event) => {
            event.preventDefault();
            dropzone.classList.remove('dragging');
        }));
        dropzone.addEventListener('drop', (event) => {
            const transfer = new DataTransfer();
            Array.from(event.dataTransfer.files).slice(0, 5).forEach((file) => transfer.items.add(file));
            photosInput.files = transfer.files;
            renderPhotos(photosInput.files);
        });
        form.addEventListener('submit', (event) => {
            if (photosInput.files.length > 5) {
                event.preventDefault();
                photosInput.setCustomValidity('Pilih maksimal 5 foto.');
                photosInput.reportValidity();
            }
        });

        updatePreview();
        renderPhotos(photosInput.files);
    })();
</script>
@endsection
