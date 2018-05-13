<?php
/**
 * Created by PhpStorm.
 * User: jarne
 * Date: 22.07.16
 * Time: 13:41
 */

namespace surva\worlds\utils;

class ArrayList {
    /* @var bool */
    private $sortValues;

    /* @var array */
    private $array;

    public function __construct(bool $sortValues = false, array $array = null) {
        $this->sortValues = $sortValues;

        if(isset($array)) {
            $this->array = $array;
        } else {
            $this->array = array();
        }
    }

    /**
     * Get the value of a key
     *
     * @param string|int $key
     * @return mixed
     */
    public function get($key) {
        return $this->array[$key];
    }

    /**
     * Get key by the value
     *
     * @param $value
     * @return string|int|false
     */
    public function getKeyByValue($value) {
        if(($key = array_search($value, $this->array)) !== false) {
            return $key;
        }

        return false;
    }

    /**
     * Add a value to the array
     *
     * @param $value
     * @param string|int|null $key
     */
    public function add($value, $key = null): void {
        if(isset($key)) {
            $this->array[$key] = $value;
        } else {
            $this->array[] = $value;
        }

        if($this->isSortValues()) {
            $this->array = array_values($this->getArray());
        }
    }

    /**
     * Remove a value by the key
     *
     * @param string|int $key
     */
    public function remove($key): void {
        unset($this->array[$key]);

        if($this->isSortValues()) {
            $this->array = array_values($this->getArray());
        }
    }

    /**
     * Remove a value by its content
     *
     * @param $value
     */
    public function removeByValue($value): void {
        if(($key = array_search($value, $this->array)) !== false) {
            unset($this->array[$key]);

            if($this->isSortValues()) {
                $this->array = array_values($this->getArray());
            }
        }
    }

    /**
     * Check if the array contains a value
     *
     * @param $value
     * @return bool
     */
    public function contains($value): bool {
        if(($key = array_search($value, $this->array)) !== false) {
            return true;
        }

        return false;
    }

    /**
     * Check if the array contains a key
     *
     * @param string|int $key
     * @return bool
     */
    public function containsKey($key): bool {
        return isset($this->array[$key]);
    }

    /**
     * Count the items of the array
     *
     * @return int
     */
    public function count(): int {
        return count($this->array);
    }

    /**
     * Reset the array
     */
    public function reset(): void {
        $this->array = array();
    }

    /**
     * @param array $array
     */
    public function setArray(array $array): void {
        $this->array = $array;
    }

    /**
     * @return array
     */
    public function getArray(): array {
        return $this->array;
    }

    /**
     * @param bool $sortValues
     */
    public function setSortValues(bool $sortValues): void {
        $this->sortValues = $sortValues;
    }

    /**
     * @return bool
     */
    public function isSortValues(): bool {
        return $this->sortValues;
    }
}
