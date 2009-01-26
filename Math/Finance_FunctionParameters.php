<?php
/**
 * Singleton class to preserve given values of other variables in the callback functions
 */
class Math_Finance_FunctionParameters
{
    var $parameters = array();

    /**
    * Constructor. Should be private, so used little hack.
    *
    * @param bool       Whether constructor has been called from a method of the class
    * @param array      Parameters (variables values of the function) to be preserved
    * @access private
    */
    function Math_Finance_FunctionParameters($called_from_get_instance = False, $parameters = array())
    {
		// PHP4 hack
		if (!$called_from_get_instance)
			trigger_error("Cannot instantiate Math_Finance_FunctionParameters class directly (It's a Singleton)", E_USER_ERROR);
        
        foreach ($parameters as $name => $value) {
            $this->parameters[$name] = $value;
        }
    }

    /**
    * Method to be called statically to create Singleton
    *
    * @param array      Parameters (variables values of the function) to be preserved
    * @param bool       Whether the Singleton should be reset
    * @static
    * @access public
    */
	function &getInstance($parameters = array(), $reset = False)
	{
		static $singleton;

        if ($reset) $singleton = null;

		if (!is_object($singleton)) {
			$singleton = new Math_Finance_FunctionParameters(True, $parameters);
		}

		return $singleton;
	}
}
?>
