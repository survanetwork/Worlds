<?php
/**
 * Worlds | defaults processing file
 */

namespace surva\worlds\types;

class Defaults extends World {
    /**
     * Get value from config
     *
     * @param string $name
     * @return mixed|null
     */
    public function getValue(string $name) {
        if(!$this->getConfig()->exists($name)) {
            return null;
        }

        switch($this->getConfig()->get($name)) {
            case "true":
                return true;
            case "false":
                return false;
            default:
                return $this->getConfig()->get($name);
        }
    }

    /**
     * Load value from config
     *
     * @param string $name
     */
    public function loadValue(string $name): void {
        if(!$this->getConfig()->exists($name)) {
            $this->$name = null;

            return;
        }

        switch($this->getConfig()->get($name)) {
            case "true":
                $this->$name = true;
                break;
            case "false":
                $this->$name = false;
                break;
            default:
                $this->$name = $this->getConfig()->get($name);
                break;
        }
    }
}
