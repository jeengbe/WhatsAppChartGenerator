<?php

    class Utils {
        // ISO 8601
        const FORMAT = "c";

        /**
         * @param int|DateTime $date UNIX-Timestamp or DateTime
         */
        static function formatWeek($date) {
            if($date instanceof DateTime)
                $date = $date->getTimestamp();
            return date("Y", $date) . " (" . sprintf('%02d', date('W', $date)) . ")";
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

            $r = [];
            $start  ->setTime(0, 0, 0, 0);
            $end    ->setTime(0, 0, 0, 0);

            $curr = $start;
            while($curr->getTimestamp() < $end->getTimestamp()) {
                $r[] = self::formatWeek($curr);

                $curr->add(new DateInterval("P1".($mode == "DAY" ? "D" : "W")));
            }
            return $r;
        }
    }

?>