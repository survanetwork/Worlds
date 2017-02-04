<?php
/**
 * Created by PhpStorm.
 * User: Jarne
 * Date: 30.06.16
 * Time: 15:03
 */

namespace surva\Worlds\Utils;

class ArrayList {
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
     * @return int|false
     */
    public function getKey($value) {
        if(($key = array_search($value, $this->array)) !== FALSE) {
            return $key;
        }

        return false;
    }

    /**
     * @param $value
     * @param int|null $key
     */
    public function add($value, int $key = null) {
        if(isset($key)) {
            $this->array[$key] = $value;
        } else {
            $this->array[] = $value;
        }

        $this->array = array_values($this->getArray());
    }

    /**
     * @param $key
     */
    public function remove($key) {
        unset($this->array[$key]);
        $this->array = array_values($this->getArray());
    }

    /**
     * @param $value
     */
    public function removeByValue($value) {
        if(($key = array_search($value, $this->array)) !== FALSE) {
            unset($this->array[$key]);
            $this->array = array_values($this->getArray());
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