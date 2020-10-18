<?php

    class Utils {
        // ISO 8601
        const FORMAT = "c";

        /**
         * @param int|DateTime $date UNIX-Timestamp or DateTime
         */
        static function formatWeek($date) {
            $d = $date;
            if($date instanceof DateTime)
                $date = $date->getTimestamp();

            $year = date("Y", $date);
            $week = (int) date('W', $date);
            // Day in last year, but first week of next year
            if($week == 1 && date("m", $date) == "12") {
                $year++;
            }
            return $year . " (" . sprintf('%02d', $week) . ")";
        }

        /**
         * Generate a list of labels given the start and end of a timespan
         *
         * @param DateTime $start
         * @param DateTime $end
         * @param "DAY"|"WEEK" $mode
         * @return string[]
         */
        static function genLabels($start, $end, $mode = "DAY") {
            if(!in_array($mode, ["DAY", "WEEK"]))
                throw new InvalidArgumentException("Invalid mode");

            $start  = clone $start;
            $end    = clone $end;

            $interval = new DateInterval("P1" . ($mode == "DAY" ? "D" : "W"));

            $r = [];
            $start  ->setTime(0, 0, 0, 0);
            $end    ->setTime(0, 0, 0, 0);

            $i = clone $interval;
            $i->invert = 1;

            $start  ->add($i);
            $end    ->add($interval);

            $curr = $start;
            while($curr->getTimestamp() < $end->getTimestamp()) {
                $r[] = self::formatWeek($curr);

                $curr->add($interval);
            }
            return $r;
        }
    }

?>