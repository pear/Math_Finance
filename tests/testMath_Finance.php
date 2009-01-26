<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

//require_once 'Math/Finance.php';
require_once 'Math/Finance.php';
require_once 'PHPUnit.php';

Class FinanceTestCase extends PHPUnit_TestCase
{
	var $finance;

	/*function testFunctionName()
	{
		$this->assertTrue(expr)
		$this->assertFalse(expr)
		$this->assertEquals(expected, actual) // uses ==
		$this->assertNull(expr)
		$this->assertSame(expected, actual)	// uses ===
		$this->assertNotSame(expcted, actual)
		$this->assertRegExp(expected, actual)	// PCRE regexpses
	}*/

	function setUp()
	{
	}

    /*******************************************************************
    ** Interest Rates Conversion Functions                         *****
    *******************************************************************/

	function testeffectiveRate()
	{
		// various random calculations
		$this->assertTrue(abs(0.14597025 - Math_Finance::effectiveRate(0.141, 2)) < FINANCE_PRECISION);
		$this->assertTrue(abs(0.14553980 - Math_Finance::effectiveRate(0.139, 3)) < FINANCE_PRECISION);
		$this->assertTrue(abs(0.00903042 - Math_Finance::effectiveRate(0.009, 4)) < FINANCE_PRECISION);
		$this->assertTrue(abs(0.44 - Math_Finance::effectiveRate(0.4, 2)) < FINANCE_PRECISION);
		$this->assertTrue(abs(0.141 - Math_Finance::effectiveRate(0.141, 1)) < FINANCE_PRECISION);
		$this->assertTrue(abs(0.14694625 - Math_Finance::effectiveRate(0.1390, 5)) < FINANCE_PRECISION);
		$this->assertTrue(abs(0.00904009 - Math_Finance::effectiveRate(0.0090, 77)) < FINANCE_PRECISION);
		$this->assertTrue(abs(0.48285538 - Math_Finance::effectiveRate(0.40, 13)) < FINANCE_PRECISION);

		// cannot pass negative number of periods per year
		$this->assertType('object', Math_Finance::effectiveRate(0.40, -13));
	}

	function testnominalRate()
	{
		// various random calculations
		$this->assertTrue(abs(0.497999 - Math_Finance::nominalRate(0.56, 2)) < FINANCE_PRECISION);
		$this->assertTrue(abs(0.303643 - Math_Finance::nominalRate(0.34, 4)) < FINANCE_PRECISION);
		$this->assertTrue(abs(0.611767 - Math_Finance::nominalRate(0.7450, 3)) < FINANCE_PRECISION);
		$this->assertTrue(abs(0.117417 - Math_Finance::nominalRate(0.1245, 88)) < FINANCE_PRECISION);
		$this->assertTrue(abs(0.031554 - Math_Finance::nominalRate(0.0320, 9)) < FINANCE_PRECISION);
		$this->assertTrue(abs(0.263683 - Math_Finance::nominalRate(0.2930, 5)) < FINANCE_PRECISION);

		// cannot pass negative number of periods per year
		$this->assertType('object', Math_Finance::nominalRate(0.2930, -5));
	}


    /*******************************************************************
    ** TVM (annuities) Functions                                   *****
    *******************************************************************/

	function testpresentValue()
	{
		// various random calculations
		$this->assertTrue(abs(-4909.073704 - Math_Finance::presentValue(0.08, 20, 500)) < FINANCE_PRECISION);
		$this->assertTrue(abs(-915.941437 - Math_Finance::presentValue(0.03, 5, 200)) < FINANCE_PRECISION);
		$this->assertTrue(abs(-286.821438 - Math_Finance::presentValue(0.29, 7, 100)) < FINANCE_PRECISION);
		$this->assertTrue(abs(-700.000000 - Math_Finance::presentValue(0, 7, 100)) < FINANCE_PRECISION);

		// cannot pass negative number of periods
		$this->assertType('object', Math_Finance::presentValue(0.29, -7, 100));
		// cannot pass a type different from 0 and 1
		$this->assertType('object', Math_Finance::presentValue(0.29, 7, 100, 0, 3));
	}

	function testfutureValue()
	{
		// various random calculations
		$this->assertTrue(abs(-22880.982149 - Math_Finance::futureValue(0.08, 20, 500)) < FINANCE_PRECISION);
		$this->assertTrue(abs(-1061.827162 - Math_Finance::futureValue(0.03, 5, 200)) < FINANCE_PRECISION);
		$this->assertTrue(abs(-1705.059664 - Math_Finance::futureValue(0.29, 7, 100)) < FINANCE_PRECISION);
		$this->assertTrue(abs(-700.000000 - Math_Finance::futureValue(0, 7, 100)) < FINANCE_PRECISION);

		// cannot pass negative number of periods
		$this->assertType('object', Math_Finance::futureValue(0.29, -7, 100));
		// cannot pass a type differenet from 0 and 1
		$this->assertType('object', Math_Finance::futureValue(0.29, 7, 100, 0, 3));
	}

	function testpayment()
	{
		// various random calculations
		$this->assertTrue(abs(-36.157534 - Math_Finance::payment(0.08, 20, 355)) < FINANCE_PRECISION);
		$this->assertTrue(abs(-180.797585 - Math_Finance::payment(0.03, 5, 828)) < FINANCE_PRECISION);
		$this->assertTrue(abs(-166.305561 - Math_Finance::payment(0.29, 7, 477)) < FINANCE_PRECISION);
		$this->assertTrue(abs(-62.142857 - Math_Finance::payment(0, 7, 435)) < FINANCE_PRECISION);
		$this->assertTrue(abs(-258.137498 - Math_Finance::payment(0.1/12, 3*12, 8000, 0, FINANCE_PAY_END)) < FINANCE_PRECISION);
		$this->assertTrue(abs(-256.004130 - Math_Finance::payment(0.1/12, 3*12, 8000, 0, FINANCE_PAY_BEGIN)) < FINANCE_PRECISION);

		// cannot pass negative number of periods
		$this->assertType('object', Math_Finance::payment(0.29, -7, 435));
		// cannot pass a type differenet from 0 and 1
		$this->assertType('object', Math_Finance::payment(0.29, 7, 435, 0, 3));
	}

	function testperiods()
	{
		// various random calculations
		$this->assertTrue(abs(0.759825 - Math_Finance::periods(0.08, -500, 355)) < FINANCE_PRECISION);
		$this->assertTrue(abs(4.486566 - Math_Finance::periods(0.03, -200, 828)) < FINANCE_PRECISION);
		$this->assertTrue(abs(0.084641 - Math_Finance::periods(0.45, -5000, 344)) < FINANCE_PRECISION);
		$this->assertTrue(abs(4.350000 - Math_Finance::periods(0, -100, 435)) < FINANCE_PRECISION);

		// arguments check
		$this->assertType('object', Math_Finance::periods(0.29, 100, 477, 0, 3));
		$this->assertType('object', Math_Finance::periods(0.5, 0, 0));
		$this->assertType('object', Math_Finance::periods(0, 0, 0));
	}

	function testrate()
	{
		// various random calculations
		$this->assertTrue(abs(0.08000 - Math_Finance::rate(20, -36.157534, 355, 0, FINANCE_PAY_END, 0.1)) < FINANCE_PRECISION);
		$this->assertTrue(abs(0.03000 - Math_Finance::rate(5, -180.797585, 828, 0, FINANCE_PAY_END, 0.1)) < FINANCE_PRECISION);
		$this->assertTrue(abs(0.45000 - Math_Finance::rate(2, -295.208163, 344, 0, FINANCE_PAY_END, 0.1)) < FINANCE_PRECISION);

		// cannot pass a type differenet from 0 and 1
		$this->assertType('object', Math_Finance::rate(20, -36.157534, 255, 0, 3));
	}

	function testinterestPayment()
	{
		// various random calculations
		$this->assertTrue(abs(-63.462189 - Math_Finance::interestPayment(0.1/12, 3, 3*12, 8000)) < FINANCE_PRECISION);
		$this->assertTrue(abs(-46.617168 - Math_Finance::interestPayment(0.1/12, 13, 3*12, 8000)) < FINANCE_PRECISION);
		$this->assertTrue(abs(-8.428265 - Math_Finance::interestPayment(0.1/12, 33, 3*12, 8000)) < FINANCE_PRECISION);
		$this->assertTrue(abs(-62.937708 - Math_Finance::interestPayment(0.1/12, 3, 3*12, 8000, 0, FINANCE_PAY_BEGIN)) < FINANCE_PRECISION);

		// cannot pass a type differenet from 0 and 1
		$this->assertType('object', Math_Finance::interestPayment(0.1/12, 3, 3*12, 8000, 0, 2));
	}

	function testprincipalPayment()
	{
		// various random calculations
		$this->assertTrue(abs(-194.675308 - Math_Finance::principalPayment(0.1/12, 3, 3*12, 8000)) < FINANCE_PRECISION);
		$this->assertTrue(abs(-211.5203289 - Math_Finance::principalPayment(0.1/12, 13, 3*12, 8000)) < FINANCE_PRECISION);
		$this->assertTrue(abs(-249.709231 - Math_Finance::principalPayment(0.1/12, 33, 3*12, 8000)) < FINANCE_PRECISION);
		$this->assertTrue(abs(-193.066421 - Math_Finance::principalPayment(0.1/12, 3, 3*12, 8000, 0, FINANCE_PAY_BEGIN)) < FINANCE_PRECISION);

		// cannot pass a type differenet from 0 and 1
		$this->assertType('object', Math_Finance::principalPayment(0.1/12, 3, 3*12, 8000, 0, 2));
	}

	function testnetPresentValue()
	{
		// various random calculations
		$this->assertTrue(abs(1188.443412 - Math_Finance::netPresentValue(0.1, array(-10000, 3000, 4200, 6800))) < FINANCE_PRECISION);

		// cash flow series must be an array
		$this->assertType('object', Math_Finance::netPresentValue(0.1, -1000, 3000, 4200, 6800));
	}

	function testinternalRateOfReturn()
	{
		// various random calculations
		$this->assertTrue(abs(-0.02124485 - Math_Finance::internalRateOfReturn(array(-70000, 12000, 15000, 18000, 21000))) < FINANCE_PRECISION);
		$this->assertTrue(abs(0.086630 - Math_Finance::internalRateOfReturn(array(-70000, 12000, 15000, 18000, 21000, 26000))) < FINANCE_PRECISION);
		$this->assertTrue(abs(-0.443507 - Math_Finance::internalRateOfReturn(array(-70000, 12000, 15000), -0.40)) < FINANCE_PRECISION);

		// cash flow series must be an array
		$this->assertType('object', Math_Finance::internalRateOfReturn(-70000, 12000, 15000, 18000, 21000));
		// cash flow must contain at least one positive value and one negative value
		$this->assertType('object', Math_Finance::internalRateOfReturn(array(70000, 12000, 15000, 18000, 21000)));
	}

    function testdaysDifference()
    {
        $this->assertTrue(62 == Math_Finance::daysDifference(mktime(0, 0, 0, 7, 1, 2005), mktime(0, 0, 0, 9, 1, 2005), FINANCE_COUNT_ACTUAL_365));
    }

	function testmodifiedInternalRateOfReturn()
	{
		// various random calculations
		$this->assertTrue(abs(0.126094 - Math_Finance::modifiedInternalRateOfReturn(array(-120000, 39000, 30000, 21000, 37000, 46000), 0.10, 0.12)) < FINANCE_PRECISION);
		$this->assertTrue(abs(-0.048044 - Math_Finance::modifiedInternalRateOfReturn(array(-120000, 39000, 30000, 21000), 0.10, 0.12)) < FINANCE_PRECISION);
		$this->assertTrue(abs(0.134759 - Math_Finance::modifiedInternalRateOfReturn(array(-120000, 39000, 30000, 21000, 37000, 46000), 0.10, 0.14)) < FINANCE_PRECISION);

		// cash flow series must be an array
		$this->assertType('object', Math_Finance::modifiedInternalRateOfReturn(-70000, 12000, 15000, 18000, 21000, 0.10, 0.12));
		// cash flow must contain at least one positive value and one negative value
		$this->assertType('object', Math_Finance::modifiedInternalRateOfReturn(array(70000, 12000, 15000, 18000, 21000), 0.10, 0.12));
	}

	function testTBillYield()
	{
		// various random calculations
		$this->assertTrue(abs(0.091417- Math_Finance::TBillYield(mktime(0, 0, 0, 3, 31, 2008), mktime(0, 0, 0, 6, 1, 2008), 98.45)) < FINANCE_PRECISION);

		// settlement must be before maturity
		$this->assertType('object', Math_Finance::TBillYield(mktime(0, 0, 0, 6, 1, 2008), mktime(0, 0, 0, 3, 31, 2008), 98.45));
		// maturity can't be more than one year after settlement
		$this->assertType('object', Math_Finance::TBillYield(mktime(0, 0, 0, 6, 1, 2008), mktime(0, 0, 0, 3, 31, 2010), 98.45));
	}

	function testTBillPrice()
	{
		// various random calculations
		$this->assertTrue(abs(98.450000- Math_Finance::TBillPrice(mktime(0, 0, 0, 3, 31, 2008), mktime(0, 0, 0, 6, 1, 2008), 0.09)) < FINANCE_PRECISION);

		// settlement must be before maturity
		$this->assertType('object', Math_Finance::TBillPrice(mktime(0, 0, 0, 6, 1, 2008), mktime(0, 0, 0, 3, 31, 2008), 0.09));
		// maturity can't be more than one year after settlement
		$this->assertType('object', Math_Finance::TBillPrice(mktime(0, 0, 0, 6, 1, 2008), mktime(0, 0, 0, 3, 31, 2010), 0.09));
	}

	function testTBillEquivalentYield()
	{
		// various random calculations
		$this->assertTrue(abs(0.094151- Math_Finance::TBillEquivalentYield(mktime(0, 0, 0, 3, 31, 2008), mktime(0, 0, 0, 6, 1, 2008), 0.0914)) < FINANCE_PRECISION);
		$this->assertTrue(abs(0.09773985- Math_Finance::TBillEquivalentYield(mktime(0, 0, 0, 3, 20, 2008), mktime(0, 0, 0, 12, 1, 2008), 0.0914)) < FINANCE_PRECISION);
		$this->assertTrue(abs(0.09778005- Math_Finance::TBillEquivalentYield(mktime(0, 0, 0, 3, 31, 1993), mktime(0, 0, 0, 12, 15, 1993), 0.0914)) < FINANCE_PRECISION);
		$this->assertTrue(abs(0.09965155- Math_Finance::TBillEquivalentYield(mktime(0, 0, 0, 1, 10, 1999), mktime(0, 0, 0, 1, 10, 2000), 0.0914)) < FINANCE_PRECISION);
		$this->assertTrue(abs(0.09994538- Math_Finance::TBillEquivalentYield(mktime(0, 0, 0, 1, 10, 2000), mktime(0, 0, 0, 1, 10, 2001), 0.0914)) < FINANCE_PRECISION);

		// settlement must be before maturity
		$this->assertType('object', Math_Finance::TBillEquivalentYield(mktime(0, 0, 0, 6, 1, 2008), mktime(0, 0, 0, 3, 31, 2008), 0.09));
		// maturity can't be more than one year after settlement
		$this->assertType('object', Math_Finance::TBillEquivalentYield(mktime(0, 0, 0, 6, 1, 2008), mktime(0, 0, 0, 3, 31, 2010), 0.09));
	}

	function testdiscountRate()
	{
		// various random calculations
		$this->assertTrue(abs(0.052071 - Math_Finance::discountRate(mktime(0, 0, 0, 1, 25, 2007), mktime(0, 0, 0, 6, 15, 2007), 97.975, 100, FINANCE_COUNT_NASD)) < FINANCE_PRECISION);
		$this->assertTrue(abs(0.052420 - Math_Finance::discountRate(mktime(0, 0, 0, 1, 25, 2007), mktime(0, 0, 0, 6, 15, 2007), 97.975, 100, FINANCE_COUNT_ACTUAL_ACTUAL)) < FINANCE_PRECISION);
		$this->assertTrue(abs(0.051702- Math_Finance::discountRate(mktime(0, 0, 0, 1, 25, 2007), mktime(0, 0, 0, 6, 15, 2007), 97.975, 100, FINANCE_COUNT_ACTUAL_360)) < FINANCE_PRECISION);
		$this->assertTrue(abs(0.052420- Math_Finance::discountRate(mktime(0, 0, 0, 1, 25, 2007), mktime(0, 0, 0, 6, 15, 2007), 97.975, 100, FINANCE_COUNT_ACTUAL_365)) < FINANCE_PRECISION);
		$this->assertTrue(abs(0.052071- Math_Finance::discountRate(mktime(0, 0, 0, 1, 25, 2007), mktime(0, 0, 0, 6, 15, 2007), 97.975, 100, FINANCE_COUNT_EUROPEAN)) < FINANCE_PRECISION);
	}

	function testpriceDiscount()
	{
		// various random calculations
		$this->assertTrue(abs(99.781250 - Math_Finance::priceDiscount(mktime(0, 0, 0, 2, 16, 2008), mktime(0, 0, 0, 3, 1, 2008), 0.0525, 100, FINANCE_COUNT_NASD)) < FINANCE_PRECISION);
		$this->assertTrue(abs(99.799180 - Math_Finance::priceDiscount(mktime(0, 0, 0, 2, 16, 2008), mktime(0, 0, 0, 3, 1, 2008), 0.0525, 100, FINANCE_COUNT_ACTUAL_ACTUAL)) < FINANCE_PRECISION);
		$this->assertTrue(abs(99.795833 - Math_Finance::priceDiscount(mktime(0, 0, 0, 2, 16, 2008), mktime(0, 0, 0, 3, 1, 2008), 0.0525, 100, FINANCE_COUNT_ACTUAL_360)) < FINANCE_PRECISION);
		$this->assertTrue(abs(99.798630 - Math_Finance::priceDiscount(mktime(0, 0, 0, 2, 16, 2008), mktime(0, 0, 0, 3, 1, 2008), 0.0525, 100, FINANCE_COUNT_ACTUAL_365)) < FINANCE_PRECISION);
		$this->assertTrue(abs(99.781250 - Math_Finance::priceDiscount(mktime(0, 0, 0, 2, 16, 2008), mktime(0, 0, 0, 3, 1, 2008), 0.0525, 100, FINANCE_COUNT_EUROPEAN)) < FINANCE_PRECISION);
	}


    /*******************************************************************
    ** Depreciation Functions                                      *****
    *******************************************************************/
    function testdepreciationFixedDeclining()
    {
		$this->assertTrue(abs(186083.333333 - Math_Finance::depreciationFixedDeclining(1000000, 100000, 6, 1, 7)) < FINANCE_PRECISION);
		$this->assertTrue(abs(186083.333333 - Math_Finance::depreciationFixedDeclining(1000000, 100000, 6, 1, 7)) < FINANCE_PRECISION);
		$this->assertTrue(abs(259639.416667 - Math_Finance::depreciationFixedDeclining(1000000, 100000, 6, 2, 7)) < FINANCE_PRECISION);
		$this->assertTrue(abs(176814.442750 - Math_Finance::depreciationFixedDeclining(1000000, 100000, 6, 3, 7)) < FINANCE_PRECISION);
		$this->assertTrue(abs(120410.635513 - Math_Finance::depreciationFixedDeclining(1000000, 100000, 6, 4, 7)) < FINANCE_PRECISION);
		$this->assertTrue(abs(81999.642784 - Math_Finance::depreciationFixedDeclining(1000000, 100000, 6, 5, 7)) < FINANCE_PRECISION);
		$this->assertTrue(abs(55841.756736 - Math_Finance::depreciationFixedDeclining(1000000, 100000, 6, 6, 7)) < FINANCE_PRECISION);
		$this->assertTrue(abs(15845.098474 - Math_Finance::depreciationFixedDeclining(1000000, 100000, 6, 7, 7)) < FINANCE_PRECISION);
    }

    function testdepreciationStraightLine()
    {
		$this->assertTrue(abs(2250 - Math_Finance::depreciationStraightLine(30000, 7500, 10)) < FINANCE_PRECISION);
    }

    function testdepreciationSYD()
    {
		$this->assertTrue(abs(4090.909091 - Math_Finance::depreciationSYD(30000, 7500, 10, 1)) < FINANCE_PRECISION);
		$this->assertTrue(abs(409.090909 - Math_Finance::depreciationSYD(30000, 7500, 10, 10)) < FINANCE_PRECISION);
    }
}

$suite = new PHPUnit_TestSuite('FinanceTestCase');
$result = PHPUnit::run($suite);
echo $result->toString();

?>
