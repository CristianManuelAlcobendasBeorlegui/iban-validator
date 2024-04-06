<?php

/** 
 * @author Cristian Manuel Alcobendas Beorlegui
 * @version 1.0
 * */

namespace Tests\Unit;

use App\Models\IBAN;
use PHPUnit\Framework\TestCase;

class IBANTest extends TestCase {
    public function test_validariban_casos_correctes() {
        $iban = new IBAN();
        $this->assertEquals(true, $iban->validateIBAN('ES6812345678061234567890'));
        $this->assertEquals(true, $iban->validateIBAN('ES9121000418450200051332'));
        $this->assertEquals(true, $iban->validateIBAN('ES80 2310 0001 1800 0001 2345'));
    }

    public function test_validariban_casos_incorrectes() {
        $iban = new IBAN();
        $this->assertEquals(false, $iban->validateIBAN('ES12345678901234455678944'));
        $this->assertEquals(false, $iban->validateIBAN('ESABBABBBBABBABAAB0A0A00A'));
        $this->assertEquals(false, $iban->validateIBAN('0AA0ER8A'));
    }

    public function test_validarCCC_casos_correctes() {
        $iban = new IBAN();
        $this->assertEquals(true, $iban->validateCCC('ES6621000418401234567891'));
        $this->assertEquals(true, $iban->validateCCC('ES6000491500051234567892'));
        $this->assertEquals(true, $iban->validateCCC('ES9420805801101234567891'));
        $this->assertEquals(true, $iban->validateCCC('ES9000246912501234567891'));
    }

    public function test_validarCCC_casos_incorrectes() {
        $iban = new IBAN();
        $this->assertEquals(false, $iban->validateCCC('ES7100302053521234567895'));
        $this->assertEquals(false, $iban->validateCCC('ES7240102053560233332116'));
        $this->assertEquals(false, $iban->validateCCC('ES7102313114679921314122'));
    }

    public function test_discoverIBAN() {
        $iban = new IBAN();
        $this->assertEquals('ES6812345678061234567890', $iban->discoverIban('ES68123456780612345678**'));
        $this->assertEquals('ES9121000418450200051332', $iban->discoverIban('ES91210004184502000513**'));
    }
}