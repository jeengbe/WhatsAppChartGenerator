<?php
include __DIR__."/wa.php";
include __DIR__."/charts.php";
include __DIR__."/utils.php";

const ME = "Jesper";

$source = MsgStore::getDate();

$con  = WA::getContactByJid($_POST["jid"]);
$msgs = $con->getMessages();

$start  = $msgs[0]->getTimestamp();
$end    = new DateTime();

// Messages per week
$msgsPerWeekLabels  = Utils::genLabels($start, $end, "WEEK");
$msgsPerWeekMe    = [];
$msgsPerWeekThem  = [];
$msgsPerWeekTotal = [];

// Fill data
foreach($msgsPerWeekLabels as $l) {
  $msgsPerWeekMe[$l] = 0;
  $msgsPerWeekThem[$l] = 0;
  $msgsPerWeekTotal[$l] = 0;
}

foreach($msgs as $m) {
  $format = Utils::formatWeek($m->getTimestamp());
  if($m->isMe()) {
    $msgsPerWeekMe[$format]++;
  } else {
    $msgsPerWeekThem[$format]++;
  }
  $msgsPerWeekTotal[$format]++;
}

$msgsPerWeek  = new LineChart("msgsPerWeek", $source);
$msgsPerWeek  ->setTitle("Messages per Week");
$msgsPerWeek  ->setLabels($msgsPerWeekLabels);
$msgsPerWeek  ->addDataset($con->getDisplayName(), "#ef9a9a", "#ef9a9acc", $msgsPerWeekThem);
$msgsPerWeek  ->addDataset(ME, "#9FA8DA", "#9FA8DAcc", $msgsPerWeekMe);
$msgsPerWeek  ->addDataset("Total", "#BDBDBD", "#BDBDBD55", $msgsPerWeekTotal, ["fill" => true]);

echo $msgsPerWeek;
?>