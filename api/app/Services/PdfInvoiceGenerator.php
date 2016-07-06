<?php

namespace App\Services;

use App\Services\TCPDF\TCPDF;
use App\Models\Setting;

class PdfInvoiceGenerator {

    private static $trans = array();
    private static $invoiceType = ''; // 'foreign' or 'domestic'

    public static function generateDomestic( $invoice )
    {
        self::setTranslation('domestic');
        return self::generate($invoice);
    }


    public static function generateForeign( $invoice )
    {
        self::setTranslation('foreign');
        return self::generate($invoice);
    }

    protected static function generate( $invoice )
    {
        // create new PDF document
        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // custom information
        $pdf->branding = $invoice->branding;
        $pdf->marginLeft = 22;

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor( $invoice->issuer_info );
        $pdf->SetTitle('Invoice / Factura / Facture');
        $pdf->SetSubject('Automatically generated invoice');
        $pdf->SetKeywords('invoice');

        $pdf->SetHeaderData();
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(20, 15, 10);
        $pdf->SetHeaderMargin(0);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        $pdf->AddFont('arial');
        $pdf->AddFont('arialbd');
        $pdf->AddFont('ariali');
        $pdf->AddFont('arialn');
        $pdf->AddFont('proximanova-bold');
        $pdf->AddFont('proximanova-regular');
        $pdf->AddFont('proximanovacond-regular');
        $pdf->AddFont('titilliumtext25l');

        // ---------------------------------------------------------

        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
        $pdf->setFooterData(array(0,64,0), array(0,64,128));
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));

        // ---------------------------------------------------------

        $pdf->setFontSubsetting(true);
        $pdf->SetFont('dejavusans', '', 14, '', true);

        $pdf->AddPage();

        $pdf->setFontSpacing( -0.1 );

        $pdf->CreateTextBox( ucfirst(self::trans('invoice')), 0, 10, 70, 10, 'proximanova-bold', 34, '', 'L', array( 88, 85, 112 ) );

        $pdf->CreateTextBox( ucfirst(self::trans('no.')), 109-22, 14.6, 41, 7, 'proximanovacond-regular', 11, '', 'R', array( 88, 85, 112 ) );
        $pdf->CreateTextBox( $invoice->invoice, 109-22, 19, 41, 8, 'proximanova-bold', 14, '', 'R' );

        $pdf->CreateTextBox( ucwords(self::trans('date issued')), 156-22, 14.6, 40, 7, 'proximanovacond-regular', 11, '', 'R', array( 88, 85, 112 ) );
        $pdf->CreateTextBox(date('j-M-Y', strtotime( $invoice->issued_on ) ), 156-22, 19, 40, 8, 'proximanova-bold', 14, '', 'R' );

        // ---------------------------------------------------------

        $pdf->CreateTextBox( ucwords(self::trans('seller')), 0, 38, 90, 6, 'proximanovacond-regular', 11, '', 'L', array( 88, 85, 112 ) );
        $pdf->CreateTextBox($invoice->seller_name, 0, 43, 90, 8, 'proximanova-bold', 14);
        $pdf->SetXY(0 + $pdf->marginLeft, 49 );
        $pdf->SetFont('arialn', '', 11 );
        $pdf->MultiCell(90, 30, str_replace('\n',"\n",$invoice->seller_info) . "\n\n" . $invoice->issuer_info, 0, 'L', false );

        $pdf->CreateTextBox( ucwords(self::trans('buyer')), 95, 38, 90, 6, 'proximanovacond-regular', 11, '', 'L', array( 88, 85, 112 ) );
        $pdf->CreateTextBox($invoice->buyer_name, 95, 43, 90, 8, 'proximanova-bold', 14);
        $pdf->SetXY(95 + $pdf->marginLeft, 49 );
        $pdf->SetFont('arialn', '', 11 );
        $pdf->MultiCell(90, 30, str_replace('\n',"\n",$invoice->buyer_info) . "\n\n" . $invoice->receiver_info, 0, 'L', false );

        $aPosition[ 'id' ] = 0;
        $aPosition[ 'desc' ] = 9;
        $aPosition[ 'quant' ] = 80;
        $aPosition[ 'unit' ] = 98;
        $aPosition[ 'total' ] = 135; // 145

        $tableHeaderTop = 120; // previously 103
        $pdf->CreateTextBox( strtoupper(self::trans('id')), $aPosition[ 'id' ], $tableHeaderTop, 8, 6, 'proximanovacond-regular', 11, 'B', 'L', array( 88, 85, 112 ) );
        $pdf->CreateTextBox( ucwords(self::trans('description')), $aPosition[ 'desc' ], $tableHeaderTop, 70, 6, 'proximanovacond-regular', 11, 'B', 'L', array( 88, 85, 112 ) );
        $pdf->CreateTextBox( ucwords(self::trans('quantity')), $aPosition[ 'quant' ], $tableHeaderTop, 17, 6, 'proximanovacond-regular', 11, 'B', 'R', array( 88, 85, 112 ) );
        $pdf->CreateTextBox( ucwords(self::trans('unit price')), $aPosition[ 'unit' ], $tableHeaderTop, 36, 6, 'proximanovacond-regular', 11, 'B', 'R', array( 88, 85, 112 ) );
        $pdf->CreateTextBox( ucwords(self::trans('total')), $aPosition[ 'total' ], $tableHeaderTop, 40, 6, 'proximanovacond-regular', 11, 'B', 'R', array( 88, 85, 112 ) );
        $pdf->SetLineStyle( array( 'width' => 0.2, 'color' => array( 88, 85, 112 ) ) );
        $pdf->Line($pdf->marginLeft, $tableHeaderTop + 6, 197, $tableHeaderTop + 6 );

        $orders = array();
        foreach( $invoice->getProducts() as $product ) {
           //dd($product);
            $price = $product['price'];
            $currency = $product['currency'];
            if( self::$invoiceType == 'domestic' && $invoice->isForeign() ) {
                $currency = strtoupper(Setting::getByName('domestic_currency'));
                $price = $product['price_domestic'];
            }
            $aOrder = array(
                'quant'         => $product[ 'quantity' ],
                'descr'         => $product[ 'description' ],
                'unit'          => $price . ' ' . $currency,
                'total'         => $price * $product[ 'quantity' ] . ' ' . $currency,
                '_price'        => $price,
                '_total'         => $price * $product[ 'quantity' ],
                '_currency'     => $currency,
                // extra
            );
            if( self::$invoiceType == 'domestic' && $invoice->isForeign() ) {
                $aOrder['_price2'] = $product['price'];
            }
            $orders[] = $aOrder;
        }

        $currY = $tableHeaderTop + 8;
        $total = 0;
        $total2 = 0;
        $iItem = 0;
        foreach ($orders as $row) {
            $iItem += 1;

            $pdf->CreateTextBox( $iItem, $aPosition[ 'id' ], $currY, 8, 5, 'arial', 9, '', 'L');
            $pdf->CreateTextBox( $row['quant'], $aPosition[ 'quant' ], $currY, 17, 5, 'arial', 9, '', 'R');
            $pdf->CreateTextBox(
                number_format(
                    (float)$row['_price'],
                    (int)Setting::getByName('decimals'),
                    Setting::getByName('decimal_point','.'),
                    Setting::getByName('thousands_separator',',')
                ) . ' ' . $row['_currency'],
                $aPosition[ 'unit' ], $currY, 36, 5, 'arial', 9, '', 'R');
            $pdf->CreateTextBox(
                number_format( (float)$row['_total'], (int)Setting::getByName('decimals'), Setting::getByName('decimal_point','.'), Setting::getByName('thousands_separator',',') ). ' ' . $row['_currency'],
                $aPosition[ 'total' ], $currY, 40, 5, 'arial', 9, '', 'R');
            if( isset( $row['unit2'] ) && isset( $row['total2'] ) ) {
                $pdf->CreateTextBox(
                    number_format( (float)$row['_price2'], (int)Setting::getByName('decimals'), Setting::getByName('decimal_point','.'), Setting::getByName('thousands_separator',',') ). ' ' . $row['_currency2'],
                    $aPosition[ 'unit' ], $currY+4, 36, 5, 'arial', 8, '', 'R');
                $pdf->CreateTextBox(
                    number_format( (float)$row['_total2'], (int)Setting::getByName('decimals'), Setting::getByName('decimal_point','.'), Setting::getByName('thousands_separator',',') ). ' ' . $row['_currency2'],
                    $aPosition[ 'total' ], $currY+4, 40, 5, 'arial', 8, '', 'R');
            }
            $pdf->SetFont('arialbd', '', 9 );
            $pdf->SetXY($aPosition[ 'desc' ] + $pdf->marginLeft, $currY );
            $pdf->MultiCell( 70, 13, $row['descr'], 0, 'L', false, 1, $aPosition[ 'desc' ] + $pdf->marginLeft, $currY, true, 0, false, true, 0, 'T', false );

            $total = $total + $row[ '_price' ] * $row[ 'quant' ];
            if( isset( $row[ '_price2' ] ) ) {
                $total2 = $total2 + $row[ '_price2' ] * $row[ 'quant' ];
            }
            $currY = $pdf->getY() + 1; //$currY+14;
        }

        $pdf->SetLineStyle( array( 'width' => 0.2, 'color' => array( 88, 85, 112 ) ) );
        $pdf->Line( 95, $currY+1, 197, $currY+1 );

        $pdf->CreateTextBox( ucwords(self::trans('subtotal')), $aPosition[ 'unit' ], $currY + 5, 36, 5, 'arial', 9, '', 'R');
        $pdf->CreateTextBox(
            number_format( $total, (int)Setting::getByName('decimals'), Setting::getByName('decimal_point','.'), Setting::getByName('thousands_separator',',') ) . ' ' . $row[ '_currency' ],
            $aPosition[ 'total' ], $currY + 5, 40, 5, 'arial', 9, '', 'R');
        $currY = $currY + 10;

        if( $invoice->vat_percent >= 0 ) {
            $pdf->CreateTextBox( ucwords(sprintf(self::trans('VAT %.2f%%'), $invoice->vat_percent)), $aPosition[ 'unit' ], $currY + 5, 36, 5, 'arial', 9, '', 'R');
            $pdf->CreateTextBox(
                number_format( $total * ($invoice->vat_percent/100),  (int)Setting::getByName('decimals'), Setting::getByName('decimal_point','.'), Setting::getByName('thousands_separator',',') ) .
                ' ' . $row[ '_currency' ], $aPosition[ 'total' ], $currY + 5, 40, 5, 'arial', 9, '', 'R');
        }

        $currY = $currY + 10;

        if( $invoice->vat_percent < 0 ) $invoice->vat_percent = 0;
        $pdf->CreateTextBox( strtoupper(self::trans('invoice total')), $aPosition[ 'unit' ], $currY + 5, 36, 5, 'arialbd', 9, '', 'R');
        $pdf->CreateTextBox(
            number_format( $total + ( $total * ( $invoice->vat_percent / 100 ) ),  (int)Setting::getByName('decimals'), Setting::getByName('decimal_point','.'), Setting::getByName('thousands_separator',',') ) . ' ' . $row[ '_currency' ],
            $aPosition[ 'total' ], $currY + 5, 40, 5, 'arialbd', 9, '', 'R');

        $currY = $currY + 25;


        if (self::$invoiceType == 'domestic' && $invoice->isForeign()) {
            $invoice->extra .= sprintf("<br><br>%s: %.2f %s + TVA %.2f%% = %s %s", ucfirst(self::trans('billed amount')), $total2, $invoice->foreignCurrency(),
                $invoice->vat_percent,
                number_format( $total2 + $total2 * ($invoice->vat_percent/100),  (int)Setting::getByName('decimals'), Setting::getByName('decimal_point','.'), Setting::getByName('thousands_separator',',') ),
                $invoice->foreignCurrency() );
            $exchangeText = sprintf( ucfirst(self::trans('exchange rate on %s') ), date('j-M-Y', strtotime( $invoice->issued_on ) ) );
            $invoice->extra .= sprintf("<br>%s: 1 %s = %.4f %s", $exchangeText,
                $invoice->foreignCurrency(), $invoice->exchangeRate(), Setting::getByName('domestic_currency')
            );
            $invoice->extra .= "<br>";
        }

        // some payment instructions or information
        $pdf->setXY(20, $currY);
        $pdf->SetFont(PDF_FONT_NAME_MAIN, '', 10);
        $pdf->MultiCell(175, 10, $invoice->extra, 0, 'L', 0, 1, '', '', true, null, true);

        // ---------------------------------------------------------
        return $pdf->Output('example_001.pdf', 'S');
    }

    public static function setTranslation( $type )
    {
        if (empty(self::$trans))
            self::loadTranslations();
        switch ($type) {
            case 'domestic':
            case 'foreign':
                self::$invoiceType = $type;
                break;
            default:
                throw new \Exception('Invalid invoice type.');
        }
    }

    protected static function trans( $text )
    {
        if (!empty(self::$trans[ self::$invoiceType ][ $text ]))
            return self::$trans[ self::$invoiceType ][ $text ];
        return $text;
    }

    protected static function loadTranslations()
    {
        /* self::$trans[ 'foreign' ] = array(
            'invoice' => 'invoice',
            'no.'  => 'no.',
            'date issued'  => 'date issued',
            'seller'  => 'seller',
            'buyer'  => 'buyer',
            'id'  => 'id',
            'description'  => 'description',
            'quantity'  => 'quantity',
            'unit price'  => 'unit price',
            'total'  => 'total',
            'subtotal'  => 'subtotal',
            'VAT %s'  => 'VAT %s',
            'invoice total'  => 'invoice total',
            'issued by'  => 'issued by',
            'customer'  => 'customer',
        ); */

        self::$trans[ 'domestic' ] = array(
            'invoice' => 'factura',
            'no.'  => 'nr.',
            'date issued'  => 'data emiterii',
            'seller'  => 'furnizor',
            'buyer'  => 'cumparator',
            'id'  => '#',
            'description'  => 'descriere',
            'quantity'  => 'cantitate',
            'unit price'  => 'pret unitar',
            'total'  => 'total',
            'subtotal'  => 'subtotal',
            'VAT %.2f%%'  => 'TVA %.2f%%',
            'invoice total'  => 'total',
            'issued by'  => 'emisa de',
            'customer'  => 'delegat',
            'billed amount' => 'valoare in valuta',
            'exchange rate on %s' => 'Curs de schimb la %s',
        );
    }
} // END class PdfInvoiceGenerator


// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

    public $marginLeft = 0;

    public $branding = '';

    public function Header()
    {
        // look! look! nothing here!
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->SetXY($this->marginLeft + 0, -25);
        $this->SetTextColor( 189, 191, 193 );
        $this->SetFont('proximanova-regular', '', 108);
        $this->Cell(180, 20, $this->branding, 0, false, 'C', false, '', 3, false, 'C', 'C');

    }

    public function CreateTextBox($textval, $x = 0, $y, $width = 0, $height = 10, $fontname = PDF_FONT_NAME_MAIN,$fontsize = 10, $fontstyle = '', $align = 'L', $fontcolor = array() )
    {
        $this->SetXY($x + $this->marginLeft, $y); // 20 = margin left
        if( count( $fontcolor ) == 3  ) {
            $this->SetTextColor( $fontcolor[0], $fontcolor[1], $fontcolor[2] );
        } else {
            $this->SetTextColor( 33, 33, 33 );
        }
        $this->SetFont($fontname, $fontstyle, $fontsize);
        $this->Cell($width, $height, $textval, 0, false, $align, false, '', 0, false, 'T', 'T');
    }

} // END class MYPDF