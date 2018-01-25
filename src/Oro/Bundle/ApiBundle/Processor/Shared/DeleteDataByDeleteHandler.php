<?php

namespace Oro\Bundle\ApiBundle\Processor\Shared;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Oro\Component\ChainProcessor\ProcessorInterface;
use Oro\Component\ChainProcessor\ContextInterface;
use Oro\Bundle\ApiBundle\Processor\Context;
use Oro\Bundle\ApiBundle\Util\DoctrineHelper;
use Oro\Bundle\SoapBundle\Handler\DeleteHandler;

abstract class DeleteDataByDeleteHandler implements ProcessorInterface
{
    /** @var DoctrineHelper */
    protected $doctrineHelper;

    /** @var ContainerInterface */
    protected $container;

    /**
     * @param DoctrineHelper     $doctrineHelper
     * @param ContainerInterface $container
     */
    public function __construct(DoctrineHelper $doctrineHelper, ContainerInterface $container)
    {
        $this->doctrineHelper = $doctrineHelper;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContextInterface $context)
    {
        /** @var Context $context */

        if (!$context->hasResult()) {
            // result deleted or not supported
            return;
        }

        if (!$this->doctrineHelper->isManageableEntityClass($context->getClassName())) {
            // only manageable entities are supported
            return;
        }

        $deleteHandlerServiceId = $context->getConfig()->getDeleteHandler();
        if (!$deleteHandlerServiceId) {
            $deleteHandlerServiceId = $this->getDefaultDeleteHandler();
        }

        $deleteHandler = $this->container->get($deleteHandlerServiceId);
        if ($deleteHandler instanceof DeleteHandler) {
            $this->processDelete($context, $deleteHandler);
            $context->removeResult();
        }
    }

    /**
     * @return string
     */
    protected function getDefaultDeleteHandler()
    {
        return 'oro_soap.handler.delete';
    }

    /**
     * Deletes entity(es) stored in the result property of the Context using the delete handler
     *
     * @param Context       $context
     * @param DeleteHandler $handler
     */
    abstract protected function processDelete(Context $context, DeleteHandler $handler);
}
