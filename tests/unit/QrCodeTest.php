<?php

use Da\QrCode\Contracts\ErrorCorrectionLevelInterface;
use Da\QrCode\Contracts\LabelInterface;
use Da\QrCode\Format\MailToFormat;
use Da\QrCode\Label;
use Da\QrCode\QrCode;
use Da\QrCode\Writer\EpsWriter;
use Da\QrCode\Writer\JpgWriter;
use Da\QrCode\Writer\SvgWriter;


class QrCodeTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;


    public function testRaw()
    {
        $qrCode = new QrCode('2amigOS');
        $out = $qrCode->writeString();
        $out = base64_encode($out);
        $expected = file_get_contents(codecept_data_dir('out.txt'));

        $this->tester->assertEquals($expected, $out);
    }

    public function testPng()
    {
        $qrCode = new QrCode((new MailToFormat(['email' => 'hola@2amigos.us'])));
        $out = $qrCode->writeString();
        $this->tester->assertEquals(file_get_contents(codecept_data_dir('data.png')), $out);
    }

    public function testJpg()
    {
        $writer = new JpgWriter();
        $qrCode = new QrCode((new MailToFormat(['email' => 'hola@2amigos.us'])), null, $writer);
        $out = $qrCode->writeString();
        $this->tester->assertEquals(file_get_contents(codecept_data_dir('data.jpg')), $out);
    }

    public function testEps()
    {
        $writer = new EpsWriter();
        $qrCode = new QrCode((new MailToFormat(['email' => 'hola@2amigos.us'])), null, $writer);
        $out = $qrCode->writeString();
        $this->tester->assertEquals(file_get_contents(codecept_data_dir('data.eps')), $out);
    }

    public function testSvg()
    {
        $writer = new SvgWriter();
        $qrCode = new QrCode((new MailToFormat(['email' => 'hola@2amigos.us'])), null, $writer);
        $out = $qrCode->writeString();
        $this->tester->assertEquals(file_get_contents(codecept_data_dir('data.svg')), $out);
    }

    public function testLogo()
    {
        $out = (new QrCode(strtoupper('https://2amigos.us'), ErrorCorrectionLevelInterface::HIGH))
            ->useLogo(codecept_data_dir('logo.png'))
            ->writeString();

        $this->tester->assertEquals(file_get_contents(codecept_data_dir('data-logo.png')), $out);
    }

    public function testLabel()
    {
        $label = new Label('2amigos.us');

        $out = (new QrCode(strtoupper('https://2amigos.us'), ErrorCorrectionLevelInterface::HIGH))
            ->setLabel($label)
            ->writeString();

        $this->tester->assertEquals(file_get_contents(codecept_data_dir('data-label.png')), $out);
    }

    public function testQrColored()
    {
        $qrCode = new QrCode((new MailToFormat(['email' => 'hola@2amigos.us'])));
        $out = $qrCode
            ->useForegroundColor(51, 153, 255)
            ->writeString();
        $this->tester->assertEquals(file_get_contents(codecept_data_dir('data-color.png')), $out);
    }

    public function testAttributes()
    {
        $file = __DIR__ . '/data/data-attributes-test.png';

        $label = (new Label('2amigos'))
            ->useFont(__DIR__ . '/../../resources/fonts/monsterrat.otf')
            ->updateFontSize(12);

        $this->tester->assertEquals('2amigos', $label->getText());
        $this->tester->assertEquals(LabelInterface::ALIGN_CENTER, $label->getAlignment());
        $margins = $label->getMargins();
        $this->tester->assertEquals(0, $margins['t']);
        $this->tester->assertEquals(10, $margins['r']);
        $this->tester->assertEquals(10, $margins['b']);
        $this->tester->assertEquals(10, $margins['l']);
        $this->tester->assertEquals(realpath(__DIR__ . '/../../resources/fonts/monsterrat.otf'), $label->getFont());
        $this->tester->assertEquals(12, $label->getFontSize());


        $qrCode = (new QrCode('Test text'))
            ->useLogo(codecept_data_dir('logo.png'))
            ->useForegroundColor(51, 153, 255)
            ->useBackgroundColor(200, 220, 210)
            ->useEncoding('UTF-8')
            ->setErrorCorrectionLevel(ErrorCorrectionLevelInterface::HIGH)
            ->setLogoWidth(60)
            ->setText('https://2amigos.us')
            ->setSize(300)
            ->setMargin(5)
            ->setLabel($label);

        $this->tester->assertEquals(realpath(codecept_data_dir('logo.png')), $qrCode->getLogoPath());
        $foregroundColor = $qrCode->getForegroundColor();
        $this->tester->assertEquals(51, $foregroundColor['r']);
        $this->tester->assertEquals(153, $foregroundColor['g']);
        $this->tester->assertEquals(255, $foregroundColor['b']);
        $backgroundColor = $qrCode->getBackgroundColor();
        $this->tester->assertEquals(200, $backgroundColor['r']);
        $this->tester->assertEquals(220, $backgroundColor['g']);
        $this->tester->assertEquals(210, $backgroundColor['b']);
        $this->tester->assertEquals('UTF-8', $qrCode->getEncoding());
        $this->tester->assertEquals(ErrorCorrectionLevelInterface::HIGH, $qrCode->getErrorCorrectionLevel());
        $this->tester->assertEquals(60, $qrCode->getLogoWidth());
        $this->tester->assertEquals('https://2amigos.us', $qrCode->getText());
        $this->tester->assertEquals('image/png', $qrCode->getContentType());
        $this->tester->assertEquals($label, $qrCode->getLabel());
        $out = $qrCode->writeString();
        $this->tester->assertEquals(file_get_contents(codecept_data_dir('data-attributes.png')), $out);
    }
}
