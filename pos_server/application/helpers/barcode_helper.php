<?php
/**
 * Barcode39 - Code 39 Barcode Image Generator
 * 
 * @package Barcode39
 * @category Barcode39
 * @name Barcode39
 * @version 1.0
 * @author Shay Anderson 05.11
 * @link http://www.shayanderson.com/php/php-barcode-generator-class-code-39.htm
 * @license http://www.gnu.org/licenses/gpl.html GPL License
 * This is free software and is distributed WITHOUT ANY WARRANTY
 */
final class Barcode39 {
	/**
	 * Code 39 format 2 specifications
	 */
	const f2B = "11";
	const f2W = "00";
	const f2b = "10";
	const f2w = "01";

	/**
	 * Barcode code
	 *
	 * @var array $_code
	 */
	private $_code = array();

	/**
	 * Code 39 matrix
	 *
	 * @var array $_codes_39
	 */
	private $_codes_39 = array(
		32 => '100011011001110110',
		36 => '100010001000100110',
		37 => '100110001000100010',
		42 => '100010011101110110',
		43 => '100010011000100010',
		45 => '100010011001110111',
		46 => '110010011001110110',
		47 => '100010001001100010',
		48 => '100110001101110110',
		49 => '110110001001100111',
		50 => '100111001001100111',
		51 => '110111001001100110',
		52 => '100110001101100111',
		53 => '110110001101100110',
		54 => '100111001101100110',
		55 => '100110001001110111',
		56 => '110110001001110110',
		57 => '100111001001110110',
		65 => '110110011000100111',
		66 => '100111011000100111',
		67 => '110111011000100110',
		68 => '100110011100100111',
		69 => '110110011100100110',
		70 => '100111011100100110',
		71 => '100110011000110111',
		72 => '110110011000110110',
		73 => '100111011000110110',
		74 => '100110011100110110',
		75 => '110110011001100011',
		76 => '100111011001100011',
		77 => '110111011001100010',
		78 => '100110011101100011',
		79 => '110110011101100010',
		80 => '100111011101100010',
		81 => '100110011001110011',
		82 => '110110011001110010',
		83 => '100111011001110010',
		84 => '100110011101110010',
		85 => '110010011001100111',
		86 => '100011011001100111',
		87 => '110011011001100110',
		88 => '100010011101100111',
		89 => '110010011101100110',
		90 => '100011011101100110'
	);

	/**
	 * Width of wide bars in barcode (should be 3:1)
	 *
	 * @var int $barcode_bar_thick
	 */
	public $barcode_bar_thick = 3;

	/**
	 * Width of thin bars in barcode (should be 3:1)
	 *
	 * @var int $barcode_bar_thin
	 */
	public $barcode_bar_thin = 1;

	/**
	 * Barcode background color (RGB)
	 *
	 * @var array $barcode_bg_rgb
	 */
	public $barcode_bg_rgb = array(255, 255, 255);

	/**
	 * Barcode height
	 *
	 * @var int $barcode_height
	 */
	public $barcode_height = 80;

	/**
	 * Barcode padding
	 *
	 * @var int $barcode_padding
	 */
	public $barcode_padding = 5;

	/**
	 * Use barcode text flag
	 *
	 * @var bool $barcode_text
	 */
	public $barcode_text = true;

	/**
	 * Barcode text size
	 *
	 * @var int $barcode_text_size
	 */
	public $barcode_text_size = 3;

	/**
	 * Use dynamic barcode width (will auto set width)
	 *
	 * @var bool $barcode_use_dynamic_width
	 */
	public $barcode_use_dynamic_width = true;

	/**
	 * Barcode width (if not using dynamic width)
	 *
	 * @var int $barcode_width
	 */
	public $barcode_width = 400;

	/**
	 * Set and format params
	 *
	 * @param string $code
	 */
	public function  __construct($code = null) {
		// format and code
		$code = (string)strtoupper($code);

		// convert code to code array
		$i = 0;
		while(isset($code[$i])) {
			$this->_code[] = $code[$i++];
		}

		// add start and stop symbols
		array_unshift($this->_code, "*");
		array_push($this->_code, "*");
	}

	/**
	 * Draw barcode (and save as file if filename set)
	 *
	 * @param string $filename (optional)
	 * @return bool
	 */
	public function draw($filename = null) {
		// check if GB library functions installed
		if(!function_exists("imagegif")) {
			return false;
		}

		// check for valid code
		if(!is_array($this->_code) || !count($this->_code)) {
			return false;
		}

		// bars coordinates and params
		$bars = array();

		// position pointer
		$pos = $this->barcode_padding;

		// barcode text
		$barcode_string = null;

		// set code 39 codes
		$i = 0;
		foreach($this->_code as $k => $v) {
			// check for valid code
			if(isset($this->_codes_39[ord($v)])) {
				// valid code add code 39, also add separator between characters if not first character
				$code = ( $i ? self::f2w : null ) . $this->_codes_39[ord($v)];

				// check for valid code 39 code
				if($code) {
					// add to barcode text
					$barcode_string .= " {$v}";

					// init params
					$w = 0;
					$f2 = $fill = null;

					// add each bar coordinates and params
					for($j = 0; $j < strlen($code); $j++) {
						// format 2 code
						$f2 .= (string)$code[$j];

						// valid format 2 code
						if(strlen($f2) == 2) {
							// set bar fill
							$fill = $f2 == self::f2B || $f2 == self::f2b ? "_000" : "_fff";

							// set bar width
							$w = $f2 == self::f2B || $f2 == self::f2W ? $this->barcode_bar_thick : $this->barcode_bar_thin;

							// check for valid bar params
							if($w && $fill) {
								// add bar coordinates and params
								$bars[] = array($pos, $this->barcode_padding, $pos - 1 + $w,
									$this->barcode_height - $this->barcode_padding - 1, $fill);

								// move position pointer
								$pos += $w;
							}

							// reset params
							$f2 = $fill = null;
							$w = 0;
						}
					}
				}
				$i++;
			// invalid code, remove character from code
			} else {
				unset($this->_code[$k]);
			}
		}

		// check for valid bar coordinates and params
		if(!count($bars)) {
			// no valid bar coordinates and params
			return false;
		}

		// set barcode width
		$bc_w = $this->barcode_use_dynamic_width ? $pos + $this->barcode_padding : $this->barcode_width;

		// if not dynamic width check if barcode wider than barcode image width
		if(!$this->barcode_use_dynamic_width && $pos > $this->barcode_width) {
			return false;
		}

		// initialize image
		$img = imagecreate($bc_w, $this->barcode_height);
		$_000 = imagecolorallocate($img, 0, 0, 0);
		$_fff = imagecolorallocate($img, 255, 255, 255);
		$_bg = imagecolorallocate($img, $this->barcode_bg_rgb[0], $this->barcode_bg_rgb[1], $this->barcode_bg_rgb[2]);

		// fill background
		imagefilledrectangle($img, 0, 0, $bc_w, $this->barcode_height, $_bg);

		// add bars to barcode
		for($i = 0; $i < count($bars); $i++) {
			imagefilledrectangle($img, $bars[$i][0], $bars[$i][1], $bars[$i][2], $bars[$i][3], $$bars[$i][4]);
		}

		// check if using barcode text
		if($this->barcode_text) {
			// set barcode text box
			$barcode_text_h = 10 + $this->barcode_padding;
			imagefilledrectangle($img, $this->barcode_padding, $this->barcode_height - $this->barcode_padding - $barcode_text_h,
				$bc_w - $this->barcode_padding, $this->barcode_height - $this->barcode_padding, $_fff);

			// set barcode text font params
			$font_size = $this->barcode_text_size;
			$font_w = imagefontwidth($font_size);
			$font_h = imagefontheight($font_size);

			// set text position
			$txt_w = $font_w * strlen($barcode_string);
			$pos_center = ceil((($bc_w - $this->barcode_padding) - $txt_w) / 2);

			// set text color
			$txt_color = imagecolorallocate($img, 0, 255, 255);

			// draw barcod text
			imagestring($img, $font_size, $pos_center, $this->barcode_height - $barcode_text_h - 2,
				$barcode_string, $_000);
		}

		// check if writing image
		if($filename) {
			imagegif($img, $filename);
		// display image
		} else {
			header("Content-type: image/gif");
			imagegif($img);
		}
		
		imagedestroy($img);

		// valid barcode
		return true;
	}
}


class BarCode128 {
  const STARTA = 103;
  const STARTB = 104;
  const STARTC = 105;
  const STOP = 106;
  private $unit_width = 1; //单位宽度 缺省1个象素
  private $is_set_height = false;
  private $width = -1;
  private $heith = 35;
  private $quiet_zone = 6;
  private $font_height = 15;
  private $font_type = 4;
  private $color =0x000000;
  private $bgcolor =0xFFFFFF;
  private $image = null;
  private $codes = array("212222","222122","222221","121223","121322","131222","122213","122312","132212","221213","221312","231212","112232","122132","122231","113222","123122","123221","223211","221132","221231","213212","223112","312131","311222","321122","321221","312212","322112","322211","212123","212321","232121","111323","131123","131321","112313","132113","132311","211313","231113","231311","112133","112331","132131","113123","113321","133121","313121","211331","231131","213113","213311","213131","311123","311321","331121","312113","312311","332111","314111","221411","431111","111224","111422","121124","121421","141122","141221","112214","112412","122114","122411","142112","142211","241211","221114","413111","241112","134111","111242","121142","121241","114212","124112","124211","411212","421112","421211","212141","214121","412121","111143","111341","131141","114113","114311","411113","411311","113141","114131","311141","411131","211412","211214","211412","2331112");
  private $valid_code = -1;
  private $type ='B';
  private $start_codes =array('A'=>self::STARTA,'B'=>self::STARTB,'C'=>self::STARTC);
  private $code ='';
  private $bin_code ='';
  private $text ='';
  public function __construct($code='',$text='',$type='B')
  {
    if (in_array($type,array('A','B','C')))
      $this->setType($type);
    else
      $this->setType('B');
    if ($code !=='')
      $this->setCode($code);
    if ($text !=='')
      $this->setText($text);
  }
  public function setUnitWidth($unit_width)
  {
    $this->unit_width = $unit_width;
    $this->quiet_zone = $this->unit_width*6;
    $this->font_height = $this->unit_width*15;
    if (!$this->is_set_height)
    {
      $this->heith = $this->unit_width*35;
    }
  }
  public function setFontType($font_type)
  {
    $this->font_type = $font_type;
  }
  public function setBgcolor($bgcoloe)
  {
    $this->bgcolor = $bgcoloe;
  }
  public function setColor($color)
  {
    $this->color = $color;
  }
  public function setCode($code)
  {
    if ($code !='')
    {
      $this->code= $code;
      if ($this->text ==='')
        $this->text = $code;
    }
  }
  public function setText($text)
  {
    $this->text = $text;
  }
  public function setType($type)
  {
    $this->type = $type;
  }
  public function setHeight($height)
  {
    $this->height = $height;
    $this->is_set_height = true;
  }
  private function getValueFromChar($ch)
  {
    $val = ord($ch);
    try
    {
      if ($this->type =='A')
      {
        if ($val > 95)
          throw new Exception(' illegal barcode character '.$ch.' for code128A in '.__FILE__.' on line '.__LINE__);
        if ($val < 32)
          $val += 64;
        else
          $val -=32;
      }
      elseif ($this->type =='B')
      {
        if ($val < 32 || $val > 127)
          throw new Exception(' illegal barcode character '.$ch.' for code128B in '.__FILE__.' on line '.__LINE__);
        else
          $val -=32;
      }
      else
      {
        if (!is_numeric($ch) || (int)$ch < 0 || (int)($ch) > 99)
          throw new Exception(' illegal barcode character '.$ch.' for code128C in '.__FILE__.' on line '.__LINE__);
        else
        {
          if (strlen($ch) ==1)
            $ch .='0';
          $val = (int)($ch);
        }
      }
    }
    catch(Exception $ex)
    {
      errorlog('die',$ex->getMessage());
    }
    return $val;
  }
  private function parseCode()
  {
    $this->type=='C'?$step=2:$step=1;
    $val_sum = $this->start_codes[$this->type];
    $this->width = 35;
    $this->bin_code = $this->codes[$val_sum];
    for($i =0;$i<strlen($this->code);$i+=$step)
    {
      $this->width +=11;
      $ch = substr($this->code,$i,$step);
      $val = $this->getValueFromChar($ch);
      $val_sum += $val;
      $this->bin_code .= $this->codes[$val];
    }
    $this->width *=$this->unit_width;
    $val_sum = $val_sum%103;
    $this->valid_code = $val_sum;
    $this->bin_code .= $this->codes[$this->valid_code];
    $this->bin_code .= $this->codes[self::STOP];
  }
  public function getValidCode()
  {
    if ($this->valid_code == -1)
      $this->parseCode();
    return $this->valid_code;
  }
  public function getWidth()
  {
    if ($this->width ==-1)
      $this->parseCode();
    return $this->width;
  }
  public function getHeight()
  {
    if ($this->width ==-1)
      $this->parseCode();
    return $this->height;
  }
  public function createBarCode($image_type ='png',$file_name=null)
  {
    $this->parseCode();
    $this->image = ImageCreate($this->width+2*$this->quiet_zone,$this->heith + $this->font_height);
    $this->bgcolor = imagecolorallocate($this->image,$this->bgcolor >> 16,($this->bgcolor >> 8)&0x00FF,$this->bgcolor & 0xFF);
    $this->color = imagecolorallocate($this->image,$this->color >> 16,($this->color >> 8)&0x00FF,$this->color & 0xFF);
    ImageFilledRectangle($this->image, 0, 0, $this->width + 2*$this->quiet_zone,$this->heith + $this->font_height, $this->bgcolor);
    $sx = $this->quiet_zone;
    $sy = $this->font_height -1;
    $fw = 10; //2或3的字w的度10,4或5的字w度11
    if ($this->font_type >3)
    {
      $sy++;
      $fw=11;
    }
    $ex = 0;
    $ey = $this->heith + $this->font_height - 2;
    for($i=0;$i<strlen($this->bin_code);$i++)
    {
      $ex = $sx + $this->unit_width*(int) $this->bin_code{$i} -1;
      if ($i%2==0)
        ImageFilledRectangle($this->image, $sx, $sy, $ex,$ey, $this->color);
      $sx =$ex + 1;
    }
    $t_num = strlen($this->text);
    $t_x = $this->width/$t_num;
    $t_sx = ($t_x -$fw)/2;    //目的为了使文字居中平均分布
    for($i=0;$i<$t_num;$i++)
    {
      imagechar($this->image,$this->font_type,6*$this->unit_width +$t_sx +$i*$t_x,0,$this->text{$i},$this->color);
    }
    if (!$file_name)
    {
      header("Content-Type: image/".$image_type);
    }
    switch ($image_type)
    {
      case 'jpg':
      case 'jpeg':
        Imagejpeg($this->image,$file_name);
        break;
      case 'png':
        Imagepng($this->image,$file_name);
        break;
      case 'gif':
        break;
        Imagegif($this->image,$file_name);
      default:
        Imagepng($this->image,$file_name);
        break;
    }
  }
}


?>
