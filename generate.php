<?php
include __DIR__."/wa.php";
include __DIR__."/charts.php";
include __DIR__."/utils.php";

const ME = "Jesper";
// Minimum messages threshold
const MIN = 500;
$source = MsgStore::getDate();

const COLORS = [
  -1 => [
    ["rgba(189,189,189,0.5)", "rgba(189,189,189,0.3)"]
  ],
  [
    ["rgba(159,168,218,1)", "rgba(159,168,217,0.8)"],
    ["rgba(239,154,154,1)", "rgba(239,154,154,0.8)"]
  ],
  [
    ["rgba(165,214,167,1)", "rgba(165,214,167,0.8)"],
    ["rgba(255,224,130,1)", "rgba(255,224,130,0.8)"]
  ],
  [
    ["rgba(255,204,128,1)", "rgba(255,204,128,0.8)"],
    ["rgba(188,170,164,1)", "rgba(188,170,164,0.8)"]
  ],
  [
    ["rgba(129,212,250,1)", "rgba(129,212,250,0.8)"],
    ["rgba(206,147,216,1)", "rgba(206,147,216,0.8)"]
  ],
];

/** @var Contact[] */
$cons = [
  WA::getContactByJid("@s.whatsapp.net"),
];

$cons = array_values(array_filter($cons, function($c) {
  return count($c->getMessages()) > MIN;
}));
/** @var Message[][] */
$msgs = array_map(function($c) {
  /** @var Contact $c */
  return $c->getMessages();
}, $cons);

if(false) {
  echo "No conversation with neither {$con1->getDisplayName()} nor {$con2->getDisplayName()}.";
  die();
}

$starts = [];
foreach($msgs as $m) {
  $starts[] = $m[0]->getTimestamp();
}
$start = min($starts);
$start = new DateTime();
$start->setTimestamp(strtotime("17. Jul 2019"));
$end   = new DateTime();

// Messages per week
$msgsPerWeekLabels  = Utils::genLabels($start, $end, "WEEK");
$msgsPerWeekMe    = array_fill(0, count($cons), []);
$msgsPerWeekThem  = array_fill(0, count($cons), []);
$msgsPerWeekTotal = [];

// Fill data
foreach(array_slice($msgsPerWeekLabels, 0, count($msgsPerWeekLabels)-1) as $l) {
  for($i = 0; $i < count($cons); $i++) {
    $msgsPerWeekMe[$i][$l] = 0;
    $msgsPerWeekThem[$i][$l] = 0;
    $msgsPerWeekTotal[$l] = 0;
  }
}

foreach($msgs as $i => $m) {
  foreach($m as $msg) {
    $format = Utils::formatWeek($msg->getTimestamp());
    if(isset($msgsPerWeekTotal[$format])) {
      if($msg->isMe()) {
        $msgsPerWeekMe[$i][$format]++;
      } else {
        $msgsPerWeekThem[$i][$format]++;
      }
      $msgsPerWeekTotal[$format]++;
    }
  }
}


$msgsPerWeek  = new LineChart("msgsPerWeek", $source);
$msgsPerWeek  ->setTitle("Messages per Week");
$msgsPerWeek  ->setLabels($msgsPerWeekLabels);
$msgsPerWeek  ->addDataset("Total", COLORS[-1][0][0], COLORS[-1][0][1], $msgsPerWeekTotal, ["fill" => true]);
for($i = 0; $i < count($cons); $i++) {
  $msgsPerWeek  ->addDataset($cons[$i]->getDisplayName(),  COLORS[$i%(count(COLORS)-1)][0][0], COLORS[$i%(count(COLORS) - 1)][0][1], $msgsPerWeekThem[$i]);
  $msgsPerWeek  ->addDataset(ME,                          COLORS[$i%(count(COLORS)-1)][1][0], COLORS[$i%(count(COLORS)-1)][1][1], $msgsPerWeekMe[$i]);
}

echo $msgsPerWeek;
?>