<?php

    // ini_set('memory_limit', '256M');
    
    class imagediff
    {
        private $image1;
        private $image2;
        
        function  __construct($img1, $img2)
        {
            $this->image1['path'] = realpath($img1);
            $this->image2['path'] = realpath($img2);
            if($this->image1['path'] === false || $this->image2['path'] === false)
            {
                throw new Exception('Image "'.htmlspecialchars( $this->image1 ? $img2 : $img1 ).'" not found!');
            }
            else
            {
                $this->image1['type'] = $this->imagetyte($this->image1['path']);
                $this->image2['type'] = $this->imagetyte($this->image2['path']);
            }
        }

        private function imagetyte($imgname)
        {
            $file_info = pathinfo($imgname);
            if(!empty ($file_info['extension']))
            {
                $filetype = strtolower($file_info['extension']);
                $filetype = $filetype == 'jpg' ? 'jpeg' : $filetype;
                $func = 'imagecreatefrom' . $filetype;
                if(function_exists($func))
                {
                    return $filetype;
                }
                else
                {
                    throw new Exception('File type "'.htmlspecialchars( $filetype ).'" not supported!');
                }
            }
            else
            {
                throw new Exception('File type not supported!');
            }
        }

        private function imagehex($image)
        {
            $size = getimagesize($image['path']);
            $func = 'imagecreatefrom'.$image['type'];
            $imageres = $func($image['path']);
            $zone = imagecreate(20, 20);
            imagecopyresized($zone, $imageres, 0, 0, 0, 0, 20, 20, $size[0], $size[1]);
            $colormap = array();
            $average = 0;
            $result = array();
            for($x=0; $x<20; $x++)
            {
                for($y=0; $y<20; $y++)
                {
                    $color = imagecolorat($zone, $x, $y);
		 
                    $color = imagecolorsforindex($zone, $color);
	 
                    $colormap[$x][$y]= 0.212671 * $color['red'] + 0.715160 * $color['green'] + 0.072169 * $color['blue'];
                    $average += $colormap[$x][$y];
                }
            }
            $average /= 400;
            for($x=0; $x<20; $x++)
            {
                for($y=0; $y<20; $y++)
                {
                    $result[]=($x<10?$x:chr($x+97)) . ($y<10?$y:chr($y+97)) . round(2*$colormap[$x][$y]/$average);
                }
            }
            return $result;
        }

        public function diff()
        {
            $hex1 = $this->imagehex($this->image1) ;
            $hex2 = $this->imagehex($this->image2);
            $result=(count($hex1) + count($hex2)) - count(array_diff($hex2,$hex1))-400; 
	    return $result / ( ( count($hex1) + count($hex2) ) / 2 );
        }
    }

//    $diff = new imagediff('cropped.jpg', 'withtrashcropped.jpg');
//    print ($diff->diff() * 100 ).'%';

?>
