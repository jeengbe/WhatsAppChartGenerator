<?php
include __DIR__."/wa.php";
include __DIR__."/charts.php";
include __DIR__."/utils.php";

const ME = "Jesper";

$source = MsgStore::getDate();

$con  = WA::getContactByJid($_POST["jid"]);
$msgs = $con->getMessages();

if(count($msgs) == 0) {
  echo "No conversation with {$con->getDisplayName()}.";
  die();
}

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
$msgsPerWeek  ->addDataset("Total", "rgba(189,189,189,1)", "rgba(189,189,189,0.3)", $msgsPerWeekTotal, ["fill" => true]);
$msgsPerWeek  ->addDataset(ME, "rgba(159,168,218,1)", "rgba(159,168,217,0.8)", $msgsPerWeekMe);
$msgsPerWeek  ->addDataset($con->getDisplayName(), "rgba(239,154,154,1)", "rgba(239,154,154,0.8)", $msgsPerWeekThem);

echo $msgsPerWeek;
?>