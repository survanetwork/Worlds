<?php
/**
 * Created by PhpStorm.
 * User: Jarne
 * Date: 02.07.16
 * Time: 09:08
 */

namespace surva\Worlds\Utils;

class StaticArrayList {
    private $array;

    public function __construct() {
        $this->array = array();
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key) {
        return $this->array[$key];
    }

    /**
     * @param $value
     * @return mixed|false
     */
    public function getKey($value) {
        if(($key = array_search($value, $this->array)) !== FALSE) {
            return $key;
        }

        return false;
    }

    /**
     * @param $value
     * @param $key
     */
    public function add($value, $key = null) {
        if(isset($key)) {
            $this->array[$key] = $value;
        } else {
            $this->array[] = $value;
        }
    }

    /**
     * @param $key
     */
    public function remove($key) {
        unset($this->array[$key]);
    }

    /**
     * @param $value
     */
    public function removeByValue($value) {
        if(($key = array_search($value, $this->array)) !== FALSE) {
            unset($this->array[$key]);
        }
    }

    /**
     * @param $value
     * @return bool
     */
    public function contains($value) {
        if(($key = array_search($value, $this->array)) !== FALSE) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $key
     * @return bool
     */
    public function containsKey($key) {
        if(isset($this->array[$key])) {
            return true;
        }

        return false;
    }

    /**
     * @return int
     */
    public function count() {
        return count($this->array);
    }

    public function reset() {
        $this->array = array();
    }

    /**
     * @return array
     */
    public function getArray() {
        return $this->array;
    }

    /**
     * @param array $array
     */
    public function setArray(array $array) {
        $this->array = $array;
    }
}