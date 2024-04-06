<?php

/** 
 * @author Cristian Manuel Alcobendas Beorlegui
 * @version 1.0
 * */

namespace App\Models;

class IBAN {
    // === ATTRIBUTES === //
    private $arrayInternationalFormats = [
        "ES" => [
            "countryName" => "ES",
            "length" => 24,
        ],

        "GB" => [
            "countryName" => "United States",
            "length" => 22,
        ],
        
        "FR" => [
            "countryName" => "Francia",
            "length" => 27,
        ],
        
        "DE" => [
            "countryName" => "Deutche",
            "length" => 22,
        ],
        
        "IE" => [
            "countryName" => "Ireland",
            "length" => 22,
        ],
        
        "PL" => [
            "countryName" => "Poland",
            "length" => 28,
        ],
        
        "IT" => [
            "countryName" => "Italy",
            "length" => 27,
        ],
        
        "NL" => [
            "countryName" => "Netherland",
            "length" => 18,
        ],
        
        "RO" => [
            "countryName" => "Rumany",
            "length" => 24,
        ],

        "PT" => [
            "countryName" => "Portugal",
            "length" => 25,
        ]
    ];

    // === METHODS === //

    /** 
     * Return a boolean indicating if given IBAN is correct.
     * 
     * @param String $iban - 
     * 
     * @return bool
     * */
    public function validateIBAN(String $iban): bool {
        $isValid = false;

        // Delete all $iban spaces
        $iban = ($iban != null) ? str_replace(' ', '', trim($iban)) : '';

        // Check if IBAN is not empty
        if ($iban != "") {
            // Check if IBAN has a min length of 2 chars
            if (strlen($iban) >= 2) {
                // Get country code
                $countryCode = substr($iban, 0, 2);

                // Check if stored country code exists in 'arrayInternationalFormats'
                if (key_exists($countryCode, $this->arrayInternationalFormats)) {
                    // Check if $iban length equals to country requires.
                    if (strlen($iban) == $this->arrayInternationalFormats[$countryCode]["length"]) {
                        // Check if CCC is valid
                        if ($this->validateCCC($iban)) {
                            // Check if IBAN code is valid
                            $ibanDigitControl = 98 - (float) bcmod(substr($iban, 4) . '142800', '97');

                            if (substr($iban, 2, 2) == (String) $ibanDigitControl) {
                                $isValid = true;
                            }
                        }
                    }
                }
            }
        }

        return $isValid;
    }

    /** 
     * Return a boolean indicating if CCC code from given IBAN is valid for Spain.
     * 
     * @param String $iban - 
     * 
     * @return bool
     * */
    public function validateCCC(String $iban): bool {
        $isValid = false;
        $arrayNumbersFirstDigit = [4, 8, 5, 10, 9, 7, 3, 6];
        $arrayNumbersSecondDigit = [1, 2, 4, 8, 5, 10, 9, 7, 3, 6];

        // Delete all $iban spaces
        $iban = ($iban != null) ? str_replace(' ', '', trim($iban)) : '';

        // Check if length of $iban equals to Spain required
        if (strlen($iban) == $this->arrayInternationalFormats['ES']['length']) {
            // Check if rest of chars in $iban, after country code, equals to number
            $digitsIban = substr($iban, 2);

            if (is_numeric($digitsIban)) {
                // Calculate first control digit
                $result = 0;                
                for ($i = 0; $i < count($arrayNumbersFirstDigit); $i++) {
                    $result += substr($digitsIban, $i + 2, 1) * $arrayNumbersFirstDigit[$i]; 
                }
                
                $firstDigit = 11 - ($result % 11);
                if ($firstDigit >= 10) {
                    $firstDigit = ($firstDigit == 10) ? 1 : 0;
                }

                // Calculate second control digit 
                $result = 0;
                for ($i = 0; $i < count($arrayNumbersSecondDigit); $i++) {
                    $result += substr($digitsIban, $i + 12, 1) * $arrayNumbersSecondDigit[$i];
                }

                $secondDigit = 11  - ($result % 11);
                if ($secondDigit >= 10) {
                    $secondDigit = ($secondDigit == 10) ? 1 : 0;
                }

                // Check if $iban CCC equals to calculated control digits
                if (substr($digitsIban, 10, 1) == $firstDigit && substr($digitsIban, 11, 1) == $secondDigit) {
                    $isValid = true;
                }
            }
        }

        return $isValid;
    }

    /** 
     * Given an uncompleted IBAN code with last two chars left, return first
     * corect code.
     * 
     * @param $uncompletIban - 
     * 
     * @return String
     * */
    public function discoverIban($uncompleteIban): String {
        $iban = '';

        // Delete all $isbn spaces
        $uncompleteIban = ($uncompleteIban != null) ? str_replace(' ', '', trim($uncompleteIban)) : '';
        

        // Check if number of ? is lower or equal than 2.
        if (substr_count($uncompleteIban, '*') == 2) {
            // Delete '*' from 'uncompleteIban'
            $uncompleteIban = str_replace('*', '', $uncompleteIban);

            for ($isValid = false, $i = 0; !$isValid; $i++) {
                // Add current $i to 'uncompleteIban', if number is lower than 10, a 0 will pad before
                $iban = $uncompleteIban . str_pad($i, 2, '0', STR_PAD_LEFT);
                
                // Check if iban is valid
                if ($this->validateIBAN($iban)) {
                    $isValid = true;
                }
            }
        }

        return $iban;
    }
}