<?php

namespace Oro\Bundle\UserBundle\Api\Processor;

use Oro\Bundle\ApiBundle\Processor\Create\CreateContext;
use Oro\Bundle\ApiBundle\Util\DoctrineHelper;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\UserBundle\Api\Model\UserLogin;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\UserBundle\Entity\UserApi;
use Oro\Component\ChainProcessor\ContextInterface;
use Oro\Component\ChainProcessor\ProcessorInterface;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Checks whether the login credentials are valid
 * and if so, sets API access key of authenticated customer user to the model.
 */
class HandleLogin implements ProcessorInterface
{
    /** @var string */
    private $authenticationProviderKey;

    /** @var AuthenticationProviderInterface */
    private $authenticationProvider;

    /** @var ConfigManager */
    private $configManager;

    /** @var DoctrineHelper */
    private $doctrineHelper;

    /** @var TranslatorInterface */
    private $translator;

      /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices()
    {
        return array_merge(parent::getSubscribedServices(), [
            CsrfTokenManagerInterface::class,
            AuthenticationUtils::class,
            RequestStack::class
        ]);
    }
    
    /**
     * @param string                          $authenticationProviderKey
     * @param AuthenticationProviderInterface $authenticationProvider
     * @param ConfigManager                   $configManager
     * @param DoctrineHelper                  $doctrineHelper
     * @param TranslatorInterface             $translator
     */
    public function __construct(
        string $authenticationProviderKey,
        AuthenticationProviderInterface $authenticationProvider,
        ConfigManager $configManager,
        DoctrineHelper $doctrineHelper,
        TranslatorInterface $translator
    ) {
        $this->authenticationProviderKey = $authenticationProviderKey;
        $this->authenticationProvider = $authenticationProvider;
        $this->configManager = $configManager;
        $this->doctrineHelper = $doctrineHelper;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContextInterface $context)
    {
        /** @var CreateContext $context */

        $model = $context->getResult();
        if (!$model instanceof UserLogin || $model->getApiKey()) {
            // the request is already handled
            return;
        }

        $authenticatedUser = $this->authenticate($model)->getUser();
        // throw new \LogicException(sprintf(
        //     'Invalid authentication provider. The provider key is "%s".',
        //     $authenticatedUser
        // ));
        if (!$authenticatedUser instanceof User) {
            throw new AccessDeniedException('The login via API is not supported for this user.');
        }

        $apiKey = $this->getApiKey($authenticatedUser);
        if (!$apiKey) {
            if (!$this->isApiKeyGenerationEnabled()) {
                throw new AccessDeniedException('The API access key was not generated for this user.');
            }
            $apiKey = $this->generateApiKey($authenticatedUser);
        }

        $model->setApiKey($apiKey);
    }

    /**
     * @param UserLogin $model
     *
     * @return TokenInterface
     */
    private function authenticate(UserLogin $model): TokenInterface
    {
        $token = new UsernamePasswordToken(
            $model->getUsername(),
            $model->getPassword(),
            $this->authenticationProviderKey
        );
       
        if (!$this->authenticationProvider->supports($token)) {
            throw new \LogicException(sprintf(
                'Invalid authentication provider. The provider key is "%s".',
                $this->authenticationProviderKey
            ));
        }

        try {
            return $this->authenticationProvider->authenticate($token);
        } catch (AuthenticationException $e) {
            throw new AccessDeniedException(sprintf(
                'The user authentication fails. Reason: %s',
                $this->translator->trans($e->getMessageKey(), $e->getMessageData(), 'security')
            ));
        }
    }

    /**
     * @return bool
     */
    private function isApiKeyGenerationEnabled()
    {
        return (bool)$this->configManager->get('oro_customer.api_key_generation_enabled');
    }

    /**
     * @param User $user
     *
     * @return string|null
     */
    private function getApiKey(User $user)
    {
        $apiKey = $user->getApiKeys()->first();
        if (!$apiKey) {
            return null;
        }

        return $apiKey->getApiKey();
    }

    /**
     * @param User $user
     *
     * @return string
     */
    private function generateApiKey(User $user)
    {
        $apiKey = new UserApi();
        $apiKey->setApiKey($apiKey->generateKey());

        $user->addApiKey($apiKey);

        $em = $this->doctrineHelper->getEntityManager($user);
        $em->persist($apiKey);
        $em->flush();

        return $apiKey->getApiKey();
    }
}
