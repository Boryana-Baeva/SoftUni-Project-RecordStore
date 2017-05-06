<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 1.5.2017 г.
 * Time: 02:12 ч.
 */

namespace RecordStoreBundle\Services;


use RecordStoreBundle\Entity\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\DateTime;

class UploadService
{
    private $dir;

    /**
     * ImageUploaderService constructor.
     * @param $dir
     * @param $imagesViewDir
     */
    public function __construct($dir)
    {
        $this->dir = $dir;

    }
    /**
     * @param $avatar Image
     */
    public function uploadAvatar($avatar)
    {
        /** @var UploadedFile $file */
        $filename = md5(uniqid().$avatar->getUrl()->guessExtension());

        $avatar->getUrl()->move(
           $this->dir . '/../web/images/avatar/',
            $filename
        );
        $avatar->setUrl("/images/avatar/".$filename);
        return $avatar;

    }
    
}
