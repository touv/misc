<?php
// vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 fdm=marker encoding=utf8 :
/**
 * try_catch_once, licensed under the MIT license.
 *
 * (c) Nicolas Thouvenin <nthouvenin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * More informations :
 *  http://github.com/touv/misc/blob/master/try_catch_once.php
 *
 *
 * Example 1 : 
 *
 *      $dom = new DomDocument;
 *
 *      $ret = try_func(array($dom,'loadXML'), $xml_string);
 *      if (!$ret) die(catch_error());
 *      
 *      $ret = try_func_array(array($dom, 'relaxNGValidate'), array('schema.rng'));
 *      if (!$ret) die(catch_error());
 *
 *
 * Example 2 : 
 *
 *      $dom = new DomDocument;
 *
 *      $ret = try_method('loadXML', $dom, $xml_string);
 *      if (!$ret) die(catch_error());
 *      
 *      $ret = try_method_array('relaxNGValidate', $dom, array('schema.rng'));
 *      if (!$ret) die(catch_error());
 *
 * Note :
 *
 * You can format in HTML the catch_error output (default in text)
 *
 * echo nl2br(catch_error());
 * echo "<pre>".catch_error()."</pre>";
 * echo catch_error('<br/>');
 * 
 **/

function try_catch_once_error_handler($errno, $errstr, $errfile, $errline, $errcontext, $ret = false)
{
    static $errs = array();

    if ($ret === true) {
        $r = $errs;
        $errs = array();
        return $r;
    }
    // For my personal use : ignore the libxml2 bug
    if (strstr($errstr, 'Unimplemented block at relaxng.c:3824')) return;

    $errs[] = $errstr;
}
function catch_error($glue = PHP_EOL)
{
    return implode($glue, try_catch_once_error_handler(null, null, null, null, null, true));
}
function try_method()
{
    $params = func_get_args();
    $method_name = array_shift($params);
    $obj = array_shift($params);
    return try_func_array(array($obj, $method_name), $params);
}
function try_method_array($method_name, &$obj, $params)
{
    return try_func_array(array($obj, $method_name), $params);
}
function try_func()
{
    $params = func_get_args();
    $callback = array_shift($params);
    return try_func_array($callback, $params);
}
function try_func_array($callback, $params)
{
    $html_errors = ini_set('html_errors', false);
    $error_handler = set_error_handler('try_catch_once_error_handler');
    $ret = call_user_func_array($callback, $params);
    set_error_handler($error_handler);
    ini_set('html_errors', $html_errors);
    return $ret;
}

