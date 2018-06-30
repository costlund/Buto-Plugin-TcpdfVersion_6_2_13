<?php
class PluginTcpdfVersion_6_2_13{
  /**
   <p>Add method param to fill $pdf with data.</p>
   */
  public function widget_output($widget_data){
    /**
     * Include.
     */
    wfPlugin::includeonce('wf/array');
    /**
     * Get widget default.
     */
    $data = wfPlugin::getWidgetDefault($widget_data);
    /**
     * Merge data.
     */
    $data = new PluginWfArray(array_merge($data->get(), $widget_data['data']));
    /**
     * Image path in SetHeaderData should be set from the web folder. 
     */
    define ('K_PATH_IMAGES', '');
    /**
     * Include tcpdf.
     */
    include_once dirname(__FILE__).'/lib/tcpdf.php';
    /**
     * Create doc.
     */
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    /**
     * Custom values.
     */
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor($data->get('author'));
    $pdf->SetTitle($data->get('title'));
    $pdf->SetSubject($data->get('subject'));
    $pdf->SetKeywords($data->get('keywords'));
    if($data->get('header_logo') && $data->get('header_logo_width')){
      $header_logo = wfArray::get($GLOBALS, 'sys/web_dir').$data->get('header_logo');
      $pdf->SetHeaderData($header_logo, $data->get('header_logo_width'), $data->get('header_title'), $data->get('header_string'));
    }
    /**
     * Standard values.
     */
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    if (false && @file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
    }
    /**
     * Data method.
     */
    if($data->get('data_method/plugin') && $data->get('data_method/method')){
      wfPlugin::includeonce($data->get('data_method/plugin'));
      $obj = wfSettings::getPluginObj($data->get('data_method/plugin'));
      $method = $data->get('data_method/method');
      $data = $obj->$method($data);
    }
    /**
     * Pages.
     */
    if($data->get('pages')){
      foreach ($data->get('pages') as $ke => $valu) {
        $pdf->AddPage();
        foreach ($valu as $key => $value) {
          $item = new PluginWfArray($value);
          $method  = $item->get('method');
          if($method == 'MultiCell'){
            $pdf = $this->MultiCell($pdf, $item, $data);
          }elseif($method == 'Cell'){
            $pdf = $this->Cell($pdf, $item);
          }elseif($method == 'SetFont'){
            $pdf = $this->SetFont($pdf, $item);
          }elseif($method == 'AddPage'){
            $pdf = $this->AddPage($pdf, $item);
          }elseif($method == 'Ln'){
            $pdf = $this->Ln($pdf, $item);
          }elseif($method == 'MoveY'){
            $pdf = $this->MoveY($pdf, $item);
          }elseif($method == 'SetTextColor'){
            $pdf = $this->SetTextColor($pdf, $item);
          }elseif($method == 'SetFillColor'){
            $pdf = $this->SetFillColor($pdf, $item);
          }elseif($method == 'WriteHTML'){
            $pdf = $this->WriteHTML($pdf, $item);
          }elseif($method == 'Line'){
            $pdf = $this->Line($pdf, $item);
          }elseif($method == 'Image'){
            $pdf = $this->Image($pdf, $item);
          }elseif($method == 'Text'){
            $pdf = $this->Text($pdf, $item);
          }
        }
      }
    }
    /**
     * Run method if set.
     */
    if($data->get('method/plugin') && $data->get('method/method')){
      wfPlugin::includeonce($data->get('method/plugin'));
      $obj = wfSettings::getPluginObj($data->get('method/plugin'));
      $method = $data->get('method/method');
      $pdf = $obj->$method($pdf);
    }
    /**
     * Output.
     */
    $pdf->Output($data->get('filename'), 'I');
    exit;
  }
  private function Line($pdf, $item){
    $x1=10; $y1=10; $x2=20; $y2=20; $style = array();
    if($item->get('data')){
      foreach ($item->get('data') as $key2 => $value2){
        if(!is_array($value2)){
          eval('$'.$key2.' = "'.$value2.'";');
        }else{
          eval('$$key2 = $value2;');
        }
      }
    }
    $pdf->Line( $x1, $y1, $x2, $y2, $style );
    return $pdf;
  }
  private function WriteHTML($pdf, $item){
    $html = ''; $ln = true; $fill = false; $reseth = false; $cell = false; $align = '';
    if($item->get('data')){foreach ($item->get('data') as $key2 => $value2){eval('$'.$key2.' = "'.$value2.'";');}}
    $pdf->writeHTML( $html, $ln, $fill, $reseth, $cell, $align );
    return $pdf;
  }
  private function MultiCell($pdf, $item, $data){
    $w = 40;
    $h = 10;
    $txt = 'Multicell text.';
    $border = 1;
    $align = 'J';
    $fill = false;
    $ln = 1;
    $x = '';
    $y = '';
    $reseth = true;
    $stretch = 0;
    $ishtml = false;
    $autopadding = true;
    $maxh = 0;
    $valign = 'T';
    $fitcell = false;
    if($item->get('data')){foreach ($item->get('data') as $key2 => $value2){eval('$'.$key2.' = "'.$value2.'";');}}
    if($item->get('data/y_minus')){
      $y = $pdf->GetY() - $item->get('data/y_minus');
    }
    if(substr($txt, 0, 5)=='data:'){
      $txt = $data->get(str_replace('data:', '', $txt));
    }
    $pdf->MultiCell( $w, $h, $txt, $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    return $pdf;
  }
  private function Cell($pdf, $item){
    $w = 40;
    $h = 5;
    $txt = 'Cell text.';
    $border = 1;
    $ln = 0;
    $align = '';
    $fill = false;
    $link = '';
    $stretch = 0;
    $ignore_min_height = false;
    $calign = 'T';
    $valign = 'T'; //M
    if($item->get('data')){foreach ($item->get('data') as $key2 => $value2){eval('$'.$key2.' = "'.$value2.'";');}}
    $pdf->Cell( $w, $h, $txt, $border, $ln, $align, $fill, $link, $stretch, $ignore_min_height, $calign, $valign );
    return $pdf;
  }
  private function SetFont($pdf, $item){
    $family = 'helvetica';
    $style = '';
    $size = null;
    $fontfile = '';
    $subset = 'default';
    $out = true;
    if($item->get('data')){foreach ($item->get('data') as $key2 => $value2){eval('$'.$key2.' = "'.$value2.'";');}}
    $pdf->SetFont( $family, $style, $size, $fontfile, $subset, $out );
    return $pdf;
  }
  private function AddPage($pdf, $item){
    $orientation = '';$format = '';$keepmargins = false;$tocpage = false;
    if($item->get('data')){foreach ($item->get('data') as $key2 => $value2){eval('$'.$key2.' = "'.$value2.'";');}}
    $pdf->AddPage( $orientation, $format, $keepmargins, $tocpage);
    return $pdf;
  }
  private function Ln($pdf, $item){
    $h = ''; $cell = false;
    if($item->get('data')){foreach ($item->get('data') as $key2 => $value2){eval('$'.$key2.' = "'.$value2.'";');}}
    $pdf->Ln( $h, $cell );
    return $pdf;
  }
  private function MoveY($pdf, $item){
    if($item->get('data/y')){
      $pdf->SetY($pdf->GetY()+$item->get('data/y'));
    }
    return $pdf;
  }
  private function SetTextColor($pdf, $item){
    $col1 = 0; $col2 = -1; $col3 = -1; $col4 = -1; $ret = false; $name = '';
    if($item->get('data')){foreach ($item->get('data') as $key2 => $value2){eval('$'.$key2.' = "'.$value2.'";');}}
    $pdf->SetTextColor( $col1, $col2, $col3, $col4, $ret, $name );
    return $pdf;
  }
  private function SetFillColor($pdf, $item){
    $col1 = 0; $col2 = 0; $col3 = 0;
    if($item->get('data')){foreach ($item->get('data') as $key2 => $value2){eval('$'.$key2.' = "'.$value2.'";');}}
    $pdf->SetFillColor( $col1, $col2, $col3 );
    return $pdf;
  }
  private function Image($pdf, $item){
    $file; $x = ''; $y = ''; $w = 0; $h = 0; $type = ''; $link = ''; $align = ''; $resize = false; $dpi = 300; $palign = ''; $ismask = false; $imgmask = false; $border = 0; $fitbox = false; $hidden = false; $fitonpage = false; $alt = false; $altimgs = array();
    if($item->get('data')){foreach ($item->get('data') as $key2 => $value2){eval('$'.$key2.' = "'.$value2.'";');}}
    $file = wfSettings::replaceDir($file);
    $pdf->Image( $file, $x, $y, $w, $h, $type, $link, $align, $resize, $dpi, $palign, $ismask, $imgmask, $border, $fitbox, $hidden, $fitonpage, $alt, $altimgs );
    return $pdf;
  }
  private function Text($pdf, $item){
    $x = ''; $y = ''; $txt = ''; $fstroke = false; $fclip = false; $ffill = true; $border = 0; $ln = 0; $align = ''; $fill = false; $link = ''; $stretch = 0; $ignore_min_height = false; $calign = 'T'; $valign = 'M'; $rtloff = false;
    if($item->get('data')){foreach ($item->get('data') as $key2 => $value2){eval('$'.$key2.' = "'.$value2.'";');}}
    $pdf->Text( $x, $y, $txt, $fstroke, $fclip, $ffill, $border, $ln, $align, $fill, $link, $stretch, $ignore_min_height, $calign, $valign, $rtloff );
    return $pdf;
  }
  public function data_method_example($data){
    $data->set('pages', array(array(
      $this->getElement('Cell', array('txt' => 'This text is from an example method.')),
      )));
    return $data;
  }
  public function getElement($method, $data = array()){
    return array('method' => $method, 'data' => $data);
  } 
}
