<?php
/**
 * PHP Short Unique ID
 *
 * Copyright (c) 2012 Brett O'Donnell <brett@mrphp.com.au>
 * Source Code: https://github.com/cornernote/php-short-unique-id
 * Home Page: http://mrphp.com.au/blog/short-unique-id-php
 * License: GPLv3
 */

class Suid
{
    /**
     * Change the order of the characters to protect the integer they are encoding
     *
     * @var string
     */
    static $chars = 'k90iuashgtyioslpbnzsquiosadfgjopoqwsafghjtwryui09xyq1748sgxcioaszyrusiozlvbquiostsxajtsqriozpqtsxhlqeurtax0938usy7t6xrasq23j8gva';

    /**
     * Suid::encode()
     *
     * Encodes an Integer into a Short Unique Identifier
     *
     * @param int $id
     * @return string $suid
     */
    public static function encode($id)
    {
        $suid = '';
        while (bccomp($id, 0, 0) != 0) {
            $rem = bcmod($id, 128);
            $id = bcdiv(bcsub($id, $rem, 0), 128, 0);
            $suid = self::$chars[$rem] . $suid;
        }
        return $suid;
    }

    /**
     * Suid::decode()
     *
     * Decodes a Short Unique Identifier into an Integer
     *
     * @param string $suid
     * @return int $id
     */
    public static function decode($suid)
    {
        $id = '';
        $len = strlen($suid);
        for ($i = $len - 1; $i >= 0; $i--) {
            $value = strpos(self::$chars, $suid[$i]);
            $id = bcadd($id, bcmul($value, bcpow(128, ($len - $i - 1))));
        }
        return $id;
    }

}
