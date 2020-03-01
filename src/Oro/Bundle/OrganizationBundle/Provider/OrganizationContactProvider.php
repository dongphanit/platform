<?php

namespace Oro\Bundle\OrganizationBundle\Provider;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Persistence\ManagerRegistry;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\EmailBundle\Entity\Provider\EmailOwnerProviderInterface;
use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;
use Oro\Bundle\OrganizationBundle\Entity\Repository\OrganizationRepository;
/**
 * Email owner provider for Organization User
 */
class OrganizationContactProvider 
{

     /** @var ManagerRegistry */
     private $registry;

     /** @var AclHelper */
     private $aclHelper;

     /**
      * @param ManagerRegistry $registry
      * @param AclHelper $aclHelper
      * @param string $customerAddressClass
      * @param string $customerUserAddressClass
      */
     public function __construct(
         ManagerRegistry $registry,
         AclHelper $aclHelper
     ) {
         $this->registry = $registry;
         $this->aclHelper = $aclHelper;
     }

     
    /**
     * {@inheritdoc}
     */
    public function getEmailOwnerClass()
    {
        return OrganizationUser::class;
    }

    /**
     * @return []
     */
    public function getOrganizationContactWithPhones($lstPhone)
    {
        
        $repository = $this->getOrganizationContactRepository();
        
        $result = $repository->getOrganizationsWithLstPhone($lstPhone);


        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function findEmailOwner(EntityManager $em, $email)
    {
        return $em->getRepository(OrganizationUser::class)->findOneBy(['email' => $email]);
    }

    /**
     * @return OrganizationRepository
     */
    protected function getOrganizationContactRepository()
    {
        return $this->registry->getManagerForClass(Organization::class)->getRepository(Organization::class);
    }

}
