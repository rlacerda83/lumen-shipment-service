<?php

namespace App\Services\Shipment;

class Package {

    /**
     *
     * @var mixed integer or float - weight of package
     */
    protected $weight = null;

    /**
     *
     * @var mixed integer or float - length of package
     */
    protected $length = null;

    /**
     *
     * @var mixed integer or float - width of package
     */
    protected $width = null;

    /**
     *
     * @var mixed integer or float - height of package
     */
    protected $height = null;

    /**
     *
     * @var int calculated size of package (length plus girth)
     */
    protected $size = null;

    protected $declaredValue = null;

    /**
     *
     * @var array package options
     *
     * acceptable keys are:
     *  string 'description'
     *  string|int 'type'
     *  float|int 'insured_amount'
     *  boolean 'signature_required'
     */
    protected $options = array();


    /**
     * Rounds a float UP to the next tenth (always rounds up) ie: 2.32 becomes 2.4, 3.58 becomes 3.6
     *
     * @version updated 12/09/2012
     * @since 12/09/2012
     * @param float $float the float to be rounded
     * @return float the rounded float
     */
    protected function roundUpToTenth($float) {
        // round each value UP to the next tenth
        return ceil($float * 10) / 10;
    }

    /**
     * @param $weight
     * @return $this
     *
     * @throws \Exception
     */
    public function setWeight($weight)
    {
        if (is_float($weight) && $weight > 0) {
            $this->weight = $this->roundUpToTenth($weight);
        } else {
            throw new \Exception('Invalid weight.');
        }

        return $this;
    }

    /**
     * @return float|int|mixed
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param $decalredValue
     *
     * @return $this
     * @throws \Exception
     */
    public function seDeclaredValue($decalredValue)
    {
        if (is_float($decalredValue)) {
            $this->declaredValue = $decalredValue;
        } else {
            throw new \Exception('Invalid value.');
        }

        return $this;
    }

    /**
     * @return float|int|mixed
     */
    public function getDeclaredValue()
    {
        return $this->declaredValue;
    }


    /**
     * @param $length
     *
     * @return $this
     * @throws \Exception
     */
    public function setLength($length)
    {
        if (is_float($length) && $length > 0) {
            $this->length = $this->roundUpToTenth($length);
        } else {
            throw new \Exception('Invalid length.');
        }

        return $this;
    }

    /**
     * @return float|int|mixed
     */
    public function getLenght()
    {
        return $this->length;
    }

    /**
     * @param $height
     *
     * @return $this
     * @throws \Exception
     */
    public function setHeight($height)
    {
        if (is_float($height) && $height > 0) {
            $this->height = $this->roundUpToTenth($height);
        } else {
            throw new \Exception('Invalid height.');
        }

        return $this;
    }

    /**
     * @return float|int|mixed
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param $width
     *
     * @return $this
     * @throws \Exception
     */
    public function setWidth($width)
    {
        if (is_float($width) && $width > 0) {
            $this->width = $this->roundUpToTenth($width);
        } else {
            throw new \Exception('Invalid width.');
        }

        return $this;
    }

    /**
     * @return float|int|mixed
     */
    public function getWidth()
    {
        return $this->width;
    }


    /**
     * Calculates the package's size (the length plus the girth)
     *
     * @version updated 01/14/2013
     * @since 12/04/2012
     * @return int the size (length plus girth of the package) and rounded
     */
    protected function calculatePackageSize() {
        return round($this->length + $this->calculatePackageGirth());
    }


    /**
     * Calculates the package's girth (the distance around the two smaller sides of the package or width + width
     *      + height + height
     *
     * @param int|float $width the width of the package (if null, the object property $this->width will be used)
     * @param int|float $height the height of the package (if null, the object property $this->height will be used)
     * @version updated 01/14/2013
     * @since 12/04/2012
     * @return int the girth of the package
     */
    public function calculatePackageGirth($width = null, $height = null) {
        // if values are null, fill them with the object properties
        if($width == null) {
            $width = $this->width;
        }
        if($height == null) {
            $height = $this->height;
        }
        // calculate and return the girth
        return 2 * ($width + $height);
    }

    /**
     * Converts an integer or float in pounds to pounds and ounces
     *
     * @param int|float $pounds pounds value to convert
     * @return array ['pounds'] and ['ounces']
     * @throws \UnexpectedValueException if $this->weight is not recognized as an integer or a float
     * @version 01/14/2013
     * @since 01/14/2013
     */
    public function convertWeightToPoundsOunces($pounds) {
        // initialize output
        $output = array();
        // see if the package weight is an integer
        if(is_integer($pounds)) {
            $output['pounds'] = intval($pounds);
            $output['ounces'] = 0;
        }
        // see if the package weight is a float
        elseif (is_float($pounds)) {
            // split the weight by the decimal point after setting to three decimal places (for uniformity)
            $w = explode('.', number_format($pounds, 3));
            // pounds are the first entry
            $output['pounds'] = intval($w[0]);
            // back up check in case integer is evaluated as a float
            if(isset($w[1])) {
                // format $w[1] back to a decimal of pounds (dividing by 1000 because it has 3 decimal places above)
                $w[1] = $w[1] / 1000;
                // convert second entry to ounces
                $ounces = 16 * $w[1];
                // round up to the tenth of an ounce
                $output['ounces'] = $this->roundUpToTenth($ounces);
            }
            else {
                $output['ounces'] = 0;
            }
        }
        // not an integer or a float
        else {
            throw new \UnexpectedValueException('Weight value (' . $this->weight . ') is not a float or an integer.');
        }
        // return array holding pounds and ounces
        return $output;
    }


    /**
     * Converts a weight in KG to pounds (rounded to hundreths)
     *
     * @param int|float $kg weight in KG
     * @return float weight in pounds
     * @throws \InvalidArgumentException if $kg is not numeric
     * @version 01/14/2013
     * @since 01/14/2013
     */
    public function convertKgToPounds($kg) {
        // make sure that supplied KG value is numeric
        if(! is_numeric($kg)) {
            throw new \InvalidArgumentException('Supplied KG value (' . $kg . ') is not numeric.');
        }
        // convert to pounds and round to hundreths
        return number_format($kg * 2.20462, 2);
    }


    /**
     * Converts a length in CM to inches (rounded to hundreths)
     *
     * @param int|float $cm length in CM
     * @return float length in inches
     * @throws \InvalidArgumentException if $cm is not numeric
     * @version 01/14/2013
     * @since 01/14/2013
     */
    public function convertCmToInches($cm) {
        // make sure that supplied CM value is numeric
        if(! is_numeric($cm)) {
            throw new \InvalidArgumentException('Supplied CM value (' . $cm . ') is not numeric.');
        }
        // convert to inches and round to hundreths
        return number_format($cm * 0.393701, 2);
    }

}