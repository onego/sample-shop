<?php

class NotNullExpectation
        extends NotEqualExpectation
{
    public
    function __construct($message = '%s')
    {
        parent::__construct(NULL, $message);
    }
}

class NestedExpectation
        extends SimpleExpectation
{
    var $_value;

    function __construct($value, $message = '%s') {
        parent::__construct($message);
        $this->_value = $value;
    }

    function test($compare)
    {
        if (is_object($this->_value) || is_array($this->_value))
        {
            $equals = true;
            foreach ($this->_value as $key => &$value)
            {
                if ($value instanceof SimpleExpectation)
                {
                    $equals = $this->evaluate($value, $compare, $key)
                            && $equals;
                }
                else if (is_array($value))
                {
                    foreach ($value as $subkey => &$subvalue)
                    {
                        if ($subvalue instanceof SimpleExpectation)
                        {
                            $equals = $this->evaluate(
                                    $subvalue,
                                    $compare->{$key},
                                    $subkey
                                ) && $equals;
                        }
                        else
                        {
                            $equals = $this->equals(
                                    $subvalue,
                                    $compare->{$key}[$subkey]
                                ) && $equals;
                        }
                    }
                }
                else
                {
                    $equals = $this->equals($value, $compare->{$key}) && $equals;
                }
            }

            return $equals;
        }

        return $this->equals($this->_value, $compare);
    }

    function equals($a, $b)
    {
        return ($a == $b) && ($b == $a);
    }

    function evaluate(&$value, $compare, $key)
    {
        if (is_array($value))
        {
            $equals = true;

            foreach ($value as $subkey => &$subvalue)
            {
                if ($subvalue instanceof SimpleExpectation)
                {
                    $equals = $this->evaluate(
                            $subvalue,
                            $compare[$subkey],
                            $key
                        ) && $equals;
                }
                else
                {
                    $equals = $this->equals(
                            $subvalue,
                            $compare[$subkey]
                        ) && $equals;
                }
            }

            return $equals;
        }

        $compareTo = is_array($compare)
                ? $compare[$key]
                : $compare->$key;
        if (!$value->test($compareTo))
        {
            $value = $value->testMessage($compareTo);
            return false;
        }

        $value = $compareTo;
        return true;
    }

    function testMessage($compare)
    {
        if ($this->test($compare))
        {
            return "Equal expectation [" . $this->_dumper->describeValue($this->_value) . "]";
        }
        else
        {
            return "Equal expectation fails " .
                    $this->_getDumper()->describeDifference($this->_value, $compare);
        }
    }
}

class FieldEqualsExpectation extends EqualExpectation {
    private $key;

    function __construct($key, $expected) 
    {
        parent::__construct($expected);
        $this->key = $key;
    }

    function test($compare) 
    {
        $key = $this->key;
        if (is_array($compare) && isset($compare[$key])) {
            return parent::test($compare[$this->key]);
        } else if (is_object($compare) && isset($compare->$key)) {
            return parent::test($compare->$key);
        }
        return false;
    }

    function testMessage($compare) {
        $key = $this->key;
        if (is_array($compare) && isset($compare[$key])) {
            return 'Key [' . $key . '] -> ' . parent::testMessage($compare[$key]);
        } else if (is_object($compare) && isset($compare->$key)) {
            return 'Key [' . $key . '] -> ' . parent::testMessage($compare->$key);
        }
        return 'Key [' . $key . '] does not exist';
    }
}
