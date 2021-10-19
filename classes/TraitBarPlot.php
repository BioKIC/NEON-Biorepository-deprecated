<?php

/*
 * A class for bi- or uni-variate bar plots.
 *
 *  Christopher D. Tyrrell, 2021
 *
 * This class is for the plot view and should be used in conjunction with the
 * TraitPlotManager.php controller class.
 *
 * Default plot size is 400 x 400 px, but svg is scalable to any size by setting
 * viewport (width, height attributes) to desired sizes, and setting the viewbox
 * to match the specified plot size (e.g., 0 0 400 400). Line widths are
 * controlled using css classes (e.g., BarPlotAxisLine).
*/

class BarPlot {

  private $PlotClass;
  private $PlotId;
  private $PlotWidth = 400;
  private $PlotHeight = 400;
  private $PlotOrigin;
  private $PlotPadding = 4;       // the distance between the axis and its label, in pixels
  private $PlotMargin = 16;       // space for label text, in pixels (browser font default = 16px)
  private $AxisRotation = 0;      // 0 = vertical bars, 1 = horizontal bars
  private $AxisLength = array();
  private $AxisLabels = array();
  private $TickNumber = 2;        // 1 = the outer edge, >1 = outer + inner, 0 = spokes only
  private $TickScale;
  private $DataValues = array();  // Future: consider making this into a 2D array holding data series
  public $ShowScale = 1;          // 1 = scale values shown, 0 = scale values hidden


  ## METHODS ##

  public function __construct($className = 'BarPlot', $id = ''){
    $this->PlotClass = $className;
    $this->PlotId = $id;
    $this->resetPlotDimensionValues();
	}

	public function __destruct(){
	}


  ### Public Methods ###

  public function setAxisNumber($n) {
    if(is_numeric($n) && $n >= 0) {
      $this->AxisNumber = $n;
      //$this->setRadInterval();
    } else {
      trigger_error("plots cannot have negative number of axes;", E_USER_NOTICE);
    }
  }

  public function setAxisRotation($r) {
    if(is_numeric($r)) {
      $this->AxisRotation = $r;
    }
  }

  public function setTickNumber($n) {
    // max out ticks at the number of pixels in the axis since they're not visible anyway.
    if(is_numeric($n)) {
      if($n > $this->AxisLength) { $n = $this->AxisLength; }
      $this->TickNumber = $n;
    }
  }

  public function setAxisLabels($l) {
    if(is_array($l)) {
      // if(count($l) != $this->AxisNumber) {
      //   trigger_error("number of labels does not match axis number;", E_USER_WARNING);
      // }
      $this->AxisLabels = $l;
    }
  }

  public function setPlotMargin($m) {
    if(is_numeric($m)) {
      $this->PlotMargin = $m;
    }
  }

  public function setDataValues($d) { //check for numeric
     if(is_array($d)) {
       $this->DataValues = $d;
       $this->setTickScale();
       return 1;
     } else {
       return 0;
     }
  }

  public function setPlotDimensions($h, $w = -1) {
    if($w < 0) { $w = $h; }
    if(is_numeric($w) && is_numeric($h)) {
      $this->PlotHeight = $h;
      $this->PlotWidth = $w;
      $this->resetPlotDimensionValues();
    }
  }

  public function getPlotWidth() {
    return $this->PlotWidth;
  }

  public function getPlotHeight() {
    return $this->PlotHeight;
  }

  public function getNumDataValues() {
    return array_sum($this->DataValues);
  }

  public function display() {
    if($this->ShowScale) {
       return $this->axisSVG() . ' ' . $this->tickSVG() . ' ' . $this->axisLabelSVG() . ' ' . $this->barsSVG();
     } else {
       return $this->axisSVG() . ' ' . $this->tickSVG() . ' ' . $this->scaleSVG() . ' ' . $this->axisLabelSVG() . ' ' . $this->barsSVG();
    }
  }


  ### Private Methods ###
  private function resetPlotDimensionValues() {
    $this->PlotOrigin = array('x' => $this->PlotMargin + $this->PlotPadding, 'y' => $this->PlotHeight * -1);
    $this->AxisLength = array('x' => $this->PlotWidth - $this->PlotPadding - $this->PlotMargin, 'y' => $this->PlotHeight - $this->PlotPadding - $this->PlotMargin);
  }

  private function setTickScale() {
    if(empty($this->DataValues)) {
      $this->TickScale = 1;
      return;
    } else {
      if(isset($this->TickNumber) && $this->TickNumber) {
        $s1 = max($this->DataValues) / $this->TickNumber;
        $s2 = 10 ** (round(log10($s1), 0) - 1);
        if($s2 == 0) {
          $s3 = 1;
        } else {
          $s3 = ceil($s1 / $s2) * $s2;
        }
        if(is_nan($s3)) {
          $this->TickScale = 1;
        } else {
          $this->TickScale = $s3;
        }
      }
    }
  }

  #### Graphic Functions ####
  private function tickSVG() {   // tick lines
    $svgStr = '';
    if(isset($this->ShowScale) && $this->ShowScale) {
      if( (isset($this->TickNumber) && $this->TickNumber) OR (isset($this->AxisNumber) && $this->AxisNumber) ) {
        $barPlotId = uniqid("BarPlotGrid-", true);
        $tickXInterval = round($this->AxisLength['x'] / $this->AxisNumber, 1);
        $tickYInterval = round($this->AxisLength['y'] / $this->TickNumber, 1);
        $svgStr .= '<defs>'.PHP_EOL.'<pattern id="'.$barPlotId.'" width="'.$tickXInterval.'" height="'.$tickYInterval.'" patternUnits="userSpaceOnUse">'.PHP_EOL;
        $svgStr .= '<rect class="' . $this->PlotClass . 'GridBackground" width="'.$tickXInterval.'" height="'.$tickYInterval.'" />'.PHP_EOL;
        $svgStr .= '<path class="' . $this->PlotClass . 'GridLine" d="M '.$tickXInterval.' 0 L 0 0 0 '.$tickYInterval.'" fill="none" />'.PHP_EOL.'</pattern>'.PHP_EOL.'</defs>'.PHP_EOL;
        $svgStr .= '<rect x="0" y="0" width="'.$this->AxisLength['x'].'" height="'.$this->AxisLength['y'].'" fill="url(#'.$barPlotId.')" />'.PHP_EOL;
      }
    }
    return $svgStr;
  }

  private function axisLabelSVG() {   // x-axis text/label
    //<text x="5" y="105" >Jan</text>
  }

  private function scaleSVG() {   // y-axis text/label
    $svgStr = '';
    if(!isset($this->TickScale)) {
      return $svgStr;
    }
    if(isset($this->TickNumber) && $this->TickNumber) {
      $tickInterval = round($this->AxisLength['y'] / $this->TickNumber, 1);
      for($i = 0; $i < $this->TickNumber; $i++) {
        $yval = $tickInterval * -1 * $i;
        $svgStr .= '<text x="'.$this->PlotOrigin['x'].'" y="'.$yval.'">'.$this->TickScale * $i.'</text>'.PHP_EOL;
      }
    }
    return $svgStr;
  }

  private function axisSVG() {   // axes lines
    $svgStr = '';

    // for($i = 0; $i < $this->AxisNumber; $i++) {
    //   $x2 = round($this->PlotCenter['x'] + $this->AxisLength * cos($radPos), 0);
    //   $y2 = round($this->PlotCenter['y'] - $this->AxisLength * sin($radPos), 0);
    //   $svgStr .= '<line x1="' . $this->PlotCenter['x'] . '" y1="' . $this->PlotCenter['y'] . '" x2="' . $x2 . '" y2="' . $y2 . '" class="' . $this->PlotClass . 'AxisLine" />' . PHP_EOL;
    //   $radPos -= $this->RadInterval;
    // }
    return $svgStr;
  }

  private function barsSVG() {   // data graphics
    $svgStr = '';
    $traitSum = array_sum($this->DataValues);
    $xinterval = $this->AxisLength['x'] / count($this->DataValues);
    $xstart = $this->PlotOrigin['x'];
    foreach($this->DataValues as $k => $d) {
      $traitPercent = round(($d/$traitSum) * 100, 1);
      $svgStr .= '<rect x=' . $xstart . ' y=' . $this->PlotOrigin['y'] . ' height=' . $traitPercent * -1 . ' />'.PHP_EOL;
      $xstart += $xinterval;
    }
    return $svgStr;
  }
}
?>
