<?php

use App\Models\Area;
use App\Models\Category;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PropertyType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


function getLang()
{
    return request()->header('Accept-Language', 'en');
}


function sendResponse($status, $message, $data = null, $statusCode = 200, $additional = null)
{
    $responseData = [
        'success' => $status,
        'message' => $message,
        'data' => $data
    ];
    if (!empty($additional) && is_array($additional)) {
        $responseData = array_merge($responseData, $additional);
    }
    return response()->json($responseData, $statusCode);
}

function user()
{
    return Auth::guard('web')->user();
}
function timeFormat($time)
{
    return $time ? date(('d M, Y H:i A'), strtotime($time)) : 'N/A';
}

function dateFormat($time)
{
    return $time ? date(('d M, Y'), strtotime($time)) : 'N/A';
}
function timeFormatHuman($time)
{
    return Carbon::parse($time)->diffForHumans();
}
function creater_name($user)
{
    return $user->name ?? 'System';
}

function updater_name($user)
{
    return $user->name ?? 'Null';
}

function deleter_name($user)
{
    return $user->name ?? 'Null';
}

function isSuperAdmin()
{
    return auth()->guard('admin')->user()->role->name == 'Super Admin';
}
function slugToTitle($slug)
{
    return Str::replace('-', ' ', $slug);
}
function slug($slug)
{
    $slug = Str::replace(' ', '-', $slug);
    $slug = Str::lower($slug);
    return $slug;
}
function storage_url($urlOrArray)
{
    $image = asset('default_img/no_img.jpg');
    if (is_array($urlOrArray) || is_object($urlOrArray)) {
        $result = '';
        $count = 0;
        $itemCount = count($urlOrArray);
        foreach ($urlOrArray as $index => $url) {

            $result .= $url ? (Str::startsWith($url, 'https://') ? $url : asset('storage/' . $url)) : $image;


            if ($count === $itemCount - 1) {
                $result .= '';
            } else {
                $result .= ', ';
            }
            $count++;
        }
        return $result;
    } else {
        return $urlOrArray ? (Str::startsWith($urlOrArray, 'https://') ? $urlOrArray : asset('storage/' . $urlOrArray)) : $image;
    }
}

function auth_storage_url($url, $gender = false)
{
    $image = asset('default_img/other.png');
    if ($gender == 1) {
        $image = asset('default_img/male.jpeg');
    } elseif ($gender == 2) {
        $image = asset('default_img/female.jpg');
    }
    return $url ? asset('storage/' . $url) : $image;
}

function getSubmitterType($className)
{
    $className = basename(str_replace('\\', '/', $className));
    return trim(preg_replace('/(?<!\ )[A-Z]/', ' $0', $className));
}
function generateOrderID()
{

    $prefix = 'ORDER-';

    $microseconds = explode(' ', microtime(true))[0];

    $date = date('ymd');
    $time = date('is');

    return $prefix . $date . $time . mt_rand(10000, 99999);
}

// function generateTransactionID($length = 10)
// {
//     $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
//     $transactionID = '';
//     for ($i = 0; $i < $length; $i++) {
//         $transactionID .= $characters[random_int(0, strlen($characters) - 1)];
//     }
//     return $transactionID;
// }


function availableTimezones()
{
    $timezones = [];
    $timezoneIdentifiers = DateTimeZone::listIdentifiers();

    foreach ($timezoneIdentifiers as $timezoneIdentifier) {
        $timezone = new DateTimeZone($timezoneIdentifier);
        $offset = $timezone->getOffset(new DateTime());
        $offsetPrefix = $offset < 0 ? '-' : '+';
        $offsetFormatted = gmdate('H:i', abs($offset));

        $timezones[] = [
            'timezone' => $timezoneIdentifier,
            'name' => "(UTC $offsetPrefix$offsetFormatted) $timezoneIdentifier",
        ];
    }

    return $timezones;
}
function isImage($path)
{
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'tiff', 'ico'];
    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    return in_array($extension, $imageExtensions);
}

function isVideo($path)
{
    $videoExtensions = ['mp4', 'mov', 'avi', 'wmv', 'flv', 'webm', 'mkv', '3gp', 'ogv', 'ts', 'mpg', 'mpeg', 'vob'];
    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    return in_array($extension, $videoExtensions);
}

if (!function_exists('titleGenerator')) {
    function titleGenerator($typeID, $categoryID, $areaID)
    {
        $type = PropertyType::find($typeID);
        $category = Category::find($categoryID);
        $area = Area::find($areaID);

        $typeName = $type ? $type->name : 'غير محدد';
        $categoryName = $category ? $category->name : 'غير محدد';
        $areaName = $area ? $area->name : 'غير محدد';

        // Concatenate the names with Arabic text
        return trim($typeName . ' في ' . $categoryName . ' في ' . $areaName);
    }
}

if (!function_exists('generateOrderID')) {
    function generateOrderID()
    {
        $prefix = 'ORDER-';
        $date = date('ymds');
        $randomNumber = mt_rand(10, 99);
        $orderID = $prefix . $date . $randomNumber;
        return Order::where('order_id', $orderID)->exists() ? generateOrderID() : $orderID;
    }

}

if (!function_exists('generateTransactionID')) {
    function generateTransactionID()
    {
        $characters = 'TRNX-';
        $date = date('ymds');
        $randomNumber = mt_rand(10, 99);
        $transactionID = $characters . $date . $randomNumber;
        return Payment::where('transaction_id', $transactionID)->exists() ? generateTransactionID() : $transactionID;
    }
}
