<?php

namespace Nxu\MagicLogin\Contracts;

interface CanLoginMagically
{
    /**
     * Gets the shared secret used to calculate the magic login token.
     *
     * @return string
     */
    public function getMagicLoginSecret() : string;
}
