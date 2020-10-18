<?php

    class LineChart {
        /** @var string */
        private $id;
        /** @var string[] */
        private $labels;
        /** @var string */
        private $title;
        /** @var array */
        private $datasets;
        /** @var DateTime */
        private $date;

        /**
         * @param string $id
         */
        function __construct($id, $date) {
            $this->id     = $id;
            $this->date   = $date;
            $this->title  = "Untitled linechart";
        }

        /**
         * @param string[] $labels
         * @return self
         */
        function setLabels($labels) : self {
            $this->labels = $labels;
            return $this;
        }

        /**
         * @param string $title
         * @return self
         */
        function setTitle($title) : self {
          $this->title = $title;
          return $this;
        }

        /**
         * @param string $label
         * @param string $borderColor
         * @param string $backgroundColor
         * @param (int|float)[] $data
         * @return self
         */
        function addDataset($label, $borderColor, $backgroundColor, $data) : self {
          $this->datasets[] = [
            "label"             => $label,
            "borderColor"       => $borderColor,
            "backgroundColor"   => $backgroundColor,
            "data"              => array_values($data),
            "fill"              => false,
            "pointRadius"       => 2,
            "pointHoverRadius"  => 2,
          ];
          return $this;
        }

        function __toString() : string {
            $config = [
              "type" => "line",
              "data" => [
                "labels"    => $this->labels,
                "datasets"  => $this->datasets
              ],
              "options" => [
                "responsive" => true,
                "title" => [
                  "display" => true,
                  "text"    => $this->title
                ],
                "elements" => [
                  "line" => [
                    "tension" => 0.4
                  ]
                ],
                "animation" => [
                  "onProgress" => "ONPROGRESSCALLBACK",
                ]
              ],
            ];
            $config = json_encode($config);
            $config = str_replace("\"ONPROGRESSCALLBACK\"", "_ => {c.font = \"12px sans-serif\";c.fillText(\"Source: ".date("d.m.Y H:i", $this->date->getTimestamp())."\", 0, 6)}", $config);

            return "<div><canvas id=\"{$this->id}\"></canvas></div><script>(_=>{var cv = $(\"#{$this->id}\")[0];var c = cv.getContext(\"2d\");new Chart(c,$config)})();";
        }
    }

?>