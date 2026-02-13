<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CaptchaController extends Controller
{
    /**
     * Genera una imagen captcha (letras y números), guarda el valor en sesión y devuelve la imagen.
     */
    public function image(Request $request)
    {
        $code = strtoupper(Str::random(6));
        Session::put('captcha', $code);

        if (function_exists('imagecreatetruecolor')) {
            return $this->outputGdImage($code);
        }

        return response()->json(['code' => $code], 200, ['Content-Type' => 'application/json']);
    }

    private function outputGdImage(string $code): \Symfony\Component\HttpFoundation\Response
    {
        $w = 160;
        $h = 50;
        $img = @imagecreatetruecolor($w, $h);
        if (! $img) {
            return response('', 500);
        }
        $bg = imagecolorallocate($img, 248, 249, 250);
        $text = imagecolorallocate($img, 44, 44, 44);
        $line = imagecolorallocate($img, 200, 200, 200);
        imagefill($img, 0, 0, $bg);
        for ($i = 0; $i < 4; $i++) {
            imageline($img, rand(0, $w), rand(0, $h), rand(0, $w), rand(0, $h), $line);
        }
        $x = 20;
        for ($i = 0; $i < strlen($code); $i++) {
            imagestring($img, 5, $x, 15, $code[$i], $text);
            $x += 22;
        }
        ob_start();
        imagepng($img);
        $png = ob_get_clean();
        imagedestroy($img);

        return response($png, 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }
}
