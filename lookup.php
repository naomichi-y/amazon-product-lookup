<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

require 'vendor/autoload.php';

use GetOptionKit\OptionCollection;
use GetOptionKit\OptionParser;

$specs = new OptionCollection;
$specs->add('access-key:', 'option requires a value.')->isa('string')->defaultValue('');
$specs->add('secret-key:', 'option requires a value.')->isa('string')->defaultValue('');
$specs->add('country?', 'option with optional value.')->isa('string')->defaultValue('com');

$parser = new OptionParser($specs);
$result = $parser->parse($argv);

$client = new AmazonECS(
  $result['access-key']->value,
  $result['secret-key']->value,
  $result['country']->value,
  'dummy_associate_tag'
);
$client->responseGroup('ItemAttributes');
$client->requestDelay(TRUE);

$reader = new SplFileObject('data/asin.csv');
$reader->setFlags(SplFileObject::READ_CSV);

$writer = new SplFileObject('data/result.csv', 'w');

foreach ($reader as $line) {
  $asin = $line[0];

  if (!is_null($line[0])) {
    printf("search: %s\n", $asin);

    $data = $client->lookup($asin);
    $title = '';
    $upc = '';
    $mpn = '';

    if (isset($data->Items->Request->Errors)) {
      printf("[ERROR] %s\n", $data->Items->Request->Errors->Error->Message);

    } else {
      $title = $data->Items->Item->ItemAttributes->Title;

      if (isset($data->Items->Item->ItemAttributes->UPC)) {
        $upc = $data->Items->Item->ItemAttributes->UPC;
      } else {
        printf("[WARN] UPC can not be found\n");
      }

      if (isset($data->Items->Item->ItemAttributes->MPN)) {
        $mpn = $data->Items->Item->ItemAttributes->MPN;
      } else {
        printf("[WARN] MPN can not be found\n");
      }
    }

    $writer->fputcsv([$asin, $title, $upc, $mpn]);
  }
}

printf("process is completed. [Export file: data/result.csv]\n");
