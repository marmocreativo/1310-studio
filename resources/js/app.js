document.addEventListener('alpine:init', () => {
    Alpine.data('galeriaUploader', ({ uploadUrl, csrfToken, inicial }) => ({
        imagenes: inicial,
        drag:     false,
        subiendo: false,
        error:    '',

        drop(e) {
            this.drag = false;
            this.subir(e.dataTransfer.files);
        },

        async subir(files) {
            if (!files.length) return;
            this.error    = '';
            this.subiendo = true;

            const form = new FormData();
            Array.from(files).forEach(f => form.append('imagenes[]', f));
            form.append('_token', csrfToken);

            try {
                const res  = await fetch(uploadUrl, { method: 'POST', body: form });
                const data = await res.json();

                if (!res.ok) {
                    this.error = data.message || 'Error al subir imágenes.';
                    return;
                }

                this.imagenes = data.galeria.map((img, i) => ({
                    ...img,
                    portada: i === 0,
                }));
            } catch (e) {
                this.error = 'Error de conexión al subir imágenes.';
            } finally {
                this.subiendo = false;
                this.$refs.fileInput.value = '';
            }
        },

        async eliminar(img) {
            if (!confirm('¿Eliminar esta imagen?')) return;

            try {
                await fetch(img.deleteUrl, {
                    method:  'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body:    new URLSearchParams({ _method: 'DELETE' }),
                });

                this.imagenes = this.imagenes
                    .filter(i => i.id !== img.id)
                    .map((i, idx) => ({ ...i, portada: idx === 0 }));
            } catch (e) {
                this.error = 'Error al eliminar la imagen.';
            }
        },
    }));

     Alpine.data('dropzone', (initial = null, currentImage = null) => ({
        active:       false,
        preview:      currentImage,   // muestra imagen actual
        hasNewFile:   false,          // indica si hay archivo nuevo
        filename:     '',

        handleDrop(e) {
            this.active = false;
            const file = e.dataTransfer.files[0];
            if (file) this.handleFile(file);
        },

        handleFile(file) {
            if (!file || !file.type.startsWith('image/')) return;
            this.filename   = file.name;
            this.hasNewFile = true;
            const reader    = new FileReader();
            reader.onload   = e => { this.preview = e.target.result; };
            reader.readAsDataURL(file);
        },

        clear() {
            this.preview    = currentImage; // vuelve a la imagen actual
            this.hasNewFile = false;
            this.filename   = '';
            this.$el.querySelector('input[type=file]').value = '';
        },
    }));

    Alpine.data('dropzoneMultiple', () => ({
        active:   false,
        previews: [],

        handleDrop(e) {
            this.active = false;
            this.handleFiles(e.dataTransfer.files);
        },

        handleFiles(files) {
            this.previews = [];
            Array.from(files).forEach(file => {
                if (!file.type.startsWith('image/')) return;
                const reader = new FileReader();
                reader.onload = e => this.previews.push(e.target.result);
                reader.readAsDataURL(file);
            });
        },

        clear() {
            this.previews = [];
            this.$el.querySelector('input[type=file]').value = '';
        },
    }));
});