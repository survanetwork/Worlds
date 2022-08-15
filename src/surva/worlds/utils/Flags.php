<?php

/**
 * Worlds | world flag definitions
 */

namespace surva\worlds\utils;

class Flags
{
    public const TYPE_BOOL = 0;
    public const TYPE_PERMISSION = 1;
    public const TYPE_GAME_MODE = 2;
    public const TYPE_CONTROL_LIST = 3;

    public const FLAG_PERMISSION = "permission";
    public const FLAG_GAME_MODE = "gamemode";
    public const FLAG_BUILD = "build";
    public const FLAG_PVP = "pvp";
    public const FLAG_DAMAGE = "damage";
    public const FLAG_INTERACT = "interact";
    public const FLAG_EXPLODE = "explode";
    public const FLAG_DROP = "drop";
    public const FLAG_HUNGER = "hunger";
    public const FLAG_FLY = "fly";
    public const FLAG_DAYLIGHT_CYCLE = "daylightcycle";
    public const FLAG_LEAVES_DECAY = "leavesdecay";
    public const FLAG_POTION = "potion";
    public const FLAG_COMMAND = "command";

    public const VALUE_TRUE = "true";
    public const VALUE_FALSE = "false";
    public const VALUE_WHITELISTED = "white";
    public const VALUE_BLACKLISTED = "black";

    public const VALID_BOOL_VALUES = [
      self::VALUE_TRUE, self::VALUE_FALSE
    ];
    public const VALID_CONTROL_LIST_VALUES = [
      self::VALUE_TRUE, self::VALUE_FALSE, self::VALUE_WHITELISTED, self::VALUE_BLACKLISTED
    ];

    public const AVAILABLE_WORLD_FLAGS
      = [
        self::FLAG_PERMISSION    => [
          "type" => self::TYPE_PERMISSION,
        ],
        self::FLAG_GAME_MODE      => [
          "type" => self::TYPE_GAME_MODE,
        ],
        self::FLAG_BUILD         => [
          "type" => self::TYPE_CONTROL_LIST,
        ],
        self::FLAG_PVP           => [
          "type" => self::TYPE_BOOL,
        ],
        self::FLAG_DAMAGE        => [
          "type" => self::TYPE_BOOL,
        ],
        self::FLAG_INTERACT      => [
          "type" => self::TYPE_CONTROL_LIST,
        ],
        self::FLAG_EXPLODE       => [
          "type" => self::TYPE_BOOL,
        ],
        self::FLAG_DROP          => [
          "type" => self::TYPE_CONTROL_LIST,
        ],
        self::FLAG_HUNGER        => [
          "type" => self::TYPE_BOOL,
        ],
        self::FLAG_FLY           => [
          "type" => self::TYPE_BOOL,
        ],
        self::FLAG_DAYLIGHT_CYCLE => [
          "type" => self::TYPE_BOOL,
        ],
        self::FLAG_LEAVES_DECAY   => [
          "type" => self::TYPE_BOOL,
        ],
        self::FLAG_POTION        => [
          "type" => self::TYPE_CONTROL_LIST,
        ],
        self::FLAG_COMMAND => [
          "type" => self::TYPE_CONTROL_LIST,
        ],
      ];

    public const AVAILABLE_DEFAULT_FLAGS
      = [
        self::FLAG_GAME_MODE      => [
          "type" => self::TYPE_GAME_MODE,
        ],
        self::FLAG_BUILD         => [
          "type" => self::TYPE_CONTROL_LIST,
        ],
        self::FLAG_PVP           => [
          "type" => self::TYPE_BOOL,
        ],
        self::FLAG_DAMAGE        => [
          "type" => self::TYPE_BOOL,
        ],
        self::FLAG_INTERACT      => [
          "type" => self::TYPE_CONTROL_LIST,
        ],
        self::FLAG_EXPLODE       => [
          "type" => self::TYPE_BOOL,
        ],
        self::FLAG_DROP          => [
          "type" => self::TYPE_CONTROL_LIST,
        ],
        self::FLAG_HUNGER        => [
          "type" => self::TYPE_BOOL,
        ],
        self::FLAG_FLY           => [
          "type" => self::TYPE_BOOL,
        ],
        self::FLAG_DAYLIGHT_CYCLE => [
          "type" => self::TYPE_BOOL,
        ],
        self::FLAG_LEAVES_DECAY   => [
          "type" => self::TYPE_BOOL,
        ],
        self::FLAG_POTION        => [
          "type" => self::TYPE_CONTROL_LIST,
        ],
        self::FLAG_COMMAND => [
          "type" => self::TYPE_CONTROL_LIST,
        ],
      ];
}
