<?php

namespace Domain\Helper;

class ConvertHelper
{
   /**
    * Déplace, s'il existe, l'article du début d'un titre à la fin du même titre.
    * Ex : 'La page blanche' devient 'Page blanche (La)'
    *
    * @param string $pTitle Titre à traiter
    * @return string Le titre traité
    */
   public static function toLibrarianTitle($pTitle)
   {
      if (is_null($pTitle)) {
         return null;
      }
      $sTitleUp = trim(strtoupper($pTitle));
      $lenTitleUp = strlen($sTitleUp);
      $sTrimTitle = trim($pTitle);
      $sTitleRet = $sTrimTitle;

      if (false == empty($sTitleUp)) {
         if ($lenTitleUp > 4 && 0 === substr_compare($sTitleUp, 'LES ', 0, 4)) {
            $sTitleRet = mb_ucfirst(substr($sTrimTitle, 4)) . ' (' . mb_ucfirst(substr($sTrimTitle, 0, 3)) . ')';
         } elseif ($lenTitleUp > 3 && 0 === substr_compare($sTitleUp, 'LE ', 0, 3)) {
            $sTitleRet = mb_ucfirst(substr($sTrimTitle, 3)) . ' (' . mb_ucfirst(substr($sTrimTitle, 0, 2)) . ')';
         } elseif ($lenTitleUp > 3 && 0 === substr_compare($sTitleUp, 'LA ', 0, 3)) {
            $sTitleRet = mb_ucfirst(substr($sTrimTitle, 3)) . ' (' . mb_ucfirst(substr($sTrimTitle, 0, 2)) . ')';
         } elseif ($lenTitleUp > 2 && 0 === substr_compare($sTitleUp, "L'", 0, 2)) {
            $sTitleRet = mb_ucfirst(substr($sTrimTitle, 2)) . ' (' . mb_ucfirst(substr($sTrimTitle, 0, 2)) . ')';
         } elseif ($lenTitleUp > 7 && 0 === substr_compare($sTitleUp, 'L&#039;', 0, 7)) {
            $sTitleRet = mb_ucfirst(substr($sTrimTitle, 7)) . ' (' . mb_ucfirst(substr($sTrimTitle, 0, 7)) . ')';
         } elseif ($lenTitleUp > 7 && 0 === substr_compare($sTitleUp, 'L\U0027', 0, 7)) {
            $sTitleRet = mb_ucfirst(substr($sTrimTitle, 7)) . ' (' . mb_ucfirst(substr($sTrimTitle, 0, 7)) . ')';
         } elseif ($lenTitleUp > 4 && 0 === substr_compare($sTitleUp, 'THE ', 0, 4)) {
            $sTitleRet = mb_ucfirst(substr($sTrimTitle, 4)) . ' (' . mb_ucfirst(substr($sTrimTitle, 0, 3)) . ')';
         }
      }
      return $sTitleRet;
   }

   /**
    * Decode HTML Special Chars with ENT_HTML5 | ENT_QUOTES
    * @param string $pString A String
    * @return null|string
    */
   public static function decodeHtmlSpecialChars($pString)
   {
      if (is_null($pString)) {
         return null;
      }
      return htmlspecialchars_decode($pString, ENT_HTML5 | ENT_QUOTES);
   }
}

/**
 * Add only if the function don't exist.
 */
if (!function_exists('mb_ucfirst')) {
   /**
    * Convert first letter to upper case with the good encoding even if the string is encoding with multiple-byte.
    * @param string $str The string to convert
    * @param string $encoding The char encoding : default UTF-8
    * @param bool $lower_str_end Force the end of string to lower case
    * @return string
    */
   function mb_ucfirst($str, $encoding = "UTF-8", $lower_str_end = false)
   {
      $first_letter = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding);
      if ($lower_str_end) {
         $str_end = mb_strtolower(mb_substr($str, 1, mb_strlen($str, $encoding), $encoding), $encoding);
      } else {
         $str_end = mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
      }
      $str = $first_letter . $str_end;
      return $str;
   }
}
