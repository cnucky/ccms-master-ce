<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-7
 * Time: 下午3:16
 */

namespace Tests\Unit;


use App\Module\Resource\FeeProcessor;
use Tests\TestCase;

class FeeProcessorTest extends TestCase
{
    public function testMultiply100()
    {
        $this->assertSame("1", FeeProcessor::multiply100("0.01"));
        $this->assertSame("10", FeeProcessor::multiply100("0.1"));
        $this->assertSame("11", FeeProcessor::multiply100("0.11"));

        $this->assertSame("100", FeeProcessor::multiply100("1."));
        $this->assertSame("130", FeeProcessor::multiply100("1.3"));
        $this->assertSame("135", FeeProcessor::multiply100("1.35"));

        $this->assertSame("100", FeeProcessor::multiply100("1"));
        $this->assertSame("1000", FeeProcessor::multiply100("10"));
        $this->assertSame("11100", FeeProcessor::multiply100("111"));

        $this->assertSame("0", FeeProcessor::multiply100("0.0"));
        $this->assertSame("0", FeeProcessor::multiply100("0."));
        $this->assertSame("0", FeeProcessor::multiply100(".0"));
        $this->assertSame("0", FeeProcessor::multiply100(""));
    }

    public function testDivide100()
    {
        $this->assertSame("0.00", FeeProcessor::divide100("0"));
        $this->assertSame("0.01", FeeProcessor::divide100("1"));
        $this->assertSame("0.11", FeeProcessor::divide100("11"));
        $this->assertSame("1.11", FeeProcessor::divide100("111"));
        $this->assertSame("11.30", FeeProcessor::divide100("1130"));
        $this->assertSame("2222.00", FeeProcessor::divide100("222200"));
    }
}