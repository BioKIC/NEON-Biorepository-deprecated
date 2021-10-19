<?php

/*
 * A class for multivariate polar coordinate plots
 *  (a.k.a. radar charts or spider/star plots).
 *
 *  Christopher D. Tyrrell, 2020
 *
 * This class is purely the plot view and should be used in conjunction with the
 * TraitPlotManager.php controller class.
 *
 * Default plot size is 400 x 400 px, but svg is scalable to any size by setting
 * viewport (width, height attributes) to desired sizes and viewbox to 0 0 400
 * 400. Line widths are controlled using css classes (e.g., PolarPlotAxisLine).
*/

class PolarPlot {

  private $PlotClass;
  private $PlotId;
  private $PlotWidth = 400;
  private $PlotHeight = 400;
  private $PlotCenter;
  private $PlotPadding = 4;       // the distance between the end of an axis and its label, in pixels
  private $PlotMargin = 16;       // space for label text, in pixels (browser font default = 16px)
  private $AxisNumber = 3;        // number of plot axes (or start unset and use count(DataValues)?)
  private $AxisRotation = 0;      // degrees clockwise from top dead center
  private $AxisLength;
  private $AxisLabels = array();
  private $TickNumber = 2;        // 1 = the outer edge, >1 = outer + inner, 0 = spokes only
  private $TickScale;
  private $DataValues = array();  // Future: consider making this into a 2D array holding data series
  private $Splines = array();
  private $SplineSmoothness = 8; // the number of iterations for the Barry-Goldman algorithm
  private $RadInterval;           // angle between axes, in radians
  private $RadTopPosition;        // "top" of the plot (endpoint of first axis), in radians
  public $ShowScale = 1;          // surpresses drawing of scale values when zero


  ## METHODS ##

  public function __construct($className = 'PolarPlot', $id = ''){
    $this->PlotClass = $className;
    $this->PlotId = $id;
    $this->PlotCenter = array('x' => $this->PlotWidth / 2, 'y' => $this->PlotHeight / 2);
    $this->AxisLength = min($this->PlotWidth, $this->PlotHeight)/2 - $this->PlotPadding - $this->PlotMargin;
    $this->setRadInterval();
    $this->setRadTopPosition();
	}

	public function __destruct(){
	}


  ### Public Methods ###

  public function setAxisNumber($n) {
    if(is_numeric($n) && $n > 2) {
      $this->AxisNumber = $n;
      $this->setRadInterval();
    } else {
      trigger_error("polar plots must have more than 2 axes;", E_USER_NOTICE);
    }
  }

  public function setAxisRotation($r) {
    // if $r is > 360 or < -360 I think it should just rollaround
    if(is_numeric($r)) {
      $this->AxisRotation = $r;
      $this->setRadTopPosition();
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

  public function setDataValues($d) {
    if(is_array($d)) { //check for $d[] numeric?
      if(!isset($this->AxisNumber)) { $this->setAxisNumber(count($d)); }
      if(isset($this->AxisNumber) && (count($d) != $this->AxisNumber)) {
        trigger_error("number of data points does not match number of axes;", E_USER_WARNING);
      }
      $this->DataValues = $d;
      $this->setTickScale();
      $this->setSplines();
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
      $this->PlotCenter = array('x' => $this->PlotWidth / 2, 'y' => $this->PlotHeight / 2);
      $this->AxisLength = min($this->PlotWidth, $this->PlotHeight)/2 - $this->PlotPadding - $this->PlotMargin;
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
    if(!$this->ShowScale) {
      return $this->axisSVG() . ' ' . $this->tickSVG() . ' ' . $this->axisLabelSVG() . ' ' . $this->splineSVG();
    } else {
      return $this->axisSVG() . ' ' . $this->tickSVG() . ' ' . $this->scaleSVG() . ' ' . $this->axisLabelSVG() . ' ' . $this->splineSVG();
    }
  }


  ### Private Methods ###

  private function makePolar($value, $axis) {
    $radDist = $this->RadTopPosition - ($axis * $this->RadInterval);
    $out['x'] = round($this->PlotCenter['x'] + $value * cos($radDist), 0);
    $out['y'] = round($this->PlotCenter['y'] - $value * sin($radDist), 0);
    return $out;
  }

  private function setSplines(){
    for($i = 0; $i < count($this->DataValues); $i++) {
      $ptidx = array();
      for($j = 0; $j < 4; $j++){
        $ptidx[] = ($i + $j) % count($this->DataValues);
      }
      $scaleFactor = $this->TickScale * $this->TickNumber;
      $spline1 = $this->makePolar($this->DataValues[$ptidx[0]] * $this->AxisLength / $scaleFactor, $i);
      $spline2 = $this->makePolar($this->DataValues[$ptidx[1]] * $this->AxisLength / $scaleFactor, $i + 1);
      $spline3 = $this->makePolar($this->DataValues[$ptidx[2]] * $this->AxisLength / $scaleFactor, $i + 2);
      $spline4 = $this->makePolar($this->DataValues[$ptidx[3]] * $this->AxisLength / $scaleFactor, $i + 3);
      $this->Splines[] = $this->CatmullRomSpline($spline1, $spline2, $spline3, $spline4);
    }
  }

  private function setRadInterval() {
    $this->RadInterval = (2 * pi()) / $this->AxisNumber;
  }

  private function setRadTopPosition() {
    $this->RadTopPosition = (pi() / 2) - deg2rad($this->AxisRotation);
  }

  private function setTickScale() {
    if(empty($this->DataValues)) {
      $this->TickScale = 1;
      return;
    }
    if(isset($this->TickNumber) && $this->TickNumber) {
      if(empty($this->DataValues)) {
        $this->TickScale = 1;
        return;
      }
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

  #### Graphic Functions ####
  private function axisSVG() {
    $svgStr = '';
    $radPos = $this->RadTopPosition;
    for($i = 0; $i < $this->AxisNumber; $i++) {
      $x2 = round($this->PlotCenter['x'] + $this->AxisLength * cos($radPos), 0);
      $y2 = round($this->PlotCenter['y'] - $this->AxisLength * sin($radPos), 0);
      $svgStr .= '<line x1="' . $this->PlotCenter['x'] . '" y1="' . $this->PlotCenter['y'] . '" x2="' . $x2 . '" y2="' . $y2 . '" class="' . $this->PlotClass . 'AxisLine" />' . PHP_EOL;
      $radPos -= $this->RadInterval;
    }
    return $svgStr;
  }

  private function axisLabelSVG() {
    $svgStr = '';
    $radPos = $this->RadTopPosition;
    $degRotation = $this->AxisRotation;
    for($i = 0; $i < $this->AxisNumber; $i++) {
      if(isset($this->AxisLabels[$i])) { $label = $this->AxisLabels[$i]; } else { $label = $i; }
      $x2 = round($this->PlotCenter['x'] + ($this->AxisLength + $this->PlotPadding) * cos($radPos), 0);
      $y2 = round($this->PlotCenter['y'] - ($this->AxisLength + $this->PlotPadding) * sin($radPos), 0);
      $svgStr .= '<text transform="translate(' . $x2 . ',' . $y2 . ') rotate(' . $degRotation . ')" class="' . $this->PlotClass . 'LabelText">' . $label . '</text>' . PHP_EOL;
      $radPos -= $this->RadInterval;
      $degRotation += rad2deg($this->RadInterval);
    }
    return $svgStr;
  }

  private function tickSVG() {
    $svgStr = '';
    if(isset($this->TickNumber) && $this->TickNumber) {
      $radPos = $this->RadTopPosition;
      $tickInterval = $this->AxisLength/$this->TickNumber;
      for($j = 1; $j <= $this->TickNumber; $j++){
        $tickRadius = round($tickInterval * $j, 1);
        $svgStr .= '<polyline class="' . $this->PlotClass . 'TickLine" points="';
        for($i = 0; $i <= $this->AxisNumber; $i++) {
          $x2 = round($this->PlotCenter['x'] + $tickRadius * cos($radPos), 0);
          $y2 = round($this->PlotCenter['y'] - $tickRadius * sin($radPos), 0);
          $svgStr .= $x2 . ',' . $y2 . ' ';
          $radPos -= $this->RadInterval;
        }
        $svgStr .= '" />' . PHP_EOL;
      }
    }
    return $svgStr;
  }

  private function scaleSVG() {
    $svgStr = '';
    if(!isset($this->TickScale)) {
      return $svgStr;
    }
    if(isset($this->TickNumber) && $this->TickNumber) {
      $tickInterval = $this->AxisLength / $this->TickNumber;
      for($j = 1; $j <= $this->TickNumber; $j++){
        $tickRadius = $this->PlotCenter['y'] - round($tickInterval * $j, 1);
        $svgStr .= '<text transform="translate(' . $this->PlotCenter['x'] . ',' . $tickRadius . ')" class="' . $this->PlotClass . 'ScaleText">' . $this->TickScale * $j . '</text>' . PHP_EOL;
      }
    }
    return $svgStr;
  }

  ##### Spline Functions #####
  private function splineSVG() {  // what does the function do if the splines are not set?
    $svgd = '';
    foreach ($this->Splines as $s) {
      $tInc = 1 / $this->SplineSmoothness;
      $t = $tInc;
      $firstPt = $this->BarryGoldmanSplinePoint($s, 0);
      $svgd .= '<path class="' . $this->PlotClass . 'FocalCurve" d="M' . $firstPt["x"] . ',' . $firstPt["y"] . ' L';
      for ($i = 0; $i < $this->SplineSmoothness; $i++) {
        $pt = $this->BarryGoldmanSplinePoint($s, $t);
        $svgd .= $pt["x"] . ',' . $pt["y"] . ' ';
        $t += $tInc;
      }
      $svgd .= '" />' . PHP_EOL;
    }
    return $svgd;
  }

  private function CatmullRomSpline($GivenPoint0, $GivenPoint1, $GivenPoint2, $GivenPoint3) {
    return array("p0" => $GivenPoint0, "p1" => $GivenPoint1, "p2" => $GivenPoint2, "p3" => $GivenPoint3);
  }

  private function BarryGoldmanSplinePoint($spline, $tVal) {
    /* Barry, PA, RN Goldman. 1988. A recursive evaluation algorithm for a class
     *  of Catmull-Rom splines. ACM SIGGRAPH Computer Graphics 22(4): 199–204
     *  https://doi.org/10.1145/378456.378511
    */
    $P0 = $spline['p0'];
    $P1 = $spline['p1'];
    $P2 = $spline['p2'];
    $P3 = $spline['p3'];
    $t0 = 0;
    $t1 = $this->NextBarryGoldmanDot($P0, $P1, $t0);
    $t2 = $this->NextBarryGoldmanDot($P1, $P2, $t1);
    $t3 = $this->NextBarryGoldmanDot($P2, $P3, $t2);
    $t = ($t2 - $t1) * $tVal + $t1;
    $nancheck = (($t0 == $t1) + ($t0 == $t2) + ($t0 == $t3) + ($t1 == $t2) + ($t1 == $t3) + ($t2 == $t3));
    if($nancheck > 0) {
      return array('x' => ($P1['x'] + $P2['x'])/2, 'y' => ($P1['x'] + $P2['x'])/2);
      # return the average of the center control points if the Barry Goldman algorithm divides by zero.
    } else {
      $A1 = array(
        'x' => ($t1 - $t) / ($t1 - $t0) * $P0['x'] + ($t - $t0) / ($t1 - $t0) * $P1['x'],
        'y' => ($t1 - $t) / ($t1 - $t0) * $P0['y'] + ($t - $t0) / ($t1 - $t0) * $P1['y']
      );
      $A2 = array(
        'x' => ($t2 - $t) / ($t2 - $t1) * $P1['x'] + ($t - $t1) / ($t2 - $t1) * $P2['x'],
        'y' => ($t2 - $t) / ($t2 - $t1) * $P1['y'] + ($t - $t1) / ($t2 - $t1) * $P2['y']
      );
      $A3 = array(
        'x' => ($t3 - $t) / ($t3 - $t2) * $P2['x'] + ($t - $t2) / ($t3 - $t2) * $P3['x'],
        'y' => ($t3 - $t) / ($t3 - $t2) * $P2['y'] + ($t - $t2) / ($t3 - $t2) * $P3['y']
      );
      $B1 = array(
        'x' => ($t2 - $t) / ($t2 - $t0) * $A1['x'] + ($t - $t0) / ($t2 - $t0) * $A2['x'],
        'y' => ($t2 - $t) / ($t2 - $t0) * $A1['y'] + ($t - $t0) / ($t2 - $t0) * $A2['y']
      );
      $B2 = array(
        'x' => ($t3 - $t) / ($t3 - $t1) * $A2['x'] + ($t - $t1) / ($t3 - $t1) * $A3['x'],
        'y' => ($t3 - $t) / ($t3 - $t1) * $A2['y'] + ($t - $t1) / ($t3 - $t1) * $A3['y']
      );
      $C = array(
        'x' => round(($t2 - $t) / ($t2 - $t1) * $B1['x'] + ($t - $t1) / ($t2 - $t1) * $B2['x'], 1),
        'y' => round(($t2 - $t) / ($t2 - $t1) * $B1['y'] + ($t - $t1) / ($t2 - $t1) * $B2['y'], 1)
      );
    }
    return $C;
  }

  private function NextBarryGoldmanDot($pt0, $pt1, $ti, $alpha=0.5) {
    # alpha = 0 ~ standard (uniform)
    # alpha = 0.5 ~ centripetal
    # alpha = 1 ~ chordal
    return sqrt((($pt1['x'] - $pt0['x']) ** 2 + ($pt1['y'] - $pt0['y']) ** 2)) ** $alpha + $ti;
  }

}
?>
