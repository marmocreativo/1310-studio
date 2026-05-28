<?php

namespace App\Http\Controllers;

use App\Enums\TipoSlide;
use App\Models\Slide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Illuminate\Support\Str;

class AdminSlidesController extends Controller
{
    public function index()
    {
        $slides = Slide::orderBy('orden')->orderBy('id')->paginate(20);

        return view('pages.admin.slides.index', compact('slides'));
    }

    public function create()
    {
        $tipos = TipoSlide::cases();

        return view('pages.admin.slides.create', compact('tipos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo'        => 'nullable|string|max:255',
            'tipo'          => 'required|in:imagen,video,capas',
            'caption'       => 'nullable|string|max:255',
            'texto_boton'   => 'nullable|string|max:100',
            'enlace_boton'  => 'nullable|string|max:500',
            'imagen_fondo'  => 'required|image|max:10240',
            'logo'          => 'nullable|image|max:5120',
            'video'         => 'nullable|file|mimes:mp4|max:102400',
            'video_youtube' => 'nullable|url|max:500',
            'imagen_overlay'=> 'nullable|image|max:10240',
            'estado'        => 'boolean',
            'orden'         => 'integer|min:0',
        ]);

        // Imagen de fondo — cover exacto 1920x1080
        $data['imagen_fondo'] = $this->procesarImagenFondo($request->file('imagen_fondo'));

        // Logo — scaleDown sin recorte
        if ($request->hasFile('logo')) {
            $data['logo'] = $this->procesarLogo($request->file('logo'));
        }

        // Video mp4 local
        if ($request->hasFile('video')) {
            $data['video'] = $request->file('video')
                ->storeAs('slides/videos', Str::uuid() . '.mp4', 'public');
        }

        // Overlay — cover exacto 1080x1080 con transparencia (PNG→WebP)
        if ($request->hasFile('imagen_overlay')) {
            $data['imagen_overlay'] = $this->procesarOverlay($request->file('imagen_overlay'));
        }

        Slide::create($data);

        return redirect()->route('admin.slides.index')
            ->with('success', 'Slide creado correctamente.');
    }

    public function edit(Slide $slide)
    {
        $tipos = TipoSlide::cases();

        return view('pages.admin.slides.edit', compact('slide', 'tipos'));
    }

    public function update(Request $request, Slide $slide)
    {
        $data = $request->validate([
            'titulo'        => 'nullable|string|max:255',
            'tipo'          => 'required|in:imagen,video,capas',
            'caption'       => 'nullable|string|max:255',
            'texto_boton'   => 'nullable|string|max:100',
            'enlace_boton'  => 'nullable|string|max:500',
            'imagen_fondo'  => 'nullable|image|max:10240',
            'logo'          => 'nullable|image|max:5120',
            'video'         => 'nullable|file|mimes:mp4|max:102400',
            'video_youtube' => 'nullable|url|max:500',
            'imagen_overlay'=> 'nullable|image|max:10240',
            'estado'        => 'boolean',
            'orden'         => 'integer|min:0',
        ]);

        if ($request->hasFile('imagen_fondo')) {
            if ($slide->imagen_fondo) {
                Storage::disk('public')->delete($slide->imagen_fondo);
            }
            $data['imagen_fondo'] = $this->procesarImagenFondo($request->file('imagen_fondo'));
        }

        if ($request->hasFile('logo')) {
            if ($slide->logo) {
                Storage::disk('public')->delete($slide->logo);
            }
            $data['logo'] = $this->procesarLogo($request->file('logo'));
        }

        if ($request->hasFile('video')) {
            if ($slide->video) {
                Storage::disk('public')->delete($slide->video);
            }
            $data['video'] = $request->file('video')
                ->storeAs('slides/videos', Str::uuid() . '.mp4', 'public');
            // Si se sube video mp4 nuevo, limpiar youtube
            $data['video_youtube'] = null;
        }

        // Permitir limpiar video_youtube si se está cambiando a mp4
        if ($request->filled('video_youtube') && !$request->hasFile('video')) {
            if ($slide->video) {
                Storage::disk('public')->delete($slide->video);
            }
            $data['video'] = null;
        }

        if ($request->hasFile('imagen_overlay')) {
            if ($slide->imagen_overlay) {
                Storage::disk('public')->delete($slide->imagen_overlay);
            }
            $data['imagen_overlay'] = $this->procesarOverlay($request->file('imagen_overlay'));
        }

        $slide->update($data);

        return redirect()->route('admin.slides.edit', $slide)
            ->with('success', 'Slide actualizado correctamente.');
    }

    public function destroy(Slide $slide)
    {
        foreach (['imagen_fondo', 'logo', 'video', 'imagen_overlay'] as $campo) {
            if ($slide->$campo) {
                Storage::disk('public')->delete($slide->$campo);
            }
        }

        $slide->delete();

        return redirect()->route('admin.slides.index')
            ->with('success', 'Slide eliminado correctamente.');
    }

    public function orden(Request $request)
    {
        $request->validate([
            'orden'   => 'required|array',
            'orden.*' => 'exists:slides,id',
        ]);

        foreach ($request->orden as $posicion => $id) {
            Slide::where('id', $id)->update(['orden' => $posicion + 1]);
        }

        return response()->json(['ok' => true]);
    }

    // ─── Procesado de imágenes ────────────────────

    // Fondo: cover exacto 1920x1080, webp 85
    private function procesarImagenFondo($file): string
    {
        $manager = new ImageManager(new Driver());
        $image   = $manager->decode($file);
        $image->cover(1920, 1080);

        $filename = 'slides/fondos/' . Str::uuid() . '.webp';
        $encoded  = $image->encode(new WebpEncoder(quality: 85));
        Storage::disk('public')->put($filename, $encoded);

        return $filename;
    }

    // Logo: scaleDown máx 600px ancho, sin recorte
    private function procesarLogo($file): string
    {
        $manager = new ImageManager(new Driver());
        $image   = $manager->decode($file);
        $image->scaleDown(width: 600);

        $filename = 'slides/logos/' . Str::uuid() . '.webp';
        $encoded  = $image->encode(new WebpEncoder(quality: 90));
        Storage::disk('public')->put($filename, $encoded);

        return $filename;
    }

    // Overlay: cover exacto 1080x1080, webp con transparencia 90
    private function procesarOverlay($file): string
    {
        $manager = new ImageManager(new Driver());
        $image   = $manager->decode($file);
        $image->cover(1080, 1080);

        $filename = 'slides/overlays/' . Str::uuid() . '.webp';
        $encoded  = $image->encode(new WebpEncoder(quality: 90));
        Storage::disk('public')->put($filename, $encoded);

        return $filename;
    }
}