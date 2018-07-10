<?php

namespace TextHyphenation\Email;

class Validator
{
    /** Valid emails:
     * silinskas.matas@gmail.com
     * silinskas@gmail.com
     * silinskas.matas@gmail.de
     * "silinskas.matas"@gmail.com
     *
     * @param string $email
     * @return bool
     */
    public function isValid(string $email) : bool
    {
        if (preg_match('#^(((\w+)[.]?)+|"(.*)")@(\w+)[.](\w){2,3}$#', $email) === 1) {
            return true;
        }
        return false;
    }
}