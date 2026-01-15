<?php

// use Thumbnail;

use App\Helper\Thumbnail;
use Carbon\Carbon;
use Carbon\CarbonInterface;

if (!function_exists('saveGeolocation')) {
    function saveGeolocation($db, $table, $resourceId, $lat = NULL, $lng = NULL)
    {
        if ($lat && $lng) {
            $db::insert("UPDATE $table SET geolocation = ST_MakePoint($lng, $lat) WHERE id = '$resourceId'");
        }
    }
}

if (!function_exists('updateLatLngDeviceToken')) {
    function updateLatLngDeviceToken($resource, $requestData = [], $db = NULL)
    {
        $resource->device_token = $requestData['device_token'] ?? $resource->device_token;
        $resource->device_type = $requestData['device_type'] ?? $resource->device_type;
        $resource->lat = $requestData['lat'] ?? $resource->lat;
        $resource->lng = $requestData['lng'] ?? $resource->lng;
        $resource->save();

        $lat = $resource->lat;
        $lng = $resource->lng;

        if ($lat && $lng) {
            $db::insert("UPDATE users SET geolocation = ST_MakePoint($lng, $lat) WHERE id = '" . $resource->id . "'");
        }
    }
}

if (!function_exists('validation_error_response')) {
    function validation_error_response($errors)
    {
        $response = [];

        $counter = 0;
        foreach ($errors as $key => $value) {
            if ($counter > 0) {
                break;
            }

            $errorMessage = $value[0];
        }
        //$response['errors'] = $errors;
        $response['message'] = $errorMessage;
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;
        return $response;
    }
}

if (!function_exists('base64_to_image')) {
    /**
     * Convert base64 string to image and save it.
     *
     * @param string $base64_string
     * @return array
     * @throws \Exception
     */
    function base64_to_image(string $base64_string): array
    {
        // Obtain the original content (usually binary data)
        $bin = base64_decode($base64_string);

        // Gather information about the image using the GD library
        $size = getImageSizeFromString($bin);

        // Check the MIME type to be sure that the binary data is an image
        if (empty($size['mime']) || strpos($size['mime'], 'image/') !== 0) {
            throw new \Exception('Base64 value is not a valid image');
        }

        // Mime types are represented as image/gif, image/png, image/jpeg, and so on
        $ext = substr($size['mime'], 6);

        // Make sure that you save only the desired file extensions
        if (!in_array($ext, ['png', 'gif', 'jpeg'])) {
            throw new \Exception('Unsupported image type');
        }

        $file_name = "chat_image_" . time() . "." . $ext;

        // Specify the location where you want to save the image
        $img_file = IMAGE_UPLOAD_PATH . $file_name;

        file_put_contents($img_file, $bin);

        return [
            'file_name' => $file_name,
            'file_type' => $ext,
        ];
    }
}

if (!function_exists('uploadImages')) {
    /**
     * Upload multiple images.
     *
     * @param array $images
     * @param string $destinationPath
     * @return array
     */
    function uploadImages(array $images = [], string $destinationPath = ''): array
    {
        $image_path_data = [];

        foreach ($images as $key => $file) {
            /** @var \Illuminate\Http\UploadedFile $file */
            $fileName = time() . '-' . $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $file->move($destinationPath, $fileName);

            $mime = $file->getClientMimeType();

            $fileType = "";
            if (strstr($mime, "video/")) {
                $fileType = "video";
            } else if (strstr($mime, "image/")) {
                $fileType = "image";
            } else if (strstr($mime, "audio/")) {
                $fileType = "audio";
            }

            $image_path_data[$key] = [
                'file_name' => $fileName,
                'file_type' => $fileType,
                'file_extension' => $extension ?? '',
            ];
        }

        return $image_path_data;
    }
}

if (!function_exists('create_thumbnail')) {
    /**
     * Create a thumbnail from an image
     *
     * @param string $filePath Directory path where file is located
     * @param string $fileName Name of the original file
     * @param int|string $userId User identifier (used in thumbnail naming)
     *
     * @return string Thumbnail filename on success, empty string on failure
     */
    function create_thumbnail(string $filePath = '', string $fileName = '', int|string $userId = ''): string
    {
        if ($filePath === '' || $fileName === '') {
            return '';
        }

        // Ensure path ends with a slash
        $filePath = rtrim($filePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $thumbnailName = $userId . time() . '_thumbnail.jpg';

        $thumbnailStatus = Thumbnail::getThumbnail(
            $filePath . $fileName,
            $filePath,
            $thumbnailName,
            2
        );

        return $thumbnailStatus ? $thumbnailName : '';
    }
}

if (!function_exists('generateNumericOTP')) {
    function generateNumericOTP($n)
    {

        // Take a generator string which consist of
        // all numeric digits
        $generator = "1357902468";

        // Iterate for n-times and pick a single character
        // from generator and append it to $result

        // Login for generating a random character from generator
        //     ---generate a random number
        //     ---take modulus of same with length of generator (say i)
        //     ---append the character at place (i) from generator to result

        $result = "";

        for ($i = 1; $i <= $n; $i++) {
            $result .= substr($generator, (rand() % (strlen($generator))), 1);
        }

        // Return result
        return $result;
    }
}

if (!function_exists('addMinutesToTime')) {
    function addMinutesToTime($timeData = [])
    {
        if (!isset($timeData['time'])) {
            $time = new DateTime();
        } else {
            $time = new DateTime($timeData['time']);
        }

        if (!isset($timeData['minute'])) {
            $minutes_to_add = 2;
        } else {
            $minutes_to_add = (int)$timeData['minute'];
        }

        $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));

        $stamp = $time->format('Y-m-d H:i:s');

        return $stamp;
    }
}

if (!function_exists('object_to_array')) {
    function object_to_array($obj, &$arr)
    {
        if (!is_object($obj) && !is_array($obj)) {
            $arr = $obj;
            return $arr;
        }

        foreach ($obj as $key => $value) {
            if (!empty($value)) {
                $arr[$key] = array();
                object_to_array($value, $arr[$key]);
            } else {
                $arr[$key] = $value;
            }
        }

        return $arr;
    }
}

if (!function_exists('sort_restaurant_days')) {
    function sort_restaurant_days(array $days): array
    {
        $weekDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $daysArr = [];
        foreach ($weekDays as $day) {
            $daysArr[$day] = [
                'is_opened' => !empty($days[$day]['is_opened']),
                'open' => $days[$day]['open'] ?? null,
                'close' => $days[$day]['close'] ?? null,
            ];
        }
        return $daysArr;
    }
}



/**
 * Generate weekly ranges grouped by month
 *
 * @param string|null $today         Starting date (Y-m-d) or null for today
 * @param int         $scheduleMonths Number of months to include
 *
 * @return array
 */
function getWeeks(?string $today = null, int $scheduleMonths = 6): array
{
    // Use provided date or today
    $today = $today ? Carbon::createFromFormat('Y-m-d', $today) : Carbon::today();

    // Align to start of calendar (Sunday of first month)
    $startDate = $today->copy()->startOfMonth()->startOfWeek(CarbonInterface::SUNDAY);
    $endDate = $today->copy()->addMonths($scheduleMonths)->endOfMonth()->endOfWeek(CarbonInterface::SATURDAY);

    $theDay = $startDate->copy();
    $week = 0;
    $data = [];

    while ($theDay <= $endDate) {
        $monthKey = $theDay->format('F Y');

        // Initialize month if not exists
        if (!isset($data[$monthKey])) {
            $data[$monthKey] = [];
        }

        // Initialize week if not exists
        if (!isset($data[$monthKey][$week])) {
            $data[$monthKey][$week] = ['day_range' => ''];
        }

        // Add start of range on Sunday
        if ($theDay->isSunday()) {
            $data[$monthKey][$week]['day_range'] = $theDay->day . '-';
        }

        // Close range on Saturday
        if ($theDay->isSaturday()) {
            $data[$monthKey][$week]['day_range'] .= $theDay->day;
            $week++;
        }

        $theDay->addDay();
    }

    return [
        'startDate'   => $startDate,
        'endDate'     => $endDate,
        'totalWeeks'  => $week,
        'schedule'    => $data,
    ];
}


function weekOfMonth($date)
{

    $firstOfMonth = strtotime(date("Y-m-01", $date));
    $lastWeekNumber = (int)date("W", $date);
    $firstWeekNumber = (int)date("W", $firstOfMonth);
    if (12 === (int)date("m", $date)) {
        if (1 == $lastWeekNumber) {
            $lastWeekNumber = (int)date("W", ($date - (7 * 24 * 60 * 60))) + 1;
        }
    } elseif (1 === (int)date("m", $date) and 1 < $firstWeekNumber) {
        $firstWeekNumber = 0;
    }
    return $lastWeekNumber - $firstWeekNumber + 1;
}

function weeks($month, $year)
{
    $lastday = date("t", mktime(0, 0, 0, $month, 1, $year));
    return weekOfMonth(strtotime($year . '-' . $month . '-' . $lastday));
}

function generateRandomToken($length = 10, $string = 'xyz')
{
    $characters = $string . '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' . time();
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function random_color_part()
{
    return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
}

function random_color()
{
    return random_color_part() . random_color_part() . random_color_part();
}

function array_values_recursive($arr)
{
    foreach ($arr as $key => $value) {
        if (is_array($value)) {
            $arr[$key] = array_values($value);
        }
    }

    return $arr;
}

function generate_random_color($i = 0)
{
    /*$colors = [
        "#2f4cdd",
        "#2bc155",
        "#ff6d4d",
        "#ff9800",
        "#3e4954",
        "#f72b50",
    ];*/
    $colors = [
        "rgb(47, 76, 221)",
        "rgb(43, 193, 85)",
        "rgb(255, 109, 77)",
        "rgb(255, 152, 0)",
        "rgb(62, 73, 84)",
        "rgb(247, 43, 80)",
    ];
    return $colors[$i];
}

if (!function_exists('x_week_range')) {
    function x_week_range($date = NULL)
    {
        $date = $date ?? date('Y-m-d');
        $day = date('N', strtotime($date));
        $week_start = date('Y-m-d', strtotime('-' . ($day - 1) . ' days', strtotime($date)));
        $week_end = date('Y-m-d', strtotime('+' . (7 - $day) . ' days', strtotime($date)));

        return [$week_start, $week_end];
    }
}

if (!function_exists('get_setting')) {
    function get_setting()
    {
        $settingsObj = \App\Models\Setting::first();

        if ($settingsObj) {
            return $settingsObj->settings;
        }

        return [];
    }
}

if (!function_exists('get_setting_by_key')) {
    function get_setting_by_key($settingName = '', $settingsKey = '')
    {
        $settingsObj = \App\Models\Setting::first();

        if ($settingsObj) {
            $settings = (array) $settingsObj->settings;

            if (!empty($settingName)) {
                if (isset($settings[$settingName])) {
                    if (isset($settings[$settingName]->$settingsKey)) {
                        return $settings[$settingName]->$settingsKey;
                    }
                }
            }
        }

        return '';
    }
}
