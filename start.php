<?php

use Aws\S3\S3Client;

require 'vendor/autoload.php';

$config=require('config.php');

// Creation of S3 instance

$s3 = S3Client::factory([
   'key' => $config['s3']['key'],
   'secret' => $config['s3']['secret']
]);

