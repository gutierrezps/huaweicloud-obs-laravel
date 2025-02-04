# Huawei Cloud OBS access in Laravel

This repository has a minimal [Laravel](https://laravel.com/) application
that uses Huawei Cloud [Object Storage Service (OBS)](https://www.huaweicloud.com/intl/en-us/product/obs.html)
as an [S3 Compatible Filesystem](https://laravel.com/docs/11.x/filesystem#amazon-s3-compatible-filesystems).

## Requirements

- PHP 8.2
- [Composer](https://getcomposer.org/)

You can follow [Laravel's Installation Guide](https://laravel.com/docs/11.x/installation#installing-php)
if you don't have a PHP development environment yet.

## Installation

1. Create an IAM User in your Huawei Cloud account:
   - Select only "Programmatic access" as Access Type;
   - Select "Access key" as Credential Type;
   - Do not assign the IAM User to any group neither give any permission;
   - Download the credentials file which contains the AK and SK;
2. Create an OBS bucket
   - Select "Public Read" as Bucket Policy if you wish final users can access
     objects directly (without going through your application), otherwise select
     "Private" as Bucket Policy
3. Configure a Bucket Policy
   - Efect: Allow
   - Principal: Current account / select the IAM User you created in first step
   - Resources: Entire bucket (inclusing the objects in it)
   - Actions: Use a template / Bucket Read/Write
4. In the bucket Overview section, copy the "Endpoint Name" attribute.
5. Copy the `.env.example`, name it `.env` and update the following variables:

    ```plain
    AWS_ACCESS_KEY_ID=
    AWS_SECRET_ACCESS_KEY=
    AWS_BUCKET=
    AWS_DEFAULT_REGION=sa-brazil-1
    AWS_ENDPOINT=https://obs.sa-brazil-1.myhuaweicloud.com
    AWS_USE_PATH_STYLE_ENDPOINT=false
    ```

    Where `sa-brazil-1` is the code for LA-Sao Paulo1 Region, where your bucket
    is created.

## Troubleshooting

### Failed to listen on 127.0.0.1:8000

When running `php artisan serve` in Windows, you can get the following error message:
`Failed to listen on 127.0.0.1:8000 (reason: ?)`.

1. Run `php --ini` and check where is your `php.ini` configuration file (e.g.
   `C:\Users\<username>\.config\herd-lite\bin\php.ini`);
2. Edit `php.ini` and change `variables_order = "EGPCS"` to `variables_order = "GPCS"`.

Reference: <https://github.com/laravel/framework/issues/34229#issuecomment-690302895>

### `cURL error 60`

When installing PHP 8.4 on Windows using Laravel's instructions, you can get the
`cURL error 60: SSL peer certificate or SSH remote key was not OK` error message
when accessing the application. This happens in Windows because Guzzle/libcurl
cannot access Windows' certificate store to validate SSL certificates. In this
case, do the following:

1. Run `php --ini` and check where is your `php.ini` configuration file (e.g.
   `C:\Users\<username>\.config\herd-lite\bin\php.ini`);
2. Download the Mozilla CA certificate store `cacert.pem` from <https://curl.se/docs/caextract.html>
   and save it to the same folder as `php.ini` is located;
3. Edit `php.ini` and add the following content at the end (replace the path
   to `cacert.pem` according to your environment):

   ```ini
   [curl]
   curl.cainfo = "C:\Users\<username>\.config\herd-lite\bin\cacert.pem"

   [openssl]
   openssl.cafile = "C:\Users\<username>\.config\herd-lite\bin\cacert.pem"
   ```

4. Restart the `php artisan serve` command.

Reference: <https://stackoverflow.com/a/34883260/2014507>
