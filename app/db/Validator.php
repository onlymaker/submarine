<?php

/**
 * Form validation library.
 *
 * @author Tasos Bekos <tbekos@gmail.com>
 * @author Chris Gutierrez <cdotgutierrez@gmail.com>
 * @author Corey Ballou <ballouc@gmail.com>
 * @see https://github.com/blackbelt/php-validation
 * @see Based on idea: http://brettic.us/2010/06/18/form-validation-class-using-php-5-3/
 *
 * Customized by Syncxplus from bzfshop:src/protected/Core/Helper/Utility/Validator.php
 *
 */

namespace db {

    require_once ROOT.'/vendor/validation/Validator.php';

    class Validator extends \Validator{

        protected $defaultFilters = array();

        /**
         * Constructor.
         * Define values to validate.
         *
         * @param array $data
         * @param string $defaultFilters the default filter list that you want to add 
         */
        function __construct(array $data = null, $defaultFilters = 'strip_tags,trim') {
            parent::__construct($data);
            if (!empty($defaultFilters)) {
                $filterList = explode(',', $defaultFilters);
                foreach ($filterList as $filterItem) {
                    if (is_callable($filterItem)) {
                        $this->defaultFilters[] = $filterItem;
                    }
                }
                $this->filters = $this->defaultFilters;
            }
        }

        /**
         * regular express match 
         * @param string $pattern
         * @param string $message
         */
        public function regx($pattern, $message = null) {
            $this->setRule(__FUNCTION__, function($val, $args) {
                        if (strlen($val) === 0) {
                            return TRUE;
                        }
                        $val = strval($val);
                        $pattern = strval($args[0]);
                        return preg_match($pattern, $val) > 0;
                    }, $message, array($pattern));
            return $this;
        }

        /**
         * if value is set, it should not be empty
         * @param string $message
         */
        public function notEmpty($message = null) {
            $this->setRule(__FUNCTION__, function($val) {
                        if (!isset($val) || null == $val) {
                            return true;
                        }
                        if (is_scalar($val)) {
                            $val = trim($val);
                        }
                        return !empty($val);
                    }, $message);
            return $this;
        }

        /**
         * require value should be array
         * @param boolean $allowEmpty whether we allow the array to be empty
         * @param string $message
         */
        public function requireArray($allowEmpty, $message = null) {
            $this->setRule(__FUNCTION__, function($val, $args) {
                        if (!isset($val) || null == $val) {
                            return true;
                        }
                        if (!is_array($val)) {
                            return false;
                        }
                        if (!$args[0]) {
                            return count($val) > 0;
                        }
                    }, $message, array($allowEmpty));
            return $this;
        }

        /**
         * require value should be scalar
         * @param string $message
         */
        public function requireScalar($message = null) {
            $this->setRule(__FUNCTION__, function($val) {
                        if (!isset($val) || null == $val) {
                            return true;
                        }
                        return is_scalar($val);
                    }, $message);
            return $this;
        }

        /**
         * require value should be object
         * @param string $message
         */
        public function requireObject($message = null) {
            $this->setRule(__FUNCTION__, function($val) {
                        if (!isset($val) || null == $val) {
                            return true;
                        }
                        return is_object($val);
                    }, $message);
            return $this;
        }

        /**
         * validate
         * @param string $key
         * @param string $label
         * @return mixed
         */
        public function validate($key, $recursive = false, $label = '') {
            // set up field name for error message
            $this->fields[$key] = (empty($label)) ? 'Field with the name of "' . $key . '"' : $label;

            // apply filters to the data
            $this->_applyFilters($key);

            $val = $this->_getVal($key);

            // validate the piece of data
            $this->_validate($key, $val, $recursive);

            // reset rules
            $this->rules = array();
            $this->filters = $this->defaultFilters;
            return $val;
        }

        /**
         * _getVal with added support for retrieving values from numeric and
         * associative multi-dimensional arrays. When doing so, use DOT notation
         * to indicate a break in keys, i.e.:
         *
         * key = "one.two.three"
         *
         * would search the array:
         *
         * array('one' => array(
         *      'two' => array(
         *          'three' => 'RETURN THIS'
         *      )
         * );
         *
         * @param string $key
         * @return mixed
         */
        protected function _getVal($key) {
            // handle multi-dimensional arrays
            if (strpos($key, '.') !== FALSE) {
                $arrData = NULL;
                $keys = explode('.', $key);
                $keyLen = count($keys);
                for ($i = 0; $i < $keyLen; ++$i) {
                    if (trim($keys[$i]) == '') {
                        return false;
                    } else {
                        if (is_null($arrData)) {
                            if (!isset($this->data[$keys[$i]])) {
                                return false;
                            }
                            $arrData = $this->data[$keys[$i]];
                        } else {
                            if (!isset($arrData[$keys[$i]])) {
                                return false;
                            }
                            $arrData = $arrData[$keys[$i]];
                        }
                    }
                }
                return $arrData;
            } else {
                return (isset($this->data[$key])) ? $this->data[$key] : null;
            }
        }

        /**
         * Get default error message.
         *
         * @param string $key
         * @param array $args
         * @return string
         */
        protected function _getDefaultMessage($rule, $args = null) {

            switch ($rule) {
                case 'email':
                    $message = '%s is an invalid email address.';
                    break;

                case 'ip':
                    $message = '%s is an invalid IP address.';
                    break;

                case 'url':
                    $message = '%s is an invalid url.';
                    break;

                case 'required':
                    $message = '%s is required.';
                    break;

                case 'float':
                    $message = '%s must consist of numbers only.';
                    break;

                case 'integer':
                    $message = '%s must consist of integer value.';
                    break;

                case 'digits':
                    $message = '%s must consist only of digits.';
                    break;

                case 'regx':
                    $message = '%s does not match pattern ' . $args[0];
                    break;

                case 'notEmpty':
                    $message = '%s can not be empty';
                    break;

                case 'requireArray':
                    $message = '%s must be an Array ' . ($args[0] ? '' : ' and can not be empty');
                    break;

                case 'requireObject':
                    $message = '%s must be an Object ';
                    break;

                case 'requireScalar':
                    $message = '%s must be an Scalar ';
                    break;

                case 'min':
                    $message = '%s must be greater than ';
                    if ($args[1] == TRUE) {
                        $message .= 'or equal to ';
                    }
                    $message .= $args[0] . '.';
                    break;

                case 'max':
                    $message = '%s must be less than ';
                    if ($args[1] == TRUE) {
                        $message .= 'or equal to ';
                    }
                    $message .= $args[0] . '.';
                    break;

                case 'between':
                    $message = '%s must be between ' . $args[0] . ' and ' . $args[1] . '.';
                    if ($args[2] == FALSE) {
                        $message .= '(Without limits)';
                    }
                    break;

                case 'minlength':
                    $message = '%s must be at least ' . $args[0] . ' characters or longer.';
                    break;

                case 'maxlength':
                    $message = '%s must be no longer than ' . $args[0] . ' characters.';
                    break;

                case 'length':
                    $message = '%s must be exactly ' . $args[0] . ' characters in length.';
                    break;

                case 'matches':
                    $message = '%s must match ' . $args[1] . '.';
                    break;

                case 'notmatches':
                    $message = '%s must not match ' . $args[1] . '.';
                    break;

                case 'startsWith':
                    $message = '%s must start with "' . $args[0] . '".';
                    break;

                case 'notstartsWith':
                    $message = '%s must not start with "' . $args[0] . '".';
                    break;

                case 'endsWith':
                    $message = '%s must end with "' . $args[0] . '".';
                    break;

                case 'notendsWith':
                    $message = '%s must not end with "' . $args[0] . '".';
                    break;

                case 'date':
                    $message = '%s is not valid date.';
                    break;

                case 'mindate':
                    $message = '%s must be later than ' . $args[0]->format($args[1]) . '.';
                    break;

                case 'maxdate':
                    $message = '%s must be before ' . $args[0]->format($args[1]) . '.';
                    break;

                case 'oneof':
                    $message = '%s must be one of ' . implode(', ', $args[0]) . '.';
                    break;

                case 'ccnum':
                    $message = '%s must be a valid credit card number.';
                    break;

                default:
                    $message = '%s has an error.';
                    break;
            }

            return $message;
        }

    }

}

//global code

namespace {

    /**
     * 用于 Validator 中，对数据做类型转换
     * 
     * '' 转为 null, '0' 转为 0
     * 
     * @param string $inputValue
     */
    function ValidatorConvertValue($inputValue, $convertor) {

        $value = trim($inputValue);
        if ('' == $value) {
            return null;
        }
        return $convertor($value);
    }

    /**
     * 用于 Validator 中，对数据做类型转换
     * 
     * '' 转为 null, '0' 转为 0
     * 
     * @param string $inputValue
     */
    function ValidatorIntValue($inputValue) {
        return ValidatorConvertValue($inputValue, 'intval');
    }

    /**
     * 用于 Validator 中，对数据做类型转换
     * 
     * '' 转为 null, '0.1' 转为 0.1
     * 
     * @param string $inputValue
     */
    function ValidatorFloatValue($inputValue) {
        return ValidatorConvertValue($inputValue, 'floatval');
    }

}