<?php

$sourceHtml = __DIR__.DIRECTORY_SEPARATOR.'documentacao-site-pokemon.html';
$buildDir = __DIR__.DIRECTORY_SEPARATOR.'.docx-build';

if (! file_exists($sourceHtml)) {
    fwrite(STDERR, "Documento HTML nao encontrado.\n");
    exit(1);
}

ensureDir($buildDir);
ensureDir($buildDir.DIRECTORY_SEPARATOR.'_rels');
ensureDir($buildDir.DIRECTORY_SEPARATOR.'word');
ensureDir($buildDir.DIRECTORY_SEPARATOR.'word'.DIRECTORY_SEPARATOR.'_rels');

$html = file_get_contents($sourceHtml);
$dom = new DOMDocument('1.0', 'UTF-8');
libxml_use_internal_errors(true);
$dom->loadHTML('<?xml encoding="UTF-8">'.$html);
libxml_clear_errors();
$xpath = new DOMXPath($dom);

$sections = iterator_to_array($xpath->query('//section[contains(concat(" ", normalize-space(@class), " "), " page ")]'));

$bodyXml = '';
$bodyXml .= pageBreakParagraph();

$summary = $sections[1] ?? null;
if ($summary) {
    $bodyXml .= paragraph('Sumário', 'Title', ['align' => 'center']);
    foreach ($xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " toc-row ")]', $summary) as $row) {
        $spans = [];
        foreach ($row->childNodes as $child) {
            if ($child instanceof DOMElement && strtolower($child->tagName) === 'span') {
                $spans[] = cleanText($child->textContent);
            }
        }

        if (count($spans) < 3) {
            continue;
        }

        $number = $spans[0];
        $title = $spans[1];
        $page = $spans[count($spans) - 1];
        $class = $row->getAttribute('class');
        $indent = str_contains($class, 'deep') ? 720 : (str_contains($class, 'sub') ? 360 : 0);
        $dots = str_repeat('.', max(12, 78 - strlen($number.' '.$title.' '.$page)));

        $bodyXml .= paragraph(trim($number.' '.$title.' '.$dots.' '.$page), 'TocLine', ['indent' => $indent]);
    }
}

$bodyXml .= sectionBreakNoFooter();

$contentPages = array_slice($sections, 2);
$lastIndex = count($contentPages) - 1;
foreach ($contentPages as $index => $section) {
    $bodyXml .= convertSection($section);

    if ($index !== $lastIndex) {
        $bodyXml .= pageBreakParagraph();
    }
}

$documentXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    .'<w:document xmlns:wpc="http://schemas.microsoft.com/office/word/2010/wordprocessingCanvas" '
    .'xmlns:mc="http://schemas.openxmlformats.org/markup-compatibility/2006" '
    .'xmlns:o="urn:schemas-microsoft-com:office:office" '
    .'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" '
    .'xmlns:m="http://schemas.openxmlformats.org/officeDocument/2006/math" '
    .'xmlns:v="urn:schemas-microsoft-com:vml" '
    .'xmlns:wp14="http://schemas.microsoft.com/office/word/2010/wordprocessingDrawing" '
    .'xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing" '
    .'xmlns:w10="urn:schemas-microsoft-com:office:word" '
    .'xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main" '
    .'xmlns:w14="http://schemas.microsoft.com/office/word/2010/wordml" '
    .'xmlns:wpg="http://schemas.microsoft.com/office/word/2010/wordprocessingGroup" '
    .'xmlns:wpi="http://schemas.microsoft.com/office/word/2010/wordprocessingInk" '
    .'xmlns:wne="http://schemas.microsoft.com/office/word/2006/wordml" '
    .'xmlns:wps="http://schemas.microsoft.com/office/word/2010/wordprocessingShape" '
    .'mc:Ignorable="w14 wp14">'
    .'<w:body>'
    .$bodyXml
    .sectionPropertiesWithFooter()
    .'</w:body></w:document>';

file_put_contents($buildDir.DIRECTORY_SEPARATOR.'[Content_Types].xml', contentTypesXml());
file_put_contents($buildDir.DIRECTORY_SEPARATOR.'_rels'.DIRECTORY_SEPARATOR.'.rels', packageRelsXml());
file_put_contents($buildDir.DIRECTORY_SEPARATOR.'word'.DIRECTORY_SEPARATOR.'document.xml', $documentXml);
file_put_contents($buildDir.DIRECTORY_SEPARATOR.'word'.DIRECTORY_SEPARATOR.'styles.xml', stylesXml());
file_put_contents($buildDir.DIRECTORY_SEPARATOR.'word'.DIRECTORY_SEPARATOR.'settings.xml', settingsXml());
file_put_contents($buildDir.DIRECTORY_SEPARATOR.'word'.DIRECTORY_SEPARATOR.'footer1.xml', footerXml());
file_put_contents($buildDir.DIRECTORY_SEPARATOR.'word'.DIRECTORY_SEPARATOR.'_rels'.DIRECTORY_SEPARATOR.'document.xml.rels', documentRelsXml());

echo "Build DOCX source generated at {$buildDir}\n";

function ensureDir(string $path): void
{
    if (! is_dir($path)) {
        mkdir($path, 0777, true);
    }
}

function convertSection(DOMElement $section): string
{
    $xml = '';
    foreach ($section->childNodes as $child) {
        $xml .= convertNode($child);
    }

    return $xml;
}

function convertNode(DOMNode $node): string
{
    if (! $node instanceof DOMElement) {
        return '';
    }

    if (hasClass($node, 'page-number')) {
        return '';
    }

    $tag = strtolower($node->tagName);

    return match ($tag) {
        'h1' => paragraph(cleanText($node->textContent), 'Heading1'),
        'h2' => paragraph(cleanText($node->textContent), 'Heading2'),
        'h3' => paragraph(cleanText($node->textContent), 'Heading3'),
        'p' => paragraph(cleanText($node->textContent), 'Normal', ['justify' => true]),
        'ul' => listBlock($node, 'bullet'),
        'ol' => listBlock($node, 'number'),
        'table' => tableBlock($node),
        'div' => divBlock($node),
        default => convertChildren($node),
    };
}

function convertChildren(DOMNode $node): string
{
    $xml = '';
    foreach ($node->childNodes as $child) {
        $xml .= convertNode($child);
    }

    return $xml;
}

function divBlock(DOMElement $node): string
{
    if (hasClass($node, 'note')) {
        return paragraph('Observação: '.cleanText($node->textContent), 'Note');
    }

    if (hasClass($node, 'arrow')) {
        return paragraph(cleanText($node->textContent), 'Normal', ['align' => 'center', 'bold' => true]);
    }

    return convertChildren($node);
}

function listBlock(DOMElement $node, string $type): string
{
    $xml = '';
    $index = 1;
    foreach ($node->childNodes as $child) {
        if (! $child instanceof DOMElement || strtolower($child->tagName) !== 'li') {
            continue;
        }

        $prefix = $type === 'number' ? $index.'. ' : '• ';
        $xml .= paragraph($prefix.cleanText($child->textContent), 'ListParagraph');
        $index++;
    }

    return $xml;
}

function tableBlock(DOMElement $table): string
{
    $xml = '<w:tbl><w:tblPr><w:tblW w:w="0" w:type="auto"/>'
        .'<w:tblBorders>'
        .'<w:top w:val="single" w:sz="4" w:space="0" w:color="B8B8B8"/>'
        .'<w:left w:val="single" w:sz="4" w:space="0" w:color="B8B8B8"/>'
        .'<w:bottom w:val="single" w:sz="4" w:space="0" w:color="B8B8B8"/>'
        .'<w:right w:val="single" w:sz="4" w:space="0" w:color="B8B8B8"/>'
        .'<w:insideH w:val="single" w:sz="4" w:space="0" w:color="B8B8B8"/>'
        .'<w:insideV w:val="single" w:sz="4" w:space="0" w:color="B8B8B8"/>'
        .'</w:tblBorders><w:tblCellMar><w:top w:w="90" w:type="dxa"/><w:left w:w="90" w:type="dxa"/><w:bottom w:w="90" w:type="dxa"/><w:right w:w="90" w:type="dxa"/></w:tblCellMar>'
        .'</w:tblPr>';

    foreach ($table->getElementsByTagName('tr') as $tr) {
        $xml .= '<w:tr>';
        foreach ($tr->childNodes as $cell) {
            if (! $cell instanceof DOMElement || ! in_array(strtolower($cell->tagName), ['td', 'th'], true)) {
                continue;
            }

            $isHeader = strtolower($cell->tagName) === 'th';
            $xml .= '<w:tc><w:tcPr>';
            if ($isHeader) {
                $xml .= '<w:shd w:fill="E5E7EB"/>';
            }
            $xml .= '</w:tcPr>';
            $xml .= paragraph(cleanText($cell->textContent), $isHeader ? 'TableHeader' : 'TableText');
            $xml .= '</w:tc>';
        }
        $xml .= '</w:tr>';
    }

    $xml .= '</w:tbl>';

    return $xml.paragraph('', 'Normal');
}

function paragraph(string $text, string $style = 'Normal', array $options = []): string
{
    $text = cleanText($text);
    $align = $options['align'] ?? null;
    $indent = (int) ($options['indent'] ?? 0);
    $justify = ! empty($options['justify']);
    $bold = ! empty($options['bold']);

    $pPr = '<w:pPr>';
    if ($style !== 'Normal') {
        $pPr .= '<w:pStyle w:val="'.xml($style).'"/>';
    }
    if ($align) {
        $pPr .= '<w:jc w:val="'.xml($align).'"/>';
    } elseif ($justify) {
        $pPr .= '<w:jc w:val="both"/>';
    }
    if ($indent > 0) {
        $pPr .= '<w:ind w:left="'.$indent.'"/>';
    }
    $pPr .= '</w:pPr>';

    $rPr = $bold ? '<w:rPr><w:b/></w:rPr>' : '';

    return '<w:p>'.$pPr.'<w:r>'.$rPr.'<w:t xml:space="preserve">'.xml($text).'</w:t></w:r></w:p>';
}

function pageBreakParagraph(): string
{
    return '<w:p><w:r><w:br w:type="page"/></w:r></w:p>';
}

function sectionBreakNoFooter(): string
{
    return '<w:p><w:pPr><w:sectPr><w:type w:val="nextPage"/>'.pageSetupXml().'</w:sectPr></w:pPr></w:p>';
}

function sectionPropertiesWithFooter(): string
{
    return '<w:sectPr><w:footerReference w:type="default" r:id="rId3"/>'.pageSetupXml().'<w:pgNumType w:start="1"/></w:sectPr>';
}

function pageSetupXml(): string
{
    return '<w:pgSz w:w="11906" w:h="16838"/><w:pgMar w:top="1418" w:right="1304" w:bottom="1418" w:left="1304" w:header="708" w:footer="708" w:gutter="0"/>';
}

function hasClass(DOMElement $node, string $class): bool
{
    return str_contains(' '.$node->getAttribute('class').' ', ' '.$class.' ');
}

function cleanText(?string $text): string
{
    $text = html_entity_decode((string) $text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = str_replace(["\xc2\xa0", "\r", "\n", "\t"], ' ', $text);
    $text = preg_replace('/\s+/u', ' ', $text);

    return trim($text);
}

function xml(string $text): string
{
    return htmlspecialchars($text, ENT_XML1 | ENT_COMPAT, 'UTF-8');
}

function contentTypesXml(): string
{
    return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
        .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
        .'<Default Extension="xml" ContentType="application/xml"/>'
        .'<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>'
        .'<Override PartName="/word/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"/>'
        .'<Override PartName="/word/settings.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.settings+xml"/>'
        .'<Override PartName="/word/footer1.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.footer+xml"/>'
        .'</Types>';
}

function packageRelsXml(): string
{
    return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>'
        .'</Relationships>';
}

function documentRelsXml(): string
{
    return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
        .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/settings" Target="settings.xml"/>'
        .'<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/footer" Target="footer1.xml"/>'
        .'</Relationships>';
}

function stylesXml(): string
{
    return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        .'<w:styles xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
        .'<w:docDefaults><w:rPrDefault><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman" w:cs="Times New Roman"/><w:sz w:val="24"/></w:rPr></w:rPrDefault></w:docDefaults>'
        .style('Normal', 'Normal', 24, false, false)
        .style('Title', 'Title', 34, true, false)
        .style('Heading1', 'Heading 1', 30, true, false)
        .style('Heading2', 'Heading 2', 25, true, false)
        .style('Heading3', 'Heading 3', 23, true, false)
        .style('TocLine', 'TOC Line', 23, false, false)
        .style('ListParagraph', 'List Paragraph', 23, false, false, 360)
        .style('TableHeader', 'Table Header', 18, true, false)
        .style('TableText', 'Table Text', 18, false, false)
        .style('Note', 'Note', 22, false, true)
        .'</w:styles>';
}

function style(string $id, string $name, int $size, bool $bold, bool $italic, int $indent = 0): string
{
    $rPr = '<w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman" w:cs="Times New Roman"/><w:sz w:val="'.$size.'"/>';
    if ($bold) {
        $rPr .= '<w:b/>';
    }
    if ($italic) {
        $rPr .= '<w:i/>';
    }
    $rPr .= '</w:rPr>';

    $pPr = '<w:pPr><w:spacing w:after="120"/>';
    if ($indent > 0) {
        $pPr .= '<w:ind w:left="'.$indent.'"/>';
    }
    $pPr .= '</w:pPr>';

    return '<w:style w:type="paragraph" w:styleId="'.xml($id).'"><w:name w:val="'.xml($name).'"/>'.$pPr.$rPr.'</w:style>';
}

function settingsXml(): string
{
    return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        .'<w:settings xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
        .'<w:updateFields w:val="true"/>'
        .'</w:settings>';
}

function footerXml(): string
{
    return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        .'<w:ftr xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
        .'<w:p><w:pPr><w:jc w:val="right"/></w:pPr>'
        .'<w:r><w:fldChar w:fldCharType="begin"/></w:r>'
        .'<w:r><w:instrText xml:space="preserve"> PAGE </w:instrText></w:r>'
        .'<w:r><w:fldChar w:fldCharType="separate"/></w:r>'
        .'<w:r><w:t>1</w:t></w:r>'
        .'<w:r><w:fldChar w:fldCharType="end"/></w:r>'
        .'</w:p></w:ftr>';
}
