<?php
namespace Tests\PhpMimeMailParser;

use PhpMimeMailParser\Parser;

/**
 * @covers \PhpMimeMailParser\Parser
 */
final class ParseHeaderTest extends TestCase
{

    /**
     * @dataProvider provideEmails
     */
    public function testFromPath($id, $subject, $from, $to, $textBody, $htmlBody, $attachments): void
    {
        $parser = Parser::fromPath(__DIR__.'/emails/'.$id.'.eml');

        $this->generic($parser, $id, $subject, $from, $to, $textBody, $htmlBody, $attachments);
    }

    /**
     * @dataProvider provideEmails
     */
    public function testFromText($id, $subject, $from, $to, $textBody, $htmlBody, $attachments): void
    {
        $parser = Parser::fromText(file_get_contents(__DIR__.'/emails/'.$id.'.eml'));

        $this->generic($parser, $id, $subject, $from, $to, $textBody, $htmlBody, $attachments);
    }

    /**
     * @dataProvider provideEmails
     */
    public function testFromStream($id, $subject, $from, $to, $textBody, $htmlBody, $attachments): void
    {
        $parser = Parser::fromStream(fopen(__DIR__.'/emails/'.$id.'.eml', 'r'));

        $this->generic($parser, $id, $subject, $from, $to, $textBody, $htmlBody, $attachments);
    }

    private function generic($parser, $id, $subject, $from, $to, $textBody, $htmlBody, $attachments): void
    {
        $attachDir = $this->tempdir("attachments_$id");

        //Test Header : subject
        $this->assertEquals($subject, $parser->getSubject());
        $this->assertArrayHasKey('subject', $parser->getHeaders());

        //Test Header : from
        $this->assertEquals($from['name'], $parser->getAddressesFrom()[0]['display']);
        $this->assertEquals($from['email'], $parser->getAddressesFrom()[0]['address']);
        $this->assertEquals($from['is_group'], $parser->getAddressesFrom()[0]['is_group']);
        $this->assertEquals($from['header_value'], $parser->getFrom());
        $this->assertArrayHasKey('from', $parser->getHeaders());

        //Test Header : to
        $this->assertEquals($to[0]['name'], $parser->getAddressesTo()[0]['display']);
        $this->assertEquals($to[0]['email'], $parser->getAddressesTo()[0]['address']);
        $this->assertEquals($to[0]['is_group'], $parser->getAddressesTo()[0]['is_group']);
        $this->assertEquals($to['header_value'], $parser->getHeader('to'));
        $this->assertArrayHasKey('to', $parser->getHeaders());
    }

    public function testInvalidHeader()
    {
        $parser = Parser::fromPath(__DIR__.'/emails/m001.eml');

        $this->assertNull($parser->getHeader('azerty'));
        $this->assertArrayNotHasKey('azerty', $parser->getHeaders());
    }

    public function testRawHeaders()
    {
        $parser = Parser::fromPath(__DIR__.'/emails/m001.eml');

        $this->assertIsArray($parser->getHeadersRaw());
    }
}
