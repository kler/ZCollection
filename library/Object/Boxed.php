<?php
interface Object_Boxed extends Object_Comparable, Object_Hashable {

    const _INT = 2;

    const _DOUBLE = 4;

    const _STRING = 8;

    const _BOOL = 16;

    const _ARRAY = 32;

    const _NULL = 64;

    const _INF = 128;

    const _CASTABLE = 62;

    public function type();

    public function __toString();
}