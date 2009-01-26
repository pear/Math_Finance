<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Math_Finance: Class of financial functions
 *
 * Assorted financial functions for interest rates, bonds, amortizations and time value of money calculations (annuities)
 * Same interface as Excel financial functions.
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   Math
 * @package    Math_Finance
 * @author     Original Author <alejandro.pedraza@dataenlace.com>
 * @copyright  2005 Alejandro Pedraza
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    CVS: $Id$
 * @link       http://pear.php.net/Math/Finance
 * @since      File available since Release 1.2.0
 */

// to be able to throw PEAR errors
require_once 'PEAR.php';

// precision of calculations
define('FINANCE_PRECISION', 1E-6);

// payment types
define('FINANCE_PAY_END', 0);
define('FINANCE_PAY_BEGIN', 1);

// types of daycount basis
define('FINANCE_COUNT_NASD', 0);
define('FINANCE_COUNT_ACTUAL_ACTUAL', 1);
define('FINANCE_COUNT_ACTUAL_360', 2);
define('FINANCE_COUNT_ACTUAL_365', 3);
define('FINANCE_COUNT_EUROPEAN', 4);

/**
 * Math_Finance: Main class
 *
 * @category   Math
 * @package    Math_Finance
 * @author     Original Author <alejandro.pedraza@dataenlace.com>
 * @copyright  2005 Alejandro Pedraza
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: @package_version@
 * @link       http://pear.php.net/Math/Finance
 * @since      Class available since Release 1.2.0
 */
class Math_Finance
{
    /*******************************************************************
    ** Interest Rates Conversion Functions                         *****
    *******************************************************************/

    /**
    * Returns the effective interest rate given the nominal rate and the number of compounding payments per year
    * Excel equivalent: EFFECT
    *
    * @param float      Nominal interest rate
    * @param int        Number of compounding payments per year
    * @return float     
    * @static
    * @access public
    */
    function effectiveRate($nominal_rate, $npery)
    {
        $npery = (int)$npery;
        if ($npery < 0) {
            return PEAR::raiseError('Number of compounding payments per year is not positive');
        }

        $effect = pow((1 + $nominal_rate / $npery), $npery) - 1;
        return $effect;
    }

    /**
    * Returns the nominal interest rate given the effective rate and the number of compounding payments per year
    * Excel equivalent: NOMINAL
    *
    * @param float      Effective interest rate
    * @param int        Number of compounding payments per year
    * @return float     
    * @static
    * @access public
    */
    function nominalRate($effect_rate, $npery)
    {
        $npery = (int)$npery;
        if ($npery < 0) {
            return PEAR::raiseError('Number of compounding payments per year is not positive');
        }

        $nominal = $npery * (pow($effect_rate + 1, 1/$npery) - 1);
        return $nominal;
    }


    /*******************************************************************
    ** TVM (annuities) Functions                                   *****
    *******************************************************************/

    /**
    * Returns the Present Value of a cash flow with constant payments and interest rate (annuities)
    * Excel equivalent: PV
    *
    *   TVM functions solve for a term in the following formula:
    *   pv(1+r)^n + pmt(1+r.type)((1+r)^n - 1)/r) +fv = 0
    *
    *
    * @param float      Interest rate per period 
    * @param int        Number of periods
    * @param float      Periodic payment (annuity)
    * @param float      Future Value
    * @param int        Payment type:
                            FINANCE_PAY_END (default):    at the end of each period
                            FINANCE_PAY_BEGIN:            at the beginning of each period
    * @return float     
    * @static
    * @access public
    */
    function presentValue($rate, $nper, $pmt, $fv = 0, $type = 0)
    {
        if ($nper < 0) {
            return PEAR::raiseError('Number of periods must be positive');
        }
        if ($type != FINANCE_PAY_END && $type != FINANCE_PAY_BEGIN) {
            return PEAR::raiseError('Payment type must be FINANCE_PAY_END or FINANCE_PAY_BEGIN');
        }

        if ($rate) {
            $pv = (-$pmt * (1 + $rate * $type) * ((pow(1 + $rate, $nper) - 1) / $rate) - $fv) / pow(1 + $rate, $nper);
        } else {
            $pv = -$fv - $pmt * $nper;
        }
        return $pv;
    }

    /**
    * Returns the Future Value of a cash flow with constant payments and interest rate (annuities)
    * Excel equivalent: FV
    *
    * @param float      Interest rate per period 
    * @param int        Number of periods
    * @param float      Periodic payment (annuity)
    * @param float      Present Value
    * @param int        Payment type:
                            FINANCE_PAY_END (default):    at the end of each period
                            FINANCE_PAY_BEGIN:            at the beginning of each period
    * @return float     
    * @static
    * @access public
    */
    function futureValue($rate, $nper, $pmt, $pv = 0, $type = 0)
    {
        if ($nper < 0) {
            return PEAR::raiseError('Number of periods must be positive');
        }
        if ($type != FINANCE_PAY_END && $type != FINANCE_PAY_BEGIN) {
            return PEAR::raiseError('Payment type must be FINANCE_PAY_END or FINANCE_PAY_BEGIN');
        }

        if ($rate) {
            $fv = -$pv * pow(1 + $rate, $nper) - $pmt * (1 + $rate * $type) * (pow(1 + $rate, $nper) - 1) / $rate;
        } else {
            $fv = -$pv - $pmt * $nper;
        }
        return $fv;
    }

    /**
    * Returns the constant payment (annuity) for a cash flow with a constant interest rate
    * Excel equivalent: PMT
    *
    * @param float      Interest rate per period 
    * @param int        Number of periods
    * @param float      Present Value
    * @param float      Future Value
    * @param int        Payment type:
                            FINANCE_PAY_END (default):    at the end of each period
                            FINANCE_PAY_BEGIN:            at the beginning of each period
    * @return float     
    * @static
    * @access public
    */
    function payment($rate, $nper, $pv, $fv = 0, $type = 0)
    {
        if ($nper < 0) {
            return PEAR::raiseError('Number of periods must be positive');
        }
        if ($type != FINANCE_PAY_END && $type != FINANCE_PAY_BEGIN) {
            return PEAR::raiseError('Payment type must be FINANCE_PAY_END or FINANCE_PAY_BEGIN');
        }

        if ($rate) {
            $pmt = (-$fv - $pv * pow(1 + $rate, $nper)) / (1 + $rate * $type) / ((pow(1 + $rate, $nper) - 1) / $rate);
        } else {
            $pmt = (-$pv - $fv) / $nper;
        }
        return $pmt;
    }

    /**
    * Returns the number of periods for a cash flow with constant periodic payments (annuities), and interest rate
    * Excel equivalent: NPER
    *
    * @param float      Interest rate per period 
    * @param float      Periodic payment (annuity)
    * @param float      Present Value
    * @param float      Future Value
    * @param int        Payment type:
                            FINANCE_PAY_END (default):    at the end of each period
                            FINANCE_PAY_BEGIN:            at the beginning of each period
    * @return float
    * @static
    * @access public
    */
    function periods($rate, $pmt, $pv, $fv = 0, $type = 0)
    {
        if ($type != FINANCE_PAY_END && $type != FINANCE_PAY_BEGIN) {
            return PEAR::raiseError('Payment type must be FINANCE_PAY_END or FINANCE_PAY_BEGIN');
        }

        if ($rate) {
            if ($pmt == 0 && $pv == 0) {
                return PEAR::raiseError('Payment and Present Value can\'t be both zero when the rate is not zero');
            }
            $nper = log(($pmt * (1 + $rate * $type) / $rate - $fv) / ($pv + $pmt * (1 + $rate * $type) / $rate))
                     / log(1 + $rate);
        } else {
            if ($pmt == 0) {
                return PEAR::raiseError('Rate and Payment can\'t be both zero');
            }
            $nper = (-$pv -$fv) / $pmt;
        }
        return $nper;
    }

    /**
    * Returns the periodic interest rate for a cash flow with constant periodic payments (annuities)
    * Excel equivalent: RATE
    *
    * @param int        Number of periods
    * @param float      Periodic payment (annuity)
    * @param float      Present Value
    * @param float      Future Value
    * @param int        Payment type:
                            FINANCE_PAY_END (default):    at the end of each period
                            FINANCE_PAY_BEGIN:            at the beginning of each period
    * @param float      guess for the interest rate
    * @return float     
    * @static
    * @access public
    */
    function rate($nper, $pmt, $pv, $fv = 0, $type = 0, $guess = 0.1)
    {
        // To solve the equation
        require_once 'Math/Numerical/RootFinding/NewtonRaphson.php';
        // To preserve some variables in the Newton-Raphson callback functions
        require_once 'Math/Finance_FunctionParameters.php';

        if ($type != FINANCE_PAY_END && $type != FINANCE_PAY_BEGIN) {
            return PEAR::raiseError('Payment type must be FINANCE_PAY_END or FINANCE_PAY_BEGIN');
        }

        // Utilization of a Singleton class to preserve given values of other variables in the callback functions
        $parameters = array(
            'nper'  => $nper,
            'pmt'   => $pmt,
            'pv'    => $pv,
            'fv'    => $fv,
            'type'  => $type,
        );
		$parameters_class =& Math_Finance_FunctionParameters::getInstance($parameters, True);

        $newtonRaphson = new Math_Numerical_RootFinding_Newtonraphson(array('err_tolerance' => FINANCE_PRECISION));
        return $newtonRaphson->compute(array('Math_Finance', '_tvm'), array('Math_Finance', '_dtvm'), $guess);
    }

    /**
    * Callback function only used by Newton-Raphson algorithm. Returns value of function to be solved.
    *
    * Uses a previously instanced Singleton class to retrieve given values of other variables in the function
    *
    * @param float      Interest rate
    * @return float     
    * @static
    * @access private
    */
    function _tvm($rate)
    {
        require_once 'Math/Finance_FunctionParameters.php';

		$parameters_class =& Math_Finance_FunctionParameters::getInstance();
        $nper   = $parameters_class->parameters['nper'];
        $pmt    = $parameters_class->parameters['pmt'];
        $pv     = $parameters_class->parameters['pv'];
        $fv     = $parameters_class->parameters['fv'];
        $type   = $parameters_class->parameters['type'];

        return $pv * pow(1 + $rate, $nper) + $pmt * (1 + $rate * $type) * (pow(1 + $rate, $nper) - 1) / $rate + $fv;
    }

    /**
    * Callback function only used by Newton-Raphson algorithm. Returns value of derivative of function to be solved.
    *
    * Uses a previously instanced Singleton class to retrieve given values of other variables in the function
    *
    * @return float     
    * @static
    * @access private
    */
    function _dtvm($rate)
    {
        require_once 'Math/Finance_FunctionParameters.php';

		$parameters_class =& Math_Finance_FunctionParameters::getInstance();
        $nper   = $parameters_class->parameters['nper'];
        $pmt    = $parameters_class->parameters['pmt'];
        $pv     = $parameters_class->parameters['pv'];
        $type   = $parameters_class->parameters['type'];

        return $nper * $pv * pow(1 + $rate, $nper - 1)
                 + $pmt *
                     ($type * (pow(1 + $rate, $nper) - 1) / $rate
                     + (1 + $rate * $type) * ($nper * $rate * pow(1 + $rate, $nper - 1) - pow(1 + $rate, $nper) + 1) / pow($rate,2));
    }

    /**
    * Returns the interest payment for a given period for a cash flow with constant periodic payments (annuities)
    * and interest rate.
    * Excel equivalent: IMPT
    *
    * @param float      Interest rate per period 
    * @param int        Period for which the interest payment will be calculated
    * @param int        Number of periods
    * @param float      Present Value
    * @param float      Future Value
    * @param int        Payment type:
                            FINANCE_PAY_END (default):    at the end of each period
                            FINANCE_PAY_BEGIN:            at the beginning of each period
    * @return float     
    * @static
    * @access public
    */
    function interestPayment($rate, $per, $nper, $pv, $fv = 0, $type = 0)
    {
        if ($type != FINANCE_PAY_END && $type != FINANCE_PAY_BEGIN) {
            return PEAR::raiseError('Payment type must be FINANCE_PAY_END or FINANCE_PAY_BEGIN');
        }

        $interestAndPrincipal = Math_Finance::_interestAndPrincipal($rate, $per, $nper, $pv, $fv, $type);
        return $interestAndPrincipal[0];
    }

    /**
    * Returns the principal payment for a given period for a cash flow with constant periodic payments (annuities)
    * and interest rate
    * Excel equivalent: PPMT
    *
    * @param float      Interest rate per period 
    * @param int        Period for which the principal payment will be calculated
    * @param int        Number of periods
    * @param float      Present Value
    * @param float      Future Value
    * @param int        Payment type:
                            FINANCE_PAY_END (default):    at the end of each period
                            FINANCE_PAY_BEGIN:            at the beginning of each period
    * @return float     
    * @static
    * @access public
    */
    function principalPayment($rate, $per, $nper, $pv, $fv = 0, $type = 0)
    {
        if ($type != FINANCE_PAY_END && $type != FINANCE_PAY_BEGIN) {
            return PEAR::raiseError('Payment type must be FINANCE_PAY_END or FINANCE_PAY_BEGIN');
        }

        $interestAndPrincipal = Math_Finance::_interestAndPrincipal($rate, $per, $nper, $pv, $fv, $type);
        return $interestAndPrincipal[1];
    }

    /**
    * Returns the interest and principal payment for a given period for a cash flow with constant 
    * periodic payments (annuities) and interest rate
    *
    * @param float      Interest rate per period 
    * @param int        Number of periods
    * @param float      Present Value
    * @param float      Future Value
    * @param int        Payment type:
                            FINANCE_PAY_END (default):    at the end of each period
                            FINANCE_PAY_BEGIN:            at the beginning of each period
    * @return array
    * @static
    * @access private
    */
    function _interestAndPrincipal($rate, $per, $nper, $pv, $fv, $type)
    {
        $pmt = Math_Finance::payment($rate, $nper, $pv, $fv, $type);
        //echo "pmt: $pmt\n\n";
        $capital = $pv;
        for ($i = 1; $i<= $per; $i++) {
            // in first period of advanced payments no interests are paid
            $interest = ($type && $i == 1)? 0 : -$capital * $rate;
            $principal = $pmt - $interest;
            $capital += $principal;
            //echo "$i\t$capital\t$interest\t$principal\n";
        }
        return array($interest, $principal);
    }

    /*******************************************************************
    ** Cash Flow Functions                                        *****
    *******************************************************************/

    /**
    * Returns the Net Present Value of a cash flow series given a discount rate
    * Excel equivalent: NPV
    *
    * @param float      Discount interest rate
    * @param array      Cash flow series
    * @return float     
    * @static
    * @access public
    */

    function netPresentValue($rate, $values)
    {
        if (!is_array($values)) {
            return PEAR::raiseError('The cash flow series most be an array');
        }
    
        return MATH_Finance::_npv($rate, $values);
    }

    /**
    * Returns the internal rate of return of a cash flow series
    * Excel equivalent: IRR
    *
    * @param array      Cash flow series
    * @param float      guess for the interest rate
    * @return float     
    * @static
    * @access public
    */
    function internalRateOfReturn($values, $guess = 0.1)
    {
        // To solve the equation
        require_once 'Math/Numerical/RootFinding/NewtonRaphson.php';
        // To preserve some variables in the Newton-Raphson callback functions
        require_once 'Math/Finance_FunctionParameters.php';

        if (!is_array($values)) {
            return PEAR::raiseError('The cash flow series most be an array');
        }
        if (min($values) * max($values) >= 0) {
            return PEAR::raiseError('Cash flow must contain at least one positive value and one negative value');
        }

		$parameters_class =& Math_Finance_FunctionParameters::getInstance(array('values' => $values), True);
        $newtonRaphson = new Math_Numerical_RootFinding_Newtonraphson(array('err_tolerance' => FINANCE_PRECISION));
        return $newtonRaphson->compute(array('Math_Finance', '_npv'), array('Math_Finance', '_dnpv'), $guess);
    }

    /**
    * Function used by NPV() and as a callback by Newton-Raphson algorithm.
    * Returns value of Net Present Value of a cash flow series.
    *
    * Uses a previously instanced Singleton class to retrieve given values of other variables in the function
    *
    * @param float      Discount interest rate
    * @param array      Cash flow series
    * @return float     
    * @static
    * @access private
    */
    function _npv($rate, $values = array())
    {
        require_once 'Math/Finance_FunctionParameters.php';

        if (!$values) {
            // called from IRR
		    $parameters_class =& Math_Finance_FunctionParameters::getInstance();
            $values = $parameters_class->parameters['values'];
        }

        $npv = 0;
        $nper = count($values);
        for ($i = 1; $i <= $nper; $i++) {
            $npv += $values[$i-1]/ pow(1 + $rate, $i);
        }
        return $npv;
    }

    /**
    * Callback function used by by Newton-Raphson algorithm to calculate IRR.
    * Returns value of derivative function to be solved.
    *
    * Uses a previously instanced Singleton class to retrieve given values of other variables in the function
    *
    * @param float      Discount interest rate
    * @param array      Cash flow series
    * @return float     
    * @static
    * @access private
    */
    function _dnpv($rate, $values = array())
    {
        require_once 'Math/Finance_FunctionParameters.php';

        if (!$values) {
            // called from IRR
		    $parameters_class =& Math_Finance_FunctionParameters::getInstance();
            $values = $parameters_class->parameters['values'];
        }

        $dnpv = 0;
        $nper = count($values);
        for ($i = 1; $i <= $nper; $i++) {
            $dnpv += $values[$i-1] * (-$i) * pow(1 + $rate, $i - 1) / pow(1 + $rate, 2 * $i);
        }
        return $dnpv;
    }

    /**
    * Returns the internal rate of return of a cash flow series, considering both financial and reinvestment rates
    * Excel equivalent: MIRR
    *
    * @param array      Cash flow series
    * @param float      Interest rate on the money used in the cash flow
    * @param float      Interest rate received when reinvested
    * @return float     
    * @static
    * @access public
    */
    function modifiedInternalRateOfReturn($values, $finance_rate, $reinvest_rate)
    {
        if (!is_array($values)) {
            return PEAR::raiseError('The cash flow series most be an array');
        }
        if (min($values) * max($values) >= 0) {
            return PEAR::raiseError('Cash flow must contain at least one positive value and one negative value');
        }

        $positive_flows = $negative_flows = array();
        foreach ($values as $value) {
            if ($value >= 0) {
                $positive_flows[] = $value;
                $negative_flows[] = 0;
            } else {
                $positive_flows[] = 0;
                $negative_flows[] = $value;
            }
        }

        $nper = count($values);

        return pow(-Math_Finance::netPresentValue($reinvest_rate, $positive_flows) * pow(1 + $reinvest_rate, $nper)
                / Math_Finance::netPresentValue($finance_rate, $negative_flows) / (1 + $finance_rate), 1/($nper - 1)) - 1;
    }

    /*******************************************************************
    ** Bonds Functions                                             *****
    *******************************************************************/

    /**
    * Returns the difference of days between two dates based on a daycount basis
    *
    * @param int        First date (UNIX timestamp)
    * @param int        Second date (UNIX timestamp)
    * @param int        Type of day count basis:
                            FINANCE_COUNT_NASD(default):    US(NASD) 30/360
                            FINANCE_COUNT_ACTUAL_ACTUAL:    Actual/actual
                            FINANCE_COUNT_ACTUAL_360:       Actual/360
                            FINANCE_COUNT_ACTUAL_365:       Actual/365
                            FINANCE_COUNT_EUROPEAN:         European 30/360
    * @return int
    * @static
    * @access public
    */
    function daysDifference($date1, $date2, $basis)
    {
        $y1 = date('Y', $date1);
        $m1 = date('n', $date1);
        $d1 = date('j', $date1);
        $y2 = date('Y', $date2);
        $m2 = date('n', $date2);
        $d2 = date('j', $date2);

        switch ($basis) {
            case FINANCE_COUNT_NASD:
                if ($d2 == 31 && ($d1 == 30 || $d1 == 31)) {
                    $d2 = 30;
                }
                if ($d1 == 31) {
                    $d1 = 30;
                }
                return ($y2 - $y1) * 360 + ($m2 - $m1) * 30 + $d2 - $d1;
            case FINANCE_COUNT_ACTUAL_ACTUAL:
            case FINANCE_COUNT_ACTUAL_360:
            case FINANCE_COUNT_ACTUAL_365:
                return ($date2 - $date1) / 86400;
            case FINANCE_COUNT_EUROPEAN: // European 30/360
                return ($y2 - $y1) * 360 + ($m2 - $m1) * 30 + $d2 - $d1;
        }
    }

    /**
    * Returns the number of days in the year based on a daycount basis
    *
    * @param int        Year
    * @param int        Type of day count basis:
                            FINANCE_COUNT_NASD(default):    US(NASD) 30/360
                            FINANCE_COUNT_ACTUAL_ACTUAL:    Actual/actual
                            FINANCE_COUNT_ACTUAL_360:       Actual/360
                            FINANCE_COUNT_ACTUAL_365:       Actual/365
                            FINANCE_COUNT_EUROPEAN:         European 30/360
    * @return int
    * @static
    * @access public
    */
    function daysPerYear($year, $basis)
    {
        switch ($basis) {
            case FINANCE_COUNT_NASD:
                return 360;
            case FINANCE_COUNT_ACTUAL_ACTUAL:
                return checkdate(2, 29, $year)? 366 : 365;
            case FINANCE_COUNT_ACTUAL_360:
                return 360;
            case FINANCE_COUNT_ACTUAL_365:
                return 365;
            case FINANCE_COUNT_EUROPEAN:
                return 360;
        }
    }

    /**
    * Returns the yield for a treasury bill
    * Excel equivalent: TBILLYIELD
    *
    * @param int        Settlement date (UNIX timestamp)
    * @param int        Maturity date (UNIX timestamp)
    * @param float      TBill price per $100 face value
    * @return float     
    * @static
    * @access public
    */
    function TBillYield($settlement, $maturity, $pr)
    {
        if ($settlement >= $maturity) {
            return PEAR::raiseError('Maturity must happen before settlement!');
        }

        $dsm = ($maturity - $settlement) / 86400;   // transform to days

        if ($dsm > 360) {
            return PEAR::raiseError("maturity can't be more than one year after settlement");
        }

        return (100 - $pr) * 360 / $pr / $dsm;
    }

    /**
    * Returns the price per $100 face value for a Treasury bill
    * Excel equivalent: TBILLPRICE
    *
    * @param int        Settlement date (UNIX timestamp)
    * @param int        Maturity date (UNIX timestamp)
    * @param float      T-Bill discount rate
    * @return float     
    * @static
    * @access public
    */
    function TBillPrice($settlement, $maturity, $discount)
    {
        if ($settlement >= $maturity) {
            return PEAR::raiseError('Maturity must happen before settlement!');
        }

        $dsm = ($maturity - $settlement) / 86400;   // transform to days

        if ($dsm > 360) {
            return PEAR::raiseError("maturity can't be more than one year after settlement");
        }

        return 100 * (1 - $discount * $dsm / 360);
    }

    /**
    * Returns the bond-equivalent yield for a Treasury bill
    * Excel equivalent: TBILLEQ
    *
    * @param int        Settlement date (UNIX timestamp)
    * @param int        Maturity date (UNIX timestamp)
    * @param float      T-Bill discount rate
    * @return float     
    * @static
    * @access public
    */
    function TBillEquivalentYield($settlement, $maturity, $discount)
    {
        if ($settlement >= $maturity) {
            return PEAR::raiseError('Maturity must happen before settlement!');
        }

        $dsm = Math_Finance::daysDifference($settlement, $maturity, FINANCE_COUNT_ACTUAL_365);

        if ($dsm <= 182) {
            // for one half year or less, the bond-equivalent-yield is equivalent to an actual/365 interest rate
            return 365 * $discount / (360 - $discount * $dsm);
        } elseif ($dsm == 366 
                  && ((date('m', $settlement) <= 2 && checkdate(2, 29, date('Y', $settlement))) 
                  || (date('m', $settlement) > 2 && checkdate(2, 29, date('Y', $maturity))))) {
            return 2 * (sqrt(1 - $discount * 366 / ($discount * 366 - 360)) - 1);
        } elseif ($dsm > 365) {
            return PEAR::raiseError("maturity can't be more than one year after settlement");
        } else {
            // thanks to Zhang Qingpo (zhangqingpo@yahoo.com.cn) for solving this riddle :)
            return (-$dsm + sqrt(pow($dsm, 2) - (2 * $dsm - 365) * $discount * $dsm * 365 / ($discount * $dsm - 360))) / ($dsm - 365 / 2);
        }
    }

    /**
    * Returns the discount rate for a bond
    * Excel equivalent: DISC
    *
    * @param int        Settlement date (UNIX timestamp)
    * @param int        Maturity date (UNIX timestamp)
    * @param float      The bond's price per $100 face value
    * @param float      The bond's redemption value per $100 face value
    * @param int        Type of day count basis:
                            FINANCE_COUNT_NASD(default):    US(NASD) 30/360
                            FINANCE_COUNT_ACTUAL_ACTUAL:    Actual/actual
                            FINANCE_COUNT_ACTUAL_360:       Actual/360
                            FINANCE_COUNT_ACTUAL_365:       Actual/365
                            FINANCE_COUNT_EUROPEAN:         European 30/360
    * @return float     
    * @static
    * @access public
    */
    function discountRate($settlement, $maturity, $pr, $redemption, $basis = 0)
    {
        $days_per_year = Math_Finance::daysPerYear(date('Y', $settlement), $basis);
        $dsm = Math_Finance::daysDifference($settlement, $maturity, $basis);

        return ($redemption - $pr) * $days_per_year / $redemption / $dsm;
    }

    /**
    * Returns the price per $100 face value of a discounted bond
    * Excel equivalent: PRICEDISC
    *
    * @param int        Settlement date (UNIX timestamp)
    * @param int        Maturity date (UNIX timestamp)
    * @param float      The bond's discount rate
    * @param float      The bond's redemption value per $100 face value
    * @param int        Type of day count basis:
                            FINANCE_COUNT_NASD(default):    US(NASD) 30/360
                            FINANCE_COUNT_ACTUAL_ACTUAL:    Actual/actual
                            FINANCE_COUNT_ACTUAL_360:       Actual/360
                            FINANCE_COUNT_ACTUAL_365:       Actual/365
                            FINANCE_COUNT_EUROPEAN:         European 30/360
    * @return float     
    * @static
    * @access public
    */
    function priceDiscount($settlement, $maturity, $discount, $redemption, $basis = 0)
    {
        $days_per_year = Math_Finance::daysPerYear(date('Y', $settlement), $basis);
        $dsm = Math_Finance::daysDifference($settlement, $maturity, $basis);

        return $redemption - $discount * $redemption * $dsm / $days_per_year;
    }


    /*******************************************************************
    ** Depreciation Functions                                      *****
    *******************************************************************/

    /**
    * Returns the depreciation of an asset using the fixed-declining balance method
    * Excel equivalent: DB
    *
    * @param float      The initial cost of the asset
    * @param float      Salvage value of the asset
    * @param int        Number of depreciation periods (same unit as $life)
    * @param int        Number of months in the first year, defaults to 12
    * @return float     
    * @static
    * @access public
    */
    function depreciationFixedDeclining($cost, $salvage, $life, $period, $month = 12)
    {
        $cost       = (float) $cost;
        $salvage    = (float) $salvage;
        $life       = (int) $life;
        $period     = (int) $period;
        $month      = (int) $month;
        if ($cost < 0 || $life < 0) {
            return PEAR::raiseError('cost and life must be absolute positive numbers');
        }
        if ($period < 1) {
            return PEAR::raiseError('period must be greater or equal than one');
        }

        $rate = 1 - pow(($salvage / $cost), (1 / $life));
        $rate = round($rate, 3);

        $acc_depreciation = 0;
        for ($i = 1; $i <= $period; $i++) {
            if ($i == 1) {
                $depreciation_period = $cost * $rate * $month / 12;
            } elseif ($i == ($life + 1)) {
                $depreciation_period = ($cost - $acc_depreciation) * $rate * (12 - $month) / 12;
            } else {
                $depreciation_period = ($cost - $acc_depreciation) * $rate;
            }
            $acc_depreciation += $depreciation_period;
        }

        return $depreciation_period;
    }

    /**
    * Returns the straight-line depreciation of an asset for each period
    * Excel equivalent: SLN
    *
    * @param float      The initial cost of the asset
    * @param float      Salvage value of the asset
    * @param int        Number of depreciation periods
    * @return float     
    * @static
    * @access public
    */
    function depreciationStraightLine($cost, $salvage, $life)
    {
        $life       = (int) $life;
        if ($cost < 0 || $life < 0) {
            return PEAR::raiseError('cost and life must be absolute positive numbers');
        }

        return (($cost - $salvage) / $life);
    }

    /**
    * Returns the depreciation for an asset in a given period using the sum-of-years' digits method
    * Excel equivalent: SYD
    *
    * @param float      The initial cost of the asset
    * @param float      Salvage value of the asset
    * @param int        Number of depreciation periods
    * @param int        Period (must be in the same unit as $life)
    * @return float     
    * @static
    * @access public
    */
    function depreciationSYD($cost, $salvage, $life, $per)
    {
        return (($cost - $salvage) * ($life - $per + 1) * 2 / ($life) / ($life +1));
    }
}

?>
