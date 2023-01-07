<?php
/**
 * Shows array is readable format.
 * @param $array
 * @return void
 */
function _pre($array): void
{
    echo "<pre>";
    print_r($array);
    echo "</pre>";
}
