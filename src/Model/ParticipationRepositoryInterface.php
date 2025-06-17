<?php 
namespace App\Model;

use App\Entity\Participation;

interface ParticipationRepositoryInterface
{
    /**
     * @return array
     */
    public function getChildMissingDatas(Participation $participation);
}
?>