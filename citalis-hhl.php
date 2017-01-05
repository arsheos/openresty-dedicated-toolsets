<?hh // partial

namespace citalisstrict;

function active_platform(): Vector<string> {
  return Vector {$_SERVER["SERVER_SOFTWARE"], phpversion(), php_uname($mode = "n")};
}

function FILESin_params<T>(): Map<string, T> {
  return new Map($_FILES["filebox"]);
}

function POSTin_params(): Map<string, string> {
  return new Map($_POST);
}

function requestMethod() : string {
  return $_SERVER['REQUEST_METHOD'];
}

echo main();
