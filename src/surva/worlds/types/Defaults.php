<?php

/**
 * Worlds | defaults processing file
 */

namespace surva\worlds\types;

use surva\worlds\utils\Flags;

class Defaults extends World
{
    /**
     * Get value from config
     *
     * @param  string  $name
     *
     * @return mixed|null
     */
    public function getValue(string $name): mixed
    {
        if (!$this->getConfig()->exists($name)) {
            return null;
        }

        return $this->getConfig()->get($name);
    }

    /**
     * Load value from config
     *
     * @param  string  $name
     * @param  int|null  $type
     *
     * @return mixed
     */
    public function loadValue(string $name, ?int $type = null): mixed
    {
        if (!$this->getConfig()->exists($name)) {
            $this->flags[$name] = null;
            return null;
        }

        $val = $this->getConfig()->get($name);
        $this->flags[$name] = $val;

        if ($type === Flags::TYPE_CONTROL_LIST) {
            if ($this->getConfig()->exists($name . "list")) {
                $this->flags[$name . "list"] = new ControlList($this->getConfig()->get($name . "list"));
            } else {
                $this->flags[$name . "list"] = new ControlList();
            }
        }

        return $val;
    }
}
