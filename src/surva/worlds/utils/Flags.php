<?php

/**
 * Worlds | world flag definitions
 */

namespace surva\worlds\utils;

class Flags
{
    public const TYPE_BOOL = 0;

    public const TYPE_PERMISSION = 1;

    public const TYPE_GAMEMODE = 2;

    public const AVAILABLE_WORLD_FLAGS
      = [
        "permission"    => [
          "type" => self::TYPE_PERMISSION,
        ],
        "gamemode"      => [
          "type" => self::TYPE_GAMEMODE,
        ],
        "build"         => [
          "type" => self::TYPE_BOOL,
        ],
        "pvp"           => [
          "type" => self::TYPE_BOOL,
        ],
        "damage"        => [
          "type" => self::TYPE_BOOL,
        ],
        "interact"      => [
          "type" => self::TYPE_BOOL,
        ],
        "explode"       => [
          "type" => self::TYPE_BOOL,
        ],
        "drop"          => [
          "type" => self::TYPE_BOOL,
        ],
        "hunger"        => [
          "type" => self::TYPE_BOOL,
        ],
        "fly"           => [
          "type" => self::TYPE_BOOL,
        ],
        "daylightcycle" => [
          "type" => self::TYPE_BOOL,
        ],
        "leavesdecay"   => [
          "type" => self::TYPE_BOOL,
        ],
        "potion"        => [
          "type" => self::TYPE_BOOL,
        ],
      ];

    public const AVAILABLE_DEFAULT_FLAGS
      = [
        "gamemode"      => [
          "type" => self::TYPE_GAMEMODE,
        ],
        "build"         => [
          "type" => self::TYPE_BOOL,
        ],
        "pvp"           => [
          "type" => self::TYPE_BOOL,
        ],
        "damage"        => [
          "type" => self::TYPE_BOOL,
        ],
        "interact"      => [
          "type" => self::TYPE_BOOL,
        ],
        "explode"       => [
          "type" => self::TYPE_BOOL,
        ],
        "drop"          => [
          "type" => self::TYPE_BOOL,
        ],
        "hunger"        => [
          "type" => self::TYPE_BOOL,
        ],
        "fly"           => [
          "type" => self::TYPE_BOOL,
        ],
        "daylightcycle" => [
          "type" => self::TYPE_BOOL,
        ],
        "leavesdecay"   => [
          "type" => self::TYPE_BOOL,
        ],
        "potion"        => [
          "type" => self::TYPE_BOOL,
        ],
      ];
}
