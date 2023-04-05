<?php

namespace Local\CMS\Traits;

// Models

use App\Events\BroadcastNotification;
use App\Models\Booking;
use App\Models\Cart;
use App\Models\User;
use App\Models\Order;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Product;
use App\Models\TitTarTour;
// API Resource
use App\Models\Voucher;
use App\Models\Platform;
// Laravel
use Monolog\Logger as Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Monolog\Handler\StreamHandler;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Log as DefaultLog;
use Illuminate\Support\Facades\DB;

// Package
use Local\CMS\Http\Resources\Response as JsonResponse;
use Exception;
use Illuminate\Support\Facades\Redis;
use Local\CMS\Models\File;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Nexmo\Client as NexmoClient;
use Nexmo\Client\Credentials\Basic as NexmoBasic;

trait Helpers
{

    /**
     * ==============================================================================
     * Private Function
     * ==============================================================================
     */

    private function __isProduction()
    {
        return config('app.env') == "production";
    }

    private function __isDebug()
    {
        return config('app.debug') && !$this->__isProduction();
    }

    private function __api(array $debug = null, bool $status, $message, int $code = 0, $data = null): array
    {
        $response = [
            "status" => $status,
            "message" => $message,
            "code" => $code,
            "data" => $data
        ];

        if ($this->__isDebug()) {
            $response['debug'] = $debug;
        }

        return $response;
    }

    private function __ajaxDatatable($data, int $total, int $draw = 1, $filteredTotal = null, $additionalParam = [])
    {
        $response = [
            "draw" => $draw,
            "recordsTotal" => $total,
            "recordsFiltered" =>  $filteredTotal !== null && is_int($filteredTotal) ? $filteredTotal : $total,
            "data" => $data,
        ];



        return array_merge($response,$additionalParam);
    }

    private function __log($type = "__normalLog")
    {

        $date = $this->__currentTime('Y-m-d');

        $log = null;

        switch ($type) {
            case '__transactionLog':
                $log = new Log($type, [
                    new StreamHandler(storage_path("logs/transactions/transaction-" . php_sapi_name() . "-$date.log"), Log::INFO)
                ]);
                break;
            case '__cronLog':
                $log = new Log($type, [
                    new StreamHandler(storage_path("logs/cron/cron-" . php_sapi_name() . "-$date.log"), Log::INFO)
                ]);
                break;
            case '__errorLog':
                $log = new Log($type, [
                    new StreamHandler(storage_path("logs/errors/error-" . php_sapi_name() . "-$date.log"), Log::ERROR)
                ]);
                break;
            default:
                $log = new Log($type, [
                    new StreamHandler(storage_path("logs/laravel-" . php_sapi_name() . "-$date.log"), Log::INFO)
                ]);
                break;
        }

        return $log;
    }

    private function __directoryExist($directory, $target)
    {
        $all = Storage::allDirectories($directory);
        return in_array($target, $all);
    }

    /**
     * ==============================================================================
     * End Of Private Function
     * ==============================================================================
     */

    /**
     * ==============================================================================
     * Public Function
     * ==============================================================================
     */

    function __currentDomain($prefix = "//")
    {
        return $prefix . request()->getHost();
    }

    function __getGuard()
    {
        // $guard = config('auth.defaults.api_guard');

        // $guards = array_keys(config('auth.guards')) ?? [];

        // foreach ($guards as $name) {
        //     if(Auth::guard($name)->user()) {
        //         $guard = $name;
        //         break;
        //     }
        // }

        $domain = request()->getHttpHost();

        switch ($domain) {
            case config('app.api_url'):
            case config('app.web_app_url'):
                $guard = "api_user";
                break;
            case config('app.driver_url'):
                $guard = "api_driver";
                break;
            default:
                $guard = config('auth.defaults.api_guard');
                break;
        }

        return $guard;
    }

    function __currentUser($guard = null)
    {
        $guard = $guard ?: $this->__getGuard();
        return request()->user() ?? Auth::guard($guard)->user();
    }

    function __hasCart($guard = null)
    {
        return $this->__currentUser($guard)->cart()->exists();
    }

    function __checkPassword($password, $guard = null)
    {
        return Hash::check($password, $this->__currentUser($guard)->password ?? null);
    }

    function __findCountry($iso)
    {
        return Country::where('iso', $iso)->first();
    }

    function __requestFilled($name, $default = null)
    {
        $request = request();

        return ($request->filled($name) && isset($name)) ? $request->get($name) : $default;
    }

    function __validationFail($validator, array $debug = null)
    {
        $messages = $validator;
        if (is_a($validator, Validator::class)) {
            $debug = $validator->getMessageBag()->toArray();
            $messages = $debug[array_keys($debug)[0]][0] ?? $debug;
        }
        return new JsonResponse($this->__api($debug, false, $messages, 0));
    }

    function __getFormattedAttributes(array $rules)
    {
        return array_combine(array_keys($rules), array_map('ucwords', str_replace('.*.', '\'s ', str_replace('_', ' ', array_keys($rules)))));
    }

    function __getFormattedMessages(array $custom = null, bool $replace = false)
    {
        $attributes = [
            "required" => ":attribute is required.",
            "exists" => ":attribute is invalid.",
            "required_with" => ":attribute is required when :values is not empty.",
            "max" => ":attribute field's maximum sizes or values is :max",
            "between" => ":attribute field's value is not between :min - :max.",
        ];

        if (!empty($custom)) {
            if ($replace) {
                $attributes = $custom;
            } else {
                $attributes = array_merge($attributes, $custom);
            }
        }

        return $attributes;
    }

    static function __generateUniqueSlug($name, $className)
    {
        $name = mb_ereg_replace('/', '', $name);
        $slug = mb_strtolower(trim(mb_ereg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));

        $unique = false;
        $counter = 0;
        while (!$unique) {
            $checkSlug = $className::whereSlug($slug)->first();
            if (empty($checkSlug)) {
                $unique = true;
            } else {
                $slug = mb_strtolower(trim(mb_ereg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-')) . "-" . $counter;
            }
            $counter++;
        }

        return $slug;
    }

    function __isApi($type = null)
    {
        $host = request()->getHost();
        return $type
            ? $host == config("app.{$type}_url")
            : ($host == config('app.api_url') || $host == config('app.driver_url'));
    }

    function __isWeb()
    {
        $host = request()->getHost();
        return $host == config('appurl') || $host == config('app.web_app_url');
    }

    function __isAdmin()
    {
        return request()->getHost() == config('app.admin_url');
    }

    function __isEndpoint()
    {
        return request()->getHost() == config('app.endpoint_url');
    }

    function __expectsJson()
    {
        return request()->expectsJson() || $this->__isApi() || $this->__isEndpoint() || request()->is('api/*');
    }

    function __trans(Request $request, $key, $replace = [])
    {
        $lang = $request->header("lang");

        return __($key, $replace, $lang);
    }

    function __apiSuccess($message, $data = null, int $code = 200, array $debug = null)
    {
        return new JsonResponse($this->__api($debug, true, $message, $code, $data));
    }

    function __apiFailed($message, $data = null, int $code = 500, array $debug = null)
    {
        return response()->json(new JsonResponse($this->__api($debug, false, $message, $code, $data)),$code);
    }

    function __apiNotFound($message, $data = null, int $code = 404, $debug = null)
    {
        return $this->__apiFailed($message, $data, $code, $debug);
    }

    function __apiMethodNotAllowed($message, $data = null, int $code = 405, $debug = null)
    {
        return $this->__apiFailed($message, $data, $code, $debug);
    }

    function __apiNotAuth($message, $data = null, int $code = 401, $returnArray = true)
    {
        if ($returnArray) {
            return new HttpResponse(
                $this->__toStr($this->__api(null, false, $message, $code, $data)),
                $code,
                ["Content-Type" => "application/json"]
            );
        }
        return $this->__apiFailed($message, $data, $code, null);
    }

    function __apiDataTable($data, int $total, int $draw = 1, $filteredTotal = null,$additionalParam = [])
    {
        return new JsonResponse($this->__ajaxDatatable($data, $total, $draw, $filteredTotal,$additionalParam));
    }

    function __currentTime($format = null)
    {
        return !empty($format) ? Carbon::now()->format($format) : Carbon::now();
    }

    function __formatDateTime($date, $format = "Y-m-d h:i:s")
    {
        return $date ? Carbon::parse($date)->format($format) : null;
    }

    function __toStr($data)
    {
        if (is_object($data) || is_array($data)) {
            $data = json_encode((array) $data, true);
        }
        return $data;
    }

    function __storeImage($file, $type = "file", $fullReso = false)
    {
        try {
            $file_name = mb_strtolower($type) . "_" . date("Ymdhis") . rand(11, 99);
            $ofile = Image::make($file);
            $ofile->orientate();
            $extension = str_replace("image/", '', $ofile->mime());
            $directory = str_replace('storage', 'public', File::PATH_TO_STORAGE);
            if (!$this->__directoryExist(storage_path(), $directory)) {
                Storage::makeDirectory($directory);
            }

            $ofile->save(public_path(File::PATH_TO_STORAGE . $file_name . '.' . $extension), 100);

            if ($fullReso) {
                $this->__moveFile($file, $type, $file_name);
                $file->storeAs(str_replace('storage', 'public', File::PATH_TO_STORAGE), $file_name . '_high_resolution.' . $extension);
                $hfile = $file_name . '_high_resolution';

                $lfile = Image::make($file);
                $lfile->orientate();
                $lfile->crop($lfile->height(), $lfile->height());
                $lfile->save(public_path(File::PATH_TO_STORAGE . $file_name . '_low_resolution.' . $extension), 50);
            }

            return $fullReso ? (object)[
                "original" => $ofile,
                "low_reso" => $lfile,
                "high_reso" => $hfile, // It's not an image class, just a `File Name` string
            ] : $ofile;
        } catch (\Throwable $th) {
            DefaultLog::info('Failed to upload: ' . var_export($th->getMessage() ?? '', true));
            return false;
        }
    }

    function __moveFile($file, $type = "file", $file_name = null,$original_path = false)
    {
        try {
            $name = $file->getClientOriginalName();

            $file_name = $file_name ?: mb_strtolower(pathinfo($name, PATHINFO_FILENAME)) . "_" . date("Ymdhis") . rand(11, 99);
            $extension = pathinfo($name, PATHINFO_EXTENSION);
            $file->storeAs("public/$type", $file_name . '.' . $extension);

            if($original_path){
                return "storage/public/$type/$file_name.$extension";
            }

            return public_path("storage/$type/$file_name.$extension");
        } catch (\Throwable $th) {
            DefaultLog::info('Failed to upload: ' . var_export($th->getMessage() ?? '', true));
            return;
        }
    }

    function __getFileOriginalName($file)
    {
        return $file->getClientOriginalName();
    }

    function __timeLeftInSeconds(Carbon $target, $readable = false)
    {
        // http://oldblog.codebyjeff.com/blog/2016/04/time-left-strings-with-carbon-and-laravel-string-helper
        $now = Carbon::now();

        if ($now->diffInSeconds($target) > 0) {
            return $readable ?
                $now->diffInSeconds($target) . Str::plural(' seconds', $now->diffInSeconds($target)) . ' left'
                : $now->diffInSeconds($target);
        }
    }

    function __isEmpty($value, $default = null)
    {

        if (is_array($value)) {
            return sizeof($value) > 0 ? $value : $default;
        }

        return empty($value) ? $default : $value;
    }


    /**
     * ==============================================================================
     * End Of Public Function
     * ==============================================================================
     */

    /**
     * ==============================================================================
     * Logging
     * ==============================================================================
     */

    function __transactionLog($message, $fileName = null, $fileLine = null)
    {
        $this->__log('__transactionLog')
            ->info($fileName . "($fileLine): " . $message);
    }

    function __transactionLogData($data, $fileName = null, $fileLine = null)
    {
        $this->__log('__transactionLog')
            ->alert($fileName . "($fileLine):", $data);
    }

    function __errorLog($data)
    {
        $this->__log('__errorLog')
            ->error(PHP_EOL . PHP_EOL  . $data);
    }

    function __normalLog($data)
    {
        $this->__log()
            ->info($data);
    }

    function __activityLog($type, $causedBy, $target_model, array $properties = [], string $action = "")
    {
        activity($type)
            ->causedBy($causedBy)
            ->performedOn($target_model)
            ->withProperties($properties)
            ->log($action);
    }

    /**
     * ==============================================================================
     * End Of Logging
     * ==============================================================================
     */

    /**
     * ==============================================================================
     * Payment / Cart
     * ==============================================================================
     */

    function __checkCart(Cart $cart)
    {
        if ($cart->cartItems()->exists()) {
            foreach ($cart->cartItems as $key => $ci) {
                if ($ci->sku()->doesntExist()) {
                    $ci->delete();
                }
            }
        }
    }

    function __calcTotalPrice(Cart $cart, $promo = null)
    {
        $totalToDiscount = $total = $cart->cartItems()
            ->where('cart_items.status', 1)
            ->leftJoin('skus', 'cart_items.sku_id', '=', 'skus.id')
            ->selectRaw('skus.price * cart_items.qty AS total')
            ->get()
            ->sum('total');

        $discount = 0;

        if (!empty($promo)) {
            $voucher = Voucher::whereCode($promo)->first();
            if (!empty($voucher)) {

                if ($voucher->products()->exists()) {

                    $skuIds = $cart->cartItems()
                        ->whereStatus(1)
                        ->pluck('sku_id');

                    $productIds = Product::whereHas('skus', function ($skuQuery) use ($skuIds) {
                        $skuQuery->whereIn('id', $skuIds);
                    })->pluck('id');

                    if ($voucher->products()->whereIn('id', $productIds)->exists()) {

                        $voucherProductIds = $voucher->products->pluck('id');
                        $totalToDiscount = $cart->cartItems()
                            ->where('cart_items.status', 1)
                            ->whereHas('sku', function ($skuQuery) use ($voucherProductIds) {
                                $skuQuery->whereIn('product_id', $voucherProductIds);
                            })
                            ->leftJoin('skus', 'cart_items.sku_id', '=', 'skus.id')
                            ->selectRaw('skus.price * cart_items.qty AS total')
                            ->get()
                            ->sum('total');
                    }
                }

                if ($total >= $voucher->min_spend) {
                    $discount += $voucher->is_fixed
                        ? ($voucher->amount > $totalToDiscount ? $totalToDiscount : $voucher->amount)
                        : ($totalToDiscount * ($voucher->amount / 100));
                }
            }
        }

        $total -= $discount;

        return $total < 0 ? 0 : $total;
    }

    function __calcOrderTotalPrice(Order $order)
    {
        $totalToDiscount = $total = $order->items()
            ->selectRaw('unit_price * qty AS total')
            ->get()
            ->sum('total');

        $vouchers = $order->orderVouchers;

        $discount = 0;

        foreach ($vouchers as $voucher) {

            if ($order->items()->whereNotNull('order_voucher_id')->exists()) {
                $totalToDiscount = $voucher->orderItems()
                    ->selectRaw('unit_price * qty AS total')
                    ->get()
                    ->sum('total');
                $discount += $voucher->is_fixed
                    ? ($voucher->amount > $totalToDiscount ? $totalToDiscount : $voucher->amount)
                    : ($totalToDiscount * ($voucher->amount / 100));
            } else {
                $discount += $voucher->is_fixed ? $voucher->amount : ($total * ($voucher->amount / 100));
            }
        }

        $total -= $discount;

        return $total < 0 ? 0 : $total;
    }

    function __getOrderDescription(Order $order)
    {
        $result = $order->items()
            ->selectRaw('GROUP_CONCAT(name) AS order_description')
            ->first();

        return $result->order_description;
    }

    function __generateUniqueCounterWithPrefix(string $prefix, $className, string $column, int $length = 6, string $pad = "0", bool $random = false)
    {
        $string = $latest = null;

        if ($random) {
            $unique = false;
            while (!$unique) {
                $latest = mt_rand(0, str_pad(9, $length, 9, STR_PAD_LEFT));
                $string = $prefix . $latest;
                if ($className::where($column, $string)->doesntExist()) {
                    $unique = true;
                }
            }
        } else {
            $latest = $className::selectRaw('REPLACE(' . $column . ',"' . $prefix . '", "") AS count')
                ->get()
                ->max('count') + 1;
            $string = $prefix . str_pad($latest, $length, $pad, STR_PAD_LEFT);
        }
        return $string;
    }

    function __paymentToken()
    {
        return Str::random(rand(10, 20)) . "-" . Str::random(rand(3, 6)) . '-' . Str::random(rand(5, 10));
    }

    /**
     * ==============================================================================
     * End Of Payment
     * ==============================================================================
     */
    /**
     * ==============================================================================
     * Others
     * ==============================================================================
     */

    public function __getCurl($url)
    {
        $client = new \GuzzleHttp\Client();
        $request = $client->get($url, ['verify' => false]);
        $response = $request->getBody();
        return $response;
    }

    public function __postCurl($url, $body)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->createRequest("POST", $url, ['body' => $body]);
        $response = $client->send($response);
        return $response;
    }

    function __calculateDistance($latitude1, $longitude1, $latitude2, $longitude2)
    {
        return (3959 * acos(cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($longitude2) - deg2rad($longitude1)) + sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))));
    }

    function __formatPlateNumber($string)
    {
        return str_replace(' ', '', strtoupper($string));
    }

    function __addContactRecord($user, $type, $country, $phone, $code = null)
    {
        $code_duration = config('sms.duration');
        $expired_duration = config('sms.expired');

        $next_available_time = Carbon::now()->addSeconds($code_duration);
        $expired_at = Carbon::now()->addSeconds($expired_duration);

        $newVerifyCode = $code ?: $this->__generateVerifyToken();
        $ref = 'SMS_' . Str::random(5) . date('Ymdhis');

        $contact = Contact::create([
            "ownerable_id" => $user->id,
            "ownerable_type" => $type,
            "ref" => $ref,
            "country_id" => $country->id,
            "number" => $phone,
            "verify_code" => $newVerifyCode,
            "available_at" => $next_available_time,
            "expired_at" => $expired_at
        ]);

        return $contact;
    }

    function __publishRedisMessage($event, $targets = [], $data = null, $channel = "redis")
    {
        /**
         * Skip if redis message is not enabled
         */
        if (!config('others.redis_message.enabled')) {
            return true;
        }

        /**
         * This will trigger an quene job. It can be view in Horizon dashboard
         */
        event(new BroadcastNotification);

        /**
         * Please note that Laravel originally have prefix for key or channel name in `database.php` config file
         */
        Redis::publish($channel, json_encode([
            "event" => $event,
            "target" => $targets,
            "data" => $data
        ]));
    }

    function __sendSMS($country_code, $phone_number, $message, $ref = null)
    {
        $sms_prefix = config('sms.prefix');

        $type = config('sms.driver');
        if ($type == "isentric") {
            return $this->__sendIsentricSMS($country_code, $phone_number, $sms_prefix . $message, $ref);
        }
        if ($type == "onewaysms") {
            return $this->__sendOneWaySms($country_code, $phone_number, $sms_prefix . $message);
        }
        if ($type == "nexmo") {
            return $this->__sendNexmoSMS($country_code, $phone_number, $sms_prefix . $message);
        }

        return;
    }

    function __sendVerificationCode($country_code, $phone_number, $code, $ref = null, $type = null)
    {

        $message = "TAC Code is $code.";

        if ($type == "login" || $type == "register") {
            $message = "Here's your $type TAC Code: $code. Please do not share with others.";
        }

        return $this->__sendSMS($country_code, $phone_number, $message, $ref);
    }

    private function __sendIsentricSMS($country_code, $phone_number, $message, $ref = null)
    {

        $curl = curl_init();

        $data = array(
            'shortcode' => 39398,
            'custid' => config("sms.isentric.cust_id"),
            'rmsisdn' => rawurlencode($country_code . $phone_number),
            'smsisdn' => 62003,
            'mtid' => $ref, // refer datatabase
            'mtprice' => '000',
            'productCode' => '',
            'productType' => 4,
            'keyword' => '',
            'dataEncoding' => 0,
            'dataStr' => stripslashes(config('app.name') . ": " . $message),
            'dataUrl' => '',
            'dnRep' => 0,
            'groupTag' => 10,
        );

        $url = "http://203.223.130.118/ExtMTPush/extmtpush";

        $data = http_build_query($data);

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url . '?' . $data,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            $this->__errorLog("cURL Error #:" . $err);
            return $err;
        } else {
            $this->__normalLog(json_decode($response));
            return $response;
        }
    }

    private function __sendOneWaySMS($country_code, $phone_number, $message)
    {
        if (config("sms.onewaysms.enable")) {

            $apiusername = config("sms.onewaysms.username");
            $apipassword = config("sms.onewaysms.password");
            $sms_from = 'INFO';

            $data = array(
                'apiusername' => $apiusername,
                'apipassword' => $apipassword,
                'senderid' => rawurlencode($sms_from),
                'mobileno' => rawurlencode($country_code . $phone_number),
                'message' => stripslashes(config('app.name') . ": " . $message),
                'languagetype' => 1
            );

            $this->__normalLog('SMS triggered: ' . var_export($data, true));

            $url = "http://gateway80.onewaysms.com.my/api2.aspx";

            $data = http_build_query($data);

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url . "?" . $data,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_TIMEOUT => 30000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                $this->__errorLog("cURL Error #:" . $err);
                return $err;
            } else {
                $this->__normalLog(json_decode($response));
                return $response;
            }
        }
    }

    public function __sendNexmoSMS($country_code, $phone_number, $message)
    {
        if (config('sms.nexmo.enable')) {

            $api_key = config("sms.nexmo.api_key");
            $secret_key = config("sms.nexmo.secret_key");

            $client = new NexmoClient(new NexmoBasic($api_key, $secret_key));

            try {
                $message = $client->message()->send([
                    'to' => $country_code . $phone_number,
                    'from' => config('app.name'),
                    'text' => $message
                ]);
                $response = $message->getResponseData();

                if ($response['messages'][0]['status'] == 0) {
                    $this->__normalLog(json_decode($response));
                    return $response;
                } else {
                    return $response['messages'][0]['status']; // Error Status
                }
            } catch (Exception $e) {
                $this->__errorLog("Nexmo SMS Error #:" . $e->getMessage());
            }
        }
    }

    function __sendFirebaseCloudMessagingToken($tokens, $type, $title, $text, $type_id = null, bool $silence = false, $sound = null,$user_id=null)// https://stackoverflow.com/questions/39375200/fcm-message-to-multiple-registration-ids-limit
    {
        if (is_array($tokens)) {
            $tokens = array_values(array_filter(array_unique($tokens)));
            $this->__normalLog('Sending FCM Token: ' . implode(', ', $tokens));
        } else {
            $this->__normalLog('Sending FCM Token: ' . $tokens);
        }

        if (!env('FCM_ENABLED',false)) {
            return false;
        }

        /**
         * registration_ids: multiple token array
         * to: single token
         */
        $tokenName = is_array($tokens) ? 'registration_ids' : 'to';

        /**
         * For in apps handling
         */
        // $extraNotificationData = [
        //     "click_action" => "FLUTTER_NOTIFICATION_CLICK",
        //     "message" => $text,
        //     "type" => $type,
        //     "type_id" => $type_id,
        //     'title' => $title,
        // ];
        $extraNotificationData = [
            "click_action" => "FLUTTER_NOTIFICATION_CLICK",
            "message" => $text,
            "type" => $type,
            "type_id" => $type_id,
            "user_id" => $user_id,
            'title' => $title,
            "silence" => (int)$silence
        ];

        $data = [
            "$tokenName" => $tokens ?? 'test',
            'data' => $extraNotificationData,
            'notification' => [
                'title' => $title,
                'text' => $text,
                // 'body' => $text, // use this if "text" not working
                'sound' => true,
                'icon' => asset('favicon.png')
            ],
            'priority' => 'high',
            'badge' => 1,
            'content_available' => $silence // set 'true' if need silent IOS notification
        ];


        if(!$silence) { // For Android silent notification
            $data = array_merge($data, [
                'notification' => [
                    'title' => $title,
                    'text' => $text,
                    'body' => $text, // body used for iOS
                    'android_channel_id' => env('FCM_ANDROID_CHANNEL','push_noti_'.config('app.name')),
                    'sound' => $sound != null ? $sound : true,
                    'icon' => asset('favicon.png')
                ]
            ]);
        }

        // $data = [
        //     "$tokenName" => $tokens,
        //     'notification' => [
        //         'title' => $title,
        //         'text' => $text,
        //         // 'body' => $text, // use this if "text" not working
        //         'sound' => true,
        //         'icon' => asset('favicon.png')
        //     ],
        //     'data' => $extraNotificationData,
        //     'priority' => 'high',
        //     'badge' => 1,
        //     'content_available' => false // set 'true' if need silent IOS notification
        // ];

        $url =  env('FCM_ENDPOINT','https://fcm.googleapis.com/fcm/send');

        $headers = [
            'Authorization: key=' .  env('FCM_SECRET'),
            'Content-Type: application/json'
        ];


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $result = curl_exec($ch);
        curl_close($ch);

        $this->__normalLog("FCM Token Sent.(" . (is_array($tokens) ? implode(', ', $tokens) : $tokens) . ")" . json_encode($result));

        return true;
    }


    function __generateBookingNo($outlet,$type)
    {

        $count = 0;
        $newBookingNo = null;
        $prefill_zero = '';

        // do {
        //     $newBookingNo = $prefix.rand(10000000, 99999999);

        //     $count = Booking::where('booking_uid', $newBookingNo)->count();
        // } while ($count > 0);

        $newBookingNo = Booking::where('outlet_id', $outlet->id)->where('bookable_type',$type)->count() + 1;
        $prefix = $outlet->outlet_booking_prefix;
        $length = strlen((string)$newBookingNo);

        do {
                $length += 1;
                $prefill_zero .= '0';
        } while ($length < 8 );

        $fullNewBookingNo = $prefix . $prefill_zero . $newBookingNo;

        return $fullNewBookingNo ;
    }

    function __generateBookingUUID($booking)
    {
        $count = 0;
        $newBookingNo = 0;
        $totalBooking = 0;
        $prefill_zero = '';

        if($booking->type == 1){
            $totalBooking = Booking::where('outlet_id',$booking->outlet_id)->whereNotNull('booking_uuid')
            ->whereHas('transaction',function($query){
                $query->where('payment_status', 1);
            })->count() + 1;
            $outlet = $booking->outlet;
            $prefix = $outlet->outlet_booking_prefix;
            $length = strlen((string)$totalBooking);
            do {
                $length += 1;
                $prefill_zero .= '0';
            } while ($length < 8 );

            return   $prefix . $prefill_zero . $totalBooking;
        }else if ($booking->type == 2){
            $bookingCountry = $booking->bookable->country;

            $sameCountryTour = TitTarTour::where('country_id',$bookingCountry->id)->get()->pluck('id')->toArray();
            $allBookingThatHaveSameCountry = Booking::where('bookable_type',TitTarTour::class)->whereIn('bookable_id',$sameCountryTour)
            ->whereHas('transaction',function($query){
                $query->where('payment_status', 1);
            })->where('booking_uuid','!=',null)->count() + 1;
            $prefix = $bookingCountry->iso;
            $length = strlen((string)$allBookingThatHaveSameCountry);
            do {
                $length += 1;
                $prefill_zero .= '0';
            } while ($length < 8 );

            return   $prefix . $prefill_zero . $allBookingThatHaveSameCountry;
        }

    }


    function __generateBookingNoTitTarTour($booking)
    {

        $count = 0;
        $newBookingNo = null;
        $prefill_zero = '';

        // do {
        //     $newBookingNo = $prefix.rand(10000000, 99999999);

        //     $count = Booking::where('booking_uid', $newBookingNo)->count();
        // } while ($count > 0);
        $currentTitTarTour = Booking::where('bookable_id', $titTarTour->id)->where('bookable_type',$type)->first();
        // $countryIso =
        $newBookingNo = Booking::where('bookable_id', $titTarTour->id)->where('bookable_type',$type)->count() + 1;
        $prefix = $titTarTour->booking_prefix;
        $length = strlen((string)$newBookingNo);

        do {
                $length += 1;
                $prefill_zero .= '0';
        } while ($length < 8 );

        $fullNewBookingNo = $prefix . $prefill_zero . $newBookingNo;

        return $fullNewBookingNo ;
    }

    function __generateUid($model,$key,$value)
    {
        $count = 0;
        $uid = null;

        do {
            $uid = $value;

            $count = $model::where($key, $uid)->count();
        } while ($count > 0);

        return $uid;
    }

    function __generateVerifyToken()
    {
        $count = 0;
        $newVerifyCode = null;

        do {
            $newVerifyCode = rand(100000, 999999);

            $count = Contact::whereNull('ownerable_id')
                ->where('verify_code', $newVerifyCode)->count();
        } while ($count > 0);

        return $newVerifyCode;
    }

    function __throwException($type)
    {
        $className = null;
        switch ($type) {
            case 'login':
                $className = AuthenticationException::class;
                break;
            default:
                # code...
                break;
        }

        if (!empty($className)) {
            throw new $className;
        }
    }

    function __platformVersion($type)
    {
        $osType = request()->header('Os-Type'); // android, ios
        $platform = Platform::whereType($type)
            ->whereOs($osType)
            ->latest()
            ->first();

        return $platform;
    }

    function __nearbyLocation($target, $latitude, $longitude, $distance = 25, $query = [])
    {
        $results = $target::select(
            DB::raw('*, ( 6367 * acos( cos( radians( ? ) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians( ? ) ) + sin( radians( ? ) ) * sin( radians( latitude ) ) ) ) AS distance')
        )
            ->having('distance', '<', $distance)
            ->setBindings([$latitude, $longitude, $latitude])
            ->orderBy('distance');

        foreach ($query as $key => $value) {
            $results->where($value[0], $value[1], $value[2]);
        }

        return $results->get();
    }

    /**
     * ==============================================================================
     * End Of Others
     * ==============================================================================
     */
}
