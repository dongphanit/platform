<?php

namespace Oro\Bundle\OrganizationBundle\Api\Processor;

use Oro\Bundle\ApiBundle\Processor\Create\CreateContext;
use Oro\Bundle\ApiBundle\Util\DoctrineHelper;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\OrganizationBundle\Api\Model\Contact;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Component\ChainProcessor\ContextInterface;
use Oro\Component\ChainProcessor\ProcessorInterface;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Oro\Bundle\OrganizationBundle\Provider\OrganizationContactProvider;
/**
 * Checks whether the login credentials are valid
 * and if so, sets API access key of authenticated Organization user to the model.
 */
class HandleSearchByPhone implements ProcessorInterface
{
    /** @var string */
    private $authenticationProviderKey;

    /** @var AuthenticationProviderInterface */
    private $organizationContactProvider;

    /** @var ConfigManager */
    private $configManager;

    /** @var DoctrineHelper */
    private $doctrineHelper;

    /** @var TranslatorInterface */
    private $translator;

    /**
     * @param string                          $authenticationProviderKey
     */
    public function __construct(
        string $authenticationProviderKey,
        OrganizationContactProvider $organizationContactProvider
    ) {
        $this->organizationContactProvider = $organizationContactProvider;
    }

    function debug_to_console($data) {
        $output = $data;
        if (is_array($output))
            $output = implode(',', $output);
    
        echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContextInterface $context)
    {
        /** @var CreateContext $context */
        $model = $context->getResult();
        $results = $this->organizationContactProvider->getOrganizationByPhone($model->getPhone(), $model->getCountryCode());
        $model-> setData($results);
    
       
        // $repository = $this->getOrganizationRepository();
        // $children = $repository->getOrganizationsWithLstPhone($model->getLstPhone(), $this->aclHelper);

        // throw new \LogicException(sprintf(
        //     'Invalid authentication provider. The provider key is "%s".',
        //     $model->getLstPhone()
        // ));
       
        // if (!$model instanceof Contact || $model->getApiKey()) {
        //     // the request is already handled
        //     return;
        // }
        // $this -> debug_to_console('hahha');
        // $model->setApiKey($apiKey);
    }

    /**
     * @return OrganizationRepository
     */
    protected function getOrganizationRepository()
    {
        return $this->doctrine->getManagerForClass(Organization::class)->getRepository(Organization::class);
    }

}
