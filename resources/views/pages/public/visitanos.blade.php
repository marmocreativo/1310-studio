<x-layouts::public :title="__('Visítanos')">

    {{-- Hero --}}
    <div class="py-24 px-8 max-w-[1440px] mx-auto text-center">
        <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-4">Encuéntranos</p>
        <h1 class="font-serif text-5xl md:text-6xl text-on-surface mb-6">Visítanos</h1>
        <p class="text-on-surface-variant font-light max-w-xl mx-auto leading-relaxed">
            Te esperamos en nuestra tienda en la Colonia Roma Norte.
            Un espacio diseñado para que descubras y te enamores de nuestras flores.
        </p>
    </div>

    {{-- Info + Mapa --}}
    <div class="px-8 max-w-[1440px] mx-auto pb-24">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 mb-24">

            {{-- Datos --}}
            <div class="flex flex-col gap-10">

                <div>
                    <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-4">Dirección</p>
                    <p class="font-serif text-2xl text-on-surface">Orizaba 78</p>
                    <p class="text-on-surface-variant font-light mt-1">Colonia Roma Norte, Cuauhtémoc</p>
                    <p class="text-on-surface-variant font-light">Ciudad de México, CDMX 06700</p>
                </div>

                <div>
                    <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-4">Horarios</p>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm border-b border-outline-variant py-2">
                            <span class="text-on-surface-variant font-light">Lunes – Viernes</span>
                            <span class="text-on-surface">9:00 – 19:00 hrs</span>
                        </div>
                        <div class="flex justify-between text-sm border-b border-outline-variant py-2">
                            <span class="text-on-surface-variant font-light">Sábado</span>
                            <span class="text-on-surface">9:00 – 15:00 hrs</span>
                        </div>
                        <div class="flex justify-between text-sm py-2">
                            <span class="text-on-surface-variant font-light">Domingo</span>
                            <span class="text-on-surface-variant">Cerrado</span>
                        </div>
                    </div>
                </div>

                <div>
                    <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-4">Contacto</p>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <flux:icon name="phone" class="w-4 h-4 text-outline" />
                            <span class="text-on-surface-variant font-light text-sm">+52 55 1234 5678</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <flux:icon name="envelope" class="w-4 h-4 text-outline" />
                            <span class="text-on-surface-variant font-light text-sm">hola@1310studio.mx</span>
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <a href="https://wa.me/5212345678"
                       target="_blank"
                       class="inline-block bg-primary text-on-primary px-10 py-4 text-xs tracking-[0.3em] uppercase hover:opacity-90 transition-all duration-300">
                        Escribirnos por WhatsApp
                    </a>
                </div>

            </div>

            {{-- Mapa placeholder --}}
            <div class="aspect-square lg:aspect-auto bg-surface-container-low flex items-center justify-center min-h-80">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3763.0!2d-99.16!3d19.42!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTnCsDI1JzEyLjAiTiA5OcKwMDknMzYuMCJX!5e0!3m2!1ses!2smx!4v1"
                    width="100%"
                    height="100%"
                    style="border:0; min-height: 400px;"
                    allowfullscreen=""
                    loading="lazy">
                </iframe>
            </div>

        </div>

        {{-- Galería de la tienda --}}
        <div>
            <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-10">Nuestro espacio</p>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach (range(1, 6) as $i)
                    <div class="aspect-square bg-surface-container overflow-hidden">
                        <div class="w-full h-full flex items-center justify-center text-outline-variant">
                            <flux:icon name="photo" class="w-8 h-8" />
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</x-layouts::public>