<?php
require_once 'vendor/autoload.php';

$ids = [
  'ocid1.instance.oc1.ap-tokyo-1.anxhiljrrqqkxxqc34ldrtysjvnrz6w3ixdplgghy3i24jn3xvqyap72i4qa',
  'ocid1.instance.oc1.ap-tokyo-1.anxhiljrrqqkxxqcgk2h4afv4pxyiiutr3fj6jmbl3slrark3kmuigzvpuia'
];

$c = new \Hitrov\OciConfig(
  getenv('OCI_REGION'), getenv('OCI_USER_ID'), getenv('OCI_TENANCY_ID'),
  getenv('OCI_KEY_FINGERPRINT'), getenv('OCI_PRIVATE_KEY_FILENAME'),
  null, getenv('OCI_SUBNET_ID'), getenv('OCI_IMAGE_ID'), 1, 1
);
$api = new \Hitrov\OciApi();

foreach ($ids as $id) {
  $url = "https://iaas.ap-tokyo-1.oraclecloud.com/20160918/instances/" . $id;
  echo "Terminating $id...\n";
  try {
    $api->call($c, $url, 'DELETE');
    echo "OK\n";
  } catch (\Exception $e) {
    $msg = $e->getMessage();
    if (strpos($msg, 'JSON') !== false || strpos($msg, 'Syntax') !== false) {
      echo "OK (204)\n";
    } else {
      echo "ERR: $msg\n";
    }
  }
}
