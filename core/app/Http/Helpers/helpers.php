<?php

use Carbon\Carbon;
use App\Lib\Captcha;
use App\Notify\Notify;
use App\Lib\ClientInfo;
use App\Lib\CurlRequest;
use App\Lib\FileManager;
use App\Models\Frontend;
use App\Models\Parcelle;
use App\Constants\Status;
use App\Models\Extension;
use App\Models\Producteur;
use Illuminate\Support\Str;
use App\Models\LivraisonInfo;
use App\Models\GeneralSetting;
use App\Lib\GoogleAuthenticator;
use App\Models\ChiffreAffairePartenaire;
use App\Models\Connaissement;
use App\Models\ConnaissementProduit;
use App\Models\CoopChiffreAffaire;
use App\Models\Inspection;
use App\Models\LivraisonProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Producteur_certification;

function systemDetails()
{
    $system['name']          = 'FieldConnect';
    $system['version']       = '3.0';
    $system['build_version'] = '4.3.6';
    return $system;
}

function slug($string)
{
    return Illuminate\Support\Str::slug($string);
}

function verificationCode($length)
{
    if ($length == 0) return 0;
    $min = pow(10, $length - 1);
    $max = (int) ($min - 1) . '9';
    return random_int($min, $max);
}

function getNumber($length = 8)
{
    $characters = '1234567890';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function activeTemplate($asset = false)
{
    $general = gs();
    $template = $general->active_template;
    if ($asset) return 'assets/templates/' . $template . '/';
    return 'templates.' . $template . '.';
}

function activeTemplateName()
{
    $general = gs();
    $template = $general->active_template;
    return $template;
}

function loadReCaptcha()
{
    return Captcha::reCaptcha();
}

function loadCustomCaptcha($width = '100%', $height = 46, $bgColor = '#003')
{
    return Captcha::customCaptcha($width, $height, $bgColor);
}

function verifyCaptcha()
{
    return Captcha::verify();
}

function loadExtension($key)
{
    $extension = Extension::where('act', $key)->where('status', Status::ENABLE)->first();
    return $extension ? $extension->generateScript() : '';
}

function getTrx($length = 12)
{
    $characters = 'ABCDEFGHJKMNOPQRSTUVWXYZ123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function getAmount($amount, $length = 0)
{
    $amount = round($amount, $length);
    $separator = ' ';
    $decimal = 0;
    $printAmount = number_format($amount, $decimal, '', $separator);
    return $printAmount;
}

function enleveaccents($chaine)
        { 
            $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð',
            'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã',
            'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ',
            'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ',
            'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę',
            'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī',
            'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ',
            'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ',
            'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 
            'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 
            'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ',
            'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');

$b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O',
            'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c',
            'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u',
            'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D',
            'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g',
            'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K',
            'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o',
            'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S',
            's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W',
            'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i',
            'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
return mb_strtoupper(str_replace($a, $b, $chaine)); 
        }

function showAmount($amount, $decimal = 0, $separate = true, $exceptZeros = false)
{
    $separator = '';
    if ($separate) {
        $separator = ' ';
    }
    $printAmount = number_format($amount, $decimal, '.', $separator);
    if ($exceptZeros) {
        $exp = explode('.', $printAmount);
        if ($exp[1] * 1 == 0) {
            $printAmount = $exp[0];
        } else {
            $printAmount = rtrim($printAmount, '0');
        }
    }
    return $printAmount;
}


function removeElement($array, $value)
{
    return array_diff($array, (is_array($value) ? $value : array($value)));
}

function cryptoQR($wallet)
{
    return "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$wallet&choe=UTF-8";
}


function keyToTitle($text)
{
    return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
}


function titleToKey($text)
{
    return strtolower(str_replace(' ', '_', $text));
}


function strLimit($title = null, $length = 10)
{
    return Str::limit($title, $length);
}


function getIpInfo()
{
    $ipInfo = ClientInfo::ipInfo();
    return $ipInfo;
}


function osBrowser()
{
    $osBrowser = ClientInfo::osBrowser();
    return $osBrowser;
}


function getTemplates()
{
    $param['purchasecode'] = env("PURCHASECODE");
    $param['website'] = @$_SERVER['HTTP_HOST'] . @$_SERVER['REQUEST_URI'] . ' - ' . env("APP_URL");
    $url = 'https://sicadevd.com/updates/templates/' . systemDetails()['name'];
    $response = CurlRequest::curlPostContent($url, $param);
    if ($response) {
        return $response;
    } else {
        return null;
    }
}


function getPageSections($arr = false)
{
    $jsonUrl = resource_path('views/') . str_replace('.', '/', activeTemplate()) . 'sections.json';
    $sections = json_decode(file_get_contents($jsonUrl));
    if ($arr) {
        $sections = json_decode(file_get_contents($jsonUrl), true);
        ksort($sections);
    }
    return $sections;
}

function loadFbComment()
{
    $comment = Extension::where('act', 'fb-comment')->where('status', 1)->first();
    return  $comment ? $comment->generateScript() : '';
}



function getImage($image, $size = null)
{
    $clean = '';
    if (file_exists($image) && is_file($image)) {
        return asset($image) . $clean;
    }
    if ($size) {
        return route('placeholder.image', $size);
    }
    return asset('assets/images/default.png');
}


function notify($user, $templateName, $shortCodes = null, $sendVia = null, $createLog = true)
{
    $general = gs();
    $globalShortCodes = [
        'site_name' => $general->site_name,
        'site_currency' => $general->cur_text,
        'currency_symbol' => $general->cur_sym,
    ];

    if (gettype($user) == 'array') {
        $user = (object) $user;
    }

    $shortCodes = array_merge($shortCodes ?? [], $globalShortCodes);

    $notify = new Notify($sendVia);
    $notify->templateName = $templateName;
    $notify->shortCodes = $shortCodes;
    $notify->user = $user;
    $notify->createLog = $createLog;
    $notify->userColumn = isset($user->id) ? $user->getForeignKey() : 'user_id';
    $notify->send();
}

function getPaginate($paginate = 20)
{
    return $paginate;
}

function paginateLinks($data)
{
    return $data->appends(request()->all())->links();
}


function menuActive($routeName, $type = null, $param = null)
{
    if ($type == 3) $class = 'side-menu--open';
    elseif ($type == 2) $class = 'sidebar-submenu__open';
    else $class = 'active';

    if (is_array($routeName)) {
        foreach ($routeName as $key => $value) {
            if (request()->routeIs($value)) return $class;
        }
    } elseif (request()->routeIs($routeName)) {
        if ($param) {
            $routeParam = array_values(@request()->route()->parameters ?? []);
            if (strtolower(@$routeParam[0]) == strtolower($param)) return $class;
            else return;
        }
        return $class;
    }
}


function fileUploader($file, $location, $size = null, $old = null, $thumb = null)
{
    $fileManager = new FileManager($file);
    $fileManager->path = $location;
    $fileManager->size = $size;
    $fileManager->old = $old;
    $fileManager->thumb = $thumb;
    $fileManager->upload();
    return $fileManager->filename;
}

function fileManager()
{
    return new FileManager();
}

function getFilePath($key)
{
    return fileManager()->$key()->path;
}

function getFileSize($key)
{
    return fileManager()->$key()->size;
}

function getFileExt($key)
{
    return fileManager()->$key()->extensions;
}

function diffForHumans($date)
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->diffForHumans();
}


function showDateTime($date, $format = 'Y-m-d h:i A')
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->translatedFormat($format);
}


function getContent($dataKeys, $singleQuery = false, $limit = null, $orderById = false)
{
    if ($singleQuery) {
        $content = Frontend::where('data_keys', $dataKeys)->orderBy('id', 'desc')->first();
    } else {
        $article = Frontend::query();
        $article->when($limit != null, function ($q) use ($limit) {
            return $q->limit($limit);
        });
        if ($orderById) {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id')->get();
        } else {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id', 'desc')->get();
        }
    }
    return $content;
}



function verifyG2fa($user, $code, $secret = null)
{
    $authenticator = new GoogleAuthenticator();
    if (!$secret) {
        $secret = $user->tsc;
    }
    $oneCode = $authenticator->getCode($secret);
    $userCode = $code;
    if ($oneCode == $userCode) {
        $user->tv = 1;
        $user->save();
        return true;
    } else {
        return false;
    }
}


function urlPath($routeName, $routeParam = null)
{
    if ($routeParam == null) {
        $url = route($routeName);
    } else {
        $url = route($routeName, $routeParam);
    }
    $basePath = route('home');
    $path = str_replace($basePath, '', $url);
    return $path;
}


function showMobileNumber($number)
{
    $length = strlen($number);
    return substr_replace($number, '***', 2, $length - 4);
}

function showEmailAddress($email)
{
    $endPosition = strpos($email, '@') - 1;
    return substr_replace($email, '***', 1, $endPosition);
}


function getRealIP()
{
    $ip = $_SERVER["REMOTE_ADDR"];
    //Deep detect ip
    if (filter_var(@$_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED'];
    }
    if (filter_var(@$_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    if ($ip == '::1') {
        $ip = '127.0.0.1';
    }

    return $ip;
}


function appendQuery($key, $value)
{
    return request()->fullUrlWithQuery([$key => $value]);
}

function dateSort($a, $b)
{
    return strtotime($a) - strtotime($b);
}

function dateSorting($arr)
{
    usort($arr, "dateSort");
    return $arr;
}

function getproducteur($date = null)
{
    $date = explode('-', $date);

    $startDate = Carbon::parse(trim($date[0]))->format('Y-m-d');
    $endDate = @$date[1] ? Carbon::parse(trim(@$date[1]))->format('Y-m-d') : $startDate;

    $producteur = Producteur::joinRelationship('programme')
        ->joinRelationship('localite.section')
        ->where('cooperative_id', auth()->user()->cooperative_id)
        ->select('producteurs.*', 'localites.nom as nomLocalite', 'programmes.libelle as nomProgramme')
        ->whereDate('producteurs.created_at', '>=', $startDate)
        ->whereDate('producteurs.created_at', '<=', $endDate)
        ->get();

    return $producteur;
}

function getProducteurProgramme($date, $certif, $programme)
{
    $date = explode('-', $date);

    $startDate = Carbon::parse(trim($date[0]))->format('Y-m-d');
    $endDate = @$date[1] ? Carbon::parse(trim(@$date[1]))->format('Y-m-d') : $startDate;

    $producteur = Producteur_certification::joinRelationship('producteur.programme')
        ->joinRelationship('producteur.localite.section')
        ->where('cooperative_id', auth()->user()->cooperative_id)
        ->where('producteur_certifications.certification', $certif)
        ->where('programme_id', $programme)
        ->select('producteurs.*', 'localites.nom as nomLocalite', 'programmes.libelle as nomProgramme', 'producteur_certifications.certification')
        ->whereDate('producteurs.created_at', '>=', $startDate)
        ->whereDate('producteurs.created_at', '<=', $endDate)
        ->get();

    return $producteur;
}
function getproducteurOrdinaire($date = null)
{
    $date = explode('-', $date);

    $startDate = Carbon::parse(trim($date[0]))->format('Y-m-d');
    $endDate = @$date[1] ? Carbon::parse(trim(@$date[1]))->format('Y-m-d') : $startDate;

    $producteur = Producteur::joinRelationship('programme')
        ->joinRelationship('localite.section')
        ->where('cooperative_id', auth()->user()->cooperative_id)
        ->where('Statut', 'Candidat')
        ->select('producteurs.*', 'localites.nom as nomLocalite', 'programmes.libelle as nomProgramme')
        ->whereDate('producteurs.created_at', '>=', $startDate)
        ->whereDate('producteurs.created_at', '<=', $endDate)
        ->get();

    return $producteur;
}

function getparcelle($date = null)
{
    $date = explode('-', $date);

    $startDate = Carbon::parse(trim($date[0]))->format('Y-m-d');
    $endDate = @$date[1] ? Carbon::parse(trim(@$date[1]))->format('Y-m-d') : $startDate;

    $parcelle = Parcelle::joinRelationship('producteur.localite.section')
        ->where('cooperative_id', auth()->user()->cooperative_id)
        ->select('parcelles.*')
        ->whereDate('parcelles.created_at', '>=', $startDate)
        ->whereDate('parcelles.created_at', '<=', $endDate)
        ->sum('superficie');

    return $parcelle;
}

function getproduction($date = null)
{
    $date = explode('-', $date);

    $startDate = Carbon::parse(trim($date[0]))->format('Y-m-d');
    $endDate = @$date[1] ? Carbon::parse(trim(@$date[1]))->format('Y-m-d') : $startDate;

    $quantite = LivraisonProduct::joinRelationship('livraisonInfo')->where('sender_cooperative_id', auth()->user()->cooperative_id)
        ->whereDate('livraison_products.created_at', '>=', $startDate)
        ->whereDate('livraison_products.created_at', '<=', $endDate)
        ->sum('qty');

    return $quantite;
}
function getChiffreDaffaireProduction($date = null)
{
    $date = explode('-', $date);

    $startDate = Carbon::parse(trim($date[0]))->format('Y');
    $endDate = @$date[1] ? Carbon::parse(trim(@$date[1]))->format('Y') : $startDate;

    $quantite = LivraisonProduct::joinRelationship('livraisonInfo')->where('sender_cooperative_id', auth()->user()->cooperative_id)
        ->whereDate('livraison_products.created_at', '>=', $startDate)
        ->whereDate('livraison_products.created_at', '<=', $endDate)
        ->sum('qty');

    return $quantite;
}
function getautreProduction($date)
{
    $date = explode('-', $date);

    $startDate = Carbon::parse(trim($date[0]))->format('Y-m-d');
    $endDate = @$date[1] ? Carbon::parse(trim(@$date[1]))->format('Y-m-d') : $startDate;
    $quantite = Inspection::joinRelationship('producteur.localite.section')
        ->where('cooperative_id', auth()->user()->cooperative_id)
        ->whereDate('inspections.date_evaluation', '>=', $startDate)
        ->whereDate('inspections.date_evaluation', '<=', $endDate)
        ->sum('production');
    return $quantite;
}
function getChiffreCcb($date)
{
    $date = explode('-', $date);

    $startDate = Carbon::parse(trim($date[0]))->format('Y');

    $endDate = @$date[1] ? Carbon::parse(trim(@$date[1]))->format('Y') : $startDate;

    $chiffre = optional(CoopChiffreAffaire::where('cooperative_id', auth()->user()->cooperative_id)
        ->where('annee', '>=', $startDate)
        ->where('annee', '<=', $endDate)
        ->select('montant')->first())->montant ?? 0;
    return $chiffre;
}
function getChiffreAutrePartenaire($date){
    $date = explode('-', $date);

    $startDate = Carbon::parse(trim($date[0]))->format('Y');

    $endDate = @$date[1] ? Carbon::parse(trim(@$date[1]))->format('Y') : $startDate;

    $chiffre = optional(ChiffreAffairePartenaire::where('cooperative_id', auth()->user()->cooperative_id)
        ->where('annee', '>=', $startDate)
        ->where('annee', '<=', $endDate)
        ->select('montant')->first())->montant ?? 0;
    return $chiffre;
}
function getproductionCertifie($date, $certif)
{
    $date = explode('-', $date);

    $startDate = Carbon::parse(trim($date[0]))->format('Y-m-d');
    $endDate = @$date[1] ? Carbon::parse(trim(@$date[1]))->format('Y-m-d') : $startDate;

    $quantite = LivraisonProduct::joinRelationship('livraisonInfo')
        ->where('sender_cooperative_id', auth()->user()->cooperative_id)
        ->where('type_produit', 'Certifie')
        ->where('certificat', $certif)
        ->whereDate('livraison_products.created_at', '>=', $startDate)
        ->whereDate('livraison_products.created_at', '<=', $endDate);
    $total = $quantite->sum('qty_sortant') + $quantite->sum('qty');

    return $total;
}

function getproductionOrdinaire($date)
{
    $date = explode('-', $date);

    $startDate = Carbon::parse(trim($date[0]))->format('Y-m-d');
    $endDate = @$date[1] ? Carbon::parse(trim(@$date[1]))->format('Y-m-d') : $startDate;

    $quantite = LivraisonProduct::joinRelationship('livraisonInfo')
        ->where('sender_cooperative_id', auth()->user()->cooperative_id)
        ->where('type_produit', 'Ordinaire')
        ->whereDate('livraison_products.created_at', '>=', $startDate)
        ->whereDate('livraison_products.created_at', '<=', $endDate);
    $total = $quantite->sum('qty_sortant') + $quantite->sum('qty');

    return $total;
}

function getproductionProgramme($date)
{
    $date = explode('-', $date);

    $startDate = Carbon::parse(trim($date[0]))->format('Y-m-d');
    $endDate = @$date[1] ? Carbon::parse(trim(@$date[1]))->format('Y-m-d') : $startDate;

    $quantite = LivraisonProduct::joinRelationship('livraisonInfo')
        ->joinRelationship('parcelle.producteur.programme')
        ->where('sender_cooperative_id', auth()->user()->cooperative_id)
        ->where('programmes.libelle', 'Bandama')
        ->whereDate('livraison_products.created_at', '>=', $startDate)
        ->whereDate('livraison_products.created_at', '<=', $endDate);
    $total = $quantite->sum('qty_sortant') + $quantite->sum('qty');

    return $total;
}

function getvente($date = null)
{
    $date = explode('-', $date);

    $startDate = Carbon::parse(trim($date[0]))->format('Y-m-d');
    $endDate = @$date[1] ? Carbon::parse(trim(@$date[1]))->format('Y-m-d') : $startDate;

    $quantite = Connaissement::where('cooperative_id', auth()->user()->cooperative_id)
        ->where('status', 2)
        ->whereDate('created_at', '>=', $startDate)
        ->whereDate('created_at', '<=', $endDate)
        ->sum('quantite_confirme');

    return $quantite;
}

function getventeCertifie($date, $certif)
{
    $date = explode('-', $date);

    $startDate = Carbon::parse(trim($date[0]))->format('Y-m-d');
    $endDate = @$date[1] ? Carbon::parse(trim(@$date[1]))->format('Y-m-d') : $startDate;

    $quantite = ConnaissementProduit::joinRelationship('connaissement')
        ->where('cooperative_id', auth()->user()->cooperative_id)
        ->where('connaissement_produits.certificat', $certif)
        ->where('connaissement_produits.type_produit', 'Certifie')
        ->where('connaissements.status', 2)
        ->whereDate('connaissements.created_at', '>=', $startDate)
        ->whereDate('connaissements.created_at', '<=', $endDate)
        ->sum('quantite');

    return $quantite;
}

function getventeOrdinaire($date)
{
    $date = explode('-', $date);

    $startDate = Carbon::parse(trim($date[0]))->format('Y-m-d');
    $endDate = @$date[1] ? Carbon::parse(trim(@$date[1]))->format('Y-m-d') : $startDate;

    $quantite = ConnaissementProduit::joinRelationship('connaissement')
        ->where('cooperative_id', auth()->user()->cooperative_id)
        ->where('connaissement_produits.type_produit', 'Ordinaire')
        ->where('status', 2)
        ->whereDate('connaissement_produits.created_at', '>=', $startDate)
        ->whereDate('connaissement_produits.created_at', '<=', $endDate)
        ->sum('quantite');

    return $quantite;
}
function getventeProgramme($date)
{
    $date = explode('-', $date);

    $startDate = Carbon::parse(trim($date[0]))->format('Y-m-d');
    $endDate = @$date[1] ? Carbon::parse(trim(@$date[1]))->format('Y-m-d') : $startDate;

    $quantite = ConnaissementProduit::joinRelationship('connaissement')
        ->joinRelationship('producteur.programme')
        ->where('cooperative_id', auth()->user()->cooperative_id)
        ->where('programmes.libelle', 'Bandama')
        ->where('connaissements.status', 2)
        ->whereDate('connaissement_produits.created_at', '>=', $startDate)
        ->whereDate('connaissement_produits.created_at', '<=', $endDate)
        ->sum('quantite');

    return $quantite;
}
function gs()
{
    $general = Cache::get('GeneralSetting');
    if (!$general) {
        $general = GeneralSetting::first();
        Cache::put('GeneralSetting', $general);
    }
    return $general;
}
