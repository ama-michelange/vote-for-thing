<?php

use App\Entity\V1\Group as VfaGroup;
use Domain\Entity\GroupEntity;
use Illuminate\Database\Eloquent\Collection;

class MigrateVfaToVft_GroupsToGroups extends MigrateVfaToVft_Base
{
   protected $vfaModelClass = VfaGroup::class;
   protected $vftModelClass = GroupEntity::class;

   /**
    * Convert VFA Entities Collections to VFT Entities and save it.
    * @param Collection $pVfaModels
    */
   protected function saveTo(Collection $pVfaModels): void
   {
      VftMigrateTools::saveToGroups($pVfaModels);
   }
}
