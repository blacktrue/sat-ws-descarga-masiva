<?php

declare(strict_types=1);

namespace PhpCfdi\SatWsDescargaMasiva\Tests\Unit\Shared;

use PhpCfdi\SatWsDescargaMasiva\Tests\TestCase;

class InteractsXmlTraitTest extends TestCase
{
    public function testNoSpacesContents(): void
    {
        $source = <<<EOT

<root>
    <foo a="1" b="2">foo</foo>
    
    <bar>
        <baz>
            BAZZ        
        </baz>
    </bar>
</root>

EOT;

        $expected = '<root><foo a="1" b="2">foo</foo><bar><baz>BAZZ</baz></bar></root>';
        $specimen = new InteractsXmlTraitSpecimen();
        $this->assertSame($expected, $specimen->nospaces($source));
    }

    public function testFindElementExpectingOne(): void
    {
        $specimen = new InteractsXmlTraitSpecimen();
        $content = $this->fileContents('verify/response-2-packages.xml');
        $root = $specimen->readXmlElement($content);

        $search = ['body', 'verificaSolicitudDescargaResponse', 'verificaSolicitudDescargaResult'];
        $this->assertCount(1, $specimen->findElements($root, ...$search));
        $this->assertSame(
            $specimen->findElements($root, ...$search)[0],
            $specimen->findElement($root, ...$search)
        );
    }

    public function testFindElementExpectingNone(): void
    {
        $specimen = new InteractsXmlTraitSpecimen();
        $content = $this->fileContents('verify/response-2-packages.xml');
        $root = $specimen->readXmlElement($content);

        $search = ['body', 'foo', 'verificaSolicitudDescargaResult'];
        $this->assertCount(0, $specimen->findElements($root, ...$search));
        $this->assertNull($specimen->findElement($root, ...$search));
    }

    public function testFindElementExpectingTwo(): void
    {
        $specimen = new InteractsXmlTraitSpecimen();
        $content = $this->fileContents('verify/response-2-packages.xml');
        $root = $specimen->readXmlElement($content);

        $search = ['body', 'verificaSolicitudDescargaResponse', 'verificaSolicitudDescargaResult', 'idsPaquetes'];
        $this->assertCount(2, $specimen->findElements($root, ...$search));
        $this->assertSame(
            $specimen->findElements($root, ...$search)[0],
            $specimen->findElement($root, ...$search)
        );
    }

    public function testFindContentWithKnownData(): void
    {
        $specimen = new InteractsXmlTraitSpecimen();
        $content = $this->fileContents('verify/response-2-packages.xml');
        $root = $specimen->readXmlElement($content);

        $search = ['body', 'verificaSolicitudDescargaResponse', 'verificaSolicitudDescargaResult', 'idsPaquetes'];
        $expectedContent = '4e80345d-917f-40bb-a98f-4a73939343c5_01';
        $this->assertSame($expectedContent, $specimen->findContent($root, ...$search));
    }

    public function testFindContentWithNotFoundElement(): void
    {
        $specimen = new InteractsXmlTraitSpecimen();
        $content = $this->fileContents('verify/response-2-packages.xml');
        $root = $specimen->readXmlElement($content);

        $search = ['body', 'verificaSolicitudDescargaResponse', 'FOO', 'idsPaquetes'];
        $this->assertSame('', $specimen->findContent($root, ...$search));
    }

    public function testFindContentWithChildrenWithContentsButNoContentByItsOwn(): void
    {
        $specimen = new InteractsXmlTraitSpecimen();
        $content = $this->fileContents('verify/response-2-packages.xml');
        $root = $specimen->readXmlElement($content);

        $search = ['body', 'verificaSolicitudDescargaResponse', 'verificaSolicitudDescargaResult'];
        $this->assertSame('', $specimen->findContent($root, ...$search));
    }

    public function testFindContents(): void
    {
        $specimen = new InteractsXmlTraitSpecimen();
        $content = $this->fileContents('verify/response-2-packages.xml');
        $root = $specimen->readXmlElement($content);

        $search = ['body', 'verificaSolicitudDescargaResponse', 'verificaSolicitudDescargaResult', 'idsPaquetes'];
        $expectedContents = [
            '4e80345d-917f-40bb-a98f-4a73939343c5_01',
            '4e80345d-917f-40bb-a98f-4a73939343c5_02',
        ];
        $this->assertSame($expectedContents, $specimen->findContents($root, ...$search));
    }

    public function testFindAttributesExpectingResults(): void
    {
        $specimen = new InteractsXmlTraitSpecimen();
        $content = $this->fileContents('verify/response-2-packages.xml');
        $root = $specimen->readXmlElement($content);

        $search = ['body', 'verificaSolicitudDescargaResponse', 'verificaSolicitudDescargaResult'];
        $expectedContents = [
            'codestatus' => '5000',
            'estadosolicitud' => '3',
            'codigoestadosolicitud' => '5000',
            'numerocfdis' => '12345',
            'mensaje' => 'Solicitud Aceptada',
        ];
        $this->assertSame($expectedContents, $specimen->findAttributes($root, ...$search));
    }

    public function testFindAttributesOnNonExistentNode(): void
    {
        $specimen = new InteractsXmlTraitSpecimen();
        $content = $this->fileContents('verify/response-2-packages.xml');
        $root = $specimen->readXmlElement($content);

        $search = ['body', 'FOO', 'verificaSolicitudDescargaResult'];
        $this->assertSame([], $specimen->findAttributes($root, ...$search));
    }
}
