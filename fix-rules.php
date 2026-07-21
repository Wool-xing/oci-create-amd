<?php
require_once 'vendor/autoload.php';

$c = new \Hitrov\OciConfig(
  'ap-tokyo-1',
  'ocid1.user.oc1..aaaaaaaarcfcfjovuaim6ga55repfnjzonscc2p6265awjw454mc5wf7shhq',
  'ocid1.tenancy.oc1..aaaaaaaaw32ngyf5nyfxt4ju7ag53mss3hpj7jdt5gbhezlrqwqvnqh2ywha',
  '3e:47:65:7c:46:06:46:86:bc:83:de:bd:39:1e:be:04',
  '/tmp/oci_key.pem', '', '', '', 1, 1
);
$api = new \Hitrov\OciApi();
$sl = 'ocid1.securitylist.oc1.ap-tokyo-1.aaaaaaaa3kaxs42iphcy2oibvxe5qebz5kkzxv3cgwvpwdp7hv5zunumfiaa';

$rules = [
  ['source'=>'0.0.0.0/0', 'protocol'=>'6', 'dstMin'=>22, 'dstMax'=>22, 'desc'=>'SSH'],
  ['source'=>'0.0.0.0/0', 'protocol'=>'6', 'dstMin'=>80, 'dstMax'=>80, 'desc'=>'HTTP'],
  ['source'=>'0.0.0.0/0', 'protocol'=>'6', 'dstMin'=>443, 'dstMax'=>443, 'desc'=>'HTTPS'],
  ['source'=>'0.0.0.0/0', 'protocol'=>'1', 'icmpType'=>3, 'icmpCode'=>4, 'desc'=>'ICMP'],
  ['source'=>'10.0.0.0/16', 'protocol'=>'1', 'icmpType'=>3, 'icmpCode'=>null, 'desc'=>'ICMP internal'],
];

$ingress = [];
foreach ($rules as $r) {
  $rule = ['source'=>$r['source'], 'protocol'=>$r['protocol'], 'description'=>$r['desc']];
  if ($r['protocol'] == '6') {
    $rule['tcpOptions'] = ['destinationPortRange'=>['min'=>$r['dstMin'], 'max'=>$r['dstMax']]];
  }
  if ($r['protocol'] == '1' && isset($r['icmpType'])) {
    $rule['icmpOptions'] = ['type'=>$r['icmpType']];
    if (isset($r['icmpCode'])) $rule['icmpOptions']['code'] = $r['icmpCode'];
  }
  $ingress[] = $rule;
}

$body = json_encode(['ingressSecurityRules'=>$ingress]);
echo "Restoring rules...\n";
$url = "https://iaas.ap-tokyo-1.oraclecloud.com/20160918/securityLists/".$sl."/";
try {
  $api->call($c, $url, 'PUT', $body);
  echo "Rules restored!\n";
} catch(\Exception $e) {
  echo "Err: ".$e->getMessage()."\n";
}
