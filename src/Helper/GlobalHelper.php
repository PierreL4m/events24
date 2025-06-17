<?php

namespace App\Helper;

use App\Entity\Accreditation;
use App\Entity\CandidateParticipation;
use App\Entity\CandidateParticipationComment;
use App\Entity\CandidateUser;
use App\Entity\ClientUser;
use App\Entity\ExposantScanUser;
use App\Entity\ExposantUser;
use App\Entity\L4MUser;
use App\Entity\Section;
use Doctrine\Common\Collections\ArrayCollection;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;
use Imagine\Imagick\Imagine;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class GlobalHelper
{
	public function escapeFrenchChar($name)
	{
		setlocale(LC_CTYPE, 'fr_FR.UTF-8');

		return iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name);
	}

	public function generateSlug($name,$underscore=null){

		$name = $this->escapeFrenchChar($name);

        if($underscore){
            $name = preg_replace('#[^a-z0-9_-]#', '_', mb_strtolower($name));
        }
        else{
            $name = preg_replace('#[^a-z0-9_-]#', '-', mb_strtolower($name));
        }
        $name = preg_replace('/-+/', '-', ($name)); // Replaces multiple hyphens with single one.

        return $name;
	}

    public function generateSlugUnderscore($name)
    {
        return $this->generateSlug($name,true);
    }

	public function escapeSpaces($name){

		$name = $this->escapeFrenchChar($name);

        return $this->escapeOnlySpaces($name);
	}

    public function escapeOnlySpaces($name){

        $name = preg_replace('#[^.a-z0-9_-]#', '', mb_strtolower($name));

        return $name;
    }

	//thanks stackoverflow
    public function get_string_between($string, $start, $end){
        $string = " ".$string;
        $ini = strpos($string,$start);
        if ($ini == 0) return "";
        $ini += strlen($start);
        $len = strpos($string,$end,$ini) - $ini;

        return substr($string,$ini,$len);
    }

    public function resize($absolute_path,$wdt,$hgt)
    {
        $imagine = new Imagine();
        $image = $imagine->open($absolute_path);
        $width = $image->getSize()->getWidth();
        $height = $image->getSize()->getHeight();

        if ($width > $wdt || $height > $hgt ){
            $box = new Box($width,$height);
            //check ratio
            if ($width > $height){
                $image->resize($box->widen($wdt));
                $new_size = $image->getSize();

                if($new_size->getHeight() > $hgt){
                    $image->resize($box->heighten($hgt));
                }
            }
            else{
                $image->resize($box->heighten($hgt));
                $new_size = $image->getSize();

                if($new_size->getWidth() > $wdt){
                    $image->resize($box->widen($wdt));
                }
            }
        }
        $image->usePalette(new RGB());
        $size  = new Box($wdt, $hgt);
        $final_image = $imagine->create($size);
        $new_size = $image->getSize();
        $new_width = $new_size->getWidth();
        $new_height = $new_size->getHeight();

        $x = ($wdt - $new_width) / 2 ;
        $y = ($hgt - $new_height) / 2 ;

        $final_image->paste($image,new Point($x, $y));

        return $final_image;
    }

    /**
     * Returns all children class of a doctrine mother class
     * @param  namespace $class is the mother class
     * @return array of string of Children class name
     */
    public function getChildren($class)
    {
        $reflexionClass = new \ReflectionClass($class);

        $class_name = $reflexionClass->getShortName();

        $doc_comments = $reflexionClass->getDocComment();
        $discriminators = $this->get_string_between($doc_comments,'@ORM\DiscriminatorMap({','})');
        $table = explode(',',$discriminators);
        $res = array();

        foreach ($table as $value) {
            $pos = strpos($value,"=");

            $child_discriminator = preg_replace('#[^.a-zA-Z0-9_-]#', '', substr($value, 0, $pos - 1 ));
            $child_class = preg_replace('#[^.a-zA-Z0-9_-]#', '', substr($value, $pos + 1 ));
            $res[$child_discriminator] = $child_class;
        }
        return $res;
    }

    /**
     * Returns all children class of a doctrine mother class
     * @param  namespace $class is the mother class
     * @return array of string of Children class name
     */
    public function getReadableChildren($class)
    {
        $reflexionClass = new \ReflectionClass($class);

        $class_name = $reflexionClass->getShortName();

        $doc_comments = $reflexionClass->getDocComment();
        $discriminators = $this->get_string_between($doc_comments,'@ORM\DiscriminatorMap({','})');
        $table = explode(',',$discriminators);
        $res = array();

        foreach ($table as $value) {
            $pos = strpos($value,"=");
            $child_class = preg_replace('#[^.a-zA-Z0-9_-]#', '', substr($value, $pos + 1 ));
            $child_namespace = '\App\Entity\\'.$child_class;
            $entity = new $child_namespace;
            $type = $entity->getType();
            $res[$type] = $child_class;
        }

        return $res;
    }

    public function checkCommentAccess(ClientUser $user, CandidateParticipationComment $comment, $context)
    {

        if(($user instanceof ExposantUser && $comment->getOrganizationParticipation()->getResponsable() == $user) ||
            ($user instanceof ExposantScanUser && $comment->getOrganizationParticipation()->getOrganization() == $user->getOrganization())
    ){

            return true;
        }
        elseif ($context == 'api') {
            $message = "Nous sommes désolés mais vous ne pouvez pas accèder à cette note";
        }
        else{
            $message = 'User '.$user." id=".$user->getId()." cannot access comment id=".$comment->getId();
        }
        throw new AccessDeniedException($message);
    }

    public function handleResponse($return)
    {
        if ($return instanceof Response){
            return $return;
        }

        return new Response($return);
    }

    /**
     * Generate a random string, using a cryptographically secure
     * pseudorandom number generator (random_int)
     *
     * For PHP 7, random_int is a PHP core function
     * For PHP 5.x, depends on https://github.com/paragonie/random_compat
     *
     * @param int $length      How many characters do we want?
     * @param string $keyspace A string of all possible characters
     *                         to select from
     * @return string
     */
    public function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces []= $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }

    public function generateQrCode(CandidateParticipation $participation)
    {
        $candidate = $participation->getCandidate();
        $qrcode = [
                    'lastName' => $candidate->getLastName(),
                    'firstName' => $candidate->getFirstName(),
                    'userId' => $candidate->getId(),
                    'participationId' => $participation->getId(),
                    'event_id' => $participation->getEvent()->getId()
                ];

        $urlQRCode = "https://api.qrserver.com/v1/create-qr-code/?size=500x500&data=".base64_encode(json_encode($qrcode));
        $participation->setQrCode($urlQRCode);
    }

    public function generateQrCodeAccred(Accreditation $accreditation)
    {
        $qrcode = [
            'lastName' => $accreditation->getLastName(),
            'firstName' => $accreditation->getFirstName(),
            'email' => $accreditation->getEmail(),
            'phone' => $accreditation->getPhone(),
            'structure' => $accreditation->getParticipation()->getCompanyName()
        ];

        $urlQRCode = "https://api.qrserver.com/v1/create-qr-code/?size=500x500&data=".base64_encode(json_encode($qrcode));;
        $accreditation->setQrCode($urlQRCode);
    }

    public function mapInputHost($host, $map) {
        return (isset($map[$host]) ? $map[$host] : $map['default']);
    }

		public function progressProfilCandidate(CandidateUser $candidate)
    {
			$mustHaveInfosArray =array($candidate->getWantedJob(), $candidate->getMobility(), $candidate->getCity(), $candidate->getCv(), $candidate->getSectors(), $candidate->getDegree(), $candidate->getWorking());
			$neededInfos = count($mustHaveInfosArray);
			foreach($mustHaveInfosArray as $missMustHaveInfos){
				if(is_null($missMustHaveInfos)){
					$neededInfos --;
				}
			}
			if(count($candidate->getSectors()) == 0){
				$neededInfos --;
			}
			return $totalValidatesInfos = $neededInfos/count($mustHaveInfosArray)*100;

    }
}
