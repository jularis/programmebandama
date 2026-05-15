<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Models\AdminNotification;
use App\Models\Connaissement;
use App\Models\CourierInfo;
use App\Models\Frontend;
use App\Models\Language;
use App\Models\LivraisonInfo;
use App\Models\Page;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class SiteController extends Controller
{
    public function index()
    { 
        return redirect('/login');
    }
 
    public function politique()
    { 
        return view('politique');
    }
    public function orderTracking(Request $request)
    {
        $pageTitle   = "Order Tracking";
        return view('templates.basic.order_tracking', compact('pageTitle'));
    }
    public function findOrder(Request $request)
    {
        $request->validate([
            'order_number' => 'required|exists:connaissements,numeroCU',
        ], [
            'order_number.exists' => "Invalid Order Number"
        ]);
        $pageTitle   = "Order Tracking";
        $orderNumber = Connaissement::where('numeroCU', $request->order_number)->first();

        return view('templates.basic.order_tracking', compact('pageTitle', 'orderNumber'));
    }
    public function placeholderImage($size = null)
    {
        $imgWidth  = explode('x', $size)[0];
        $imgHeight = explode('x', $size)[1];
        $text      = $imgWidth . '×' . $imgHeight;
        $fontFile = realpath('assets/font/RobotoMono-Regular.ttf');
        $fontSize  = round(($imgWidth - 50) / 8);
        if ($fontSize <= 9) {
            $fontSize = 9;
        }
        if ($imgHeight < 100 && $fontSize > 30) {
            $fontSize = 30;
        }

        $image     = imagecreatetruecolor($imgWidth, $imgHeight);
        $colorFill = imagecolorallocate($image, 100, 100, 100);
        $bgFill    = imagecolorallocate($image, 175, 175, 175);
        imagefill($image, 0, 0, $bgFill);
        $textBox    = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textWidth  = abs($textBox[4] - $textBox[0]);
        $textHeight = abs($textBox[5] - $textBox[1]);
        $textX      = ($imgWidth - $textWidth) / 2;
        $textY      = ($imgHeight + $textHeight) / 2;
        header('Content-Type: image/jpeg');
        imagettftext($image, $fontSize, 0, $textX, $textY, $colorFill, $fontFile, $text);
        imagejpeg($image);
        imagedestroy($image);
    }

}
