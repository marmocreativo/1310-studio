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

            const archivosComprimidos = await Promise.all(
                Array.from(files).map(f => comprimirImagen(f, 1200, 0.8))
            );
            archivosComprimidos.forEach(f => form.append('imagenes[]', f));

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
        preview:      currentImage,
        hasNewFile:   false,
        filename:     '',
        comprimiendo: false,

        handleDrop(e) {
            this.active = false;
            const file = e.dataTransfer.files[0];
            if (file) this.handleFile(file, e.dataTransfer);
        },

        async handleFile(file, originDataTransfer = null) {
            if (!file || !file.type.startsWith('image/')) return;

            this.filename     = file.name;
            this.hasNewFile   = true;
            this.comprimiendo = true;

            const inputEl = this.$el.querySelector('input[type=file]');

            try {
                const comprimido = await comprimirImagen(file, 1200, 0.8);

                const dt = new DataTransfer();
                dt.items.add(comprimido);
                inputEl.files = dt.files;

                const reader  = new FileReader();
                reader.onload = e => { this.preview = e.target.result; };
                reader.readAsDataURL(comprimido);
            } finally {
                this.comprimiendo = false;
            }
        },

        clear() {
            this.preview    = currentImage;
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

    Alpine.data('variacionesManager', (config) => ({

        cargando:        true,
        guardando:       false,
        tipos:           [],
        skus:            [],
        defaults:        [],
        nuevoTipo:       '',
        mostrarDefaults: false,
        errorTipo:       '',
        errorGlobal:     '',
        mensajeGenerar:  '',

        async init() {
            await Promise.all([this.cargarDatos(), this.cargarDefaults()]);
            this.cargando = false;
        },

        async cargarDatos() {
            const res  = await fetch(config.urlIndex, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            this.aplicarPayload(data);
        },

        async cargarDefaults() {
            const res  = await fetch(config.urlDefaults, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            this.defaults = data.tipos ?? [];
        },

        aplicarPayload(data) {
            this.tipos = (data.tipos ?? []).map(t => ({
                ...t,
                _editando:    false,
                _nombreTemp:  '',
                _nuevaOpcion: '',
                opciones: (t.opciones ?? []).map(o => ({
                    ...o,
                    _editando:   false,
                    _nombreTemp: '',
                })),
            }));
            this.skus = (data.skus ?? []).map(s => ({
                ...s,
                _precioVenta: s.precio_venta,
                _precioLista: s.precio_lista ?? '',
            }));
        },

        async api(url, method = 'GET', body = null) {
            this.guardando   = true;
            this.errorGlobal = '';
            try {
                const opts = {
                    method,
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': config.csrfToken },
                };
                if (body instanceof FormData) {
                    opts.body = body;
                } else if (body) {
                    opts.headers['Content-Type'] = 'application/json';
                    opts.body = JSON.stringify(body);
                }
                const res  = await fetch(url, opts);
                const data = await res.json();
                if (!res.ok) { this.errorGlobal = data.message ?? 'Error en la solicitud.'; return null; }
                return data;
            } catch (e) {
                this.errorGlobal = 'Error de conexión.';
                return null;
            } finally {
                this.guardando = false;
            }
        },

        urlBase()              { return config.urlTipoStore.replace('/tipos', ''); },
        urlTipo(tipo)          { return `${this.urlBase()}/tipos/${tipo.id}`; },
        urlOpcionStore(tipo)   { return `${this.urlBase()}/tipos/${tipo.id}/opciones`; },
        urlOpcion(opcion)      { return `${this.urlBase()}/opciones/${opcion.id}`; },
        urlSku(sku)            { return `${config.urlSkuStore}/${sku.id}`; },
        urlSkuImagen(sku)      { return `${config.urlSkuStore}/${sku.id}/imagen`; },

        // ── Defaults ──────────────────────────────────────────────────────────
        async importarDefault(def) {
            const data = await this.api(config.urlTipoStore, 'POST', { nombre: def.nombre, id_tipo_default: def.id });
            if (!data) return;

            const tipo = { ...data.tipo, _editando: false, _nombreTemp: '', _nuevaOpcion: '', opciones: [] };

            for (const opDef of (def.opciones ?? [])) {
                const opData = await this.api(this.urlOpcionStore(tipo), 'POST', { nombre: opDef.nombre, id_opcion_default: opDef.id });
                if (opData) tipo.opciones.push({ ...opData.opcion, _editando: false, _nombreTemp: '' });
            }

            this.tipos.push(tipo);
        },

        // ── Tipos ─────────────────────────────────────────────────────────────
        async agregarTipo() {
            this.errorTipo = '';
            if (!this.nuevoTipo.trim()) return;
            const data = await this.api(config.urlTipoStore, 'POST', { nombre: this.nuevoTipo.trim() });
            if (!data) return;
            this.tipos.push({ ...data.tipo, _editando: false, _nombreTemp: '', _nuevaOpcion: '', opciones: [] });
            this.nuevoTipo = '';
        },

        async guardarNombreTipo(tipo) {
            if (!tipo._nombreTemp.trim()) return;
            const data = await this.api(this.urlTipo(tipo), 'PATCH', { nombre: tipo._nombreTemp.trim() });
            if (!data) return;
            tipo.nombre    = data.nombre;
            tipo._editando = false;
        },

        async eliminarTipo(tipo) {
            if (!confirm(`¿Eliminar el tipo "${tipo.nombre}" y todas sus opciones? Se eliminarán también las combinaciones afectadas.`)) return;
            const data = await this.api(this.urlTipo(tipo), 'DELETE');
            if (!data) return;
            this.aplicarPayload(data.payload);
        },

        // ── Opciones ──────────────────────────────────────────────────────────
        async agregarOpcion(tipo) {
            if (!tipo._nuevaOpcion || !tipo._nuevaOpcion.trim()) return;
            const data = await this.api(this.urlOpcionStore(tipo), 'POST', { nombre: tipo._nuevaOpcion.trim() });
            if (!data) return;
            tipo.opciones.push({ ...data.opcion, _editando: false, _nombreTemp: '' });
            tipo._nuevaOpcion = '';
        },

        async guardarNombreOpcion(tipo, opcion) {
            if (!opcion._nombreTemp.trim()) return;
            const data = await this.api(this.urlOpcion(opcion), 'PATCH', { nombre: opcion._nombreTemp.trim() });
            if (!data) return;
            opcion.nombre    = data.opcion.nombre;
            opcion._editando = false;
        },

        async eliminarOpcion(tipo, opcion) {
            if (!confirm(`¿Eliminar la opción "${opcion.nombre}"? Se eliminarán las combinaciones que la usen.`)) return;
            const data = await this.api(this.urlOpcion(opcion), 'DELETE');
            if (!data) return;
            this.aplicarPayload(data.payload);
        },

        async subirImagenOpcion(tipo, opcion, event) {
            const file = event.target.files[0];
            if (!file) return;
            const form = new FormData();
            form.append('imagen',  file);
            form.append('nombre',  opcion.nombre);
            form.append('_method', 'PATCH');
            const data = await this.api(this.urlOpcion(opcion), 'POST', form);
            if (!data) return;
            opcion.imagen = data.opcion.imagen;
        },

        // ── SKUs ──────────────────────────────────────────────────────────────
        async generarSkus() {
            this.mensajeGenerar = '';
            const data = await this.api(config.urlSkuGenerar, 'POST');
            if (!data) return;
            if (!data.ok) { this.errorGlobal = data.message; return; }
            this.mensajeGenerar = data.creados > 0
                ? `Se generaron ${data.creados} combinaciones nuevas.`
                : 'Todas las combinaciones posibles ya existen.';
            this.aplicarPayload(data.payload);
            setTimeout(() => { this.mensajeGenerar = ''; }, 4000);
        },

        async actualizarSku(sku) {
            await this.api(this.urlSku(sku), 'PATCH', {
                precio_venta: sku._precioVenta,
                precio_lista: sku._precioLista || null,
                estado:       sku.estado,
                notas:        sku.notas,
            });
        },

        async toggleEstadoSku(sku) {
            const data = await this.api(this.urlSku(sku), 'PATCH', {
                precio_venta: sku._precioVenta,
                precio_lista: sku._precioLista || null,
                estado:       !sku.estado,
                notas:        sku.notas,
            });
            if (!data) return;
            sku.estado = data.sku.estado;
        },

        async eliminarSku(sku) {
            if (!confirm(`¿Eliminar la combinación "${sku.label}"?`)) return;
            const data = await this.api(this.urlSku(sku), 'DELETE');
            if (!data) return;
            this.skus = this.skus.filter(s => s.id !== sku.id);
        },

        async subirImagenSku(sku, event) {
            const file = event.target.files[0];
            if (!file) return;
            const form = new FormData();
            form.append('imagen', file);
            const data = await this.api(this.urlSkuImagen(sku), 'POST', form);
            if (!data) return;
            sku.imagen = data.url;
        },
    }));
});

/**
 * Redimensiona y comprime una imagen en el navegador usando canvas
 * antes de subirla al servidor. Devuelve un File nuevo en formato JPEG.
 */
async function comprimirImagen(file, maxDimension = 1200, calidad = 0.8) {
    if (!file.type.startsWith('image/') || file.type === 'image/gif') {
        return file;
    }

    const bitmap = await createImageBitmap(file);

    let { width, height } = bitmap;

    if (width > maxDimension || height > maxDimension) {
        if (width > height) {
            height = Math.round((height * maxDimension) / width);
            width  = maxDimension;
        } else {
            width  = Math.round((width * maxDimension) / height);
            height = maxDimension;
        }
    }

    const canvas = document.createElement('canvas');
    canvas.width  = width;
    canvas.height = height;

    const ctx = canvas.getContext('2d');
    ctx.drawImage(bitmap, 0, 0, width, height);
    bitmap.close?.();

    const blob = await new Promise((resolve) => {
        canvas.toBlob(resolve, 'image/jpeg', calidad);
    });

    if (!blob) return file;

    const nombreBase = file.name.replace(/\.[^/.]+$/, '');
    return new File([blob], `${nombreBase}.jpg`, { type: 'image/jpeg' });
}