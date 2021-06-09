<?php

namespace App\Helpers;

use Illuminate\Http\File;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class CustomValidators
{
  public static function validate_base64($base64data, array $allowedMime)
  {

    if (strpos($base64data, ';base64') === false) return false;

    // strip out data uri scheme information (see RFC 2397)
    list(, $base64data) = explode(';', $base64data);
    list(, $base64data) = explode(',', $base64data);

    // strict mode filters for non-base64 alphabet characters
    if (!base64_decode($base64data, true)) return false;

    // decoding and then reeconding should not change the data
    if (base64_encode(base64_decode($base64data)) !== $base64data) {
      return false;
    }

    $binaryData = base64_decode($base64data);

    // temporarily store the decoded data on the filesystem to be able to pass it to the fileAdder
    $tmpFile = tempnam(sys_get_temp_dir(), 'medialibrary');
    file_put_contents($tmpFile, $binaryData);

    // guard Against Invalid MimeType
    $allowedMime = Arr::flatten($allowedMime);

    // no allowedMimeTypes, then any type would be ok
    if (empty($allowedMime)) return true;

    // Check the MimeTypes
    $validation = Validator::make(
      ['file' => new File($tmpFile)],
      ['file' => ['mimes:' . implode(',', $allowedMime), 'max:2048']]
    );

    return !$validation->fails();
  }
}
